<?php
/**
 * @title          API for Provider classes
 *
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Payment / Inc / Class
 */

namespace PH7;

use PH7\Framework\Ip\Ip;
use PH7\Framework\Registry\Registry;

trait Api
{
    /**
     * Save log message.
     *
     * @param mixed $mData
     * @param Registry $oRegistry
     *
     * @return int|bool Returns the number of bytes that were written to the file, or FALSE on failure.
     */
    public function saveLog($mData, Registry $oRegistry)
    {
        return file_put_contents(
            $oRegistry->path_module_inc . '_log/' . Ip::get() . '.log',
            $mData,
            FILE_APPEND
        );
    }
}
