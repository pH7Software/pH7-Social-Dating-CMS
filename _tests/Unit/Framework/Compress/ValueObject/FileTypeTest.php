<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Test / Unit / Framework / Compress / ValueObject
 */

namespace PH7\Test\Unit\Framework\Date\ValueObject;

use PH7\Framework\Compress\ValueObject\FileType;
use PH7\Framework\Compress\ValueObject\InvalidFileTypeException;
use PHPUnit_Framework_TestCase;

class FileTypeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $sFileType
     *
     * @dataProvider validTypesProvider
     */
    public function testValidFileType($sFileType)
    {
        $oFileType = new FileType($sFileType);
        $this->assertSame($sFileType, $oFileType->getValue());
    }

    /**
     * @param string $sFileType
     *
     * @dataProvider invalidTypesProvider
     */
    public function testInvalidFileType($sFileType)
    {
        $this->expectException(InvalidFileTypeException::class);

        new FileType($sFileType);
    }

    /**
     * @return array
     */
    public function validTypesProvider()
    {
        return [
            ['js'],
            ['css']
        ];
    }

    /**
     * @return array
     */
    public function invalidTypesProvider()
    {
        return [
            [''],
            ['php']
        ];
    }
}
