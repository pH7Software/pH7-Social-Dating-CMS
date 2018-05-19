<?php
/**
 * We made this code.
 * By pH7 (Pierre-Henry SORIA).
 */

namespace PFBC\Validation;

class AlphaNumeric extends RegExp
{
    /**
     * @param string $sMessage
     */
    public function __construct($sMessage = '')
    {
        $this->message = t('Error: %element% must be alphanumeric (letters A-Z, numbers 0-9, underscores and dashes).');

        parent::__construct('/^[a-zA-Z0-9_-]+$/', $sMessage);
    }
}
