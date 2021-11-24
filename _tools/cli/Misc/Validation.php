<?php

namespace PH7\Cli\Misc;

class Validation
{
    private const NAME_MIN_LENGTH = 2;
    private const NAME_MAX_LENGTH = 20;

    private $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function isValidEmail(): bool
    {
        return filter_var($this->value, FILTER_VALIDATE_EMAIL) !== false;
    }

    public function isValidName(): bool
    {
        return (is_string($this->value) &&
            mb_strlen($this->value) >= self::NAME_MIN_LENGTH &&
            mb_strlen($this->value) <= self::NAME_MAX_LENGTH);
    }
}
