<?php
/**
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / App / System / Core / Asset / File
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Mvc\Request\Http;
use PH7\Framework\Security\Spam\Captcha\Captcha;

(new Captcha)->show( (new Http)->getExists('r') );
