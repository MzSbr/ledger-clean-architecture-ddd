<?php

namespace App\Shared\Domain\ValueObject;

abstract class ValueObject
{
    /**
     * Compare this value object with another value object.
     *
     * @param ValueObject $other
     * @return bool
     */
    abstract public function equals(ValueObject $other): bool;
    
    /**
     * Convert the value object to an array.
     *
     * @return array
     */
    abstract public function toArray(): array;
}
