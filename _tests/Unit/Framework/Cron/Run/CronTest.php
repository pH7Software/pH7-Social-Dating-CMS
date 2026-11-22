<?php
/**
 * @copyright        (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / Framework / Cron / Run
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Cron\Run;

use PH7\Framework\Cron\Run\Cron;
use PH7\Framework\File\File;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

final class CronTest extends TestCase
{
    public function testFirstRunWritesDelayTimestampAsString(): void
    {
        $sDelayPath = sys_get_temp_dir() . '/ph7-cron-delay-test.txt';
        $iTimestamp = 1790000000;

        $oFile = $this->createMock(File::class);
        $oFile->expects($this->once())
            ->method('existFile')
            ->with($sDelayPath)
            ->willReturn(false);
        $oFile->expects($this->once())
            ->method('putFile')
            ->with($sDelayPath, (string)$iTimestamp)
            ->willReturn(10);

        $oCron = new class extends Cron {
            public function __construct()
            {
                // The cron scheduler's other services are not needed for this unit test.
            }

            public function useFile(File $oFile): void
            {
                $this->file = $oFile;
            }

            public function checkDelayForTest(): bool
            {
                return $this->checkDelay();
            }
        };

        $oCron->useFile($oFile);
        (new ReflectionProperty(Cron::class, 'iTime'))->setValue($oCron, $iTimestamp);
        (new ReflectionProperty(Cron::class, 'sDelayPathFile'))->setValue($oCron, $sDelayPath);

        $this->assertTrue($oCron->checkDelayForTest());
    }
}
