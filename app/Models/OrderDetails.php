<?php

namespace App\Models;
use illuminate\database\eloquent\model;


class OrderDetails extends model{
    
    protected $table = 'orderdetails';
    
    protected $guarded = ['id', 'created_at', 'updated_at'];
 
 
    public function product()    {
         return $this->belongsTo('App\Models\Product','product_id')->withDefault(['name' => 'N-A']);
    }
    
   
}