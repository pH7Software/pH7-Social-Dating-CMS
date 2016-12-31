<?php
/**
 * @title            Form Class
 * @desc             Class offering useful methods for the management of forms.
 *                   It allows, among other display popular predefined messages to avoid duplicate text (message) similar.
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Layout / Form
 * @version          1.0
 */

namespace PH7\Framework\Layout\Form;

abstract class Form extends \PH7\Framework\Core\Core
{

    /**
     * Launch Error Token Message.
     *
     * @return string
     */
    public static function errorTokenMsg()
    {
        return t('The security token does not exist or the security time of the token has expired. Please try again!');
    }

    /**
     * Launch Wrong Image File Type Message.
     *
     * @return string
     */
    public static function wrongImgFileTypeMsg()
    {
        return t('The file type is incompatible or too large. Please try again a smaller image with .gif, .png or .jpg extension.');
    }

    /**
     * Launch Wrong Video File Type Message.
     *
     * @return string
     */
    public static function wrongVideoFileTypeMsg()
    {
        return t('The file type is incompatible or too large. The accepted file types are: .mov, .avi, .flv, .mp4, .mpg/.mpeg, .wmv, .ogg, .ogv, .webm or .mkv');
    }

    /**
     * Launch Wrong HTTP Request Method Message.
     *
     * @param string $sMethodName
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
        return t('Oops! Our email server encountered an internal error and the email could not be sent. Please try again later!');
    }

    /**
     * Number of connection attempts exceeded.
     *
     * @param integer $iWaitTime
     * @return string
     */
    public static function loginAttemptsExceededMsg($iWaitTime)
    {
        return t('Oops! You have exceeded allowed login attemts. Please try again in %0% %1%.', static::_convertTime($iWaitTime), static::_getTimeText($iWaitTime));
    }

    /**
     * Duplicate Content.
     *
     * @return string
     */
    public static function duplicateContentMsg()
    {
        return t('Oops! It seems that you have already sent the same message to prevent spam you can not send multiple same content.');
    }

    /**
     * Wait to write a new message (mainly to reduce spam).
     *
     * @param integer $iWaitTime (in munutes)
     * @return string
     */
    public static function waitWriteMsg($iWaitTime)
    {
        return t('Oops! You have to wait %0% %1% before you can send another.', static::_convertTime($iWaitTime), static::_getTimeText($iWaitTime));
    }

    /**
     * Wait to new registration (mainly to reduce spam).
     *
     * @param integer $iWaitTime (in munutes)
     * @return string
     */
    public static function waitRegistrationMsg($iWaitTime)
    {
        return t('Oops! Somebody has recently registered with the same IP address. You have to wait %0% %1% before try again.', static::_convertTime($iWaitTime), static::_getTimeText($iWaitTime));
    }

    /**
     * Get Time text.
     *
     * @param integer $iWaitTime
     * @return integer
     */
    private static function _getTimeText($iWaitTime)
    {
        $iWaitTime = (int) $iWaitTime;

        return ($iWaitTime < 2 ? t('minute') : ($iWaitTime < 60 ? t('minutes') : ($iWaitTime < 120 ? t('hour') : ($iWaitTime < 1440 ? t('hours') : ($iWaitTime < 2880 ? t('day') : t('days'))))));
    }

    /**
     * Conversion time if necessary (we do not do the conversion of minutes so you should rather take hours sharp (same thing for days).
     *
     * @param integer $iWaitTime
     * @return integer
     */
    private static function _convertTime($iWaitTime)
    {
        $iWaitTime = (int) $iWaitTime;

        if($iWaitTime > 60) {
            $iDivide = ($iWaitTime < 1440) ? 60 : 1440;
            $iWaitTime = floor($iWaitTime / $iDivide);
        }

        return $iWaitTime;
    }

}
