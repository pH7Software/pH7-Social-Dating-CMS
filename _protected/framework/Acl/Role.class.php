<?php
/**
 * @title            Acl Role Class
 *
 * @author           Pierre-Henry SORIA <ph7software@gmail.com>
 * @copyright        (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Acl
 * @version          0.9
 */

namespace PH7\Framework\Acl;
defined('PH7') or exit('Restricted access');

class Role
{

    /**
     * @desc Settor
     * @param string $sName
     * @param string $sValue
     */
    public function __set($sName, $sValue)
    {
        switch ($sName)
        {
            case 'sName':
            case 'sPermissions':
                $this->$sName = $sValue;
            break;

            default:
                throw new Exception("Unable to set \"$sName\".");
        }
    }

    /**
     * @desc Gettor
     * @param string $sName
     * @return string
     */
    public function __get($sName)
    {
        switch ($sName)
        {
            case 'sName':
            case 'sPermissions':
                return $this->sName;
            break;

            default:
                throw new Exception("Unable to get \"$sName\".");
        }
    }

}
