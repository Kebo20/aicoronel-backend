<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MoveProduct extends Model
{
    //
    protected $table = "moves_product";
    protected $primaryKey = "id_move_product";

    public function product() {
        return $this->hasOne('App\Product', 'id_product', 'id_product');
    }

    public function lot() {
        return $this->hasOne('App\Lot', 'id_lot', 'id_lot');
    }

}
