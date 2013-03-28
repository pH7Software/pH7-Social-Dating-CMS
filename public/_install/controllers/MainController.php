<?php
/**
 * @title            MainController Class
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Install / Controller
 * @version          1.0
 */

namespace PH7;
defined('PH7') or exit('Restricted access');

class MainController extends Controller
{

    /********************* ERROR 404 *********************/
    public function error_404 ()
    {
        $this->view->display('error_404.tpl');
    }

}
