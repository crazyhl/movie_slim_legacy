<?php


namespace App\Model\CustomTrait;


use Carbon\Carbon;

trait DateTime
{
    protected function getArrayableAppends()
    {
        $this->appends = array_unique(array_merge($this->appends, ['create_date', 'create_time', 'update_date', 'update_time']));
        return parent::getArrayableAppends();
    }

    public function getCreateDateAttribute()
    {
        return $this->attributes['created_at'] ? $this->created_at->toDateString() : '';
    }

    public function getCreateTimeAttribute()
    {
        return $this->attributes['created_at'] ? $this->created_at->toTimeString() : '';
    }

    public function getUpdateDateAttribute()
    {
        return $this->attributes['updated_at'] ? $this->updated_at->toDateString() : '';
    }

    public function getUpdateTimeAttribute()
    {
        return $this->attributes['updated_at'] ? $this->updated_at->toTimeString() : '';
    }
}