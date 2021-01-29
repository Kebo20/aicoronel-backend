<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Storage extends Model
{
    //
    protected $table = "storages";
    protected $primaryKey = "id_storage";

    public function lots() {
        return $this->hasMany('App\Lot', 'id_storage', 'id_storage');
    }

    public function purchases() {
        return $this->hasMany('App\Purchase', 'id_storage', 'id_storage');
    }

    public function sales() {
        return $this->hasMany('App\Sale', 'id_storage', 'id_storage');
    }
}
