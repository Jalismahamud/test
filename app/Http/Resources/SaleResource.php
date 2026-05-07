<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'uuid'           => $this->uuid,
            'invoice_number' => $this->invoice_number,
            'subtotal'       => $this->subtotal,
            'tax_amount'     => $this->tax_amount,
            'discount_amount'=> $this->discount_amount,
            'total_amount'   => $this->total_amount,
            'paid_amount'    => $this->paid_amount,
            'change_amount'  => $this->change_amount,
            'payment_method' => $this->payment_method,
            'status'         => $this->status,
            'note'           => $this->note,
            'sold_at'        => $this->sold_at->toDateTimeString(),
            'synced_at'      => $this->synced_at?->toDateTimeString(),
            'cashier'        => $this->whenLoaded('cashier', fn() => ['id' => $this->cashier->id, 'name' => $this->cashier->name]),
            'customer'       => $this->whenLoaded('customer', fn() => $this->customer ? ['id' => $this->customer->id, 'name' => $this->customer->name] : null),
            'items'          => $this->whenLoaded('items', fn() => $this->items->map(fn($i) => [
                'id'           => $i->id,
                'product_id'   => $i->product_id,
                'product_name' => $i->product_name,
                'product_sku'  => $i->product_sku,
                'quantity'     => $i->quantity,
                'unit_price'   => $i->unit_price,
                'discount'     => $i->discount,
                'tax_amount'   => $i->tax_amount,
                'total'        => $i->total,
            ])),
        ];
    }
}
