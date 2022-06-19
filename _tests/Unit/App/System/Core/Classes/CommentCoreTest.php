<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / App / System / Core / Classes
 */

namespace PH7\Test\Unit\App\System\Core\Classes;

require_once PH7_PATH_SYS . 'core/classes/CommentCore.php';

use PH7\CommentCore;
use PH7\Framework\Error\CException\PH7InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class CommentCoreTest extends TestCase
{
    /**
     * @dataProvider tableNamesProvider
     */
    public function testCorrectTable(string $sTableName): void
    {
        $this->assertSame($sTableName, CommentCore::checkTable($sTableName));
    }

    public function testIncorrectTable(): void
    {
        $this->expectException(PH7InvalidArgumentException::class);

        CommentCore::checkTable('incorrect_table');
    }

    public function tableNamesProvider(): array
    {
        return [
            ['profile'],
            ['picture'],
            ['video'],
            ['blog'],
            ['note']
        ];
    }
}
