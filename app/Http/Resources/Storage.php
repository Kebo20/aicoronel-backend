<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Storage extends JsonResource
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
            'id_storage'=>htmlspecialchars($this->id_storage),
            'name'=>htmlspecialchars($this->name),
            'address'=>htmlspecialchars($this->address),
            'responsable'=>htmlspecialchars($this->responsable),
            //'purchases'=>Purchase::collection($this->purchases),
            'status'=>$this->status
        ];
    }
}
