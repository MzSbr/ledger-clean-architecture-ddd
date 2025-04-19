<?php

namespace App\Shared\Domain\Traits;

use Illuminate\Support\Str;

trait HasUuid
{
    /**
     * Initialize the UUID when creating a new entity.
     */
    protected function initializeUuid(): void
    {
        if (empty($this->uuid)) {
            $this->uuid = (string) Str::uuid();
        }
    }

    /**
     * Get the UUID.
     *
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * Set the UUID.
     *
     * @param string $uuid
     * @return self
     */
    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;
        return $this;
    }
}
