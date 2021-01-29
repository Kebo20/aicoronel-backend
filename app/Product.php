<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    //
    protected $table = "products";
    protected $primaryKey = "id_product";

    public function category() {
        return $this->hasOne('App\Category', 'id_category', 'id_category');
    }

    public function lots() {
        return $this->hasMany('App\Lot', 'id_product', 'id_product');
    }

    public function purchasesDetail() {
        return $this->hasMany('App\PurchaseDetail', 'id_product', 'id_product');
    }

    public function salesDetail() {
        return $this->hasMany('App\SaleDetail', 'id_product', 'id_product');
    }
}
