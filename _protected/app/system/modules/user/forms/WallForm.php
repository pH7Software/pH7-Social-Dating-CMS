<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / User / Form
 */
namespace PH7;

use PH7\Framework\Session\Session, PH7\Framework\Mvc\Request\Http;

class WallForm
{

/*
 * This class is still under development, if you are a developer and you want to help us and join our volunteer team of developers to continue development of this class, you are welcome!
 * Please contact us by email: ph7software@gmail.com
 *
 * Thank you,
 * The developers team (Pierre-Henry Soria).
 */

   public static function display()
   {
        if (isset($_POST['submit_wall']))
        {
            if (\PFBC\Form::isValid($_POST['submit_wall']))
                new WallFormProcess();

            Framework\Url\Header::redirect();
        }

        $oForm = new \PFBC\Form('form_wall', 500);
        $oForm->configure(array('action' => '' ));
        $oForm->addElement(new \PFBC\Element\Hidden('submit_wall', 'form_wall'));
        $oForm->addElement(new \PFBC\Element\Token('wall'));
        $oForm->addElement(new \PFBC\Element\Textarea(t('Content:'), 'post', array('validation'=>new \PFBC\Validation\Str(1,900))));
        $oForm->addElement(new \PFBC\Element\Button);
        $oForm->render();
    }

}
