<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        CC-BY License - http://creativecommons.org/licenses/by/3.0/
 * @package        PH7 / Framework / Core
 */

namespace PH7\Framework\Core;

defined('PH7') or exit('Restricted access');

class License
{
    const ACTIVE_STATUS = 'active';
    const INVALID_STATUS = 'invalid';
    const EXPIRED_STATUS = 'expired';
    const SUSPENDED_STATUS = 'suspended';
}