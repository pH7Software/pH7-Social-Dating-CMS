<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2026, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / App / System / Module / Newsletter / Inc / Classes
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Module\Newsletter\Inc\Classes;

require_once PH7_PATH_SYS_MOD . 'newsletter/models/SubscriberModel.php';
require_once PH7_PATH_SYS_MOD . 'newsletter/inc/class/Newsletter.php';

use PH7\Newsletter;
use PH7\SubscriberModel;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use stdClass;

final class NewsletterTest extends TestCase
{
    public function testConfirmedStandaloneSubscriberIsEligible(): void
    {
        $oReflection = new ReflectionClass(Newsletter::class);
        $oNewsletter = $oReflection->newInstanceWithoutConstructor();
        $oReflection->getProperty('sSubscribersMethod')->setValue($oNewsletter, 'getSubscribers');

        $this->assertTrue(
            $oReflection->getMethod('isOptedInSubscriber')->invoke($oNewsletter, new stdClass)
        );
    }

    public function testMemberNewsletterPreferenceIsRespected(): void
    {
        $oReflection = new ReflectionClass(Newsletter::class);
        $oSubscriber = (object)['profileId' => 42];

        foreach ([true, false] as $bEnabled) {
            $oNewsletter = $oReflection->newInstanceWithoutConstructor();
            $oReflection->getProperty('sSubscribersMethod')->setValue($oNewsletter, 'getProfiles');
            $oReflection->getProperty('oSubscriberModel')->setValue(
                $oNewsletter,
                new class ($bEnabled) extends SubscriberModel {
                    public function __construct(private readonly bool $bEnabled)
                    {
                    }

                    public function isNotification($iProfileId, $sNotifName): bool
                    {
                        return $this->bEnabled;
                    }
                }
            );

            $this->assertSame(
                $bEnabled,
                $oReflection->getMethod('isOptedInSubscriber')->invoke($oNewsletter, $oSubscriber)
            );
        }
    }
}
