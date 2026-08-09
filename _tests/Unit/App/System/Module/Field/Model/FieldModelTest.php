<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Module\Field\Model;

require_once PH7_PATH_SYS_MOD . 'field/models/FieldModel.php';

use PH7\FieldModel;
use PH7\DbTableName;
use PH7\Framework\Error\CException\PH7InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

final class FieldModelTest extends TestCase
{
    #[DataProvider('validFieldNameProvider')]
    public function testFieldNameIsValidatedAndQuoted(string $sFieldName): void
    {
        $oMethod = new ReflectionMethod(FieldModel::class, 'quoteColumnName');

        $this->assertTrue(FieldModel::isValidColumnName($sFieldName));
        $this->assertSame('`' . $sFieldName . '`', $oMethod->invoke(null, $sFieldName));
    }

    public static function validFieldNameProvider(): array
    {
        return [
            'letters' => ['aboutMe'],
            'underscores' => ['about_me'],
            'numeric prefix' => ['12_field']
        ];
    }

    #[DataProvider('invalidFieldNameProvider')]
    public function testUnsafeFieldNameIsRejected(mixed $mFieldName): void
    {
        $oMethod = new ReflectionMethod(FieldModel::class, 'quoteColumnName');

        $this->assertFalse(FieldModel::isValidColumnName($mFieldName));
        $this->expectException(PH7InvalidArgumentException::class);
        $oMethod->invoke(null, $mFieldName);
    }

    public static function invalidFieldNameProvider(): array
    {
        return [
            'sql syntax' => ['field` INT; DROP TABLE ph7_members; --'],
            'space' => ['bad field'],
            'too short' => ['x'],
            'too long' => [str_repeat('x', 31)],
            'array shaped input' => [['field_name']]
        ];
    }

    public function testArrayShapedFieldNameIsRejectedBeforeDatabaseAccess(): void
    {
        $this->expectException(PH7InvalidArgumentException::class);

        new FieldModel(DbTableName::MEMBER_INFO, ['field_name']);
    }

    #[DataProvider('emptyDefaultValueProvider')]
    public function testEmptyDefaultsRemainTypeSafe(string $sType, ?string $sExpectedDefault): void
    {
        $oModel = (new ReflectionClass(FieldModel::class))->newInstanceWithoutConstructor();
        (new ReflectionProperty(FieldModel::class, 'sType'))->setValue($oModel, $sType);
        (new ReflectionProperty(FieldModel::class, 'iLength'))->setValue($oModel, 0);
        (new ReflectionProperty(FieldModel::class, 'sDefVal'))->setValue($oModel, null);

        $sSqlType = (new ReflectionMethod(FieldModel::class, 'getSqlType'))->invoke($oModel);
        $mDefault = (new ReflectionProperty(FieldModel::class, 'sDefVal'))->getValue($oModel);

        $this->assertIsString($sSqlType);
        $this->assertSame($sExpectedDefault, $mDefault);
    }

    public static function emptyDefaultValueProvider(): array
    {
        return [
            'textbox keeps SQL NULL' => ['textbox', null],
            'number uses a string zero' => ['number', '0']
        ];
    }
}
