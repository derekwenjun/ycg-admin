<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'order_products';
    
    /**
     * Get the phone record associated with the user.
     */
    public function category()
    {
        return $this->belongsTo('App\Category');
    }
}
