<?php

namespace App\Shared\Domain\ValueObject;

/**
 * EntityRef - A value object representing a reference to an external entity.
 * 
 * This class provides a way to reference external entities by type, code, and/or UUID.
 * It ensures that at least one identifier (code or uuid) is provided and supports
 * validation, comparison, and string representation.
 */
class EntityRef extends ValueObject
{
    /**
     * @var string|null The entity type (user, invoice, etc.)
     */
    private ?string $type;

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
     * @param string|null $type The entity type (user, invoice, etc.)
     * @param string|null $code The entity code
     * @param string|null $uuid The entity UUID
     * @throws \InvalidArgumentException If neither code nor uuid is provided
     */
    public function __construct(?string $type = null, ?string $code = null, ?string $uuid = null)
    {
        if ($code === null && $uuid === null) {
            throw new \InvalidArgumentException('EntityRef must have at least one of code or uuid');
        }

        $this->type = $type;
        $this->code = $code;
        $this->uuid = $uuid;
    }

    /**
     * Get the entity type.
     *
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
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
     * @param array $data Array containing type, code, and/or uuid keys
     * @return self
     * @throws \InvalidArgumentException If neither code nor uuid is provided
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['type'] ?? null,
            $data['code'] ?? null,
            $data['uuid'] ?? null
        );
    }

    /**
     * Create an EntityRef from mixed data.
     * 
     * If data is an array, it will be passed to fromArray.
     * If data is a string, it will be used as the code.
     *
     * @param array|string $data
     * @return self
     * @throws \InvalidArgumentException If data is invalid
     */
    public static function fromMixed($data): self
    {
        if (is_array($data)) {
            return self::fromArray($data);
        } elseif (is_string($data)) {
            return new self(null, $data, null);
        } else {
            throw new \InvalidArgumentException('EntityRef::fromMixed expects array or string');
        }
    }

    /**
     * Convert the EntityRef to an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        $result = [];
        
        if ($this->type !== null) {
            $result['type'] = $this->type;
        }
        
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

        // If types are different, entities are different
        if (($this->type ?? null) !== ($other->type ?? null)) {
            return false;
        }

        // If both have codes, compare codes
        if ($this->code !== null && $other->code !== null) {
            return $this->code === $other->code;
        }

        // If both have UUIDs, compare UUIDs
        if ($this->uuid !== null && $other->uuid !== null) {
            return $this->uuid === $other->uuid;
        }

        // If we get here, one has a code and the other has a UUID, or other edge cases
        return false;
    }

    /**
     * Check if this EntityRef refers to the same entity as another EntityRef.
     * Alias for equals() with more descriptive name.
     *
     * @param EntityRef $ref
     * @return bool
     */
    public function sameAs(EntityRef $ref): bool
    {
        return $this->equals($ref);
    }

    /**
     * Validate the EntityRef against a code format pattern.
     *
     * @param string $codeFormat Regular expression for validating code property
     * @return self
     * @throws \InvalidArgumentException If validation fails
     */
    public function validate(string $codeFormat = ''): self
    {
        $errors = [];

        // Ensure at least one identifier is present
        if (!isset($this->code) && !isset($this->uuid)) {
            $errors[] = 'EntityRef must include at least one of code or uuid';
        }

        // Validate code format if provided
        if (isset($this->code) && $codeFormat !== '') {
            if (!preg_match($codeFormat, $this->code)) {
                $errors[] = "Entity code must match the format $codeFormat";
            }
        }

        if (count($errors) !== 0) {
            throw new \InvalidArgumentException(implode('; ', $errors));
        }

        return $this;
    }

    /**
     * String representation of the entity reference.
     *
     * @return string
     */
    public function __toString(): string
    {
        $parts = [];
        
        if ($this->type !== null) {
            $parts[] = 'type:' . $this->type;
        }
        
        if ($this->code !== null) {
            $parts[] = 'code:' . $this->code;
        }
        
        if ($this->uuid !== null) {
            $parts[] = 'uuid:' . $this->uuid;
        }
        
        return '{' . implode(' / ', $parts) . '}';
    }
}
