<?php

namespace App\Exceptions;

use RuntimeException;

class SyncConflictException extends RuntimeException
{
    public string $uuid;
    public array $conflictData;

    public function __construct(string $uuid, array $conflictData = [])
    {
        $this->uuid = $uuid;
        $this->conflictData = $conflictData;
        parent::__construct("Sync conflict for UUID: {$uuid}");
    }
}
