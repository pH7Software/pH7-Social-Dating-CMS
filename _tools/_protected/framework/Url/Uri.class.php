<?php
/**
 * @title            Uri Class
 * @desc             URI URL methods.
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Url
 */

namespace PH7\Framework\Url;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Mvc\Request\Http as HttpRequest;
use PH7\Framework\Pattern\Singleton;

/**
 * @class Singleton Class
 */
class Uri
{
    /** @var array $aFragments */
    private static $aFragments;

    /** @var string $sUri */
    private $sUri;

    /** Import the Singleton trait */
    use Singleton;

    /**
     * Construct the query string URI.
     */
    private function __construct()
    {
        $this->sUri = (new HttpRequest)->requestUri();

        // Strip the trailing slash from the URL to avoid taking a wrong URL fragment
        $this->sUri = rtrim($this->sUri, PH7_SH);

        /*** Here, we put the string into array ***/
        self::$aFragments = explode(PH7_SH, $this->sUri);
    }

    /**
     * Counting all fragments of a URL.
     *
     * @return int
     */
    public function totalFragment()
    {
        return count(self::$aFragments);
    }

    /**
     * Gets URI fragment.
     *
     * @param int $iKey The URI key.
     *
     * @return bool|string Returns FALSE if key is not found, otherwise STRING of the URI fragment if success.
     */
    public function fragment($iKey)
    {
        if (array_key_exists($iKey, self::$aFragments)) {
            return self::$aFragments[$iKey];
        }

        return false;
    }

    /**
     * Gets URI segments.
     *
     * @param int $iOffset The sequence will start at that offset in the array.
     *
     * @return array Returns the slice segments URI.
     */
    public function segments($iOffset)
    {
        return array_slice(self::$aFragments, $iOffset);
    }
}
