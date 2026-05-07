<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'uuid'           => $this->uuid,
            'name'           => $this->name,
            'sku'            => $this->sku,
            'barcode'        => $this->barcode,
            'description'    => $this->description,
            'selling_price'  => $this->selling_price,
            'cost_price'     => $this->cost_price,
            'tax_rate'       => $this->tax_rate,
            'unit'           => $this->unit,
            'image_url'      => $this->image ? Storage::url($this->image) : null,
            'is_active'      => $this->is_active,
            'track_inventory'=> $this->track_inventory,
            'alert_quantity' => $this->alert_quantity,
            'current_stock'  => $this->current_stock,
            'category'       => $this->whenLoaded('category', fn() => ['id' => $this->category->id, 'name' => $this->category->name]),
            'brand'          => $this->whenLoaded('brand', fn() => $this->brand ? ['id' => $this->brand->id, 'name' => $this->brand->name] : null),
            'created_at'     => $this->created_at->toDateTimeString(),
        ];
    }
}
