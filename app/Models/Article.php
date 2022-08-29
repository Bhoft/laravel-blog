<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentTaggable\Taggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Article extends Model
{
    use HasFactory;
    use Taggable;

    protected $fillable = [
        'user_id',
        'title',
        'body',
        'publication_date',
        'expire',
        'slug',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
