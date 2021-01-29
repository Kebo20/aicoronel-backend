<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Client extends JsonResource
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
            'id_client'=>$this->id_client,
            'name'=>htmlspecialchars($this->name),
            'type_doc'=>htmlspecialchars($this->type_doc),
            'number_doc'=>htmlspecialchars($this->number_doc),
            'address'=>htmlspecialchars($this->address),
            'phone'=>htmlspecialchars($this->phone),
            'email'=>htmlspecialchars($this->email),
            'status'=>$this->status
        ];
    }
}
