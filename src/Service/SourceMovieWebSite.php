<?php


namespace App\Service;

use App\Model\SourceMovieWebsite as SourceMovieWebsiteModel;

class SourceMovieWebSite
{
    public static function getFullMovies($websiteId) {
        return self::getMovies($websiteId, [
            'ac' => 'videolist',
        ]);
    }

    public static function getMovies($websiteId, $params, $page = 1)
    {
        $webSite = SourceMovieWebsiteModel::find(1);

        return $webSite;
    }
}