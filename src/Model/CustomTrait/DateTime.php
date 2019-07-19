<?php


namespace App\Model\CustomTrait;


use Carbon\Carbon;

trait DateTime
{
    public function getCreateDateAttribute()
    {
        return $this->attributes['created_at'] ? $this->created_at->toDateString() : '';
    }

    protected function getArrayableAppends()
    {
        $this->appends = array_unique(array_merge($this->appends, ['create_date']));
        return parent::getArrayableAppends();
    }
}