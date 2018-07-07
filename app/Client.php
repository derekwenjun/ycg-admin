<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    /**
     * 一个客户属于一个服务点
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    /**
     * 一个客户可以拥有很多发货地址
     */
    public function addresses()
    {
        return $this->hasMany('App\Address');
    }

    /**
     * 一个客户可以拥有很多发货地址
     */
    public function shippingAddresses()
    {
        return $this->hasMany('App\ShippingAddress');
    }
}
