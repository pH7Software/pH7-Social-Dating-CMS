<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Mail / Asset / Ajax
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Http\Http;
use PH7\Framework\Mvc\Request\Http as HttpRequest;
use PH7\Framework\Security\CSRF\Token as SecurityToken;
use PH7\Framework\Session\Session;
use Teapot\StatusCode;

class Mail
{
    /** @var Session */
    private $oSession;

    /** @var HttpRequest */
    private $oHttpRequest;

    /** @var MailModel */
    private $oMailModel;

    /** @var string */
    private $sMsg;

    /** @var bool */
    private $bStatus;

    public function __construct()
    {
        if (!(new SecurityToken)->check('mail')) {
            exit(jsonMsg(0, Form::errorTokenMsg()));
        }

        /** Instance objects for the class **/
        $this->oSession = new Session;
        $this->oHttpRequest = new HttpRequest;
        $this->oMailModel = new MailModel;

        switch ($this->oHttpRequest->post('type')) {
            case 'trash':
                $this->trash();
                break;

            case 'restore':
                $this->restore();

            case 'delete':
                $this->delete();
                break;

            default:
                Http::setHeadersByCode(StatusCode::BAD_REQUEST);
                exit('Bad Request Error!');
        }
    }

    protected function trash()
    {
        $this->bStatus = $this->oMailModel->setTo(
            $this->oSession->get('member_id'),
            $this->oHttpRequest->post('msg_id'),
            MailModel::TRASH_MODE
        );

        if (!$this->bStatus) {
            $this->sMsg = jsonMsg(0, t('Your message does not exist anymore in your trash bin.'));
        } else {
            $this->sMsg = jsonMsg(1, t('Your message has been moved to your trash bin.'));
        }

        echo $this->sMsg;
    }

    protected function restore()
    {
        $this->bStatus = $this->oMailModel->setTo(
            $this->oSession->get('member_id'),
            $this->oHttpRequest->post('msg_id'),
            MailModel::RESTORE_MODE
        );

        if (!$this->bStatus) {
            $this->sMsg = jsonMsg(0, t('Your message does not exist anymore in your inbox.'));
        } else {
            $this->sMsg = jsonMsg(1, t('Your message has been moved to your inbox.'));
        }

        echo $this->sMsg;
    }

    protected function delete()
    {
        if (AdminCore::auth() && !UserCore::auth()) {
            $this->bStatus = $this->oMailModel->adminDeleteMsg(
                $this->oHttpRequest->post('msg_id')
            );
        } else {
            $this->bStatus = $this->oMailModel->setTo(
                $this->oSession->get('member_id'),
                $this->oHttpRequest->post('msg_id'),
                MailModel::DELETE_MODE
            );
        }

        if (!$this->bStatus) {
            $this->sMsg = jsonMsg(0, t('Your message does not exist anymore.'));
        } else {
            $this->sMsg = jsonMsg(1, t('Your message has been successfully removed!'));
        }

        echo $this->sMsg;
    }
}

// Only for the Members and Admins.
if (UserCore::auth() || AdminCore::auth()) {
    new Mail;
}
