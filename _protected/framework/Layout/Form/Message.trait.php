<?php
/**
 * Helper that gives popular predefined form messages to avoid duplicating same strings over and over again.
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Layout / Form
 */

namespace PH7\Framework\Layout\Form;

use PH7\Framework\Image\Image;
use PH7\Framework\Video\Video;

trait Message
{
    /**
     * Launch Error Token Message.
     *
     * @return string
     */
    public static function errorTokenMsg()
    {
        return t('The security token does not exist or its lifetime has expired. Please try once again');
    }

    /**
     * Launch Wrong Image File Type Message.
     *
     * @return string
     */
    public static function wrongImgFileTypeMsg()
    {
        return t('The file type is incompatible or too large. Please try with a smaller image with one of the following extensions: %0%', self::getImageExtensions());
    }

    /**
     * Launch Wrong Video File Type Message.
     *
     * @return string
     */
    public static function wrongVideoFileTypeMsg()
    {
        return t('File type is incompatible or too large. The accepted file types are: %0%', self::getVideoExtensions());
    }

    /**
     * Launch Wrong HTTP Request Method Message.
     *
     * @param string $sMethodName
     *
     * @return string
     */
    public static function wrongRequestMethodMsg($sMethodName)
    {
        return t('The HTTP parameter must be a %0% type!', $sMethodName);
    }

    /**
     * Launch an Error Sending Email.
     *
     * @return string
     */
    public static function errorSendingEmail()
    {
        return t('Oops! Our email server encountered an internal error. The email could not be sent. Please try again later');
    }

    /**
     * Number of connection attempts exceeded.
     *
     * @param int $iWaitTime
     *
     * @return string
     */
    public static function loginAttemptsExceededMsg($iWaitTime)
    {
        return t('Oops! You have exceeded the allowed login attempts. Please try again in %0% %1%.', self::convertTime($iWaitTime), self::getTimeText($iWaitTime));
    }

    /**
     * @return string
     */
    public static function duplicateContentMsg()
    {
        return t("It seems you previously sent the same message. Be unique and you'll increase your chances of receiving a reply 😉");
    }

    /**
     * @return string
     */
    public static function tooManyUrlsMsg()
    {
        return t('Oops! It seems you are abusing of links. Why are links so important to you?');
    }

    /**
     * @return string
     */
    public static function tooManyEmailsMsg()
    {
        return t('Oops! It seems you abused of emails. Why are emails so important to you?');
    }


    /**
     * Wait to write a new message (mainly to reduce spam).
     *
     * @param int $iWaitTime (in minutes)
     *
     * @return string
     */
    public static function waitWriteMsg($iWaitTime)
    {
        return t('Oops! You should wait %0% %1% before you can send another one 😉', self::convertTime($iWaitTime), self::getTimeText($iWaitTime));
    }

    /**
     * Wait to new registration (mainly to reduce spam).
     *
     * @param int $iWaitTime (in minutes)
     *
     * @return string
     */
    public static function waitRegistrationMsg($iWaitTime)
    {
        return t('Oops! Somebody has recently registered with the same IP address. Do you mind waiting %0% %1%?', self::convertTime($iWaitTime), self::getTimeText($iWaitTime));
    }

    /**
     * Get Time text.
     *
     * @param int $iWaitTime
     *
     * @return int
     */
    private static function getTimeText($iWaitTime)
    {
        $iWaitTime = (int)$iWaitTime;

        return ($iWaitTime < 2 ? t('minute') : ($iWaitTime < 60 ? t('minutes') : ($iWaitTime < 120 ? t('hour') : ($iWaitTime < 1440 ? t('hours') : ($iWaitTime < 2880 ? t('day') : t('days'))))));
    }

    /**
     * Conversion time if necessary (we do not do the conversion of minutes so you should rather take hours sharp (same thing for days).
     *
     * @param int $iWaitTime
     *
     * @return int
     */
    private static function convertTime($iWaitTime)
    {
        $iWaitTime = (int)$iWaitTime;

        if ($iWaitTime > 60) {
            $iDivide = ($iWaitTime < 1440) ? 60 : 1440;
            $iWaitTime = floor($iWaitTime / $iDivide);
        }

        return $iWaitTime;
    }

    /**
     * @return string e.g., .jpg, .png, .gif, .webp
     */
    private static function getImageExtensions()
    {
        return '.' . implode(', .', Image::SUPPORTED_TYPES);
    }

    /**
     * @return string e.g., .mov, .avi, .flv, .mp4, .mpg
     */
    private static function getVideoExtensions()
    {
        return '.' . implode(', .', array_keys(Video::SUPPORTED_TYPES));
    }
}
