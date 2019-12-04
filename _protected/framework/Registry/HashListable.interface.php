<?php
/**
 * @title            Hash/List Interface
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Registry
 */

namespace PH7\Framework\Registry;

interface HashListable
{
    /**
     * Get data in the list.
     *
     * @param string $sName
     *
     * @return string Returns the converted value if successful otherwise returns false.
     */
    public function get($sName);
}
