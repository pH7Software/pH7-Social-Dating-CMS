<?php

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Core;

use PH7\Framework\Core\Kernel;
use PHPUnit\Framework\TestCase;

final class KernelTest extends TestCase
{
    public function testReleaseUrlUsesOfficialGitHubRepository(): void
    {
        self::assertSame(
            Kernel::SOFTWARE_GIT_REPO_URL . '/releases',
            Kernel::SOFTWARE_RELEASE_URL
        );
    }
}
