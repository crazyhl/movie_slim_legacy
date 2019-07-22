<?php


namespace App\Model;

class CronJob extends Base
{
    protected $table = 'cron_job';


    protected $fillable = [
        'name',
        'params',
        'type',
        'execute_time',
        'max_execute_time',
        'start_time',
        'status',
    ];
}