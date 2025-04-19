<?php

namespace App\Shared\Domain\Traits;

trait HasMeta
{
    /**
     * @var array Extra metadata associated with the entity
     */
    protected array $meta = [];
    
    /**
     * Get all metadata.
     *
     * @return array
     */
    public function getMeta(): array
    {
        return $this->meta;
    }
    
    /**
     * Set all metadata.
     *
     * @param array $meta
     * @return self
     */
    public function setMeta(array $meta): self
    {
        $this->meta = $meta;
        return $this;
    }
    
    /**
     * Get a specific metadata value.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getMetaValue(string $key, $default = null)
    {
        return $this->meta[$key] ?? $default;
    }
    
    /**
     * Set a specific metadata value.
     *
     * @param string $key
     * @param mixed $value
     * @return self
     */
    public function setMetaValue(string $key, $value): self
    {
        $this->meta[$key] = $value;
        return $this;
    }
    
    /**
     * Check if a specific metadata key exists.
     *
     * @param string $key
     * @return bool
     */
    public function hasMetaKey(string $key): bool
    {
        return array_key_exists($key, $this->meta);
    }
    
    /**
     * Remove a specific metadata key.
     *
     * @param string $key
     * @return self
     */
    public function removeMetaKey(string $key): self
    {
        if ($this->hasMetaKey($key)) {
            unset($this->meta[$key]);
        }
        return $this;
    }
}
