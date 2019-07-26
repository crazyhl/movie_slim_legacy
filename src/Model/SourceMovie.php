<?php


namespace App\Model;

class SourceMovie extends Base
{
    protected $table = 'source_movie';

    public function localMovie()
    {
        return $this->belongsTo(Movie::class, 'name_md5', 'name_md5');
    }

    public function getFormatMovieListAttribute($value)
    {
        $movieList = explode('#', $this->movie_list);
        $formatMovieList = [];
        foreach ($movieList as $movie) {
            $movieInfo = explode('$', $movie);
            $formatMovieList[] = [
                'name' => $movieInfo[0],
                'url' => $movieInfo[1],
            ];
        }
        return $formatMovieList;
    }
}