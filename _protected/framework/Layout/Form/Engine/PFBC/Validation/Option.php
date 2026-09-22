<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

namespace PFBC\Validation;

use PFBC\Validation;

/**
 * Accepts only the values an option element offered. Anything else was crafted, and form
 * processors pass these values straight to the database.
 */
class Option extends Validation
{
    /** @var array<string, true> The offered values, as keys for lookups. */
    private array $aOptionValues;

    /**
     * @param string[] $aOptionValues The values the element rendered, in their submitted form.
     */
    public function __construct(array $aOptionValues)
    {
        $this->aOptionValues = array_fill_keys($aOptionValues, true);
        $this->message = t('%element% is invalid.');
    }

    /**
     * @param array|string|null $mValue One value, or several for multi-value elements.
     */
    public function isValid($mValue): bool
    {
        // Empty submissions are for Required to judge; an element without options has nothing to check.
        if ($mValue === null || $mValue === '' || empty($this->aOptionValues)) {
            return true;
        }

        foreach ((array)$mValue as $sValue) {
            if (!is_string($sValue) || !isset($this->aOptionValues[$sValue])) {
                return false;
            }
        }

        return true;
    }
}
