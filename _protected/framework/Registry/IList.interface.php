<?php
/**
 * @title            List Interface
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Registry
 * @version          1.0
 */

namespace PH7\Framework\Registry;

interface IList
{

    /**
     * Add data in the list.
     *
     * @param string $sName
     * @param string $sValue
     * @return void
     */
    public function add($sName, $sValue);

}
