<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2018, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Test / Unit / Framework / Mvc/ Model / Engine / Util
 */

namespace PH7\Test\Unit\Framework\Mvc\Model\Engine\Util;

use PH7\DbTableName;
use PH7\Framework\Mvc\Model\Engine\Util\Various as DbVarious;
use PHPUnit_Framework_TestCase;

class VariousTest extends PHPUnit_Framework_TestCase
{
    public function testCorrectTable()
    {
        $this->assertSame('albums_pictures', DbVarious::checkTable(DbTableName::ALBUM_PICTURE));
    }

    /**
     * @expectedException \PH7\Framework\Error\CException\PH7InvalidArgumentException
     */
    public function testIncorrectTable()
    {
        DbVarious::checkTable('incorrect_table');
    }

    public function testCorrectModelTable()
    {
        $this->assertSame('members_info', DbVarious::checkModelTable(DbTableName::MEMBER_INFO));
    }

    /**
     * @expectedException \PH7\Framework\Error\CException\PH7InvalidArgumentException
     */
    public function testIncorrectModelTable()
    {
        DbVarious::checkModelTable('incorrect_table');
    }

    public function testCorrectModToTable()
    {
        $this->assertSame('members', DbVarious::convertModToTable('user'));
    }

    /**
     * @expectedException \PH7\Framework\Error\CException\PH7InvalidArgumentException
     */
    public function testIncorrectModToTable()
    {
        DbVarious::convertModToTable('wrong_module');
    }

    public function testCorrectTableToMod()
    {
        $this->assertSame('user', DbVarious::convertTableToMod(DbTableName::MEMBER));
    }

    /**
     * @expectedException \PH7\Framework\Error\CException\PH7InvalidArgumentException
     */
    public function testIncorrectTableToMod()
    {
        DbVarious::convertTableToMod('wrong_table');
    }

    public function testCorrectTableToId()
    {
        $this->assertSame('profileId', DbVarious::convertTableToId(DbTableName::MEMBER));
    }

    /**
     * @expectedException \PH7\Framework\Error\CException\PH7InvalidArgumentException
     */
    public function testIncorrectTableToId()
    {
        DbVarious::convertTableToId('wrong_table');
    }
}
