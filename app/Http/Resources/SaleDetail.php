<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SaleDetail extends JsonResource
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
            'product_name'=>htmlspecialchars($this->product->name),
            'status'=>$this->status
        ];
    }
}
