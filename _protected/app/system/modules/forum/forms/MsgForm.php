<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Forum / Form
 */

namespace PH7;

use PFBC\Element\Button;
use PFBC\Element\CCaptcha;
use PFBC\Element\Hidden;
use PFBC\Element\HTMLExternal;
use PFBC\Element\Select;
use PFBC\Element\Textbox;
use PFBC\Element\Token;
use PFBC\Validation\RegExp;
use PFBC\Validation\Str;
use PH7\Framework\Config\Config;
use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Mvc\Request\Http;
use PH7\Framework\Url\Header;

class MsgForm
{
    public static function display()
    {
        if (isset($_POST['submit_msg'])) {
            if (\PFBC\Form::isValid($_POST['submit_msg'])) {
                new MsgFormProcess();
            }

            Header::redirect();
        }

        $aForumsName = [];
        $oForums = (new ForumModel)->getForum();
        foreach ($oForums as $oForum) {
            $aForumsName[$oForum->forumId] = $oForum->name;
        }
        unset($oForums);

        $sTitlePattern = Config::getInstance()->values['module.setting']['url_title.pattern'];

        $oForm = new \PFBC\Form('form_msg');
        $oForm->configure(['action' => '']);
        $oForm->addElement(new Hidden('submit_msg', 'form_msg'));
        $oForm->addElement(new Token('msg'));
        $oForm->addElement(
            new Select(
                t('Forum:'),
                'forum',
                $aForumsName,
                [
                    'value' => (new Http)->get('forum_id')
                ]
            )
        );
        $oForm->addElement(
            new Textbox(
                t('Subject:'),
                'title',
                [
                    'id' => 'str_title',
                    'onblur' => 'CValid(this.value,this.id,2,60)',
                    'pattern' => $sTitlePattern,
                    'required' => 1,
                    'validation' => new RegExp($sTitlePattern)
                ]
            )
        );
        $oForm->addElement(new HTMLExternal('<span class="input_error str_title"></span>'));

        $sEditorClass = FormHelper::getEditorPfbcClassName();
        $oForm->addElement(
            new $sEditorClass(
                t('Message:'),
                'message',
                [
                    'required' => 1,
                    'validation' => new Str(4)
                ]
            )
        );

        if (DbConfig::getSetting('isCaptchaForum')) {
            $oForm->addElement(
                new CCaptcha(
                    t('Captcha'),
                    'captcha',
                    [
                        'id' => 'ccaptcha',
                        'onkeyup' => 'CValid(this.value, this.id)',
                        'description' => t('Enter the below code:')
                    ]
                )
            );
            $oForm->addElement(new HTMLExternal('<span class="input_error ccaptcha"></span>'));
        }

        $oForm->addElement(new Button);
        $oForm->addElement(new HTMLExternal('<script src="' . PH7_URL_STATIC . PH7_JS . 'validate.js"></script>'));
        $oForm->render();
    }
}
