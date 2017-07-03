<?php
/**
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Inc / Ajax
 */

namespace PH7;
define('PH7', 1);

require '_ajax.inc.php';

if (!empty($_POST['lic'])) {
    if (check_license($_POST['lic']))
        echo '<p class="success">' . $LANG['success_license'] . '</p>';
    else
        echo '<p class="error">' . $LANG['failure_license'] . '</p>';
}
