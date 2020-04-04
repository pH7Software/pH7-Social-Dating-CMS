<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Test / Unit / Framework / Mvc/ Model / Engine / Util
 */

namespace PH7\Test\Unit\Framework\Mvc\Model\Engine\Util;

use PH7\DbTableName;
use PH7\Framework\Mvc\Model\Engine\Util\Various as DbVarious;
use PHPUnit_Framework_TestCase;

class VariousTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $sTable
     * @param string $sExpectedTable
     *
     * @dataProvider tablesProvider
     */
    public function testCorrectTable($sTable, $sExpectedTable)
    {
        $this->assertSame($sExpectedTable, DbVarious::checkTable($sTable));
    }

    /**
     * @expectedException \PH7\Framework\Error\CException\PH7InvalidArgumentException
     */
    public function testIncorrectTable()
    {
        DbVarious::checkTable('incorrect_table');
    }

    /**
     * @param string $sTable
     * @param string $sExpectedTable
     *
     * @dataProvider modelTablesProvider
     */
    public function testCorrectModelTable($sTable, $sExpectedTable)
    {
        $this->assertSame($sExpectedTable, DbVarious::checkModelTable($sTable));
    }

    /**
     * @expectedException \PH7\Framework\Error\CException\PH7InvalidArgumentException
     */
    public function testIncorrectModelTable()
    {
        DbVarious::checkModelTable('incorrect_table');
    }

    /**
     * @param string $sExpectedTable
     * @param string $sMod
     *
     * @dataProvider modsToTablesProvider
     */
    public function testCorrectModToTable($sMod, $sExpectedTable)
    {
        $this->assertSame($sExpectedTable, DbVarious::convertModToTable($sMod));
    }

    /**
     * @expectedException \PH7\Framework\Error\CException\PH7InvalidArgumentException
     */
    public function testIncorrectModToTable()
    {
        DbVarious::convertModToTable('wrong_module');
    }

    /**
     * @param string $sTable
     * @param string $sExpectedMod
     *
     * @dataProvider tablesToModsProvider
     */
    public function testCorrectTableToMod($sTable, $sExpectedMod)
    {
        $this->assertSame($sExpectedMod, DbVarious::convertTableToMod($sTable));
    }

    /**
     * @expectedException \PH7\Framework\Error\CException\PH7InvalidArgumentException
     */
    public function testIncorrectTableToMod()
    {
        DbVarious::convertTableToMod('wrong_table');
    }

    /**
     * @param string $sTable
     * @param string $sExpectedColumnId
     *
     * @dataProvider tablesToIdsProvider
     */
    public function testCorrectTableToId($sTable, $sExpectedColumnId)
    {
        $this->assertSame($sExpectedColumnId, DbVarious::convertTableToId($sTable));
    }

    /**
     * @expectedException \PH7\Framework\Error\CException\PH7InvalidArgumentException
     */
    public function testIncorrectTableToId()
    {
        DbVarious::convertTableToId('wrong_table');
    }

    /**
     * @return array
     */
    public function tablesProvider()
    {
        return [
            [DbTableName::MEMBER, 'members'],
            [DbTableName::ALBUM_PICTURE, 'albums_pictures'],
            [DbTableName::ALBUM_VIDEO, 'albums_videos'],
            [DbTableName::PICTURE, 'pictures'],
            [DbTableName::VIDEO, 'videos'],
            [DbTableName::GAME, 'games'],
            [DbTableName::BLOG, 'blogs'],
            [DbTableName::NOTE, 'notes'],
            [DbTableName::AD, 'ads'],
            [DbTableName::AD_AFFILIATE, 'ads_affiliates']
        ];
    }

    /**
     * @return array
     */
    public function modelTablesProvider()
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

    /**
     * @return array
     */
    public function modsToTablesProvider()
    {
        return [
            ['user', 'members'],
            ['affiliate', 'affiliates'],
            ['newsletter', 'subscribers'],
            [PH7_ADMIN_MOD, 'admins']
        ];
    }

    /**
     * @return array
     */
    public function tablesToModsProvider()
    {
        return [
            [DbTableName::MEMBER, 'user'],
            [DbTableName::AFFILIATE, 'affiliate'],
            [DbTableName::SUBSCRIBER, 'newsletter'],
            [DbTableName::ADMIN, PH7_ADMIN_MOD]
        ];
    }

    /**
     * @return array
     */
    public function tablesToIdsProvider()
    {
        return [
            [DbTableName::MEMBER, 'profileId'],
            [DbTableName::PICTURE, 'pictureId'],
            [DbTableName::ALBUM_PICTURE, 'albumId'],
            [DbTableName::VIDEO, 'videoId'],
            [DbTableName::ALBUM_VIDEO, 'albumId'],
            [DbTableName::BLOG, 'blogId'],
            [DbTableName::NOTE, 'noteId'],
            [DbTableName::GAME, 'gameId'],
            [DbTableName::FORUM_TOPIC, 'topicId']
        ];
    }
}
