<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / User / Form
 */

namespace PH7;

use PH7\Framework\Mvc\Request\Http;
use PH7\Framework\Session\Session;
use PH7\Framework\Url\Header;

class EditWallForm
{
    /*
     * This class is still under development, if you are a developer and you want to contribute,
     * Feel free to fork pH7CMS repo https://github.com/pH7Software/pH7-Social-Dating-CMS and open a PR with your changes.
     */
    public static function display()
    {
        if (isset($_POST['submit_edit_wall'])) {
            if (\PFBC\Form::isValid($_POST['submit_edit_wall'])) {
                new EditWallFormProcess();
            }

            Header::redirect();
        }

        $oWallData = (new WallModel)->get((new Session)->get('member_id'), (new Http)->get('wall_id'), 0, 1);

        $oForm = new \PFBC\Form('form_edit_wall');
        $oForm->configure(['action' => '']);
        $oForm->addElement(new \PFBC\Element\Hidden('submit_edit_wall', 'form_edit_wall'));
        $oForm->addElement(new \PFBC\Element\Token('edit_wall'));
        $oForm->addElement(new \PFBC\Element\Textarea(t('Content:'), 'post', ['value' => $oWallData->post, 'validation' => new \PFBC\Validation\Str(1, 900)]));
        $oForm->addElement(new \PFBC\Element\Button);
        $oForm->render();
    }
}
