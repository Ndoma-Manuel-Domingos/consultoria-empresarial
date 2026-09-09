<?php

namespace App\Support;

use App\Models\Tenant;
use RuntimeException;

class TenantManager
{
    protected ?Tenant $tenant = null;

    public function set(Tenant $tenant): void
    {
        $this->tenant = $tenant;
    }

    public function current(): ?Tenant
    {
        return $this->tenant;
    }

    public function id(): ?int
    {
        return $this->tenant?->id;
    }

    public function has(): bool
    {
        return $this->tenant !== null;
    }

    public function forget(): void
    {
        $this->tenant = null;
    }

    public function get(): Tenant
    {
        if (!$this->tenant) {
            throw new RuntimeException(
                'Nenhum tenant foi definido.'
            );
        }

        return $this->tenant;
    }
}
