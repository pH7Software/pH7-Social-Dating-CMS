<?php
/**
 * @title            ACL (Access Control Lists) Main Class
 *
 * @author           Pierre-Henry SORIA <ph7software@gmail.com>
 * @copyright        (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Acl
 * @version          0.9
 */

namespace PH7\Framework\Acl;
defined('PH7') or exit('Restricted access');

class Acl
{

    /**
     * @access protected
     * @var array $aRoles
     */
    protected $aRoles = array();

    /**
     * @access protected
     * @var array $aResources
     */
    protected $aResources = array();

    /**
     * @desc Add a role
     * @param string $sName
     * @return object Instance of Role
     */
    public function addRole($sName)
    {
        $oRole = new Role;
        $role->sName = $sName;
        $this->aRoles[] = $oRole;

        // allow for chaining
        return $oRole;
    }

    /**
     * @desc Add a resource
     * @param string $sName
     * @param array $aAllowed
     * @return object Instance of Resource
     */
    public function addResource($sName, array $aAllowed)
    {
        $oResource = new Resource;
        $oResource->sName = $sName;
        $oResource->aAllowed = $aAllowed;
        $this->aResources[] = $oResource;
        // allow chaining
        return $oResource;
    }

    /**
     * @desc Allowed
     * @param object $oRole
     * @param object $oResource
     * @return boolean
     */
    public function isAllowed($oRole, $oResource)
    {
        return in_array($oRole->sName, $oResource->aAllowed);
    }


    /**
     * @desc Get a resource
     * @param string $sName
     * @return resource
     */
    public function getResource($sName)
    {
        $rResource = null;

        foreach ($this->aResources as $r)
        {
            if ($r->getName() == $sName)
            {
                $rResource = $r;
                break;
            }
        }
        return $rResource;
    }

    /**
     * @desc Get a role
     * @param string $sName
     * @return role
     */
    public function getRole($sName)
    {
        foreach ($this->aRoles as $r)
        {
            if ($r->getName() == $sName)
            {
                $rRole = $r;
                break;
            }
        }
        //var_dump($rRole); exit;
        return $rRole;
    }

    /* Add more methods here */
}
