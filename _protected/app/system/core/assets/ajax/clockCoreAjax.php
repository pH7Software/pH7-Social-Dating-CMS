<?php
/**
 * @title          Clock Core Ajax
 * @desc           Displays fortune in real time using Ajax and PHP
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2016, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Asset / Ajax
 * @version        1.0
 */
namespace PH7;
defined('PH7') or exit('Restricted access');

use PH7\Framework\Date\CDateTime;

echo (new CDateTime)->get()->time();
