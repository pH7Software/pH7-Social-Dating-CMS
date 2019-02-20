<?php
/**
 * @title            Loader File
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Inc
 * @version          1.0
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

// Autoloading Class Files
spl_autoload_register(function ($sClass) {
    // Hack to remove namespace and backslash
    $sClass = str_replace(array(__NAMESPACE__ . '\\', '\\'), '/', $sClass);

    if (is_file(PH7_ROOT_INSTALL . 'library/' . $sClass . '.class.php'))
        require_once PH7_ROOT_INSTALL . 'library/' . $sClass . '.class.php';

    if (is_file(PH7_ROOT_INSTALL . 'controllers/' . $sClass . '.php'))
        require_once PH7_ROOT_INSTALL . 'controllers/' . $sClass . '.php';
});
