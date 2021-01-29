<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PurchaseDetail extends Model
{
    //
    protected $table = "purchases_detail";
    protected $primaryKey = "id_purchase_detail";

    protected function purchase() {
        return $this->hasOne('App\Purchase', 'id_purchase', 'id_purchase');
    }

    protected function product() {
        return $this->hasOne('App\Product', 'id_product', 'id_product');
    }

    protected function lot() {
        return $this->hasOne('App\Lot', 'id_lot', 'id_lot');
    }
}
