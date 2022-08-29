<?php

namespace App\Repositories\Eloquent;

use App\Models\Article;
use App\Repositories\Contracts\IArticle;
use App\Repositories\Eloquent\BaseRepository;
use Illuminate\Http\Request;

class ArticleRepository extends BaseRepository implements IArticle
{
    public function model()
    {
        return Article::class;
    }

    public function applyTags($id, array $data)
    {
        $design = $this->find($id);
        $design->retag($data);
        return $design;
    }


    public function search(Request $request, array $withCriteria = [])
    {
        // extend this query
        $query = $this->model;

        // or create new
        // $query = (new $this->model)->newQuery();

        // return only designs by user
        if ($request->has_user) {
            $query->has('user');
        }

        // also possible to make it manual
        if ($request->byAuthor) {
            $query->where('user_id', (int) $request->byAuthor);
        }

        // search title and description for provided string
        if ($request->q) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->q . '%')
                    ->orWhere('body', 'like', '%' . $request->q . '%');
            });
        }

        // order the query by likes or latest first
        if (isset($request->orderBy)) {
            if (in_array($request->orderBy, ['title', 'body', 'user_id', 'updated_at', 'created_at'])) {
                $query->orderByDesc($request->orderBy);
            }
        } else {
            $query->latest();
        }

        // debug sql query
        // check if in development mode

        if ($request->_debug && config('app.debug')) {
            $sql = str_replace(array('?'), array('\'%s\''), $query->toSql());
            $sql = vsprintf($sql, $query->getBindings());
            dump($sql);
        }

        // perhaps always load the user relation here?
        // return $query->with('user')->get();
        return $query->get();
    }
}
