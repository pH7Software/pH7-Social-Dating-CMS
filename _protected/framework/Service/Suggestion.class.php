<?php
/**
 * @title            Suggestion Class
 * @desc             Suggestion Service.
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2015, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Service
 * @version          1.0
 */

namespace PH7\Framework\Service;
defined('PH7') or exit('Restricted access');

class Suggestion
{

    const
    DIR = 'suggestions/',
    EMAIL_FILE = 'email.txt';

    private static $_sFile;

    /**
     * Private constructor to prevent instantiation of class since it is a private class.
     *
     * @access private
     */
    private function __construct() {}

    /**
     * @static
     * @return string Suggestion email address.
     */
    public static function email()
    {
        static::$_sFile = static::EMAIL_FILE;
        return static::_gets();
    }

   /**
    * Generic method to to pick and translate words.
    *
    * @access private
    * @static
    * @return string The transform words.
    */
   private static function _gets()
   {
       $aSuggestions = file(PH7_PATH_APP_CONFIG . static::DIR . static::$_sFile);

       // It removes all spaces, line breaks, ...
       $aSuggestions = array_map('trim', $aSuggestions);

       return implode('\',\'', $aSuggestions);
   }

    /**
     * Block cloning.
     *
     * @access private
     */
    private function __clone() {}

}
