<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Test / Unit / App / System / Core / Classes
 */

namespace PH7\Test\Unit\App\System\Core\Classes;

require_once PH7_PATH_SYS . 'core/classes/AdsCore.php';

use PH7\AdsCore;
use PH7\DbTableName;
use PHPUnit_Framework_TestCase;

class AdsCoreTest extends PHPUnit_Framework_TestCase
{
    public function testGetAffiliateTable()
    {
        $_GET['ads_type'] = 'affiliate';

        $this->assertSame('ads_affiliates', AdsCore::getTable());
    }

    public function testGetAdsTable()
    {
        $this->assertSame('ads', AdsCore::getTable());
    }

    /**
     * @param string $sTableName
     * @param string $sExpectedValue
     *
     * @dataProvider tableNamesProvider
     */
    public function testCorrectTable($sTableName, $sExpectedValue)
    {
        $this->assertSame($sExpectedValue, AdsCore::checkTable($sTableName));
    }

    /**
     * @expectedException \PH7\Framework\Error\CException\PH7InvalidArgumentException
     */
    public function testIncorrectTable()
    {
        AdsCore::checkTable('wrong_table');
    }

    /**
     * @param string $sTableName
     *
     * @dataProvider tableNamesProvider
     */
    public function testCorrectTableToId($sTableName)
    {
        $this->assertSame('adsId', AdsCore::convertTableToId($sTableName));
    }

    /**
     * @expectedException \PH7\Framework\Error\CException\PH7InvalidArgumentException
     */
    public function testIncorrectTableToId()
    {
        AdsCore::convertTableToId('wrong_table');
    }

    /**
     * @return array
     */
    public function tableNamesProvider()
    {
        return [
            [DbTableName::AD, 'ads'],
            [DbTableName::AD_AFFILIATE, 'ads_affiliates']
        ];
    }
}
