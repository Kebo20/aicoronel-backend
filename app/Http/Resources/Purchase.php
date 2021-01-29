<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Purchase extends JsonResource
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
            'id_purchase'=>$this->id_purchase,
            'date'=>htmlspecialchars(date_create_from_format('Y-m-d', $this->date)->format('d/m/Y')),
            
            'subtotal'=>sprintf('%.2f',(htmlspecialchars($this->subtotal))),
            'igv'=>sprintf('%.2f',(htmlspecialchars($this->igv))),
            'total'=>sprintf('%.2f',(htmlspecialchars($this->total))),
            'type_doc'=>htmlspecialchars($this->type_doc),
            'number_doc'=>htmlspecialchars($this->number_doc),
            'observation'=>htmlspecialchars($this->observation),
            'id_provider'=>$this->id_provider,
            'provider_name'=>htmlspecialchars($this->provider->name),
            'id_storage'=>$this->id_storage,
            'storage_name'=>htmlspecialchars($this->storage->name),
            'detail'=>PurchaseDetail::collection($this->purchasesDetail),
            'status'=>$this->status
        ];
    }
}
