<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentTaggable\Taggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Article extends Model
{
    use HasFactory;
    use Taggable;


    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'publication_date',
        'expire_at'
    ];

    protected $fillable = [
        'user_id',
        'title',
        'body',
        'publication_date',
        'expire_at',
        'slug',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
