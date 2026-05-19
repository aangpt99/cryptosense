<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $table = 'articles';

    protected $fillable = [
        'title',
        'url',
        'source',
        'thumbnail',
        'published_at',
        'sentiment',
        'sentiment_score',
        'coin_symbol',
        'inserted_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'inserted_at'  => 'datetime',
    ];

    public $timestamps = false;
}