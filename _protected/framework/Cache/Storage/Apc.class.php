<?php
/**
 * @title            Apc Class
 * @desc             APC (Alternative PHP Cache).
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Cache / Storage
 * @version          1.3
 */

namespace PH7\Framework\Cache\Storage;

defined('PH7') or exit('Restricted access');

class Apc
{
    /** @var int|float */
    private $iDefaultTtl;

    /** @var array */
    private $aTtlOverride = [];

    /**
     * @param int $iDefaultTtl
     *
     * @throws MissingExtensionException
     */
    public function __construct($iDefaultTtl = 0)
    {
        $this->iDefaultTtl = abs($iDefaultTtl);

        if (!extension_loaded('apc')) {
            throw new MissingExtensionException('APC PHP extension is not installed.');
        }
    }

    /**
     * @param string $sId
     * @param string $sValue
     * @param int $iTtl
     *
     * @return bool
     */
    public function add($sId, $sValue, $iTtl = 0)
    {
        $iTtl = abs($iTtl);

        if ($iTtl === 0) {
            $iTtl = $this->iDefaultTtl;
        }

        // See if this ID exists in the override table.
        if (isset($this->aTtlOverride[$sId])) {
            $iTtl = $this->aTtlOverride[$sId];
        }

        if ($sValue !== null) {
            apc_store($sId, $sValue, $iTtl);
        }

        return true;
    }

    /**
     * @param string $sId
     * @param bool $bPurge
     *
     * @return mixed
     */
    public function fetch($sId, $bPurge = false)
    {
        $sValue = apc_fetch($sId);

        if ($bPurge === true) {
            apc_delete($sId);
        }

        return $sValue;
    }

    /**
     * @param string $sId
     *
     * @return bool
     */
    public function exists($sId)
    {
        // Ignore the value returned, and just set the value of $sId
        $bCached = false;
        apc_fetch($sId, $bCached);

        return $bCached;
    }

    /**
     * @param string $sId
     *
     * @return bool
     */
    public function remove($sId)
    {
        apc_delete($sId);

        return true;
    }

    /**
     * @return bool
     */
    public function clear()
    {
        apc_clear_cache();

        return true;
    }
}
