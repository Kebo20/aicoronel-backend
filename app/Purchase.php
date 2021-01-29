<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    //
    protected $table = "purchases";
    protected $primaryKey = "id_purchase";

    protected function provider() {
        return $this->hasOne('App\Provider', 'id_provider', 'id_provider');
    }

    protected function storage() {
        return $this->hasOne('App\Storage', 'id_storage', 'id_storage');
    }

    protected function purchasesDetail() {
        return $this->hasMany('App\PurchaseDetail', 'id_purchase', 'id_purchase');
    }
}
