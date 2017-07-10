<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Service / SearchImage
 */

namespace PH7\Framework\Service\SearchImage;

defined('PH7') or exit('Restricted access');

class Url
{
    /**
     * @param string $sUrl
     *
     * @throws InvalidUrlException
     */
    public function __construct($sUrl)
    {
        if (
            filter_var($sUrl, FILTER_VALIDATE_URL) === false ||
            strlen($sUrl) >= PH7_MAX_URL_LENGTH
        ) {
            throw new InvalidUrlException('Invalid URL');
        }

        $this->sUrl = $sUrl;
    }

    /**
     * @param string
     */
    public function getValue()
    {
        return $this->sUrl;
    }
}
