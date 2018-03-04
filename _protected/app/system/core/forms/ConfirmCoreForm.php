<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Form
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Mvc\Router\Uri;

class ConfirmCoreForm
{
    /**
     * @param array $aParam The parameters
     *
     * @return void
     */
    public static function display(array $aParam)
    {
        $sUrl = Uri::get($aParam['module'], $aParam['controller'], $aParam['action']);

        $oForm = new \PFBC\Form('form_confirm');
        $oForm->configure(['action' => $sUrl]);
        $oForm->addElement(new \PFBC\Element\Hidden('submit_confirm', 'form_confirm'));
        $oForm->addElement(new \PFBC\Element\Token(substr($sUrl, -14, -6))); // Create a name token and generate a random token
        $oForm->addElement(new \PFBC\Element\Hidden('id', $aParam['id']));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<h2>' . t('Are you sure you want to do this?') . '</h2>'));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<p class="err_msg s_marg">' . t('Warning, this action is irreversible!') . '</p>'));
        $oForm->addElement(new \PFBC\Element\Button($aParam['label'], 'submit'));
        /**
         * Bug Ajax jQuery -> https://github.com/jquery/jquery-mobile/issues/3202
         * $oForm->addElement(new \PFBC\Element\Button($aParam['label'], 'submit', ['formaction'=>$sUrl]));
         */
        $oForm->addElement(new \PFBC\Element\Button(t('Cancel'), 'cancel', ['onclick' => '$("form").attr("action", "");parent.$.colorbox.close();return false', 'icon' => 'cancel']));
        $oForm->render();
    }
}
