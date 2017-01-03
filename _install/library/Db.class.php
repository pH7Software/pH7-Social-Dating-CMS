<?php
/**
 * @title            Helper PDO Database Class
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Install / Library
 */

namespace PH7;
defined('PH7') or die('Restricted access');

class Db extends \PDO
{

    public function __construct(array $aParams)
    {
        $aDriverOptions[\PDO::MYSQL_ATTR_INIT_COMMAND] = 'SET NAMES ' . $aParams['db_charset'];
        parent::__construct("{$aParams['db_type']}:host={$aParams['db_hostname']};dbname={$aParams['db_name']};", $aParams['db_username'], $aParams['db_password'], $aDriverOptions);
        $this->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

}
