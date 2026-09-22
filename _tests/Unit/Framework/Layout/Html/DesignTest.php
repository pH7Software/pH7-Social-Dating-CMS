<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2017-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / Framework / Layout / Html
 */

namespace PH7\Test\Unit\Framework\Layout\Html;

@session_start();

use ErrorException;
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

    /**
     * A member without a country, or an IP that Geo can't locate, gives no country code.
     * That must show the neutral flag without passing NULL on to string functions (PHP 9 rejects it).
     */
    public function testSmallFlagIconForUnknownCountryIsTheNeutralFlag(): void
    {
        set_error_handler(static function (int $iSeverity, string $sMessage): bool {
            throw new ErrorException($sMessage, 0, $iSeverity);
        });

        try {
            foreach ([null, ''] as $mCountryCode) {
                ob_start();
                try {
                    $this->oDesign->getSmallFlagIcon($mCountryCode);
                } finally {
                    $sOutput = ob_get_clean();
                }

                $this->assertSame(PH7_IMG . 'flag/s/' . Design::NONE_FLAG_FILENAME, $sOutput);
            }
        } finally {
            restore_error_handler();
        }
    }
}
