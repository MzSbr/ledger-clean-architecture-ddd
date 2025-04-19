<?php

namespace App\Shared\Domain\Traits;

trait HasCodes
{
    /**
     * @var string A unique identifier.
     */
    protected string $code;

    /**
     * @var string|null A new code to be assigned in an update operation.
     */
    protected ?string $toCode = null;

    /**
     * Get the code value.
     *
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * Set the code value.
     *
     * @param string $code
     * @return self
     */
    public function setCode(string $code): self
    {
        $this->code = $this->formatCode($code);
        return $this;
    }

    /**
     * Get the toCode value.
     *
     * @return string|null
     */
    public function getToCode(): ?string
    {
        return $this->toCode;
    }

    /**
     * Set the toCode value for update operations.
     *
     * @param string $toCode
     * @return self
     */
    public function setToCode(string $toCode): self
    {
        $this->toCode = $this->formatCode($toCode);
        return $this;
    }

    /**
     * Format the code according to business rules.
     *
     * @param string $code
     * @param bool $uppercase Whether to transform code to uppercase (default true)
     * @return string
     */
    protected function formatCode(string $code, bool $uppercase = true): string
    {
        return $uppercase ? strtoupper($code) : $code;
    }

    /**
     * Validate the code according to business rules.
     *
     * @param string $code
     * @param string $regEx Regular expression for validation
     * @return bool
     */
    protected function validateCode(string $code, string $regEx = '/^[^ \t\r\n*]*$/'): bool
    {
        return (bool) preg_match($regEx, $code);
    }
}
