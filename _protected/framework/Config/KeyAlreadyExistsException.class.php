<?php
/**
 * @title            Config Class
 * @desc             Loading and management config files.
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2017-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Config
 */

namespace PH7\Framework\Config;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Error\CException\PH7InvalidArgumentException;

class KeyAlreadyExistsException extends PH7InvalidArgumentException
{
}
