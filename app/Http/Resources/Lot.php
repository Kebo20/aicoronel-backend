<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Lot extends JsonResource
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
            'id_lot'=>$this->id_lot,
            'quantity'=>htmlspecialchars($this->quantity),
            'id_product'=>$this->id_product,
            'product_name'=>htmlspecialchars($this->product->name),
            'id_storage'=>$this->id_storage,
            'storage_name'=>htmlspecialchars($this->storage->name),
            //'purchases_detail'=>PurchaseDetail::collection($this->purchases_detail),
            'status'=>$this->status
        ];
    }
}
