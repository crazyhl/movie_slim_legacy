<?php


namespace App\Model;

use Illuminate\Database\Eloquent\Builder;

class MovieSiteCategoryRelation extends Base
{
    public $timestamps = false;

    protected $table = 'source_website_category_local_category_relation';

    protected $fillable = ['source_website_id', 'source_website_category_id', 'local_category_id', 'is_show'];

    protected $primaryKey = ['source_website_id', 'source_website_category_id'];
    public $incrementing = false;

    protected function setKeysForSaveQuery(Builder $query)
    {
        foreach($this->primaryKey as $pk) {
            $query = $query->where($pk, $this->attributes[$pk]);
        }
        return $query;
    }
}