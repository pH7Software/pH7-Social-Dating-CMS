<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / User / Form
 */
namespace PH7;

use PH7\Framework\Mvc\Request\Http;
use PH7\Framework\Session\Session;

class EditWallForm
{

/*
 * This class is still under development, if you are a developer and you want to help us and join our volunteer team of developers to continue development of this module, you are welcome!
 * Please contact us by email: ph7software@gmail.com
 *
 * Thank you,
 * The developers team.
 */

    public static function display()
    {
        if (isset($_POST['submit_edit_wall']))
        {
            if (\PFBC\Form::isValid($_POST['submit_edit_wall']))
                new EditWallFormProcess();

            Framework\Url\Header::redirect();
        }

        $oWallData = (new WallModel)->get((new Session)->get('member_id'), (new Http)->get('wall_id'), 0, 1);

        $oForm = new \PFBC\Form('form_edit_wall');
        $oForm->configure(array('action' => '' ));
        $oForm->addElement(new \PFBC\Element\Hidden('submit_edit_wall', 'form_edit_wall'));
        $oForm->addElement(new \PFBC\Element\Token('edit_wall'));
        $oForm->addElement(new \PFBC\Element\Textarea(t('Content:'), 'post', array('value'=>$oWallData->post, 'validation'=>new \PFBC\Validation\Str(1,900))));
        $oForm->addElement(new \PFBC\Element\Button);
        $oForm->render();
    }

}
