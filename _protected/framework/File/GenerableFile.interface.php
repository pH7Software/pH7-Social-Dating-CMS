<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Framework / File
 */

namespace PH7\Framework\File;

interface GenerableFile
{
    /**
     * Returns the file header containing useful information relative to the generated file.
     *
     * @return string
     */
    public function getHeaderContents();
}
