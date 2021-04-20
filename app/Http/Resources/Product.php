<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Product extends JsonResource
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
            'id_product'=>$this->id_product,
            'code'=>htmlspecialchars($this->code),
            'name'=>htmlspecialchars($this->name),
            'name_brand'=>htmlspecialchars($this->code .'  '.$this->name),
            'price'=>htmlspecialchars($this->price),
            'price2'=>htmlspecialchars($this->price2),
            'price_min'=>htmlspecialchars($this->price_min),
            'brand'=>htmlspecialchars($this->brand),
            'units'=>htmlspecialchars($this->units),
            'id_category'=>$this->id_category,
            'category_name'=>$this->id_category?htmlspecialchars($this->category->name):'',
            //'purchases_detail'=>PurchaseDetail::collection($this->purchases_detail),
            'status'=>$this->status
        ];
    }
}
