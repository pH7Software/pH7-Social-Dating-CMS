<?php

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Module\Api\Controller;

use PHPUnit\Framework\TestCase;

final class UserControllerInputGuardTest extends TestCase
{
    private string $sController;

    protected function setUp(): void
    {
        $sFile = dirname(__DIR__, 7) . '/_protected/app/system/modules/api/controllers/UserController.php';
        $sController = file_get_contents($sFile);

        $this->assertIsString($sController);
        $this->sController = $sController;
    }

    public function testJsonPayloadShapeIsCheckedBeforeAccountFieldsAreRead(): void
    {
        $iGuardPosition = strpos($this->sController, '$this->areFieldsExist($mData');
        $iBirthDatePosition = strpos($this->sController, "Validate::normalizeBirthDate(\$aData['birth_date'])");

        $this->assertIsInt($iGuardPosition);
        $this->assertIsInt($iBirthDatePosition);
        $this->assertLessThan($iBirthDatePosition, $iGuardPosition);
        $this->assertStringContainsString('if (!is_array($mData))', $this->sController);
        $this->assertStringContainsString('!is_scalar($mData[$sName])', $this->sController);
    }

    public function testCreateAndLoginNormalizeValidatedPayloads(): void
    {
        $this->assertSame(2, substr_count($this->sController, '$this->areFieldsExist($mData'));
        $this->assertSame(2, substr_count($this->sController, '$this->normalizeFields($mData'));
    }

    public function testCreateAccountReturnsAControlledResponseWhenPersistenceFails(): void
    {
        $iTry = strpos($this->sController, 'try {', strpos($this->sController, "'ip' =>"));
        $iAdd = strpos($this->sController, '$this->oUserModel->add(', $iTry);
        $iCatch = strpos($this->sController, 'catch (\\Throwable $oException)', $iAdd);
        $iResponse = strpos($this->sController, 'StatusCode::INTERNAL_SERVER_ERROR', $iCatch);

        $this->assertIsInt($iTry);
        $this->assertIsInt($iAdd);
        $this->assertIsInt($iCatch);
        $this->assertIsInt($iResponse);
        $this->assertLessThan($iAdd, $iTry);
        $this->assertLessThan($iCatch, $iAdd);
        $this->assertLessThan($iResponse, $iCatch);
    }
}
