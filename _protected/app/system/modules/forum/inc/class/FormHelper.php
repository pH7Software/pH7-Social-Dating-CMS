<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

namespace PH7;

use PFBC\Element\Textarea;

class FormHelper
{
    /**
     * @return string
     */
    public static function getEditorPfbcClassName()
    {
        return Textarea::class;
    }
}
