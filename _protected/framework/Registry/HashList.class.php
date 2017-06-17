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

final class HashList extends File implements HashListable, Hashable
{
    /**
     * @staticvar array $_aData
     */
    private static $_aData = array();

    /**
     * {@inheritDoc}
     */
    public function get($sName)
    {
        if (isset(self::$_aData[$sName])) {
            return $this->unserialize($this->read())[$sName];
        }

        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function push($sName, $sValue)
    {
        self::$_aData[$sName] = $sValue;
        $this->write($this->serialize(self::$_aData));
    }
}
