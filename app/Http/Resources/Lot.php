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
            'quantity'=>htmlspecialchars($this->quantity),

            'product_name'=>htmlspecialchars($this->product->name),
            'category_name'=>htmlspecialchars($this->product->category->name),

            'storage_name'=>htmlspecialchars($this->storage->name),

            'status'=>$this->status
        ];
    }
}
