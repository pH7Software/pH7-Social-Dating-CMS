<?php
/**
 * @title          Login Form Process
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2016, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License <http://www.gnu.org/licenses/gpl.html>
 * @package        PH7 / App / Module / Fake Admin Panel / Form / Processing
 * @version        1.1.0
 */

namespace PH7;

class LoginFormProcess extends Form
{

    public function __construct()
    {
        parent::__construct();

        (new Logger)->init($_POST);

        sleep(6); // Security against brute-force attack and this will irritate the hacker
        $this->session->set('captcha_admin_enabled', 1); // Enable Captcha
        \PFBC\Form::setError('form_login', t('"Email", "Username" or "Password" is Incorrect'));
    }

}
