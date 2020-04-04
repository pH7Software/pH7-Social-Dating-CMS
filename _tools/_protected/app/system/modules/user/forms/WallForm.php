<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / User / Form
 */

namespace PH7;

use PFBC\Element\Button;
use PFBC\Element\Hidden;
use PFBC\Element\Textarea;
use PFBC\Element\Token;
use PFBC\Validation\Str;
use PH7\Framework\Url\Header;

class WallForm
{
    /*
     * This class is still under development, if you are a developer and you want to contribute,
     * Feel free to fork pH7CMS repo https://github.com/pH7Software/pH7-Social-Dating-CMS and open a PR with your changes.
     */
    public static function display()
    {
        if (isset($_POST['submit_wall'])) {
            if (\PFBC\Form::isValid($_POST['submit_wall'])) {
                new WallFormProcess();
            }

            Header::redirect();
        }

        $oForm = new \PFBC\Form('form_wall', 500);
        $oForm->configure(['action' => '']);
        $oForm->addElement(new Hidden('submit_wall', 'form_wall'));
        $oForm->addElement(new Token('wall'));
        $oForm->addElement(new Textarea(t('Content:'), 'post', ['validation' => new Str(1, 900)]));
        $oForm->addElement(new Button);
        $oForm->render();
    }
}
