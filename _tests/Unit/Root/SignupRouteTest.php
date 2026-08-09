<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / Test / Unit / Root
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Root;

use DOMDocument;
use PH7\Framework\Mvc\Router\Uri;
use PHPUnit\Framework\TestCase;

final class SignupRouteTest extends TestCase
{
    private const PROJECT_ROOT = __DIR__ . '/../../..';

    private const SIGNUP_ROUTES = [
        'step1' => 'signup',
        'step2' => 'signup/step2',
        'step3' => 'signup/step3',
        'step4' => 'signup/step4',
        'done' => 'signup/done'
    ];

    public function testEveryShippedLanguageDefinesTheCompleteSignupFlow(): void
    {
        $aRouteFiles = glob(self::PROJECT_ROOT . '/_protected/app/configs/routes/*.xml');
        $this->assertIsArray($aRouteFiles);
        $this->assertNotEmpty($aRouteFiles);

        foreach ($aRouteFiles as $sRouteFile) {
            $oRoutes = new DOMDocument;
            $this->assertTrue($oRoutes->load($sRouteFile));
            $aSignupRoutes = [];

            foreach ($oRoutes->getElementsByTagName('route') as $oRoute) {
                if ($oRoute->getAttribute('module') !== 'user' ||
                    $oRoute->getAttribute('controller') !== 'signup'
                ) {
                    continue;
                }

                $aSignupRoutes[$oRoute->getAttribute('action')] = $oRoute->getAttribute('url');
            }

            foreach (self::SIGNUP_ROUTES as $sAction => $sUrl) {
                $this->assertSame(
                    $sUrl,
                    $aSignupRoutes[$sAction] ?? null,
                    sprintf('%s must map signup %s', $sRouteFile, $sAction)
                );
            }
        }
    }

    public function testUriGeneratesPublicUrlsForEverySignupStep(): void
    {
        Uri::clearCache('routefile');

        foreach (self::SIGNUP_ROUTES as $sAction => $sUrl) {
            Uri::clearCache('geturiusersignup' . $sAction);
            $this->assertSame(PH7_URL_ROOT . $sUrl, Uri::get('user', 'signup', $sAction));
        }
    }
}
