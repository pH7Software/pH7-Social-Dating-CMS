<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2016, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / User / Controller
 */
namespace PH7;

use PH7\Framework\Mvc\Router\Uri, PH7\Framework\Url\Header;

class AccountController extends Controller
{

    private $sTitle;

    public function index()
    {
        Header::redirect(Uri::get('user', 'main', 'index'));
    }

    public function activate($sMail, $sHash)
    {
        (new UserCore)->activateAccount($sMail, $sHash, $this->config, $this->registry);
    }

}
