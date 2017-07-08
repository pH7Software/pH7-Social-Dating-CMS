<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Friend / Asset / Ajax
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Http\Http;
use PH7\Framework\Mail\Mail;
use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Security\CSRF\Token;

class FriendAjax extends Core
{
    /** @var FriendModel */
    private $_oFriendModel;

    /** @var string */
    private $_sMsg;

    /**
     * @var boolean|string $mStatus
     */
    private $_mStatus;

    public function __construct()
    {
        parent::__construct();

        if (!(new Token)->check('friend')) {
            exit(jsonMsg(0, Form::errorTokenMsg()));
        }

        $this->_oFriendModel = new FriendModel;

        switch ($this->httpRequest->post('type')) {
            case 'add':
                $this->add();
                break;

            case 'approval';
                $this->approval();
                break;

            case 'delete':
                $this->delete();
                break;

            default:
                Http::setHeadersByCode(400);
                exit('Bad Request Error!');
        }
    }

    protected function add()
    {
        $iFriendId = $this->httpRequest->post('friendId', 'int');
        $iMemberId = $this->session->get('member_id');

        if ($iMemberId == $iFriendId) {
            $this->_sMsg = jsonMsg(0, t('You cannot be your own friend.'));
        } else {
            $this->_mStatus = $this->_oFriendModel->add(
                $this->session->get('member_id'),
                $iFriendId,
                $this->dateTime->get()->dateTime('Y-m-d H:i:s')
            );

            if ($this->_mStatus == 'error') {
                $this->_sMsg = jsonMsg(0, t('Unable to add to friends list. Please try later.'));
            } elseif ($this->_mStatus == 'friend_exists') {
                $this->_sMsg = jsonMsg(0, t('This profile already exists in your friends list.'));
            } elseif ($this->_mStatus == 'id_does_not_exist') {
                $this->_sMsg = jsonMsg(0, t('Profile ID does not exist.')); // Should never happen unless someone changes the source code with firebug or other
            } elseif ($this->_mStatus == 'success') {
                $this->_sMsg = jsonMsg(1, t('Profile successfully added to your friends list.'));

                $oUserModel = new UserCoreModel;
                if (!$oUserModel->isNotification($iFriendId, 'friendRequest')
                    && !$oUserModel->isOnline($iFriendId)
                ) {
                    // Send email if the notification is accepted and if the user isn't online
                    $this->sendMail($iFriendId, $oUserModel);
                }
                unset($oUserModel);
            }
        }

        echo $this->_sMsg;
    }

    protected function approval()
    {
        $this->_mStatus = $this->_oFriendModel->approval(
            $this->session->get('member_id'),
            $this->httpRequest->post('friendId')
        );

        if (!$this->_mStatus) {
            $this->_sMsg = jsonMsg(0, t('Cannot approve the friend. Please try later.'));
        } else {
            $this->_sMsg = jsonMsg(1, t('The friend has been approved.'));
        }

        echo $this->_sMsg;
    }

    protected function delete()
    {
        $this->_mStatus = $this->_oFriendModel->delete(
            $this->session->get('member_id'),
            $this->httpRequest->post('friendId')
        );

        if (!$this->_mStatus) {
            $this->_sMsg = jsonMsg(0, t('Cannot remove the friend. Please try later.'));
        } else {
            $this->_sMsg = jsonMsg(1, t('The friend has been removed.'));
        }

        echo $this->_sMsg;
    }

    /**
     * Send an email to warn the friend request.
     *
     * @param int $iId friend ID
     * @param UserCoreModel $oUserModel
     */
    protected function sendMail($iId, UserCoreModel $oUserModel)
    {
        $sFriendEmail = $oUserModel->getEmail($iId);
        $sFriendUsername = $oUserModel->getUsername($iId);

        /**
         * Note: The predefined variables as %site_name% does not work here,
         * because we are in an ajax script that is called before the definition of these variables.
         */

        /**
         * Get the site name, because we do not have access to predefined variables.
         */
        $sSiteName = DbConfig::getSetting('siteName');

        $this->view->content = t('Hello %0%!', $sFriendUsername) . '<br />' .
            t('<strong>%0%</strong> sent you a friendship request on %1%.', $this->session->get('member_username'), $sSiteName) . '<br />' .
            t('<a href="%0%">Click here</a> to see your friend request.', Uri::get('friend', 'main', 'index'));

        /* Because we work in Ajax, the constant "PH7_TPL_NAME" is not yet defined.
         * So we use the constant "PH7_DEFAULT_THEME" is already defined.
         */
        $sMessageHtml = $this->view->parseMail(PH7_PATH_SYS . 'global/' . PH7_VIEWS . PH7_DEFAULT_THEME . '/tpl/mail/sys/mod/friend/friend_request.tpl', $sFriendEmail);

        $aInfo = [
            'to' => $sFriendEmail,
            'subject' => t('%0% wants to be friends with you on %1%', $this->session->get('member_first_name'), $sSiteName)
        ];

        (new Mail)->send($aInfo, $sMessageHtml);
    }
}

// Only for Members
if (UserCore::auth()) {
    new FriendAjax;
}
