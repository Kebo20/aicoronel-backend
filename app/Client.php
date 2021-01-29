<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    //
    protected $table = "clients";
    protected $primaryKey = "id_client";

    protected function sales() {
        return $this->hasMany('App\Sale', 'id_client', 'id_client');
    }
}
