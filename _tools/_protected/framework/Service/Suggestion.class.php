<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
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

    const DIR = 'suggestions/';
    const EXT = '.txt';
    const EMAIL_FILE = 'email.txt';

    /** @var string */
    private static $sFile;

    /**
     * @return string Suggestion email address.
     */
    public static function email()
    {
        self::$sFile = static::EMAIL_FILE;

        return self::get();
    }

    /**
     * Generic method to to pick and translate words.
     *
     * @return string The transform words.
     */
    private static function get()
    {
        $aSuggestions = file(PH7_PATH_APP_CONFIG . static::DIR . self::$sFile);

        // It removes all spaces, line breaks, ...
        $aSuggestions = array_map('trim', $aSuggestions);

        return implode('\',\'', $aSuggestions);
    }
}
