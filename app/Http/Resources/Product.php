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
            'name'=>htmlspecialchars($this->name),
            'name_brand'=>$this->brand?htmlspecialchars($this->name.' - '.$this->brand):htmlspecialchars($this->name),
            'price'=>htmlspecialchars($this->price),
            'price2'=>htmlspecialchars($this->price2),

            'brand'=>htmlspecialchars($this->brand),
            'units'=>htmlspecialchars($this->units),
            'id_category'=>$this->id_category,
            'category_name'=>$this->id_category?htmlspecialchars($this->category->name):'',
            //'purchases_detail'=>PurchaseDetail::collection($this->purchases_detail),
            'status'=>$this->status
        ];
    }
}
