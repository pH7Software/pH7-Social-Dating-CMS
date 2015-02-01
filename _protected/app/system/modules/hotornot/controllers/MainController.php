<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2015, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / HotOrNot / Controller
 */
namespace PH7;

class MainController extends Controller
{

    private $oHoNModel;

    public function __construct()
    {
        parent::__construct();
        $this->oHoNModel = new HotOrNotModel();
    }

    public function rating()
    {
        /*** JS File Only to Members. For its part, the Rating System will redirect the visitors who are not connected to the registration form. ***/
        if (UserCore::auth())
            $this->design->addJs(PH7_LAYOUT . PH7_SYS . PH7_MOD . $this->registry->module . PH7_SH . PH7_TPL . PH7_TPL_MOD_NAME . PH7_SH . PH7_JS, 'script.js');

        /*** Meta Tags ***/
        /**
         * @internal We can include HTML tags in the title since the template will erase them before display.
         */
        $sMenDesc = t('You men!') . '<br />' . t('Vote for the most beautiful women, the sexiest and hottest online dating free site!');
        $sWomenDesc = t('You women!') . '<br />' .t('Vote for the best men, the sexiest and hottest free online dating site!');

        $this->view->page_title = t('Hot On Not - Free Online Dating Site');
        $this->view->meta_description = $sMenDesc . ' ' . $sWomenDesc;
        $this->view->meta_keywords = t('hot, hot or not, hotornot, sexy, rate, rating, voting, women, free, dating, speed dating, flirt');
        $this->view->desc_for_man = $sMenDesc;
        $this->view->desc_for_woman = $sWomenDesc;

        /*** Display ***/
        // If the user is connected, we do not display its own avatar since this user can not vote for himself.
        $iProfileId = (UserCore::auth()) ? $this->session->get('member_id') : null;
        $oData = $this->oHoNModel->getPicture($iProfileId);

        if (empty($oData))
        {
            Framework\Http\Http::setHeadersByCode(404);
            $this->view->error = t('Sorry, We did not find any photo to Hot Or Not Party.');
        }
        else
        {
            $this->view->avatarDesign = new AvatarDesignCore; // Avatar Design Class
            $this->view->data = $oData;
        }

        $this->output();
    }

}
