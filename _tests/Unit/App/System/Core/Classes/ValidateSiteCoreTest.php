<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Test / Unit / App / System / Core / Classes
 */

namespace PH7\Test\Unit\App\System\Core\Classes;

require_once PH7_PATH_SYS . 'core/classes/AdsCore.php';

use PH7\Framework\Layout\Html\Design;
use PH7\Framework\Session\Session;
use PH7\ValidateSiteCore;
use Phake;
use PHPUnit_Framework_TestCase;

class ValidateSiteCoreTest extends PHPUnit_Framework_TestCase
{
    /** @var ValidateSiteCore */
    private $oValidateSite;

    protected function setUp(): void
    {
        $oSession = Phake::mock(Session::class);
        $this->oValidateSite = new ValidateSiteCore($oSession);
    }

    public function testInjectAssetSuggestionBoxFiles()
    {
        $oDesign = Phake::mock(Design::class);

        $this->oValidateSite->injectAssetSuggestionBoxFiles($oDesign);

        Phake::verify($oDesign)->addJs(Phake::anyParameters());
    }
}
