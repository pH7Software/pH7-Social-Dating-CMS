<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2026, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / App / System / Module / Admin123 / Forms / Processing
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Module\Admin123\Forms\Processing;

require_once PH7_PATH_SYS_MOD . 'admin123/forms/processing/AddFakeProfilesFormProcess.php';

use PH7\AddFakeProfilesFormProcess;
use PH7\GenderTypeUserCore;
use PH7\Framework\File\File;
use PH7\Framework\Mvc\Request\Http;
use Phake;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

final class AddFakeProfilesFormProcessTest extends TestCase
{
    public function testGetApiClientReturnsNullWhenApiResponsesAreEmpty(): void
    {
        $oHttpRequest = Phake::mock(Http::class);
        Phake::when($oHttpRequest)->post('num')->thenReturn('10');
        Phake::when($oHttpRequest)->post('sex')->thenReturn('both');
        Phake::when($oHttpRequest)->post('nat')->thenReturn('ALL');

        $oFile = Phake::mock(File::class);
        Phake::when($oFile)->getFile(Phake::anyParameters())->thenReturn(false);

        $oProcess = $this->newProcessInstanceWithoutConstructor();
        $this->setKernelProperty($oProcess, 'httpRequest', $oHttpRequest);
        $this->setKernelProperty($oProcess, 'file', $oFile);

        $oMethod = new ReflectionMethod(AddFakeProfilesFormProcess::class, 'getApiClient');
        $mResult = $oMethod->invoke($oProcess);

        $this->assertNull($mResult);
        Phake::verify($oFile, Phake::times(2))->getFile(Phake::anyParameters());
    }

    public function testGetApiParametersOmitsUnsupportedGenderAndAllNationality(): void
    {
        $oHttpRequest = Phake::mock(Http::class);
        Phake::when($oHttpRequest)->post('num')->thenReturn('7');
        Phake::when($oHttpRequest)->post('sex')->thenReturn('both');
        Phake::when($oHttpRequest)->post('nat')->thenReturn('ALL');

        $oProcess = $this->newProcessInstanceWithoutConstructor();
        $this->setKernelProperty($oProcess, 'httpRequest', $oHttpRequest);

        $aParams = $this->invokeGetApiParameters($oProcess);

        $this->assertSame('7', $aParams['results']);
        $this->assertSame('', $aParams['gender']);
        $this->assertSame('', $aParams['nat']);
        $this->assertSame(1, $aParams['noinfo']);
    }

    public function testGetApiParametersKeepsSupportedGenderAndNationality(): void
    {
        $oHttpRequest = Phake::mock(Http::class);
        Phake::when($oHttpRequest)->post('num')->thenReturn('4');
        Phake::when($oHttpRequest)->post('sex')->thenReturn(GenderTypeUserCore::MALE);
        Phake::when($oHttpRequest)->post('nat')->thenReturn('US');

        $oProcess = $this->newProcessInstanceWithoutConstructor();
        $this->setKernelProperty($oProcess, 'httpRequest', $oHttpRequest);

        $aParams = $this->invokeGetApiParameters($oProcess);

        $this->assertSame('4', $aParams['results']);
        $this->assertSame(GenderTypeUserCore::MALE, $aParams['gender']);
        $this->assertSame('US', $aParams['nat']);
        $this->assertSame(1, $aParams['noinfo']);
    }

    private function newProcessInstanceWithoutConstructor(): AddFakeProfilesFormProcess
    {
        $oReflector = new ReflectionClass(AddFakeProfilesFormProcess::class);

        /** @var AddFakeProfilesFormProcess $oProcess */
        $oProcess = $oReflector->newInstanceWithoutConstructor();

        return $oProcess;
    }

    private function setKernelProperty(AddFakeProfilesFormProcess $oProcess, string $sPropertyName, object $oValue): void
    {
        $oProperty = new ReflectionProperty($oProcess, $sPropertyName);
        $oProperty->setValue($oProcess, $oValue);
    }

    private function invokeGetApiParameters(AddFakeProfilesFormProcess $oProcess): array
    {
        $oMethod = new ReflectionMethod(AddFakeProfilesFormProcess::class, 'getApiParameters');

        /** @var array $aParams */
        $aParams = $oMethod->invoke($oProcess);

        return $aParams;
    }
}
