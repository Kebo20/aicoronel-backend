<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lot extends Model
{
    //
    protected $table = "lots";
    protected $primaryKey = "id_lot";

    public function product() {
        return $this->hasOne('App\Product', 'id_product', 'id_product');
    }

    public function storage() {
        return $this->hasOne('App\Storage', 'id_storage', 'id_storage');
    }

    public function purchasesDetail() {
        return $this->hasMany('App\PurchaseDetail', 'id_lot', 'id_lot');
    }

    public function saleDetail() {
        return $this->hasMany('App\SaleDetail', 'id_lot', 'id_lot');
    }
}
