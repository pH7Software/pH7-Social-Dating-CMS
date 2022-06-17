<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / App / System / Core / Classes
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Core\Classes;

require_once PH7_PATH_SYS . 'core/classes/AdsCore.php';

use PH7\AdsCore;
use PH7\DbTableName;
use PH7\Framework\Error\CException\PH7InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class AdsCoreTest extends TestCase
{
    public function testGetAffiliateTable(): void
    {
        $_GET['ads_type'] = 'affiliate';

        $this->assertSame('ads_affiliates', AdsCore::getTable());
    }

    public function testGetAdsTable(): void
    {
        $this->assertSame('ads', AdsCore::getTable());
    }

    /**
     * @dataProvider tableNamesProvider
     */
    public function testCorrectTable(string $sTableName, string $sExpectedValue): void
    {
        $this->assertSame($sExpectedValue, AdsCore::checkTable($sTableName));
    }

    public function testIncorrectTable(): void
    {
        $this->expectException(PH7InvalidArgumentException::class);

        AdsCore::checkTable('wrong_table');
    }

    /**
     * @dataProvider tableNamesProvider
     */
    public function testCorrectTableToId(string $sTableName): void
    {
        $this->assertSame('adsId', AdsCore::convertTableToId($sTableName));
    }

    public function testIncorrectTableToId(): void
    {
        $this->expectException(PH7InvalidArgumentException::class);

        AdsCore::convertTableToId('wrong_table');
    }

    public function tableNamesProvider(): array
    {
        return [
            [DbTableName::AD, 'ads'],
            [DbTableName::AD_AFFILIATE, 'ads_affiliates']
        ];
    }
}
