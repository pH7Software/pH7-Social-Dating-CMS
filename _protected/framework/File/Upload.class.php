<?php
/**
 * @title            Upload File Class
 *
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2013-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Framework / File
 */

declare(strict_types=1);

namespace PH7\Framework\File;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Error\CException\PH7InvalidArgumentException;

abstract class Upload
{
    protected string $sMaxSize;

    protected int $iFileSize;

    /**
     * Check if everything is correct.
     *
     * @throws PH7InvalidArgumentException
     */
    public function check(): bool
    {
        return $this->checkSize();
    }

    /**
     * Get maximum file size.
     *
     * @throws PH7InvalidArgumentException
     */
    public function getMaxSize(): int
    {
        $iMaxSize = Various::sizeToBytes($this->sMaxSize);
        $iUploadMaxFileSize = Various::sizeToBytes(ini_get('upload_max_filesize'));
        $iPostMaxSize = Various::sizeToBytes(ini_get('post_max_size'));

        return (int)min($iMaxSize, $iUploadMaxFileSize, $iPostMaxSize);
    }

    /**
     * Check the file size.
     *
     * @throws PH7InvalidArgumentException
     */
    protected function checkSize(): bool
    {
        return $this->iFileSize < $this->getMaxSize();
    }
}
