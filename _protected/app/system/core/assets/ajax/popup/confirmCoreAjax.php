<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Asset / Ajax / Popup
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Layout\Html\Design;
use PH7\Framework\Mvc\Request\Http;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Header;
use PH7\Framework\Url\Url;

if (AdminCore::auth() || UserCore::auth() || AffiliateCore::auth()) {
    $oHttpRequest = new Http;
    $oDesign = new Design;
    $oDesign->htmlHeader();
    $oDesign->usefulHtmlHeader();
    echo '<div class="center">';

    if ($oHttpRequest->getExists(['mod', 'ctrl', 'act', 'id'])) {
        $sLabel = $oHttpRequest->get('label');
        $sMod = $oHttpRequest->get('mod');
        $sCtrl = $oHttpRequest->get('ctrl');
        $sAct = $oHttpRequest->get('act');
        $mId = $oHttpRequest->get('id');

        ConfirmCoreForm::display(
            [
                'label' => Url::decode($sLabel),
                'module' => $sMod,
                'controller' => $sCtrl,
                'action' => $sAct,
                'id' => $mId
            ]
        );
    } else {
        echo '<p>' . t('Wrong parameters in the URL!') . '</p>';
    }

    echo '</div>';
    $oDesign->htmlFooter();
    unset($oHttpRequest, $oDesign);
} else {
    Header::redirect(
        Uri::get('user',
            'signup',
            'step1'),
        t('You must be registered to report an abuse.')
    );
}
