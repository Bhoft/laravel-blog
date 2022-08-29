<?php

namespace App\Repositories\Contracts;

use App\Repositories\Contracts\IBase;

interface IArticle extends IBase
{
    public function applyTags($id, array $data);
}
