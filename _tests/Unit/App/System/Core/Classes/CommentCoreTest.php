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
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class CommentCoreTest extends TestCase
{
    #[DataProvider('tableNamesProvider')]
    public function testCorrectTable(string $sTableName): void
    {
        $this->assertSame($sTableName, CommentCore::checkTable($sTableName));
    }

    public function testIncorrectTable(): void
    {
        $this->expectException(PH7InvalidArgumentException::class);

        CommentCore::checkTable('incorrect_table');
    }

    #[DataProvider('tableNamesProvider')]
    public function testKnownTableIsValid(string $sTableName): void
    {
        $this->assertTrue(CommentCore::isValidTable($sTableName));
        $this->assertTrue(CommentCore::isValidTable(ucfirst($sTableName)));
    }

    /**
     * URLs such as "/comment/read/Nope/1" or "/comment/comment/read/note,1" carry these values,
     * which must be reported as unknown rather than thrown on.
     */
    #[DataProvider('unknownTableNamesProvider')]
    public function testUnknownTableIsNotValid(string $sTableName): void
    {
        $this->assertFalse(CommentCore::isValidTable($sTableName));
    }

    public static function tableNamesProvider(): array
    {
        return [
            ['profile'],
            ['picture'],
            ['video'],
            ['blog'],
            ['note']
        ];
    }

    public static function unknownTableNamesProvider(): array
    {
        return [
            ['Nope'],
            [''],
            ['note,1'],
            ['members']
        ];
    }
}
