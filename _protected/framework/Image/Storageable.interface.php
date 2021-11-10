<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2021, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Image
 */

namespace PH7\Framework\Image;

interface Storageable
{
    /**
     * @param string $sFile
     * @return self
     */
    public function save($sFile);

    /**
     * @param string $sFile
     * @return self
     */
    public function remove($sFile);
}
