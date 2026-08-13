<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / Test / Unit / Root
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Root;

use PHPUnit\Framework\TestCase;

final class SignupExperienceTest extends TestCase
{
    private const PROJECT_ROOT = __DIR__ . '/../../..';

    public function testRequiredSignupStepsReportAccurateProgress(): void
    {
        $sController = $this->readProjectFile(
            '_protected/app/system/modules/user/controllers/SignupController.php'
        );
        $sProgressTemplate = $this->readProjectFile(
            '_protected/app/system/modules/user/views/base/tpl/progressbar.inc.tpl'
        );

        $this->assertStringContainsString('setupProgressbar(1, 33)', $sController);
        $this->assertStringContainsString('setupProgressbar(2, 66)', $sController);
        $this->assertStringContainsString('setupProgressbar(3, 100)', $sController);
        $this->assertStringNotContainsString('setupProgressbar(3, 99)', $sController);
        $this->assertStringContainsString("'general.css'", $sController);
        $this->assertStringContainsString('aria-valuetext=', $sProgressTemplate);
        $this->assertStringContainsString('Step %0% of %1%', $sProgressTemplate);
        $this->assertStringContainsString('signup_progress_label', $sProgressTemplate);
    }

    public function testSignupCopyIsWelcomingAndSpecific(): void
    {
        $sController = $this->readProjectFile(
            '_protected/app/system/modules/user/controllers/SignupController.php'
        );
        $sFirstStep = $this->readProjectFile(
            '_protected/app/system/modules/user/views/base/tpl/signup/step1.tpl'
        );
        $sGuestHomepage = $this->readProjectFile(
            '_protected/app/system/modules/user/views/base/tpl/main/index.guest.inc.tpl'
        );

        $this->assertStringContainsString('Create your profile on %site_name%', $sController);
        $this->assertStringNotContainsString('sex friends', $sController);
        $this->assertStringContainsString('Registration takes three short steps.', $sFirstStep);
        $this->assertStringContainsString('privacy, and notification settings', $sFirstStep);
        $this->assertStringContainsString('Community members', $sGuestHomepage);
        $this->assertStringNotContainsString('People love us!', $sGuestHomepage);
    }

    public function testSignupSupportRemainsUsableOnSmallScreens(): void
    {
        $sStyles = $this->readProjectFile(
            'templates/system/modules/user/themes/base/css/general.css'
        );

        $this->assertStringContainsString('.signup_support', $sStyles);
        $this->assertStringContainsString('@media (max-width: 767px)', $sStyles);
    }

    public function testSignupDoesNotGuessIdentityOrLocation(): void
    {
        $sForm = $this->readProjectFile(
            '_protected/app/system/modules/user/forms/JoinForm.php'
        );

        $this->assertStringNotContainsString('predictGenderFromFirstName', $sForm);
        $this->assertStringNotContainsString('getOppositeGenderPreferences', $sForm);
        $this->assertStringContainsString("['' => t('')] + Form::getCountryValues()", $sForm);
        $this->assertStringContainsString('Country::fixCode(Geo::getCountryCode())', $sForm);
    }

    private function readProjectFile(string $sRelativePath): string
    {
        $sContents = file_get_contents(self::PROJECT_ROOT . '/' . $sRelativePath);
        $this->assertIsString($sContents);

        return $sContents;
    }
}
