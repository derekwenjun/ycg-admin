<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    public function orders()
    {
        return $this->hasMany('App\Order');
    }
    
    /**
     * 一个批次属于一个状态
     */
    public function status()
    {
        return $this->belongsTo('App\BatchStatus');
    }
    
    /**
     * 对应的追踪信息
     */
    public function trackings()
    {
        return $this->hasMany('App\BatchTracking');
    }
}
