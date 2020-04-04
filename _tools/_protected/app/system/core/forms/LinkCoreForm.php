<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Form
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PFBC\Element\Hidden;
use PFBC\Element\Submit;
use PFBC\Element\Token;
use PH7\Framework\Mvc\Request\Http;
use PH7\Framework\Mvc\Router\Uri;

class LinkCoreForm
{
    /**
     * @param string $sLabel Link name of submit form
     * @param string $sModule
     * @param string $sController
     * @param string $sAction
     * @param array $aParams The parameters
     *
     * @return void
     *
     * @throws Framework\File\IOException
     */
    public static function display($sLabel, $sModule, $sController, $sAction, array $aParams)
    {
        $sUrl = self::getFormUrl($sModule, $sController, $sAction);

        $oForm = new \PFBC\Form('form_link');
        $oForm->configure(['action' => $sUrl, 'class' => 'form_link']);
        $oForm->addElement(new Hidden('submit_link', 'form_link'));
        $oForm->addElement(new Token(substr($sUrl, -14, -6))); // Create a name token and generate a random token

        foreach ($aParams as $sKey => $sVal) {
            $oForm->addElement(new Hidden($sKey, $sVal));
        }

        $oForm->addElement(new Submit($sLabel, ['class' => 'form_link']));
        $oForm->render();
    }

    /**
     * @param string $sModule
     * @param string $sController
     * @param string $sAction
     *
     * @return string
     *
     * @throws Framework\File\IOException
     */
    private static function getFormUrl($sModule, $sController, $sAction)
    {
        if (!isset($sModule, $sController, $sAction)) {
            return (new Http)->currentUrl();
        }

        return Uri::get($sModule, $sController, $sAction);
    }
}
