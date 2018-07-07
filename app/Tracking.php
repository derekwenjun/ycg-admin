<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tracking extends Model
{
    /**
     * 一个客户属于一个服务点
     */
    public function order()
    {
        return $this->belongsTo('App\Order');
    }
}
