<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / Test / Unit / Framework / Layout
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Layout;

use PH7\Framework\Layout\LoadTemplate;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

final class LoadTemplateTest extends TestCase
{
    private array $aRequestBackup;
    private array $aCookieBackup;

    protected function setUp(): void
    {
        $this->aRequestBackup = $_REQUEST;
        $this->aCookieBackup = $_COOKIE;
        $_REQUEST = [];
        $_COOKIE = [];
    }

    public function testAcceptsSafeCustomThemeName(): void
    {
        $_REQUEST['tpl'] = 'date.love_2-custom';

        $this->assertSame('date.love_2-custom', $this->getUserTemplate(new LoadTemplate()));
    }

    public function testRejectsDirectoryTraversalRequest(): void
    {
        foreach (['../../_protected/app/system/global/views/base', '..\\base', '.', '..'] as $sTemplateName) {
            $_REQUEST['tpl'] = $sTemplateName;

            $this->assertNull($this->getUserTemplate(new LoadTemplate()));
        }
    }

    public function testRejectsArrayRequestWithoutTypeError(): void
    {
        $_REQUEST['tpl'] = ['base'];

        $this->assertNull($this->getUserTemplate(new LoadTemplate()));
    }

    public function testRejectsAndRemovesInvalidTemplateCookie(): void
    {
        $_COOKIE['site_tpl'] = '../base';

        $this->assertNull($this->getUserTemplate(new LoadTemplate()));
        $this->assertArrayNotHasKey('site_tpl', $_COOKIE);
    }

    protected function tearDown(): void
    {
        $_REQUEST = $this->aRequestBackup;
        $_COOKIE = $this->aCookieBackup;

        parent::tearDown();
    }

    private function getUserTemplate(LoadTemplate $oLoadTemplate): ?string
    {
        $oReflection = new ReflectionClass($oLoadTemplate);

        return $oReflection->getProperty('sUserTpl')->getValue($oLoadTemplate);
    }
}
