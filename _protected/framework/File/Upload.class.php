<?php
/**
 * @title            Upload File Class
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2013-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / File
 */

namespace PH7\Framework\File;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Error\CException\PH7InvalidArgumentException;

abstract class Upload
{
    /** @var string */
    protected $sMaxSize;

    /** @var int */
    protected $iFileSize;

    /**
     * Check if everything is correct.
     *
     * @return bool
     *
     * @throws PH7InvalidArgumentException
     */
    public function check()
    {
        return $this->checkSize();
    }

    /**
     * Get maximum file size.
     *
     * @return int Bytes.
     *
     * @throws PH7InvalidArgumentException
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
     * @return bool
     *
     * @throws PH7InvalidArgumentException
     */
    protected function checkSize()
    {
        return $this->iFileSize < $this->getMaxSize();
    }
}
