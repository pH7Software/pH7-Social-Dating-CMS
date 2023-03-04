<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2019-2022, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Install / Library
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

final class SqlQuery
{
    public const ADD_ADMIN = 'INSERT INTO %s (profileId , username, password, email, firstName, lastName, joinDate, lastActivity, ip)
    VALUES (1, :username, :password, :email, :firstName, :lastName, :joinDate, :lastActivity, :ip)';

    public const UPDATE_SITE_NAME = "UPDATE %s SET settingValue = :siteName WHERE settingName = 'siteName' OR settingName = 'watermarkTextImage' OR settingName = 'emailName'";

    public const UPDATE_ADMIN_EMAIL = "UPDATE %s SET settingValue = :adminEmail WHERE settingName = 'adminEmail' LIMIT 1";

    public const UPDATE_FEEDBACK_EMAIL = "UPDATE %s SET settingValue = :feedbackEmail WHERE settingName = 'feedbackEmail' LIMIT 1";

    public const UPDATE_RETURN_EMAIL = "UPDATE %s SET settingValue = :returnEmail WHERE settingName = 'returnEmail' LIMIT 1";

    public const UPDATE_THEME = "UPDATE %s SET settingValue = :theme WHERE settingName = :setting LIMIT 1";

    public const UPDATE_SYS_MODULE = "UPDATE %s SET enabled = :status WHERE folderName = :modName LIMIT 1";

    public const UPDATE_CRON_SECURITY_HASH = "UPDATE %s SET settingValue = :securityHash WHERE settingName = 'cronSecurityHash' LIMIT 1";
}
