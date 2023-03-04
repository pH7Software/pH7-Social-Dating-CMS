<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / Framework / Mvc/ Model / Engine / Util
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Mvc\Model\Engine\Util;

use PH7\DbTableName;
use PH7\Framework\Error\CException\PH7InvalidArgumentException;
use PH7\Framework\Mvc\Model\Engine\Util\Various as DbVarious;
use PHPUnit\Framework\TestCase;

class VariousTest extends TestCase
{
    /**
     * @dataProvider tablesProvider
     */
    public function testCorrectTable(string $sTable, string $sExpectedTable): void
    {
        $this->assertSame($sExpectedTable, DbVarious::checkTable($sTable));
    }

    public function testIncorrectTable(): void
    {
        $this->expectException(PH7InvalidArgumentException::class);

        DbVarious::checkTable('incorrect_table');
    }

    /**
     * @dataProvider modelTablesProvider
     */
    public function testCorrectModelTable(string $sTable, string $sExpectedTable): void
    {
        $this->assertSame($sExpectedTable, DbVarious::checkModelTable($sTable));
    }

    public function testIncorrectModelTable(): void
    {
        $this->expectException(PH7InvalidArgumentException::class);

        DbVarious::checkModelTable('incorrect_table');
    }

    /**
     * @dataProvider modsToTablesProvider
     */
    public function testCorrectModToTable(string $sMod, string $sExpectedTable): void
    {
        $this->assertSame($sExpectedTable, DbVarious::convertModToTable($sMod));
    }

    public function testIncorrectModToTable(): void
    {
        $this->expectException(PH7InvalidArgumentException::class);

        DbVarious::convertModToTable('wrong_module');
    }

    /**
     * @dataProvider tablesToModsProvider
     */
    public function testCorrectTableToMod(string $sTable, string $sExpectedMod): void
    {
        $this->assertSame($sExpectedMod, DbVarious::convertTableToMod($sTable));
    }

    public function testIncorrectTableToMod(): void
    {
        $this->expectException(PH7InvalidArgumentException::class);

        DbVarious::convertTableToMod('wrong_table');
    }

    /**
     * @dataProvider tablesToIdsProvider
     */
    public function testCorrectTableToId(string $sTable, string $sExpectedColumnId): void
    {
        $this->assertSame($sExpectedColumnId, DbVarious::convertTableToId($sTable));
    }

    public function testIncorrectTableToId(): void
    {
        $this->expectException(PH7InvalidArgumentException::class);

        DbVarious::convertTableToId('wrong_table');
    }

    public function tablesProvider(): array
    {
        return [
            [DbTableName::MEMBER, 'members'],
            [DbTableName::ALBUM_PICTURE, 'albums_pictures'],
            [DbTableName::ALBUM_VIDEO, 'albums_videos'],
            [DbTableName::PICTURE, 'pictures'],
            [DbTableName::VIDEO, 'videos'],
            [DbTableName::BLOG, 'blogs'],
            [DbTableName::NOTE, 'notes'],
            [DbTableName::AD, 'ads'],
            [DbTableName::AD_AFFILIATE, 'ads_affiliates']
        ];
    }

    public function modelTablesProvider(): array
    {
        return [
            [DbTableName::MEMBER, 'members'],
            [DbTableName::AFFILIATE, 'affiliates'],
            [DbTableName::MEMBER_INFO, 'members_info'],
            [DbTableName::AFFILIATE_INFO, 'affiliates_info'],
            [DbTableName::MEMBER_COUNTRY, 'members_countries'],
            [DbTableName::AFFILIATE_COUNTRY, 'affiliates_countries'],
            [DbTableName::SUBSCRIBER, 'subscribers'],
            [DbTableName::ADMIN, 'admins']
        ];
    }

    public function modsToTablesProvider(): array
    {
        return [
            ['user', 'members'],
            ['affiliate', 'affiliates'],
            ['newsletter', 'subscribers'],
            [PH7_ADMIN_MOD, 'admins']
        ];
    }

    public function tablesToModsProvider(): array
    {
        return [
            [DbTableName::MEMBER, 'user'],
            [DbTableName::AFFILIATE, 'affiliate'],
            [DbTableName::SUBSCRIBER, 'newsletter'],
            [DbTableName::ADMIN, PH7_ADMIN_MOD]
        ];
    }

    public function tablesToIdsProvider(): array
    {
        return [
            [DbTableName::MEMBER, 'profileId'],
            [DbTableName::PICTURE, 'pictureId'],
            [DbTableName::ALBUM_PICTURE, 'albumId'],
            [DbTableName::VIDEO, 'videoId'],
            [DbTableName::ALBUM_VIDEO, 'albumId'],
            [DbTableName::BLOG, 'blogId'],
            [DbTableName::NOTE, 'noteId'],
            [DbTableName::FORUM_TOPIC, 'topicId']
        ];
    }
}
