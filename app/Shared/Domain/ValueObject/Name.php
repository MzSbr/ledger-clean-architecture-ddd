<?php

namespace App\Shared\Domain\ValueObject;

use App\Shared\Domain\ValueObject\ValueObject;

class Name extends ValueObject
{
    /**
     * @var string The name value
     */
    private string $value;
    
    /**
     * @var string|null The language code for this name
     */
    private ?string $language;
    
    /**
     * @var bool Whether this is the default name
     */
    private bool $isDefault;
    
    /**
     * Create a new Name instance.
     *
     * @param string $value
     * @param string|null $language
     * @param bool $isDefault
     */
    public function __construct(string $value, ?string $language = null, bool $isDefault = false)
    {
        $this->value = $value;
        $this->language = $language;
        $this->isDefault = $isDefault;
    }
    
    /**
     * Get the name value.
     *
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }
    
    /**
     * Get the language code.
     *
     * @return string|null
     */
    public function getLanguage(): ?string
    {
        return $this->language;
    }
    
    /**
     * Check if this is the default name.
     *
     * @return bool
     */
    public function isDefault(): bool
    {
        return $this->isDefault;
    }
    
    /**
     * Create a Name from an array.
     *
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['name'] ?? $data['value'] ?? '',
            $data['language'] ?? null,
            $data['isDefault'] ?? $data['default'] ?? false
        );
    }
    
    /**
     * Convert the Name to an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'name' => $this->value,
            'language' => $this->language,
            'isDefault' => $this->isDefault,
        ];
    }
    
    /**
     * Compare with another Name object.
     *
     * @param Name $other
     * @return bool
     */
    public function equals(Name $other): bool
    {
        return $this->value === $other->value
            && $this->language === $other->language
            && $this->isDefault === $other->isDefault;
    }
}
