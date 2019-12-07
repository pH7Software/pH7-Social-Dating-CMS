<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Friend / Controller
 */

namespace PH7;

use PH7\Framework\Layout\Html\Meta;
use PH7\Framework\Navigation\Page;
use PH7\Framework\Security\CSRF\Token;

class MainController extends Controller
{
    const MAX_FRIEND_PER_PAGE = 10;
    const MAX_MUTUAL_FRIEND_PER_PAGE = 10;

    /** @var UserCoreModel */
    private $oUserModel;

    /** @var FriendModel */
    private $oFriendModel;

    /** @var Page */
    private $oPage;

    /** @var string */
    private $sUsername;

    /** @var string */
    private $sTitle;

    /** @var bool|int */
    private $iId;

    /** @var string */
    private $iMemberId;

    /** @var int */
    private $iTotalFriends;

    public function __construct()
    {
        parent::__construct();

        $this->oUserModel = new UserCoreModel;
        $this->oFriendModel = new FriendModel;
        $this->oPage = new Page;

        /**
         *  Adding JavaScript file for Ajax friend.
         */
        $this->design->addJs(
            PH7_LAYOUT . PH7_SYS . PH7_MOD . $this->registry->module . PH7_SH . PH7_TPL . PH7_TPL_MOD_NAME . PH7_SH . PH7_JS,
            'friend.js'
        );

        $this->iMemberId = $this->session->get('member_id');
        $this->sUsername = $this->getUsername();

        /**
         * FIRST USERNAME LETTER IN UPPERCASE
         * We can do this because the SQL search is case insensitive.
         * Be careful not to do this if you need this username in the method \PH7\Framework\Layout\Html::getUserAvatar()
         * since it won't find the user folder because it is case-sensitive.
         */
        $this->sUsername = $this->str->upperFirst($this->sUsername);

        $this->iId = $this->oUserModel->getId(null, $this->sUsername);
        $this->view->username = $this->sUsername;
        $this->view->sess_member_id = $this->iMemberId;
        $this->view->member_id = $this->iId;
        $this->view->csrf_token = (new Token)->generate('friend');
        $this->view->avatarDesign = new AvatarDesignCore;
        $this->view->header = Meta::NOINDEX;

        /**
         *  Predefined meta_description.
         */
        $this->view->meta_description = t("%0%'s friends. Meet new people and make new friends, sex friends, hot friends for Flirt, Speed Dating or social relationship with %site_name%", $this->sUsername);

        /**
         *  Predefined meta_keywords tags.
         */
        $this->view->meta_keywords = t('friend,friends,girl friend,boy friend,sex friend,hot friend,new friend,friendship,dating,flirt,%0%', $this->sUsername);
    }

    public function index()
    {
        $sKeywords = $this->httpRequest->get('looking');
        $sOrder = $this->httpRequest->get('order');
        $iSortBy = $this->httpRequest->get('sort', 'int');

        $this->iTotalFriends = $this->oFriendModel->get(
            $this->iId,
            null,
            $sKeywords,
            true,
            $sOrder,
            $iSortBy,
            null,
            null
        );
        $this->view->total_pages = $this->oPage->getTotalPages(
            $this->iTotalFriends,
            self::MAX_FRIEND_PER_PAGE
        );

        $this->view->current_page = $this->oPage->getCurrentPage();

        $oFriend = $this->oFriendModel->get(
            $this->iId,
            null,
            $sKeywords,
            false,
            $sOrder,
            $iSortBy,
            $this->oPage->getFirstItem(),
            $this->oPage->getNbItemsPerPage()
        );

        if (empty($oFriend)) {
            $this->sTitle = t("No Friend found on %0%'s profile", $this->sUsername);
            $this->view->page_title = $this->sTitle;
            $this->view->h2_title = $this->sTitle;
            $this->view->error = t('No friends found.');
        } else {
            $this->sTitle = t("%0%'s Friends:", $this->sUsername);
            $this->view->page_title = $this->sTitle;
            $this->view->h2_title = $this->sTitle;
            $this->view->friend_number = nt('%n% Friend', '%n% Friends', $this->iTotalFriends);
            $this->view->friends = $oFriend;
        }

        $this->view->action = '';
        $this->manualTplInclude('index.tpl');

        $this->output();
    }

    public function mutual()
    {
        $sKeywords = $this->httpRequest->get('looking');
        $sOrder = $this->httpRequest->get('order');
        $iSortBy = $this->httpRequest->get('sort', 'int');

        $this->iTotalFriends = $this->oFriendModel->get(
            $this->iMemberId,
            $this->iId,
            $sKeywords,
            true,
            $sOrder,
            $iSortBy,
            null,
            null
        );
        $this->view->total_pages = $this->oPage->getTotalPages(
            $this->iTotalFriends, self::MAX_MUTUAL_FRIEND_PER_PAGE
        );
        $this->view->current_page = $this->oPage->getCurrentPage();

        $oFriend = $this->oFriendModel->get(
            $this->iMemberId,
            $this->iId,
            $sKeywords,
            false,
            $sOrder,
            $iSortBy,
            $this->oPage->getFirstItem(),
            $this->oPage->getNbItemsPerPage()
        );

        if (empty($oFriend)) {
            $this->sTitle = t("No Mutual Friend found on %0%'s profile", $this->sUsername);
            $this->view->page_title = $this->sTitle;
            $this->view->h2_title = $this->sTitle;
            $this->view->error = t('No mutual friends found.');
        } else {
            $this->sTitle = t("%0%'s Mutual Friends:", $this->sUsername);
            $this->view->page_title = $this->sTitle;
            $this->view->h2_title = $this->sTitle;
            $this->view->friend_number = nt('%n% Mutual Friend', '%n% Mutual Friends', $this->iTotalFriends);
            $this->view->friends = $oFriend;
        }

        $this->view->action = 'mutual';
        $this->manualTplInclude('index.tpl');
        $this->output();
    }

    public function search()
    {
        $this->sTitle = t("Search %0%'s friends", $this->sUsername);
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;

        $this->output();
    }

    /**
     * If the user is logged in, get 'member_username' session, otherwise get username from URL.
     *
     * @return string
     */
    private function getUsername()
    {
        if (!$this->httpRequest->getExists('username')) {
            return $this->session->get('member_username');
        }

        return $this->httpRequest->get('username');
    }
}
