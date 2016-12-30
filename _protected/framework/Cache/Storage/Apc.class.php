<?php
/**
 * @title            Apc Class
 * @desc             APC (Alternative PHP Cache).
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Cache / Storage
 * @version          1.1
 */

namespace PH7\Framework\Cache\Storage;
defined('PH7') or exit('Restricted access');

class Apc
{

    private $_iDefaultTtl, $_bStarted = false, $_aTtlOverride = array();

    public function __construct($iDefaultTtl = 0)
    {
        $this->_iDefaultTtl = abs($iDefaultTtl);

        // Yoda Condition
        if (false === extension_loaded('apc'))
            throw new \PH7\Framework\Cache\Exception('The APC extension is not loaded and thus, this can not be used.');

        $this->_bStarted = true;
    }

    public function add($sId, $sValue, $iTtl = 0)
    {
        $iTtl = abs($iTtl);

        if (0 == $iTtl)
            $iTtl = $this->_iDefaultTtl;

        // See if this ID exists in the override table.
        if (true === isset($this->_aTtlOverride[$sId]))
            $iTtl = $this->_aTtlOverride[$sId];

        if (false === is_null($sValue))
            apc_store($sId, $sValue, $iTtl);

        return true;
    }

    public function fetch($sId, $bPurge = false)
    {
        $sValue = apc_fetch($sId);

        if (true === $bPurge)
            apc_delete($sId);

        return $sValue;
    }

    public function exists($sId)
    {
        // Ignore the value returned, and just set the value of $sId
        $bCached = false;
        apc_fetch($sId, $bCached);
        return $bCached;
    }

    public function remove($sId)
    {
        apc_delete($sId);
        return true;
    }

    public function clear()
    {
        apc_clear_cache();
        return true;
    }

}
