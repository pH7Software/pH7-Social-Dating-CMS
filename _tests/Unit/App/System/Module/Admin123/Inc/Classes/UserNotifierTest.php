<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2018, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Test / Unit / App / System / Module / Admin123 / Inc / Classes
 */

namespace PH7\Test\Unit\App\System\Module\Admin123\Inc\Classes;

require_once PH7_PATH_SYS_MOD . 'admin123/inc/class/UserNotifier.php';
require_once PH7_PATH_SYS_MOD . 'admin123/inc/class/UserNotifierString.php';

use PH7\Framework\Error\CException\PH7RuntimeException;
use PH7\Framework\Layout\Tpl\Engine\PH7Tpl\PH7Tpl;
use PH7\Framework\Mail\InvalidEmailException;
use PH7\Framework\Mail\Mailable;
use PH7\UserNotifier;
use Phake;
use PHPUnit_Framework_TestCase;

class UserNotifierTest extends PHPUnit_Framework_TestCase
{
    const VALID_EMAIL = 'hi@ph7.me';

    /** @var UserNotifier */
    private $oUserNotifier;

    /** @var Mailable|Phake_IMock */
    private $oMailMock;

    /** @var PH7Tpl|Phake_IMock */
    private $oViewMock;

    protected function setUp()
    {
        $this->oMailMock = Phake::mock(Mailable::class);
        $this->oViewMock = Phake::mock(PH7Tpl::class);
        $this->oUserNotifier = new UserNotifier($this->oMailMock, $this->oViewMock);
    }

    public function testSendApprovedContent()
    {
        $this->oUserNotifier
            ->setUserEmail(self::VALID_EMAIL)
            ->approvedContent()
            ->send();

        Phake::verify($this->oViewMock)->parseMail();
        Phake::verify($this->oMail)->send();
    }

    public function testSendDisapprovedContent()
    {
        $this->oUserNotifier
            ->setUserEmail(self::VALID_EMAIL)
            ->disapprovedContent()
            ->send();

        Phake::verify($this->oViewMock)->parseMail();
        Phake::verify($this->oMail)->send();
    }

    public function testThrowsExceptionWhenContentStatusIsMissing()
    {
        $this->expectException(PH7RuntimeException::class);

        $this->oUserNotifier
            ->setUserEmail(self::VALID_EMAIL)
            ->send();
    }

    public function testThrowsExceptionWhenEmailIsNull()
    {
        $this->expectException(InvalidEmailException::class);

        $this->oUserNotifier
            ->setUserEmail(null)
            ->approvedContent()
            ->send();
    }

    public function testThrowsExceptionWhenEmailIsInvalid()
    {
        $this->expectException(InvalidEmailException::class);

        $this->oUserNotifier
            ->setUserEmail('pierrehenry.be')
            ->approvedContent()
            ->send();
    }
}
