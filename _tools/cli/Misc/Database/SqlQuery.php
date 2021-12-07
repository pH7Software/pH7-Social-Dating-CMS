<?php
/**
 * Copyright (c) Pierre-Henry Soria <hi@ph7.me>
 * MIT License - https://opensource.org/licenses/MIT
 */

namespace PH7\Cli\Misc\Database;

final class SqlQuery
{
    public const ADD_ADMIN = 'INSERT INTO %s (profileId , username, password, email, firstName, lastName, joinDate, lastActivity, ip)
    VALUES (1, :username, :password, :email, :firstName, :lastName, :joinDate, :lastActivity, :ip)';

    public const UPDATE_SITE_NAME = "UPDATE %s SET settingValue = :siteName WHERE settingName = 'siteName' OR settingName = 'watermarkTextImage' OR settingName = 'emailName'";

    public const UPDATE_ADMIN_EMAIL = "UPDATE %s SET settingValue = :adminEmail WHERE settingName = 'adminEmail' LIMIT 1";

    public const UPDATE_FEEDBACK_EMAIL = "UPDATE %s SET settingValue = :feedbackEmail WHERE settingName = 'feedbackEmail' LIMIT 1";

    public const UPDATE_RETURN_EMAIL = "UPDATE %s SET settingValue = :returnEmail WHERE settingName = 'returnEmail' LIMIT 1";
}
