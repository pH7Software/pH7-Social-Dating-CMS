<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2018-2020, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Install / Library
 */

namespace PH7;

defined('PH7') or die('Restricted access');

final class DbDefaultConfig
{
    const HOSTNAME = 'localhost';
    const USERNAME = 'root';
    const NAME = 'ph7cms';
    const PREFIX = 'ph7_';
    const PORT = 3306;
    const CHARSET = 'utf8mb4';
}
