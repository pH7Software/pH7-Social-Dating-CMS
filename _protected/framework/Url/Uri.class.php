<?php
/**
 * @title            Uri Class
 * @desc             URI URL methods.
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2014, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Url
 * @version          1.0
 */

namespace PH7\Framework\Url;
defined('PH7') or exit('Restricted access');

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
        $this->_sUri = $_SERVER['QUERY_STRING'];

        /*** We remove the last slash to avoid taking a new fragment url empty ***/
        if (substr($this->_sUri, -1) === PH7_SH)
            $this->_sUri = substr($this->_sUri, 0, -1);

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
     * @return mixed boolean|string Returns false if key is not found otherwise string of the fragment URI if success.
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
