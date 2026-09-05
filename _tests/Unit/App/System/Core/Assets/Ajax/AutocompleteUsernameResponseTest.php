<?php

/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2026, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / App / System / Core / Assets / Ajax
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Core\Assets\Ajax;

use PHPUnit\Framework\TestCase;

final class AutocompleteUsernameResponseTest extends TestCase
{
    public function testSkippingCurrentMemberPreservesLaterSuggestions(): void
    {
        class_alias(AutocompleteMemberStub::class, 'PH7\UserCore');
        class_alias(AutocompleteModelStub::class, 'PH7\UserCoreModel');
        class_alias(AutocompleteSessionStub::class, 'PH7\Framework\Session\Session');
        class_alias(AutocompleteRequestStub::class, 'PH7\Framework\Mvc\Request\Http');
        class_alias(AutocompleteAvatarStub::class, 'PH7\Framework\Layout\Html\Design');

        ob_start();
        require PH7_PATH_SYS . 'core/assets/ajax/autocompleteUsernameCoreAjax.php';
        $sResponse = ob_get_clean();

        $oXml = simplexml_load_string($sResponse);
        $this->assertNotFalse($oXml);
        $this->assertSame(2, $oXml->ul->li->count());
        $this->assertSame('alex', (string)$oXml->ul->li[0]->username);
        $this->assertSame('sam', (string)$oXml->ul->li[1]->username);
        $this->assertSame('https://example.com/sam.svg', (string)$oXml->ul->li[1]->avatar);
    }
}

final class AutocompleteMemberStub
{
    public static function auth(): bool
    {
        return true;
    }
}

final class AutocompleteModelStub
{
    public function getUsernameList(string $sQuery): array
    {
        return [
            (object)['profileId' => 1, 'username' => 'alex', 'sex' => 'male'],
            (object)['profileId' => '2', 'username' => 'current', 'sex' => 'male'],
            (object)['profileId' => 3, 'username' => 'sam', 'sex' => 'female']
        ];
    }
}

final class AutocompleteSessionStub
{
    public function get(string $sKey): int
    {
        return 2;
    }
}

final class AutocompleteRequestStub
{
    public function postExists(string $sKey): bool
    {
        return true;
    }

    public function post(string $sKey): string
    {
        return 'a';
    }
}

final class AutocompleteAvatarStub
{
    public function getUserAvatar(string $sUsername, string $sSex, int $iSize): void
    {
        echo 'https://example.com/' . $sUsername . '.svg';
    }
}
