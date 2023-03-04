<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2021, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Framework / Image
 */

declare(strict_types=1);

namespace PH7\Framework\Image;

interface Storageable
{
    public function save(string $sFile): self;

    public function remove(string $sFile): self;
}
