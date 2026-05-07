<?php

namespace App\Exceptions;

use RuntimeException;

class InsufficientStockException extends RuntimeException
{
    public string $productName;

    public function __construct(string $productName)
    {
        $this->productName = $productName;
        parent::__construct("Insufficient stock for: {$productName}");
    }
}
