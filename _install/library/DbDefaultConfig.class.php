<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2018-2020, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Install / Library
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

final class DbDefaultConfig
{
    public const HOSTNAME = 'localhost';
    public const USERNAME = 'root';
    public const NAME = 'ph7cms';
    public const PREFIX = 'ph7_';
    public const PORT = '3306';
    public const CHARSET = 'utf8mb4';
}
