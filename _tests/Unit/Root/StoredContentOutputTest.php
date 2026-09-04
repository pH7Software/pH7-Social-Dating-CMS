<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Root;

use PH7\Framework\Config\Config;
use PH7\Framework\Layout\Tpl\Engine\PH7Tpl\Syntax\Curly;
use PH7\Framework\Mvc\Model\Engine\Db;
use PH7\Framework\Str\Str;
use PH7\ProfileBaseController;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;

#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
final class StoredContentOutputTest extends TestCase
{
    protected function setUp(): void
    {
        define('PH7_DOMAIN_COOKIE', 'localhost');
        define('PH7_PATH_STATIC', dirname(PH7_PATH_PROTECTED) . '/static/');
        define('PH7_PATH_TPL', dirname(PH7_PATH_PROTECTED) . '/templates/themes/');
        define('PH7_ADMIN_USERNAME', 'admin');

        Config::getInstance()->values['cache']['enable.general.cache'] = false;
        $oPdo = new \PDO('sqlite::memory:');
        $oPdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $oPdo->exec('CREATE TABLE ph7_settings (settingName TEXT, settingValue TEXT)');
        $oPdo->exec("INSERT INTO ph7_settings VALUES ('banWordReplace', '***')");
        (new \ReflectionProperty(Db::class, 'oInstance'))->setValue(null, (new \ReflectionClass(Db::class))->newInstanceWithoutConstructor());
        (new \ReflectionProperty(Db::class, 'oDb'))->setValue(null, $oPdo);
        (new \ReflectionProperty(Db::class, 'sPrefix'))->setValue(null, 'ph7_');
    }

    #[DataProvider('profileProvider')]
    public function testStoredProfileDescriptionIsSanitizedAtReadTime(string $sModule, string $sClass, string $sContent): void
    {
        require_once PH7_PATH_SYS_MOD . $sModule . '/controllers/' . $sClass . '.php';

        $oController = (new \ReflectionClass('PH7\\' . $sClass))->newInstanceWithoutConstructor();
        (new \ReflectionProperty($oController, 'str'))->setValue($oController, new Str());
        $aData = (new \ReflectionMethod(ProfileBaseController::class, 'getFilteredData'))->invoke(
            $oController,
            (object)['birthDate' => '1990-01-01'],
            (object)['description' => $sContent]
        );

        $oDocument = $this->parseHtml($aData['description']);
        $this->assertSame(0, (new \DOMXPath($oDocument))->query('//script|//svg|//iframe|//@onerror|//@onload|//@onclick|//@style')->length);
        $this->assertStringNotContainsString('javascript:', strtolower($aData['description']));
        $this->assertStringContainsString('Hello', $oDocument->textContent);
        $this->assertStringContainsString('<strong>Hello</strong>', $aData['description']);

        // Both controllers must pass the filtered value to their base/premium views.
        $sController = file_get_contents(PH7_PATH_SYS_MOD . $sModule . '/controllers/' . $sClass . '.php');
        $this->assertStringContainsString('$aData = $this->getFilteredData($oUser, $oFields);', $sController);
        $this->assertStringContainsString('$this->view->description = nl2br($aData[\'description\']);', $sController);
    }

    public static function profileProvider(): array
    {
        $aCases = [];
        foreach (['user' => 'ProfileController', 'cool-profile-page' => 'MainController'] as $sModule => $sClass) {
            foreach (self::payloads() as $sName => $sPayload) {
                $aCases[$sModule . ': ' . $sName] = [$sModule, $sClass, '<strong>Hello</strong>' . $sPayload];
            }
        }

        return $aCases;
    }

    #[DataProvider('messageProvider')]
    public function testStoredMessageIsEscapedInMemberAndAdminLists(bool $bAdmin, string $sContent, string $sExpectedText): void
    {
        require_once PH7_PATH_SYS_MOD . 'mail/models/MailModel.php';

        $aVariables = [
            'error' => '',
            'is_admin_auth' => $bAdmin,
            'is_user_auth' => !$bAdmin,
            'member_id' => 2,
            'csrf_token' => 'test-token',
            'msgs' => [(object)[
                'messageId' => 1,
                'username' => 'sender',
                'firstName' => 'Sender',
                'title' => 'Hello',
                'message' => $sContent,
                'sender' => 1,
                'recipient' => 2,
                'trash' => '',
                'status' => 0,
                'sendDate' => '2026-01-01 12:00:00'
            ]],
            'design' => new class {
                public function url(...$aParts): void
                {
                    echo '/test-message';
                }
            },
            'avatarDesign' => new class {
                public function get(...$aParts): void
                {
                    echo 'Sender';
                }
            },
            'designSecurity' => new class {
                public function inputToken(string $sName): void
                {
                }
            }
        ];
        $sOutput = $this->renderTemplate('list.inc.tpl', $aVariables);
        $oXPath = new \DOMXPath($this->parseHtml($sOutput));

        $this->assertSame(0, $oXPath->query('//script|//svg|//iframe|//img|//@onerror|//@onload')->length);
        $this->assertSame(0, $oXPath->query('//div[@class="message"]/*|//div[@id="divShow_1"]/*')->length);
        $this->assertSame(1, $oXPath->query('//div[@class="message"]')->length);
        $this->assertSame($sExpectedText, $oXPath->query('//div[@class="message"]')->item(0)->textContent);
        if ($bAdmin) {
            $oMessage = $oXPath->query('//div[@id="divShow_1"]')->item(0);
            $this->assertNotNull($oMessage);
            $this->assertSame($sExpectedText, $oMessage->textContent);
        } else {
            require_once PH7_PATH_FRAMEWORK . 'Layout/Form/Engine/PFBC/Form.class.php';
            $aVariables['msg'] = $aVariables['msgs'][0];
            $oDetail = new \DOMXPath($this->parseHtml($this->renderTemplate('main/msg.inc.tpl', $aVariables)));
            $this->assertSame(0, $oDetail->query('//dd[3]/*')->length);
            $this->assertSame($sContent, $oDetail->query('//dd')->item(2)->textContent);
        }
    }

    public static function messageProvider(): array
    {
        $aCases = [];
        $aExpected = [
            'svg_event' => '',
            'image_event' => '',
            'script' => 'alert(1)',
            'encoded_scheme' => 'link',
            'encoded_markup' => '<img src=x onerror=alert(1)>'
        ];
        foreach ([false, true] as $bAdmin) {
            foreach (self::payloads() as $sName => $sPayload) {
                $aCases[($bAdmin ? 'admin: ' : 'member: ') . $sName] = [$bAdmin, 'Hello' . $sPayload, 'Hello' . $aExpected[$sName]];
            }
        }

        return $aCases;
    }

    public function display(string $sTemplate, string $sDirectory): void
    {
        // Pagination does not consume message content; leave only that include out.
        $this->assertSame('page_nav.inc.tpl', $sTemplate);
    }

    private static function payloads(): array
    {
        return [
            'svg_event' => '<svg onload="alert(1)"></svg>',
            'image_event' => '<img src="x" onerror="alert(1)">',
            'script' => '<script>alert(1)</script>',
            'encoded_scheme' => '<a href="jav&#x61;script:alert(1)">link</a>',
            'encoded_markup' => '&lt;img src=x onerror=alert(1)&gt;'
        ];
    }

    private function parseHtml(string $sHtml): \DOMDocument
    {
        $oDocument = new \DOMDocument();
        $oDocument->loadHTML('<!doctype html><html><body>' . $sHtml . '</body></html>', LIBXML_NOERROR | LIBXML_NOWARNING);

        return $oDocument;
    }

    private function renderTemplate(string $sTemplate, array $aVariables): string
    {
        $oSyntax = new Curly();
        $oSyntax->setCode(file_get_contents(PH7_PATH_SYS_MOD . 'mail/views/base/tpl/' . $sTemplate));
        $oSyntax->parse();
        extract($aVariables, EXTR_SKIP);
        ob_start();
        try {
            // Only trusted, repository-owned template code is evaluated, never a payload.
            eval('namespace PH7; ?>' . $oSyntax->getParsedCode());

            return (string)ob_get_contents();
        } finally {
            ob_end_clean();
        }
    }
}
