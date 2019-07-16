<?php


namespace App\Model;


class Category extends Base
{
    protected $table = 'category';

    public function parent() {
        return $this->belongsTo($this, 'parent_id', 'id');
    }

    public function childList() {
        return $this->hasMany($this, 'parent_id','id');
    }
}