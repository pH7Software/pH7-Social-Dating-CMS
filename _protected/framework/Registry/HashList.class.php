<?php
/**
 * @title            Hash List Class
 * @desc             Hash List with serialization.
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Registry
 * @version          1.1
 */

namespace PH7\Framework\Registry;
defined('PH7') or exit('Restricted access');

final class HashList extends File implements IHashList, IHash
{

    /**
     * @staticvar array $_aData
     */
    private static $_aData = array();

    /**
     * Get a data in the list.
     *
     * @param string $sName
     * @return mixed (boolean | integer | float | string | array | object) Returns the converted value if successful otherwise returns false.
     */
    public function get($sName)
    {
        if(isset(self::$_aData[$sName]))
            return $this->unserialize($this->read())[$sName];

        return null;
    }

    /**
     * Push data in the list.
     *
     * @param string $sName
     * @param string $sValue
     * @return void
     */
    public function push($sName, $sValue)
    {
        self::$_aData[$sName] = $sValue;
        $this->write($this->serialize(self::$_aData));
    }

}
