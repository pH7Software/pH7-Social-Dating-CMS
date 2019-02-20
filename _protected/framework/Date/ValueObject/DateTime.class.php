<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Date / ValueObject
 */

namespace PH7\Framework\Date\ValueObject;

defined('PH7') or exit('Restricted access');

class DateTime
{
    const REGEX_DATE_FORMAT = '/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/';

    /** @var string */
    private $sDateTime;

    /**
     * @param string $sDateTime
     *
     * @throws InvalidDateFormatException
     */
    public function __construct($sDateTime)
    {
        if (!preg_match(self::REGEX_DATE_FORMAT, $sDateTime)) {
            throw new InvalidDateFormatException(
                sprintf('Invalid %s date format.', $sDateTime)
            );
        }

        $this->sDateTime = $sDateTime;
    }

    /**
     * @return string
     */
    public function asString()
    {
        return $this->sDateTime;
    }
}
