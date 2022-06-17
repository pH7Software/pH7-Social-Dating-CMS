<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2017-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / Framework / Layout / Html
 */

namespace PH7\Test\Unit\Framework\Layout\Html;

@session_start();

use PH7\Framework\Config\Config;
use PH7\Framework\Layout\Html\Design;
use PH7\Framework\Session\Session;
use PHPUnit\Framework\TestCase;

class DesignTest extends TestCase
{
    private Design $oDesign;

    private Session $oSession;

    private Config $oConfig;

    protected function setUp(): void
    {
        $this->oConfig = Config::getInstance();
        $this->oSession = new Session;
        $this->oDesign = new Design;
    }

    public function testSetFlashMsgWithDefaultType(): void
    {
        $this->oDesign->setFlashMsg('Hey You!');
        $this->assertSame('Hey You!', $this->oSession->get('flash_msg'));
        $this->assertSame('success', $this->oSession->get('flash_type'));
    }

    public function testSetFlashMsgWithErrorType(): void
    {
        $this->oDesign->setFlashMsg('Wrong Message!', Design::ERROR_TYPE);
        $this->assertSame('Wrong Message!', $this->oSession->get('flash_msg'));
        $this->assertSame('danger', $this->oSession->get('flash_type'));
    }

    public function testSetFlashMsgWithWrongType(): void
    {
        $this->oDesign->setFlashMsg('blabla', 'wrong_type');
        $this->assertSame('blabla', $this->oSession->get('flash_msg'));
        $this->assertSame('success', $this->oSession->get('flash_type'));
    }
}
