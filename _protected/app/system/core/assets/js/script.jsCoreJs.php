<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / App / System / Core / Asset / Js
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

echo (new Framework\Mvc\Model\Design)->customCode('js');
