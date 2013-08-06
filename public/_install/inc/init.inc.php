<?php
/**
 * @title            Init Controller File
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Inc
 * @version          1.2
 */

defined('PH7') or exit('Restricted access');

$sController = ucfirst((!empty($_GET['c']) ? $_GET['c'] : 'install')) . 'Controller';
$sAction = (!empty($_GET['a'])) ? $_GET['a'] : 'index';

if (is_file(PH7_ROOT_PUBLIC . '_constants.php') && ($sAction == 'index' || $sAction == 'license' || $sAction == 'config_path'))
    exit('Your site is already installed.<br />If you want to redo a clean install, please delete your "_constants.php" file and delete all the content of your database.');

try
{
    if (is_file(PH7_ROOT_INSTALL . 'controllers/' . $sController . '.php'))
    {
        $sController = 'PH7\\' . $sController;
        $oCtrl = new $sController;

        if (method_exists($oCtrl, $sAction))
            call_user_func(array($oCtrl, $sAction));
        else
            (new PH7\MainController)->error_404();
    }
    else
        (new PH7\MainController)->error_404();

}
catch (Exception $oE)
{
    echo $oE->getMessage();
}
