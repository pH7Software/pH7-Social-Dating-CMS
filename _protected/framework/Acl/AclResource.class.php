<?php
/**
 * @title            Acl Resource Class
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Acl
 * @version          0.9
 */

namespace PH7\Framework\Acl;

defined('PH7') or exit('Restricted access');

class AclResource
{
    /**
     * @param string $sName
     *
     * @throws Exception
     */
    public function __get($sName)
    {
        switch ($sName) {
            case 'sName':
            case 'aAllowed':
                return $this->$sName;

            default:
                throw new Exception(
                    sprintf('Unable to get "%s"', $sName)
                );
        }
    }

    /**
     * @param string $sName
     * @param string $sValue
     *
     * @throws Exception
     */
    public function __set($sName, $sValue)
    {
        switch ($sName) {
            case 'sName':
            case 'aAllowed':
                $this->$sName = $sValue;
                break;

            default:
                throw new Exception(
                    sprintf('Unable to set "%s"', $sName)
                );
        }
    }

    /**
     * @param string $sName
     *
     * @return bool
     */
    public function __isset($sName)
    {
        return isset($this->$sName);
    }
}
