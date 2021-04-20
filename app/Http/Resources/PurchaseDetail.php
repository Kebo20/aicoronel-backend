<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseDetail extends JsonResource
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
            
            'subtotal'=>sprintf('%.2f',(htmlspecialchars($this->subtotal))),
            'price'=>sprintf('%.2f',(htmlspecialchars($this->price))),
            'quantity'=>sprintf('%.2f',(htmlspecialchars($this->quantity))),
            'product_name'=>htmlspecialchars($this->product->code).' '.htmlspecialchars($this->product->name),
            'id_product'=>($this->id_product),
            'id_lot'=>($this->id_lot),
            'status'=>$this->status,
            // 'purchase'=>new Purchase($this->purchase)
        ];
    }
}
