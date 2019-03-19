<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Install / Library
 */

namespace PH7;

defined('PH7') or die('Restricted access');

final class SqlQuery
{
    const ADD_ADMIN = 'INSERT INTO %s (profileId , username, password, email, firstName, lastName, joinDate, lastActivity, ip)
    VALUES (1, :username, :password, :email, :firstName, :lastName, :joinDate, :lastActivity, :ip)';

    const UPDATE_SITE_NAME = "UPDATE %s SET settingValue = :siteName WHERE settingName = 'siteName' OR settingName = 'watermarkTextImage' OR settingName = 'emailName'";

    const UPDATE_ADMIN_EMAIL = "UPDATE %s SET settingValue = :adminEmail WHERE settingName = 'adminEmail'  LIMIT 1";

    const UPDATE_FEEDBACK_EMAIL = "UPDATE %s SET settingValue = :feedbackEmail WHERE settingName = 'feedbackEmail'  LIMIT 1";

    const UPDATE_RETURN_EMAIL = "UPDATE %s SET settingValue = :returnEmail WHERE settingName = 'returnEmail'  LIMIT 1";

    const UPDATE_THEME = "UPDATE %s SET settingValue = :theme WHERE settingName = :setting LIMIT 1";

    const UPDATE_SYS_MODULE = "UPDATE %s SET enabled = :status WHERE folderName = :modName LIMIT 1";
}
