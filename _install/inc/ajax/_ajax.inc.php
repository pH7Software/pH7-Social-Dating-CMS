<?php
/**
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Inc / Ajax
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

require_once dirname(dirname(__DIR__)) . '/constants.php';
require_once PH7_ROOT_INSTALL . 'inc/fns/misc.php';
require_once PH7_ROOT_INSTALL . 'library/Controllable.class.php';
require_once PH7_ROOT_INSTALL . 'library/Controller.class.php';
require_once PH7_ROOT_INSTALL . 'library/Language.class.php';
include_once PH7_ROOT_INSTALL . Language::LANG_FOLDER_NAME . (new Language)->get() . PH7_DS . Language::LANG_FILENAME;
