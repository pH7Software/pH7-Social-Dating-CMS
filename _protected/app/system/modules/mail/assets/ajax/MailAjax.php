<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Mail / Asset / Ajax
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Mvc\Request\Http;
use PH7\Framework\Security\CSRF\Token as SecurityToken;
use PH7\Framework\Session\Session;

class Mail
{
    private $_oSession, $_oHttpRequest, $_oMailModel, $_sMsg, $_bStatus;

    public function __construct()
    {
        if (!(new SecurityToken)->check('mail')) {
            exit(jsonMsg(0, Form::errorTokenMsg()));
        }

        /** Instance objects for the class * */
        $this->_oSession = new Session;
        $this->_oHttpRequest = new Http;
        $this->_oMailModel = new MailModel;

        switch ($this->_oHttpRequest->post('type')) {
            case 'trash':
                $this->trash();
                break;

            case 'restor':
                $this->restor();

            case 'delete':
                $this->delete();
                break;

            default:
                Framework\Http\Http::setHeadersByCode(400);
                exit('Bad Request Error!');
        }
    }

    protected function trash()
    {
        $this->_bStatus = $this->_oMailModel->setTo(
            $this->_oSession->get('member_id'),
            $this->_oHttpRequest->post('msg_id'),
            MailModel::TRASH_MODE
        );

        if (!$this->_bStatus) {
            $this->_sMsg = jsonMsg(0, t('Your message does not exist anymore in your trash bin.'));
        } else {
            $this->_sMsg = jsonMsg(1, t('Your message has been moved to your trash bin.'));
        }

        echo $this->_sMsg;
    }

    protected function restor()
    {
        $this->_bStatus = $this->_oMailModel->setTo(
            $this->_oSession->get('member_id'),
            $this->_oHttpRequest->post('msg_id'),
            MailModel::RESTOR_MODE
        );

        if (!$this->_bStatus) {
            $this->_sMsg = jsonMsg(0, t('Your message does not exist anymore in your inbox.'));
        } else {
            $this->_sMsg = jsonMsg(1, t('Your message has been moved to your inbox.'));
        }

        echo $this->_sMsg;
    }

    protected function delete()
    {
        if (AdminCore::auth() && !UserCore::auth()) {
            $this->_bStatus = $this->_oMailModel->adminDeleteMsg($this->_oHttpRequest->post('msg_id'));
        } else {
            $this->_bStatus = $this->_oMailModel->setTo(
                $this->_oSession->get('member_id'),
                $this->_oHttpRequest->post('msg_id'),
                MailModel::DELETE_MODE
            );
        }

        if (!$this->_bStatus) {
            $this->_sMsg = jsonMsg(0, t('Your message does not exist anymore.'));
        } else {
            $this->_sMsg = jsonMsg(1, t('Your message has been successfully removed!'));
        }

        echo $this->_sMsg;
    }
}

// Only for the Members and Admins.
if (UserCore::auth() || AdminCore::auth()) {
    new Mail;
}
