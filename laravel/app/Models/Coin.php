<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coin extends Model
{
    protected $primaryKey = 'symbol'; // ⬅️ ini kuncinya

    public $incrementing = false; // karena bukan angka

    protected $keyType = 'string'; // karena symbol string

    public $timestamps = false;

    protected $fillable = [
        'name',
        'symbol',
        'logo',
        'keywords',
        'color',
    ];
}