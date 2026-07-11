<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2026, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / App / System / Core / Models
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Core\Models;

use PH7\FriendCoreModel;
use PHPUnit\Framework\TestCase;

final class FriendCoreModelTest extends TestCase
{
    public function testGetJsAssetDirResolvesExistingFriendJavascript(): void
    {
        $sAssetDir = FriendCoreModel::getJsAssetDir();

        $this->assertSame(
            PH7_LAYOUT . PH7_SYS . PH7_MOD . 'friend' . PH7_SH . PH7_TPL . 'base' . PH7_SH . PH7_JS,
            $sAssetDir
        );
        $this->assertFileExists(dirname(PH7_PATH_PROTECTED) . PH7_DS . $sAssetDir . FriendCoreModel::JS_FILENAME);
    }
}
