<?php

namespace App\Shared\Domain\Traits;

trait HasRules
{
    /**
     * @var array The validation rules
     */
    protected array $rules = [];
    
    /**
     * Get all validation rules.
     *
     * @return array
     */
    public function getRules(): array
    {
        return $this->rules;
    }
    
    /**
     * Set validation rules.
     *
     * @param array $rules
     * @return self
     */
    public function setRules(array $rules): self
    {
        $this->rules = $rules;
        return $this;
    }
    
    /**
     * Add a validation rule.
     *
     * @param string $field
     * @param string|array $rule
     * @return self
     */
    public function addRule(string $field, $rule): self
    {
        $this->rules[$field] = $rule;
        return $this;
    }
    
    /**
     * Remove a validation rule.
     *
     * @param string $field
     * @return self
     */
    public function removeRule(string $field): self
    {
        if (isset($this->rules[$field])) {
            unset($this->rules[$field]);
        }
        return $this;
    }
    
    /**
     * Check if a field has validation rules.
     *
     * @param string $field
     * @return bool
     */
    public function hasRule(string $field): bool
    {
        return isset($this->rules[$field]);
    }
    
    /**
     * Get validation rules for a specific field.
     *
     * @param string $field
     * @return string|array|null
     */
    public function getRule(string $field)
    {
        return $this->rules[$field] ?? null;
    }
}
