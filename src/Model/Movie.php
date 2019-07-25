<?php


namespace App\Model;

class Movie extends Base
{
    protected $table = 'movie';

    public function sourceMovies()
    {
        return $this->hasMany(SourceMovie::class, 'name_md5', 'name_md5');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }
}