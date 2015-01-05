<?php
/**
 * @title          API for Provider classes
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2015, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Payment / Inc / Class
 * @version        0.9
 */
namespace PH7;

use PH7\Framework\Ip\Ip;

trait Api
{

    /**
     * Save log message.
     *
     * @param mixed $mData
     * @return Returns the number of bytes that were written to the file, or FALSE on failure.
     */
    public function saveLog($mData)
    {
        return file_put_contents($this->registry->path_module_inc . '_log/' . Ip::get() . '.log', $mData, FILE_APPEND);
    }

}
