<?php
/**
 * Copyright (c) Pierre-Henry Soria <hi@ph7.me>
 * MIT License - https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace PH7\Cli\Misc;

class Validation
{
    private const NAME_MIN_LENGTH = 2;
    private const NAME_MAX_LENGTH = 20;

    private ?string $value;

    public function __construct(?string $value)
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
