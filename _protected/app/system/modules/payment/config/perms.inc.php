<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Payment / Config
 */
namespace PH7;
defined('PH7') or exit('Restricted access');

/**
 * The default data permissions of memberships.
 * After the data will be serialized and stored in the database for the personalized membership groups.
 *
 * 1 = Yes | 0 = No
 */

return array(
    'quick_search_profiles' => 1,
    'advanced_search_profiles' => 1,
);
