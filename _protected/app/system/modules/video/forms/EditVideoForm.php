<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2020, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Video / Form
 */

namespace PH7;

use PFBC\Element\Button;
use PFBC\Element\Hidden;
use PFBC\Element\Textarea;
use PFBC\Element\Textbox;
use PFBC\Element\Token;
use PFBC\Validation\RegExp;
use PFBC\Validation\Str;
use PH7\Framework\Config\Config;
use PH7\Framework\Mvc\Request\Http as HttpRequest;
use PH7\Framework\Session\Session;
use PH7\Framework\Url\Header;

class EditVideoForm
{
    public static function display()
    {
        if (isset($_POST['submit_edit_video'])) {
            if (\PFBC\Form::isValid($_POST['submit_edit_video'])) {
                new EditVideoFormProcess;
            }

            Header::redirect();
        }

        $sTitlePattern = Config::getInstance()->values['module.setting']['url_title.pattern'];

        $oForm = new \PFBC\Form('form_edit_video');
        $oForm->configure(['action' => '']);
        $oForm->addElement(new Hidden('submit_edit_video', 'form_edit_video'));
        $oForm->addElement(new Token('edit_video'));

        $oHttpRequest = new HttpRequest;
        $oVideo = (new VideoModel)->video(
            (new Session)->get('member_id'),
            $oHttpRequest->get('album_id'),
            $oHttpRequest->get('video_id'),
            1,
            0,
            1
        );
        unset($oHttpRequest);

        $oForm->addElement(
            new Textbox(
                t('Video Name:'),
                'title',
                [
                    'value' => $oVideo->title,
                    'required' => 1,
                    'pattern' => $sTitlePattern,
                    'validation' => new RegExp($sTitlePattern)
                ]
            )
        );
        $oForm->addElement(
            new Textarea(
                t('Video Description:'),
                'description',
                [
                    'value' => $oVideo->description,
                    'validation' => new Str(Form::MIN_STRING_FIELD_LENGTH, Form::MAX_STRING_FIELD_LENGTH)
                ]
            )
        );
        $oForm->addElement(new Button);
        $oForm->render();
    }
}
