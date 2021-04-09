<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MoveProduct extends JsonResource
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
            'date'=>htmlspecialchars(date_create_from_format('Y-m-d', $this->date)->format('d/m/Y')),
            'quantity'=>htmlspecialchars($this->quantity),
            'product_name'=>htmlspecialchars($this->product->code).' '.htmlspecialchars($this->product->name),
            'category_name'=>$this->product->id_category?htmlspecialchars($this->product->category->name):'',
            'storage_name'=>htmlspecialchars($this->lot->storage->name),
            'type'=>$this->type,

        ];
    }
}
