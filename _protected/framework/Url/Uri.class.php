<?php
/**
 * @title            Uri Class
 * @desc             URI URL methods.
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Url
 * @version          1.0
 */

namespace PH7\Framework\Url;
defined('PH7') or exit('Restricted access');

use PH7\Framework\Mvc\Request\Http as HttpRequest;

/**
 * @class Singleton Class
 */
class Uri
{

    /**
     * @var array $_aFragments
     */
    private static $_aFragments;

    /**
     * @var string $_sUri
     */
    private $_sUri;

    /**
     * Import the Singleton trait.
     */
    use \PH7\Framework\Pattern\Singleton;

    /**
     * Construct the query string URI.
     *
     * @access private
     */
    private function __construct()
    {
        $this->_sUri = (new HttpRequest)->requestUri();

        // Strip the trailing slash from the URL to avoid taking a wrong URL fragment
        $this->_sUri = rtrim($this->_sUri, PH7_SH);

        /*** Here, we put the string into array ***/
        self::$_aFragments = explode(PH7_SH, $this->_sUri);
    }

    /**
     * Counting all fragments of a URL.
     *
     * @return integer
     */
    public function totalFragment()
    {
        return count(self::$_aFragments);
    }

    /**
     * Gets URI fragment.
     *
     * @param integer $iKey The uri key.
     * @return mixed boolean|string Returns FALSE if key is not found, otherwise STRING of the URI fragment if success.
     */
    public function fragment($iKey)
    {
        if (array_key_exists($iKey, self::$_aFragments))
            return self::$_aFragments[$iKey];

        return false;
    }

    /**
     * Gets URI segments.
     *
     * @param integer $iOffset The sequence will start at that offset in the array.
     * @return array Returns the slice segments URI.
     */
    public function segments($iOffset)
    {
        return array_slice(self::$_aFragments, $iOffset);
    }

}
