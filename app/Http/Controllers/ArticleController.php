<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Resources\ArticleResource;
use App\Http\Resources\ArticleListResource;
use App\Repositories\Eloquent\Criteria\IsLive;
use App\Repositories\Eloquent\ArticleRepository;
use App\Repositories\Eloquent\Criteria\EagerLoad;
use App\Repositories\Eloquent\Criteria\LatestFirst;

use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;

class ArticleController extends Controller
{
    protected $articles;

    public function __construct(ArticleRepository $articles)
    {
        $this->articles = $articles;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // return ArticleResource::collection(Article::all());

        // use repository
        $articles = $this->articles->withCriteria([
            new LatestFirst(),
            new IsLive(),
            new EagerLoad(['user']),
        ])->all();

        return ArticleListResource::collection($articles);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'publication_date' => ['date'],
            'expire_at' => ['date'],
            'tags' => ['required'],

        ]);

        // would require login which i don't have
        // $article = auth()->user()->articles()->create([
        //     'title' => $request->title,
        //     'body' => $request->body,
        // ]);

        $article = new Article;
        $article->title = $request->title;
        $article->body = $request->body;
        $article->publication_date = $request->publication_date;
        $article->expire_at = $request->expire_at;

        // later login user
        $article->user_id = $request->user_id;
        $article->save();

        return new ArticleResource($article);
    }


    public function findArticle(Request $request, $id)
    {
        $article = $this->articles->find($id);
        return new ArticleResource($article);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response
     */
    // public function update(Request $request, Article $article)
    public function update(Request $request, $id)
    {
        $article = $this->articles->find($id);
        // $this->authorize('update', $article);

        $this->validate($request, [
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],

            'publication_date' => ['date'],
            'expire_at' => ['date'],
            // 'publication_date' => 'date|after:tomorrow'
        ]);

        $article = $this->articles->update($id, [
            'title' => $request->title,
            'body' => $request->body,
            'publication_date' => $request->publication_date,
            'expire_at' => $request->expire_at,
            'slug' => Str::slug($request->title),
        ]);

        $article = $this->articles->applyTags($id, $request->tags);

        return new ArticleResource($article);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response
     */
    // public function destroy(Article $article)
    public function destroy(int $id)
    {
        $article = $this->articles->find($id);

        // $this->authorize('delete', $article);

        $this->articles->delete($id);

        return response()->json(['message' => 'Record deleted'], 200);
    }

    /*
    automatically publish directly
    */

    /**
     * publish
     *
     * @param  mixed $id
     * @return void
     */
    public function publish($id)
    {
        $article = $this->articles->find($id);
        $article = $this->articles->update($id, [
            'publication_date' => Carbon::now(),
        ]);

        return new ArticleResource($article);
    }

    /**
     * updateAge
     *
     * @param  mixed $id
     * @return void
     */
    public function updateAge($id)
    {
        $article = $this->articles->find($id);

        $name = $article->user->name;

        $client = new \GuzzleHttp\Client(['headers' => ['Accept' => 'application/json']]);
        $url = 'https://api.agify.io?name=' . $name;

        $res = $client->request('GET', $url, []);

        if ($res->getStatusCode() == 200) {
            if ($res->getBody()) {
                $result = json_decode($res->getBody(), true);
            }
            if (isset($result['age'])) {
                $this->user->age = (int) $result['age'];
                $this->user->save();
            }
        }

        // $article = $this->update($id, [
        //     'publication_date' => Carbon::now(),
        // ]);

        return new ArticleResource($article);
    }
}
