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
use Ramsey\Uuid\Type\Integer;

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
     * @return App\Http\Resources\ArticleListResource
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
     * @return App\Http\Resources\ArticleResource
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
        $article->slug = Str::slug($request->title);

        // later login user
        $article->user_id = $request->user_id;
        $article->save();


        // apply tags
        $article = $this->articles->applyTags($article->id, $request->tags);

        return new ArticleResource($article);
    }


    /**
     * findArticle
     * find Article by id
     *
     * @param  int $id
     * @return App\Http\Resources\ArticleResource
     */
    public function findArticle(int $id): ArticleResource
    {
        $article = $this->articles->find($id);
        return new ArticleResource($article);
    }

    /**
     * findBySlug
     * Get Article by slug for public view
     *
     * @param  mixed $slug
     * @return ArticleResource
     */
    public function findBySlug(string $slug): ArticleResource
    {
        $article = $this->articles->findWhereFirst('slug', $slug);
        return new ArticleResource($article);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request

     * @return ArticleResource
     */
    // public function update(Request $request, Article $article)
    public function update(Request $request, int $id): ArticleResource
    {
        $article = $this->articles->find($id);
        // $this->authorize('update', $article);

        $this->validate($request, [
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'publication_date' => ['date'],
            'expire_at' => ['date'],
            'tags' => ['required'],
        ]);

        $article = $this->articles->update($id, [
            'title' => $request->title,
            'body' => $request->body,
            'publication_date' => $request->publication_date,
            'expire_at' => $request->expire_at,
            'slug' => Str::slug($request->title),
        ]);


        // apply tags
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

    /**
     * publish
     * publish an article by id by setting the current date
     *
     * @param  mixed $id
     * @return ArticleResource
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
