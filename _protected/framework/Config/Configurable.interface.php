<?php
/**
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Config
 */

namespace PH7\Framework\Config;

// The prototypes of the methods
interface Configurable
{

    public function load($sFile);

    public function getValue($sKey);

    public function setValue($sKey, $sValue);

}
