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

    public function applyTags($id, array $data)
    {
        $design = $this->find($id);
        $design->retag($data);
        return $design;
    }
    
}
