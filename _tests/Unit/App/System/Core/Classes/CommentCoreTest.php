<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Test / Unit / App / System / Core / Classes
 */

namespace PH7\Test\Unit\App\System\Core\Classes;

require_once PH7_PATH_SYS . 'core/classes/CommentCore.php';

use PH7\CommentCore;
use PHPUnit_Framework_TestCase;

class CommentCoreTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider tableNamesProvider
     */
    public function testCorrectTable($sTableName)
    {
        $this->assertSame($sTableName, CommentCore::checkTable($sTableName));
    }

    /**
     * @expectedException \PH7\Framework\Error\CException\PH7InvalidArgumentException
     */
    public function testIncorrectTable()
    {
        CommentCore::checkTable('incorrect_table');
    }

    /**
     * @return array
     */
    public function tableNamesProvider()
    {
        return [
            ['profile'],
            ['picture'],
            ['video'],
            ['blog'],
            ['note'],
            ['game']
        ];
    }
}
