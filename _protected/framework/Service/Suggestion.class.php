<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Service
 */

namespace PH7\Framework\Service;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Pattern\Statik;

class Suggestion
{
    /**
     * The trait sets private constructor & cloning since it's a static class
     */
    use Statik;

    const
    DIR = 'suggestions/',
    EMAIL_FILE = 'email.txt';

    private static $_sFile;

    /**
     * @static
     * @return string Suggestion email address.
     */
    public static function email()
    {
        self::$_sFile = static::EMAIL_FILE;
        return self::_get();
    }

    /**
     * Generic method to to pick and translate words.
     *
     * @access private
     * @static
     * @return string The transform words.
     */
    private static function _get()
    {
        $aSuggestions = file(PH7_PATH_APP_CONFIG . static::DIR . self::$_sFile);

        // It removes all spaces, line breaks, ...
        $aSuggestions = array_map('trim', $aSuggestions);

        return implode('\',\'', $aSuggestions);
    }
}
