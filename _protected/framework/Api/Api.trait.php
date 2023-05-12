<?php
/**
 * @author           Pierre-Henry SORIA <hello@ph7builder.com>
 * @copyright        (c) 2015-2023, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Framework / Api
 */

namespace PH7\Framework\Api;

defined('PH7') or exit('Restricted access');

trait Api
{
    /**
     * Encode the data to JSON
     *
     * @param mixed $mData
     *
     * @return string|bool Returns the data encoded to JSON or FALSE if the data is invalid.
     */
    public function set($mData): string|bool
    {
        if (is_array($mData)) {
            return json_encode($mData);
        }

        return false;
    }
}
