<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    //
    protected $table = "providers";
    protected $primaryKey = "id_provider";

    protected function purchases() {
        return $this->hasMany('App\Purchase', 'id_provider', 'id_provider');
    }
}
