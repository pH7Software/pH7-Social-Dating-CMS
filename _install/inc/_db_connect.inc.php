<?php
/**
 * @title            Db Connect File
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Inc
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

$aParams = array(
    'db_type' => $_SESSION['db']['type'],
    'db_hostname' => $_SESSION['db']['hostname'],
    'db_name' => $_SESSION['db']['name'],
    'db_username' => $_SESSION['db']['username'],
    'db_password' => $_SESSION['db']['password'],
    'db_charset' => $_SESSION['db']['charset']
);

$DB = new Db($aParams);
