<?php
/**
 * We made this code.
 * By pH7 (Pierre-Henry SORIA).
 */
namespace PFBC\Validation;

class AlphaNumeric extends RegExp
{

    public function __construct($sMessage = '')
    {
        $this->message = t('Error: %element% must be alphanumeric (contain only numbers, letters, underscores, and/or hyphens).');
        parent::__construct('/^[a-zA-Z0-9_-]+$/', $sMessage);
    }

}
