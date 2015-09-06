<?php
/**
 * @title            Api Trait
 *
 * @author           Pierre-Henry SORIA <ph7software@gmail.com>
 * @copyright        (c) 2015, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Api
 */

namespace PH7\Framework\Api;
defined('PH7') or exit('Restricted access');

trait Api
{

    public function __construct()
    {
    }

    /**
     * Set the Json Output data.
     *
     * @return string
     */
    public function set($mData)
    {
        if (is_array($mData))
            return json_encode($mData);
    }

}