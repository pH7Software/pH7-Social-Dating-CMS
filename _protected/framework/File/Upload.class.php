<?php
/**
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2013, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / File
 */

namespace PH7\Framework\File;
defined('PH7') or exit('Restricted access');

abstract class Upload
{

    protected $iMaxSize, $iFileSize;

    /**
     * Check the video size.
     *
     * @return boolean
     */
    public function check()
    {
        if ($this->iFileSize < $this->getMaxSize())
            return true;

        return false;
    }

    /**
     * Get maximum file size.
     *
     * @return integer Bytes.
     */
    public function getMaxSize()
    {
        $iMaxSize = Various::sizeToBytes($this->iMaxSize);
        $iUploadMaxFileSize = Various::sizeToBytes(ini_get('upload_max_filesize'));
        $iPostMaxSize = Various::sizeToBytes(ini_get('post_max_size'));

        return min($iMaxSize, $iUploadMaxFileSize, $iPostMaxSize);
    }

}
