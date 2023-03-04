<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2017-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Framework / Service / SearchImage
 */

namespace PH7\Framework\Service\SearchImage;

defined('PH7') or exit('Restricted access');

interface Imageable
{
    /**
     * @return string
     */
    public function getProviderUrl();

    /**
     * @return string
     */
    public function getSearchImageUrl();
}
