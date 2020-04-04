<?php
/**
 * @title          Login Form Process
 *
 * @author         Pierre-Henry Soria <hi@ph7.me>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License <http://www.gnu.org/licenses/gpl.html>
 * @package        PH7 / App / Module / Fake Admin Panel / Form / Processing
 */

namespace PH7;

class LoginFormProcess extends Form
{
    const BRUTE_FORCE_SLEEP_DELAY = 6;

    public function __construct()
    {
        parent::__construct();

        (new Logger)->init($_POST);

        // Security against brute-force attack and this will irritate hackers
        $this->preventBruteForce(self::BRUTE_FORCE_SLEEP_DELAY);

        $this->session->set('captcha_admin_enabled', 1); // Enable Captcha
        \PFBC\Form::setError('form_login', t('"Email", "Username" or "Password" is Incorrect'));
    }
}
