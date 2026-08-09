<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / Test / Unit / Framework / Acl
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Acl;

use PH7\Framework\Acl\Acl;
use PHPUnit\Framework\TestCase;

final class AclTest extends TestCase
{
    public function testResourceAcceptsTheAllowedRoleList(): void
    {
        $oAcl = new Acl;
        $oMember = $oAcl->addRole('member');
        $oGuest = $oAcl->addRole('guest');
        $oResource = $oAcl->addResource('admin', ['member']);

        $this->assertTrue($oAcl->isAllowed($oMember, $oResource));
        $this->assertFalse($oAcl->isAllowed($oGuest, $oResource));
    }
}
