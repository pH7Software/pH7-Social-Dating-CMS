<?php
/**
 * @title            Hash/List Interface
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Registry
 * @version          1.0
 */

namespace PH7\Framework\Registry;

interface IHashList
{

    /**
     * Get data in the list.
     *
     * @param string $sName
     * @return mixed (boolean | integer | float | string | array | object) Returns the converted value if successful otherwise returns false.
     */
    public function get($sName);

}
