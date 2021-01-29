<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SaleDetail extends Model
{
    //
    protected $table = "sales_detail";
    protected $primaryKey = "id_sale_detail";

    protected function sale() {
        return $this->hasOne('App\Sale', 'id_sale', 'id_sale');
    }

    protected function product() {
        return $this->hasOne('App\Product', 'id_product', 'id_product');
    }

    protected function lot() {
        return $this->hasOne('App\Lot', 'id_lot', 'id_lot');
    }
}
