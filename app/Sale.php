<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    //
    protected $table = "sales";
    protected $primaryKey = "id_sale";

    protected function storage() {
        return $this->hasOne('App\Storage', 'id_storage', 'id_storage');
    }

    protected function client() {
        return $this->hasOne('App\Client', 'id_client', 'id_client');
    }

    protected function salesDetail() {
        return $this->hasMany('App\SaleDetail', 'id_sale', 'id_sale');
    }
}
