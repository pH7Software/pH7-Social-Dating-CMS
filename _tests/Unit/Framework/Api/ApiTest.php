<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2017-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / Framework / Api
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Api;

use PH7\Framework\Api\Api;
use PHPUnit\Framework\TestCase;

final class ApiTest extends TestCase
{
    use Api;

    public function testSetWithWrongDataType(): void
    {
        $this->assertFalse($this->set('wrong type'));
    }

    public function testSetWithValidData(): void
    {
        $sJsonData = $this->set(['status' => 1, 'msg' => 'Hello World!']);

        $this->assertSame(
            ['status' => 1, 'msg' => 'Hello World!'],
            json_decode($sJsonData, true, 512, JSON_THROW_ON_ERROR)
        );
    }

    public function testSetOmitsCredentialFieldsRecursively(): void
    {
        $oProfile = (object) [
            'username' => 'alice',
            'password' => 'password-hash',
            'hashValidation' => 'validation-secret',
            'twoFactorAuthSecret' => 'two-factor-secret'
        ];

        $sJsonData = $this->set([
            'profile' => $oProfile,
            'related' => [
                [
                    'username' => 'bob',
                    'password' => 'plaintext-password'
                ]
            ]
        ]);
        $aData = json_decode($sJsonData, true, 512, JSON_THROW_ON_ERROR);

        $this->assertSame('alice', $aData['profile']['username']);
        $this->assertSame('bob', $aData['related'][0]['username']);
        $this->assertArrayNotHasKey('password', $aData['profile']);
        $this->assertArrayNotHasKey('hashValidation', $aData['profile']);
        $this->assertArrayNotHasKey('twoFactorAuthSecret', $aData['profile']);
        $this->assertArrayNotHasKey('password', $aData['related'][0]);
        $this->assertSame('password-hash', $oProfile->password);
    }
}
