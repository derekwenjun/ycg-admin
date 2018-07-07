<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


/**
 * 订单类型：
 * 0. 自己操作的BC订单
 * 1. 自己操作的CC订单
 * 6. EMS托管的订单
 */
class Order extends Model
{
    public function orderProducts()
    {
        return $this->hasMany('App\OrderProduct');
    }
    
    // 对应的追踪信息
    public function trackings()
    {
        return $this->hasMany('App\Tracking');
    }
    
    /**
     * 一个运单属于一个网点
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }
    
    /**
     * 一个运单属于一个网点
     */
    public function client()
    {
        return $this->belongsTo('App\Client');
    }
    
    /**
     * 一个运单属于一个类型
     */
    public function type()
    {
        return $this->belongsTo('App\OrderType');
    }
    
    /**
     * 一个运单属于一个状态
     */
    public function status()
    {
        return $this->belongsTo('App\OrderStatus');
    }
}
