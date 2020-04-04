<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2017-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Test / Unit / Framework / Layout / Html
 */

namespace PH7\Test\Unit\Framework\Layout\Html;

@session_start();

use PH7\Framework\Config\Config;
use PH7\Framework\Layout\Html\Design;
use PH7\Framework\Session\Session;
use PHPUnit_Framework_TestCase;

class DesignTest extends PHPUnit_Framework_TestCase
{
    /** @var Design */
    private $oDesign;

    /** @var Session */
    private $oSession;

    /** @var Config */
    private $oConfig;

    protected function setUp()
    {
        $this->oConfig = Config::getInstance();
        $this->oSession = new Session;
        $this->oDesign = new Design;
    }

    public function testSetFlashMsgWithDefaultType()
    {
        $this->oDesign->setFlashMsg('Hey You!');
        $this->assertSame('Hey You!', $this->oSession->get('flash_msg'));
        $this->assertSame('success', $this->oSession->get('flash_type'));
    }

    public function testSetFlashMsgWithErrorType()
    {
        $this->oDesign->setFlashMsg('Wrong Message!', Design::ERROR_TYPE);
        $this->assertSame('Wrong Message!', $this->oSession->get('flash_msg'));
        $this->assertSame('danger', $this->oSession->get('flash_type'));
    }

    public function testSetFlashMsgWithWrongType()
    {
        $this->oDesign->setFlashMsg('blabla', 'wrong_type');
        $this->assertSame('blabla', $this->oSession->get('flash_msg'));
        $this->assertSame('success', $this->oSession->get('flash_type'));
    }
}
