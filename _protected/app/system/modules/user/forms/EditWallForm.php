<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Module / User / Form
 */

namespace PH7;

use PFBC\Element\Button;
use PFBC\Element\Hidden;
use PFBC\Element\Textarea;
use PFBC\Element\Token;
use PFBC\Validation\Str;
use PH7\Framework\Layout\Html\Design;
use PH7\Framework\Mvc\Request\Http;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Session\Session;
use PH7\Framework\Url\Header;

class EditWallForm
{
    /*
     * This class is still under development, if you are a developer and you want to contribute,
     * Feel free to fork pH7Builder repo https://github.com/pH7Software/pH7-Social-Dating-CMS and open a PR with your changes.
     */
    public static function display()
    {
        if (isset($_POST['submit_edit_wall'])) {
            if (\PFBC\Form::isValid($_POST['submit_edit_wall'])) {
                new EditWallFormProcess();
            }

            Header::redirect();
        }

        $iWallId = (int)(new Http())->get('wall_id');
        $aWallData = (new WallModel())->get((new Session())->get('member_id'), $iWallId, 0, 1);
        $oWallData = $aWallData[0] ?? null;
        if ($oWallData === null) {
            Header::redirect(
                Uri::get('user', 'main', 'index'),
                t('The wall post does not exist.'),
                Design::ERROR_TYPE
            );
        }

        $oForm = new \PFBC\Form('form_edit_wall');
        $oForm->configure(['action' => '']);
        $oForm->addElement(new Hidden('submit_edit_wall', 'form_edit_wall'));
        $oForm->addElement(new Hidden('wall_id', $iWallId));
        $oForm->addElement(new Token('edit_wall'));
        $oForm->addElement(new Textarea(t('Content:'), 'post', ['value' => $oWallData->post, 'validation' => new Str(1, 900)]));
        $oForm->addElement(new Button());
        $oForm->render();
    }
}
