<?php

namespace App\Repositories\Eloquent;

use App\Models\Article;
use App\Repositories\Contracts\IArticle;
use App\Repositories\Eloquent\BaseRepository;

class ArticleRepository extends BaseRepository implements IArticle
{
    public function model()
    {
        return Article::class;
    }
}
