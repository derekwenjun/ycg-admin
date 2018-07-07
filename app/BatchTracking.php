<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BatchTracking extends Model
{
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'batch_trackings';
    
    /**
     * 一个追踪信息属于一个批次
     */
    public function order()
    {
        return $this->belongsTo('App\Batch');
    }
}
