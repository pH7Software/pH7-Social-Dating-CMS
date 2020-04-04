<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Payment / Config
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

/**
 * Default data permissions of memberships.
 * After the data will be serialized and stored in the database for the personalized membership groups.
 *
 * 1 = Yes | 0 = No
 */
return [
    'quick_search_profiles' => 1,
    'advanced_search_profiles' => 1,
    'read_mails' => 1,
    'send_mails' => 1,
    'view_pictures' => 1,
    'upload_pictures' => 1,
    'view_videos' => 1,
    'upload_videos' => 1,
    'instant_messaging' => 1,
    'chat' => 1,
    'chatroulette' => 1,
    'hot_or_not' => 1,
    'love_calculator' => 1,
    'read_notes' => 1,
    'write_notes' => 1,
    'read_blog_posts' => 1,
    'view_comments' => 1,
    'write_comments' => 1,
    'forum_access' => 1,
    'create_forum_topics' => 1,
    'answer_forum_topics' => 1,
    'games_access' => 1,
    'webcam_access' => 1,
    'member_site_access' => 1,
];
