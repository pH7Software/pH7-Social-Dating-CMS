<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2018, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / App / Include / Class
 */

namespace PH7;

class DbTableName
{
    const ADMIN = 'admins';
    const MEMBER = 'members';
    const MEMBERSHIP = 'memberships';
    const AFFILIATE = 'affiliates';
    const SUBSCRIBER = 'subscribers';
    const MEMBER_INFO = 'members_info';
    const AFFILIATE_INFO = 'affiliates_info';
    const MEMBER_PRIVACY = 'members_privacy';
    const MEMBER_NOTIFICATION = 'members_notifications';
    const BLOCK_IP = 'block_ip';
    const AD = 'ads';
    const AD_AFFILIATE = 'ads_affiliates';
    const PICTURE = 'pictures';
    const VIDEO = 'videos';
    const ALBUM_PICTURE = 'albums_pictures';
    const ALBUM_VIDEO = 'albums_videos';
    const ANALYTIC_API = 'analytics_api';
    const BLOG = 'blogs';

    const USER_TABLES = [
        self::ADMIN,
        self::MEMBER,
        self::AFFILIATE
    ];
}
