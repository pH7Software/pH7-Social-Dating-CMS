<?php
/**
 * @author         Pierre-Henry Soria <hi@ph7.me>
 * @copyright      (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / Inc / Class
 */

namespace PH7;

use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Pattern\Statik;

final class UserNotifierString
{
    /**
     * Statik trait sets private constructor & cloning, since it's a static class
     */
    use Statik;

    /**
     * @return string
     */
    public static function getApprovedSubject()
    {
        return t('Your content has been approved!');
    }

    /**
     * @return string
     */
    public static function getDisapprovedSubject()
    {
        return t('Your content has been disapproved :(');
    }

    /**
     * @return string
     */
    public static function getApprovedMessage()
    {
        $sMsg = t('Congratulation! The content you recently posted at <a href="%site_url%">%site_name%</a> has been successfully approved by the team.');
        $sMsg .= '<br />';
        $sMsg .= t('Other users will now enjoy what you posted and thanks to you, our online service gets better! :)');

        return $sMsg;
    }

    /**
     * @return string
     *
     * @throws Framework\File\IOException
     */
    public static function getDisapprovedMessage()
    {
        $sTermsUrl = Uri::get('page', 'main', 'terms');

        $sMsg = t('Your content you recently posted at <a href="%site_url%">%site_name%</a> has unfortunately been disapproved by our moderation team.');
        $sMsg .= '<br />';
        $sMsg .= t('Indeed, it looks like it does not respect our <a href="%0%">terms of service</a>.', $sTermsUrl);
        $sMsg .= '<br />';
        $sMsg .= t('Please feel free to post again a content at any time as long as it respects our <a href="%0%">terms of service</a>', $sTermsUrl);

        return $sMsg;
    }
}
