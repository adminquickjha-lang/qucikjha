<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seo extends Model
{
    protected $table = 'seo';

    protected $fillable = [
        'key',
        'label',
        'title',
        'description',
        'keywords',
        'og_image',
    ];
}
