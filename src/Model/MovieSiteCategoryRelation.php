<?php


namespace App\Model;

class MovieSiteCategoryRelation extends Base
{
    public $timestamps = false;

    protected $table = 'source_website_category_local_category_relation';

    protected $fillable = ['source_website_id', 'source_website_category_id', 'local_category_id', 'is_show'];

    public $incrementing = false;
}