<?php

namespace App\Shared\Domain\ValueObject;

use App\Shared\Domain\ValueObject\ValueObject;

class EntityRef extends ValueObject
{
    /**
     * @var string|null The entity code (unique in context)
     */
    private ?string $code;

    /**
     * @var string|null The UUID of the entity
     */
    private ?string $uuid;

    /**
     * Create a new EntityRef instance.
     *
     * @param string|null $code
     * @param string|null $uuid
     */
    public function __construct(?string $code = null, ?string $uuid = null)
    {
        if ($code === null && $uuid === null) {
            throw new \InvalidArgumentException('EntityRef must have at least one of code or uuid');
        }
        
        $this->code = $code;
        $this->uuid = $uuid;
    }

    /**
     * Get the entity code.
     *
     * @return string|null
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * Get the entity UUID.
     *
     * @return string|null
     */
    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    /**
     * Create an EntityRef from an array.
     *
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['code'] ?? null,
            $data['uuid'] ?? null
        );
    }

    /**
     * Convert the EntityRef to an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        $result = [];
        
        if ($this->code !== null) {
            $result['code'] = $this->code;
        }
        
        if ($this->uuid !== null) {
            $result['uuid'] = $this->uuid;
        }
        
        return $result;
    }

    /**
     * Compare with another EntityRef object.
     *
     * @param ValueObject $other
     * @return bool
     */
    public function equals(ValueObject $other): bool
    {
        if (!$other instanceof EntityRef) {
            return false;
        }
        
        if ($this->code !== null && $other->code !== null) {
            return $this->code === $other->code;
        }
        
        if ($this->uuid !== null && $other->uuid !== null) {
            return $this->uuid === $other->uuid;
        }
        
        return false;
    }

    /**
     * String representation of the entity reference.
     *
     * @return string
     */
    public function __toString(): string
    {
        $parts = [];
        
        if ($this->code !== null) {
            $parts[] = 'code:' . $this->code;
        }
        
        if ($this->uuid !== null) {
            $parts[] = 'uuid:' . $this->uuid;
        }
        
        return '{' . implode(' / ', $parts) . '}';
    }
}
