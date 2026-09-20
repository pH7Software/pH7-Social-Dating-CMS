<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Core\Forms;

use DOMDocument;
use PFBC\Element\Textbox;
use PH7\SearchUserCoreForm;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use ReflectionProperty;

#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
final class SearchUserCoreFormTest extends TestCase
{
    protected function setUp(): void
    {
        // Keep request handling, the form and PFBC real; isolate database/session/GeoIP services.
        class_alias(SearchFormUserModelStub::class, 'PH7\\UserCoreModel');
        class_alias(SearchFormUserStub::class, 'PH7\\UserCore');
        class_alias(SearchFormSessionStub::class, 'PH7\\Framework\\Session\\Session');
        class_alias(SearchFormSettingsStub::class, 'PH7\\Framework\\Mvc\\Model\\DbConfig');
        class_alias(SearchFormGeoStub::class, 'PH7\\Framework\\Geo\\Ip\\Geo');
        require_once PH7_PATH_SYS . 'core/classes/SearchQueryCore.php';
        require_once PH7_PATH_SYS . 'core/classes/GenderTypeUserCore.php';
        require_once PH7_PATH_SYS . 'core/forms/SearchUserCoreForm.php';
        require_once PH7_PATH_FRAMEWORK . 'Layout/Form/Engine/PFBC/Form.class.php';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET = [];
    }

    #[DataProvider('provideCityValues')]
    public function testRequestedCityRemainsPlainText($mCity, string $sExpected): void
    {
        $_GET['city'] = $mCity;
        (new ReflectionMethod(SearchUserCoreForm::class, 'setAttrVals'))->invoke(null);
        $this->assertPlainTextField('aCityOption', $sExpected);
    }

    public static function provideCityValues(): array
    {
        return [
            'apostrophe' => ["O'Fallon", "O'Fallon"],
            'ampersand' => ['A & B', 'A & B'],
            'markup' => ['"><img src=x onerror=alert(1)>', '"><img src=x onerror=alert(1)>'],
            'javascript' => ["';window.ph7Test=1;//", "';window.ph7Test=1;//"],
            'empty filter' => ['', ''],
            'array rejected' => [['unexpected'], '']
        ];
    }

    public function testGeoLocationNamesDoNotBecomeJavascript(): void
    {
        (new ReflectionMethod(SearchUserCoreForm::class, 'setAttrVals'))->invoke(null);
        $this->assertPlainTextField('aCityOption', "O'Fallon");
        $this->assertPlainTextField('aStateOption', "Provence-Alpes-Côte d'Azur");
    }

    private function assertPlainTextField(string $sProperty, string $sExpected): void
    {
        $aOptions = (new ReflectionProperty(SearchUserCoreForm::class, $sProperty))->getValue();
        ob_start();
        (new Textbox('Location', 'location', $aOptions))->render();
        $sHtml = ob_get_clean();
        $oDocument = new DOMDocument;
        $oDocument->loadHTML('<?xml encoding="UTF-8">' . $sHtml);
        $oInput = $oDocument->getElementsByTagName('input')->item(0);

        self::assertSame($sExpected, $oInput->getAttribute('value'));
        self::assertFalse($oInput->hasAttribute('onfocus'));
        self::assertFalse($oInput->hasAttribute('onblur'));
        self::assertSame(0, $oDocument->getElementsByTagName('img')->length);
        self::assertSame(0, $oDocument->getElementsByTagName('script')->length);
    }
}

final class SearchFormUserModelStub
{
}

final class SearchFormSessionStub
{
}

final class SearchFormUserStub
{
    public static function auth(): bool
    {
        return false;
    }
}

final class SearchFormSettingsStub
{
    public static function getSetting(string $sName): int
    {
        return $sName === 'minAgeRegistration' ? 18 : 99;
    }
}

final class SearchFormGeoStub
{
    public static function getCountryCode(): string
    {
        return 'US';
    }

    public static function getCity(): string
    {
        return "O'Fallon";
    }

    public static function getState(): string
    {
        return "Provence-Alpes-Côte d'Azur";
    }
}
