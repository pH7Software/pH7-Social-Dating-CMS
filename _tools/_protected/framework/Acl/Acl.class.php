<?php
/**
 * @title            ACL (Access Control Lists) Main Class
 *
 * @author           Pierre-Henry SORIA <hello@ph7cms.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Acl
 * @version          0.9
 */

namespace PH7\Framework\Acl;

defined('PH7') or exit('Restricted access');

class Acl
{
    /**
     * @var array $aRoles
     */
    protected $aRoles = [];

    /**
     * @var array $aResources
     */
    protected $aResources = [];

    /**
     * @param string $sName
     *
     * @return Role Instance of Role
     */
    public function addRole($sName)
    {
        $oRole = new Role;
        $oRole->sName = $sName;
        $this->aRoles[] = $oRole;

        // allow for chaining
        return $oRole;
    }

    /**
     * @param string $sName
     * @param array $aAllowed
     *
     * @return AclResource
     */
    public function addResource($sName, array $aAllowed)
    {
        $oResource = new AclResource;
        $oResource->sName = $sName;
        $oResource->aAllowed = $aAllowed;
        $this->aResources[] = $oResource;

        // allow chaining
        return $oResource;
    }

    /**
     * @param Role $oRole
     * @param AclResource $oResource
     *
     * @return boolean
     */
    public function isAllowed(Role $oRole, AclResource $oResource)
    {
        return in_array($oRole->sName, $oResource->aAllowed, true);
    }


    /**
     * @param string $sName
     *
     * @return resource
     */
    public function getResource($sName)
    {
        foreach ($this->aResources as $oResource) {
            if ($oResource->getName() == $sName) {
                return $oResource;
            }
        }
    }

    /**
     * @param string $sName
     *
     * @return Role
     */
    public function getRole($sName)
    {
        foreach ($this->aRoles as $oRole) {
            if ($oRole->getName() == $sName) {
                return $oRole;
            }
        }
    }

    /* Add more methods here */
}
