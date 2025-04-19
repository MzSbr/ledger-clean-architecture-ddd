<?php

namespace App\Shared\Domain\Traits;

use App\Shared\Domain\ValueObject\Name;
use Illuminate\Support\Collection;

trait HasNames
{
    /**
     * @var Collection<Name> A collection of Name value objects.
     */
    protected Collection $names;

    /**
     * Initialize the names collection.
     */
    protected function initializeNames(): void
    {
        if (!isset($this->names)) {
            $this->names = new Collection();
        }
    }

    /**
     * Get all names.
     *
     * @return Collection<Name>
     */
    public function getNames(): Collection
    {
        $this->initializeNames();
        return $this->names;
    }

    /**
     * Add a name to the collection.
     *
     * @param Name $name
     * @return self
     */
    public function addName(Name $name): self
    {
        $this->initializeNames();
        $this->names->push($name);
        return $this;
    }

    /**
     * Set the names collection.
     *
     * @param Collection<Name> $names
     * @return self
     */
    public function setNames(Collection $names): self
    {
        $this->names = $names;
        return $this;
    }

    /**
     * Get the primary name (first in the collection).
     *
     * @return Name|null
     */
    public function getPrimaryName(): ?Name
    {
        $this->initializeNames();
        return $this->names->first();
    }

    /**
     * Set the primary name (replaces the first name in the collection).
     *
     * @param Name $name
     * @return self
     */
    public function setPrimaryName(Name $name): self
    {
        $this->initializeNames();
        if ($this->names->isEmpty()) {
            $this->names->push($name);
        } else {
            $this->names->put(0, $name);
        }
        return $this;
    }
}
