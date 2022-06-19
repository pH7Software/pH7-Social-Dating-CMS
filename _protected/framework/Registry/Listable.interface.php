<?php
/**
 * @title            List Interface
 *
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Framework / Registry
 */

namespace PH7\Framework\Registry;

interface Listable
{
    /**
     * Add data in the list.
     *
     * @param string $sName
     * @param string $sValue
     *
     * @return void
     */
    public function add($sName, $sValue);
}
