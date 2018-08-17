<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Report / Form
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Mvc\Request\Http;
use PH7\Framework\Url\Header as HeaderUrl;

class ReportForm
{
    public static function display()
    {
        $oHttpRequest = new Http;

        if ($oHttpRequest->postExists('submit_report')) {
            if (\PFBC\Form::isValid($oHttpRequest->post('submit_report'))) {
                new ReportFormProcess();
            }

            HeaderUrl::redirect();
        }

        $oForm = new \PFBC\Form('form_report');
        $oForm->configure(['action' => $oHttpRequest->currentUrl()]);
        $oForm->addElement(new \PFBC\Element\Hidden('submit_report', 'form_report'));
        $oForm->addElement(new \PFBC\Element\Token('report'));
        $oForm->addElement(new \PFBC\Element\Hidden('spammer', $oHttpRequest->get('spammer'), ['required' => 1]));
        $oForm->addElement(new \PFBC\Element\Hidden('url', $oHttpRequest->get('url'), ['validation' => new \PFBC\Validation\Url]));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<h3 class="center">' . t('Do your want to report this?') . '</h4>'));
        $oForm->addElement(new \PFBC\Element\Select(t('Type the Content'), 'type', ['user' => t('Profile'), 'avatar' => t('Avatar'), 'mail' => t('Message'), 'comment' => t('Comment'), 'picture' => t('Photo'), 'video' => t('Video'), 'forum' => t('Forum'), 'note' => t('Note')], ['value' => $oHttpRequest->get('type'), 'required' => 1]));
        $oForm->addElement(new \PFBC\Element\Textarea(t('Comment:'), 'desc', ['title' => t('Please tell us why you want to report this content (scam, illegal content, adult content, etc.). Help us to eliminate scams, fake profiles, spam ... Thank you'), 'required' => 1]));
        $oForm->addElement(new \PFBC\Element\Button(t('Report It'), 'submit', ['icon' => 'check']));
        $oForm->addElement(new \PFBC\Element\Button(t('Cancel'), 'cancel', ['onclick' => 'parent.$.colorbox.close();return false', 'icon' => 'cancel']));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<script src="' . PH7_URL_STATIC . PH7_JS . 'str.js"></script>'));
        $oForm->render();
    }
}
