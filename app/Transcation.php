<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transcation extends Model
{
    /**
     * 一个客户属于一个服务点
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }
    
    /**
     * 一个客户属于一个服务点
     */
    public function client()
    {
        return $this->belongsTo('App\Client');
    }
}
