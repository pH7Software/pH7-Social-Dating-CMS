<?php
/**
 *                   This are the some security helpers to displaying.
 *
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2012-2020, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Framework / Layout / Html
 */

namespace PH7\Framework\Layout\Html;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Security\CSRF\Token;

class Security
{
    /**
     * Get the CSRF token.
     *
     * @return void
     */
    public function token()
    {
        echo (new Token)->url();
    }

    /**
     * Field Form CSRF.
     *
     * @param string $sName The name of token.
     *
     * @return void
     */
    public function inputToken($sName)
    {
        echo '<input type="hidden" name="security_token" value="', (new Token)->generate($sName), '" />';
    }
}
