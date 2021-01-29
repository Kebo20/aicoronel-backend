<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Sale extends JsonResource
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
            'id_sale'=>$this->id_sale,
            'date'=>htmlspecialchars(date_create_from_format('Y-m-d', $this->date)->format('d/m/Y')),
            'subtotal'=>htmlspecialchars($this->subtotal),
            'igv'=>htmlspecialchars($this->igv),
            'total'=>htmlspecialchars($this->total),
            'discount'=>htmlspecialchars($this->discount),
            'type_doc'=>htmlspecialchars($this->type_doc),
            'number_doc'=>htmlspecialchars($this->number_doc),
            'observation'=>htmlspecialchars($this->observation),
            'id_client'=>$this->id_client,
            'client_name'=>htmlspecialchars($this->client->name),
            'id_storage'=>$this->id_storage,
            'storage_name'=>htmlspecialchars($this->storage->name),
            'detail'=>SaleDetail::collection($this->salesDetail),

            'status'=>$this->status
        ];
    }
}
