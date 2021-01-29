<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Provider extends JsonResource
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
            'id_provider'=>$this->id_provider,
            'name'=>htmlspecialchars($this->name),
            'type_doc'=>htmlspecialchars($this->type_doc),
            'number_doc'=>htmlspecialchars($this->number_doc),
            'address'=>htmlspecialchars($this->address),
            'phone'=>htmlspecialchars($this->phone),
            'email'=>htmlspecialchars($this->email),
            //'purchases'=>Purchase::collection($this->purchases),
            'status'=>$this->status
        ];
    }
}
