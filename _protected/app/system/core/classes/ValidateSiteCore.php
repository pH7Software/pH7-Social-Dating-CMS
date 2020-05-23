<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2015-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Class
 */

namespace PH7;

use PH7\Framework\Date\Various as VDate;
use PH7\Framework\Layout\Html\Design;
use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Session\Session;
use PH7\Framework\Url\Header;

class ValidateSiteCore
{
    const SESS_IS_VISITED = 'suggestionbox_visited';
    const VALIDATE_FORM_PAGE_DELAY = '2 months';

    const SUGGESTION_BOX_CSS_FILENAME = 'suggestionbox.css';
    const SUGGESTION_BOX_JS_FILENAME = 'suggestionbox.js';

    /**
     * Add more "true" or "false" to give
     * more/less probability of showing up the dialog.
     */
    const STATUS = [
        true,
        true,
        true,
        false
    ];

    const VALIDATE_FORM_POPUP_DELAYS = [
        '4 hours',
        '1 day',
        '2 days',
        '3 days',
        '5 days',
        '8 days',
        '13 days',
        '21 days'
    ];

    /** @var Session */
    private $oSession;

    /** @var ValidateSiteCoreModel */
    private $oValidateSiteModel;

    /** @var int */
    private $iSiteCreationDate;

    public function __construct(Session $oSession)
    {
        $this->oSession = $oSession;
        $this->oValidateSiteModel = new ValidateSiteCoreModel;
        $this->iSiteCreationDate = VDate::getTime(StatisticCoreModel::getDateOfCreation());
    }

    /**
     * Check if the JS donation box has to be added and redirect if the site hasn't been validated yet for a while.
     *
     * @return bool
     */
    public function needToInject()
    {
        if (self::STATUS[array_rand(self::STATUS)] === false) {
            return false;
        }

        if ($this->shouldBeRedirected()) {
            $this->redirectToDonationBox();
        }

        return $this->shouldSeeDialog();
    }

    public function injectAssetSuggestionBoxFiles(Design $oDesign)
    {
        $oDesign->addCss(
            PH7_LAYOUT . PH7_SYS . PH7_MOD . 'ph7cms-helper' . PH7_SH . PH7_TPL . PH7_TPL_MOD_NAME . PH7_SH . PH7_CSS,
            self::SUGGESTION_BOX_CSS_FILENAME
        );
        $oDesign->addJs(
            PH7_LAYOUT . PH7_SYS . PH7_MOD . 'ph7cms-helper' . PH7_SH . PH7_TPL . PH7_TPL_MOD_NAME . PH7_SH . PH7_JS,
            self::SUGGESTION_BOX_JS_FILENAME
        );
    }

    /**
     * After over 2 months, if the site is still not validated, maybe the validation box doesn't really work...,
     * so we redirect directly to the page form.
     *
     * @return bool
     */
    private function shouldBeRedirected()
    {
        return $this->isNotValidated() && $this->hasPageNotBeenSeenYet() &&
            ($this->isSoftwareNoticeHidden() || $this->removeTime(self::VALIDATE_FORM_PAGE_DELAY) >= $this->iSiteCreationDate);
    }

    /**
     * @return bool
     */
    private function shouldSeeDialog()
    {
        $sTime = self::VALIDATE_FORM_POPUP_DELAYS[array_rand(self::VALIDATE_FORM_POPUP_DELAYS)];

        return !$this->oValidateSiteModel->is() && $this->removeTime($sTime) >= $this->iSiteCreationDate;
    }

    private function redirectToDonationBox()
    {
        $aBoxes = ['donationbox', 'upsetbox'];

        Header::redirect(
            Uri::get(
                'ph7cms-helper',
                'main',
                'suggestionbox',
                '?box=' . $aBoxes[array_rand($aBoxes)]
            )
        );
    }

    /**
     * @return bool
     */
    private function hasPageNotBeenSeenYet()
    {
        return !$this->oSession->exists(self::SESS_IS_VISITED);
    }

    /**
     * @return bool
     */
    private function isSoftwareNoticeHidden()
    {
        return !(bool)DbConfig::getSetting('displayPoweredByLink');
    }

    /**
     * @return bool
     */
    private function isNotValidated()
    {
        return !$this->oValidateSiteModel->is();
    }

    /**
     * @param string $sTime
     *
     * @return int The changed timestamp.
     */
    private function removeTime($sTime)
    {
        return VDate::setTime('-' . $sTime);
    }
}
