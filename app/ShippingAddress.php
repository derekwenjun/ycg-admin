<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShippingAddress extends Model
{

	/**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'shipping_addresses';

    /**
     * 一个运单属于一个网点
     */
    public function client()
    {
        return $this->belongsTo('App\Client');
    }
}
