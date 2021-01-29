<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Role extends JsonResource
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
            'id_role'=>$this->id_role,
            'name'=>htmlspecialchars($this->name),
            'users'=>User::collection($this->users),
            'status'=>$this->status
        ];
    }
}
