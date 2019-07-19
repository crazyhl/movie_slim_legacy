<?php


namespace App\Model;

use App\Model\Builder\CustomBuilder;
use App\Model\CustomTrait\DateTime;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Base extends Model
{
    use DateTime;

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function newEloquentBuilder($query)
    {
        return new CustomBuilder($query);
    }
}