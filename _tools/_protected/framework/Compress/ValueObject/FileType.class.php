<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Compress / ValueObject
 */

namespace PH7\Framework\Compress\ValueObject;

defined('PH7') or exit('Restricted access');

class FileType
{
    const JS_TYPE = 'js';
    const CSS_TYPE = 'css';

    const VALID_TYPES = [
        self::JS_TYPE,
        self::CSS_TYPE
    ];

    /** @var string */
    private $sFileType;

    /**
     * @param string $sFileType
     *
     * @throws InvalidFileTypeException
     */
    public function __construct($sFileType)
    {
        if (!$this->isValid($sFileType)) {
            throw new InvalidFileTypeException(
                sprintf(
                    'The filetype is invalid. Must be "%s".',
                    implode('" or "', self::VALID_TYPES)
                )
            );
        }

        $this->sFileType = $sFileType;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->sFileType;
    }

    /**
     * @param string $sFileType
     *
     * @return bool
     */
    private function isValid($sFileType)
    {
        return in_array($sFileType, self::VALID_TYPES, true);
    }
}
