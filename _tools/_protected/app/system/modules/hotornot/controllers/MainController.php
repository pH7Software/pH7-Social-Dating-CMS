<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / HotOrNot / Controller
 */

namespace PH7;

use PH7\Framework\Http\Http;
use Teapot\StatusCode;

class MainController extends Controller
{
    /** @var HotOrNotModel */
    private $oHoNModel;

    public function __construct()
    {
        parent::__construct();

        $this->oHoNModel = new HotOrNotModel();
    }

    public function rating()
    {
        /**
         * Add JS file only for Members.
         * Otherwise, the Rating System will redirect visitors who aren't logged in to the registration form.
         */
        if (UserCore::auth()) {
            $this->addJsFile();
        }

        /*** Meta Tags ***/
        /**
         * @internal We can include HTML tags in the title since the template will erase them before display.
         */
        $sMenDesc = t('You Men!') . '<br />' . t('Vote for the most beautiful women, the sexiest and hottest!');
        $sWomenDesc = t('You Women!') . '<br />' . t('Vote for the best men, the sexiest and hottest!');

        $this->view->page_title = t('Hot On Not - Free Online Dating Site');
        $this->view->meta_description = $sMenDesc . ' ' . $sWomenDesc;
        $this->view->meta_keywords = t('hot, hot or not, hotornot, sexy, rate, rating, voting, women, free, dating, speed dating, flirt');
        $this->view->desc_for_man = $sMenDesc;
        $this->view->desc_for_woman = $sWomenDesc;

        // If the user is logged in, we do not display its own avatar since the user cannot vote for himself.
        $iProfileId = UserCore::auth() ? $this->session->get('member_id') : null;
        $oData = $this->oHoNModel->getPicture($iProfileId);

        if (empty($oData)) {
            Http::setHeadersByCode(StatusCode::NOT_FOUND);
            $this->view->error = t("Sorry, we haven't found any photos for the HotOrNot Party :(");
        } else {
            $this->view->avatarDesign = new AvatarDesignCore; // Avatar Design Class
            $this->view->data = $oData;
        }

        $this->output();
    }

    private function addJsFile()
    {
        $this->design->addJs(
            PH7_LAYOUT . PH7_SYS . PH7_MOD . $this->registry->module . PH7_SH . PH7_TPL . PH7_TPL_MOD_NAME . PH7_SH . PH7_JS,
            'script.js'
        );
    }
}
