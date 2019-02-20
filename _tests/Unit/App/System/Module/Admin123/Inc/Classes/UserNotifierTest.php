<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Test / Unit / App / System / Module / Admin123 / Inc / Classes
 */

namespace PH7\Test\Unit\App\System\Module\Admin123\Inc\Classes;

require_once PH7_PATH_SYS_MOD . 'admin123/inc/class/UserNotifier.php';
require_once PH7_PATH_SYS_MOD . 'admin123/inc/class/UserNotifierString.php';

use PH7\Framework\Error\CException\PH7RuntimeException;
use PH7\Framework\Layout\Tpl\Engine\Templatable;
use PH7\Framework\Mail\InvalidEmailException;
use PH7\Framework\Mail\Mailable;
use PH7\UserNotifier;
use Phake;
use Phake_IMock;
use PHPUnit_Framework_TestCase;

class UserNotifierTest extends PHPUnit_Framework_TestCase
{
    const VALID_EMAIL = 'hi@ph7.me';

    /** @var UserNotifier */
    private $oUserNotifier;

    /** @var Mailable|Phake_IMock */
    private $oMailMock;

    /** @var Templatable|Phake_IMock */
    private $oViewMock;

    protected function setUp()
    {
        $this->oMailMock = Phake::mock(Mailable::class);
        $this->oViewMock = Phake::mock(Templatable::class);
        $this->oUserNotifier = new UserNotifier($this->oMailMock, $this->oViewMock);
    }

    public function testSendApprovedContent()
    {
        $this->oUserNotifier
            ->setUserEmail(self::VALID_EMAIL)
            ->approvedContent()
            ->send();

        $this->assertSendMethodsCalled();
    }

    public function testSendDisapprovedContent()
    {
        $this->oUserNotifier
            ->setUserEmail(self::VALID_EMAIL)
            ->disapprovedContent()
            ->send();

        $this->assertSendMethodsCalled();
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

    private function assertSendMethodsCalled()
    {
        Phake::inOrder(
            Phake::verify($this->oViewMock)->parseMail(
                PH7_PATH_SYS . 'global/' . PH7_VIEWS . PH7_TPL_MAIL_NAME . UserNotifier::MAIL_TEMPLATE_FILE,
                self::VALID_EMAIL
            ),
            Phake::verify($this->oMailMock)->send(Phake::anyParameters())
        );
    }
}
