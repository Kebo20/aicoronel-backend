<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class User extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'=>$this->id,
            'name'=>htmlspecialchars($this->name),
            'email'=>htmlspecialchars($this->email),
            'password'=>htmlspecialchars($this->password),
            'id_role'=>$this->id_role,
            'role_name'=>htmlspecialchars($this->role->name),
            'status'=>$this->status
        ];
    }
}
