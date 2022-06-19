<?php
/**
 * @title            ACL (Access Control Lists) Main Class
 *
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2012-2022, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Framework / Acl
 * @version          0.9
 */

declare(strict_types=1);

namespace PH7\Framework\Acl;

defined('PH7') or exit('Restricted access');

class Acl
{
    protected array $aRoles = [];

    protected array $aResources = [];

    public function addRole(string $sName): Role
    {
        $oRole = new Role;
        $oRole->sName = $sName;
        $this->aRoles[] = $oRole;

        // allow for chaining
        return $oRole;
    }

    public function addResource(string $sName, array $aAllowed): AclResource
    {
        $oResource = new AclResource;
        $oResource->sName = $sName;
        $oResource->aAllowed = $aAllowed;
        $this->aResources[] = $oResource;

        // allow chaining
        return $oResource;
    }

    public function isAllowed(Role $oRole, AclResource $oResource): bool
    {
        return in_array($oRole->sName, $oResource->aAllowed, true);
    }

    public function getResource(string $sName): ?AclResource
    {
        foreach ($this->aResources as $oResource) {
            if ($oResource->getName() === $sName) {
                return $oResource;
            }
        }

        return null;
    }

    public function getRole(string $sName): ?Role
    {
        foreach ($this->aRoles as $oRole) {
            if ($oRole->getName() === $sName) {
                return $oRole;
            }
        }

        return null;
    }

    /* Add more methods here */
}
