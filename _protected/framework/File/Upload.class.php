<?php
/**
 * @title            Upload File Class
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2013-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / File
 */

namespace PH7\Framework\File;
defined('PH7') or exit('Restricted access');

abstract class Upload
{
    /**
     * @internal Protected access because these attributes are used in \PH7\Framework\Video\Video class
     */
    protected $sMaxSize, $iFileSize;

    /**
     * Check if everything is correct.
     *
     * @return boolean
     */
    public function check()
    {
        return $this->checkSize();
    }

    /**
     * Get maximum file size.
     *
     * @return integer Bytes.
     */
    public function getMaxSize()
    {
        $iMaxSize = Various::sizeToBytes($this->sMaxSize);
        $iUploadMaxFileSize = Various::sizeToBytes(ini_get('upload_max_filesize'));
        $iPostMaxSize = Various::sizeToBytes(ini_get('post_max_size'));

        return min($iMaxSize, $iUploadMaxFileSize, $iPostMaxSize);
    }

    /**
     * Check the file size.
     *
     * @return boolean
     */
    protected function checkSize()
    {
        return ($this->iFileSize < $this->getMaxSize());
    }

}
