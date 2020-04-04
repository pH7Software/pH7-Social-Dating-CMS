<?php
/**
 * @title          Spam Class
 * @desc           To prevent spam.
 *
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / Framework / Security / Spam
 * @version        0.1
 */

namespace PH7\Framework\Security\Spam;

defined('PH7') or exit('Restricted access');

class Spam
{
    const DEFAULT_MAX_ALLOWED_LINKS = 1;
    const DEFAULT_MAX_ALLOWED_EMAILS = 1;

    const REGEX_URL_FORMAT = '#https?://(?:www\.)?[a-z0-9._-]{2,}\.[a-z]{2,5}/?#i';
    const REGEX_EMAIL_FORMAT = '#[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}#i';

    const ERASE_CHARACTERS = [
        '#',
        '@',
        '&nbsp;',
        '-',
        '_',
        '|',
        ';',
        '.',
        ',',
        '!',
        '?',
        '&',
        "'",
        '"',
        '(',
        ')',
        '<p>',
        '</p>',
        '<span>',
        '</span>',
        '<div>',
        '</div>',
        '<br',
        '<',
        '>',
        "\n",
        "\r",
        "\t",
        ' '
    ];

    /**
     * Detect duplicate contents. Processing strings case-insensitive.
     *
     * @param string $sText1
     * @param string $sText2
     *
     * @return bool Returns TRUE if similar content was found in the table, FALSE otherwise.
     */
    public static function detectDuplicate($sText1, $sText2)
    {
        $sText1 = str_ireplace(static::ERASE_CHARACTERS, '', $sText1);
        $sText2 = str_ireplace(static::ERASE_CHARACTERS, '', $sText2);

        return stripos($sText1, $sText2) !== false;
    }

    /**
     * Check if there are (x) links in text.
     *
     * @param string $sText
     * @param int $iMaxAmount Number maximum of URLs the text can contain.
     *
     * @return bool TRUE if there are too many emails, FALSE otherwise.
     */
    public static function areUrls($sText, $iMaxAmount = self::DEFAULT_MAX_ALLOWED_LINKS)
    {
        return self::are(self::REGEX_URL_FORMAT, $sText, $iMaxAmount);
    }

    /**
     * Check if there are (x) emails in the text.
     *
     * @param string $sText
     * @param int $iMaxAmount Number maximum of emails the text can contain.
     *
     * @return bool TRUE if there are too many links than the accepted amount, FALSE otherwise.
     */
    public static function areEmails($sText, $iMaxAmount = self::DEFAULT_MAX_ALLOWED_EMAILS)
    {
        return self::are(self::REGEX_EMAIL_FORMAT, $sText, $iMaxAmount);
    }

    /**
     * @param string $sRegex
     * @param string $sText
     * @param int $iMaxAmount
     *
     * @return bool
     */
    private static function are($sRegex, $sText, $iMaxAmount)
    {
        preg_match_all($sRegex, $sText, $aMatch);

        return count($aMatch[0]) > $iMaxAmount;
    }
}
