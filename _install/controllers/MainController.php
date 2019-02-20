<?php
/**
 * @title            MainController Class
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Install / Controller
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

class MainController extends Controller
{
    /********************* ERROR 404 *********************/
    public function error_404()
    {
        $this->oView->display('error_404.tpl');
    }
}
