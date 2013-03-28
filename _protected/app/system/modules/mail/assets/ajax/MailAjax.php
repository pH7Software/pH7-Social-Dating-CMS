<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Mail / Asset / Ajax
 */
namespace PH7;
defined('PH7') or exit('Restricted access');

use PH7\Framework\Session\Session, PH7\Framework\Mvc\Request\HttpRequest;

class Mail
{

    private $_oSession, $_oHttpRequest, $_oMailModel, $_sMsg, $_bStatus;

    public function __construct()
    {
        if (!(new Framework\Security\CSRF\Token)->check('mail'))
        exit(jsonMsg(0, Form::errorTokenMsg()));

        /** Instance objects for the class * */
        $this->_oSession = new Session;
        $this->_oHttpRequest = new HttpRequest;
        $this->_oMailModel = new MailModel;

        switch ($this->_oHttpRequest->post('type')) {
            case 'delete':
                $this->delete();
            break;

            default:
                Framework\Http\Http::setHeadersByCode(400);
                exit('Bad Request Error!');
        }
    }

    protected function delete()
    {
        if(AdminCore::auth() && !UserCore::auth())
            $this->_bStatus = $this->_oMailModel->adminDeleteMessage($this->_oHttpRequest->post('msg_id'));
        else
            $this->_bStatus = $this->_oMailModel->deleteMessage($this->_oSession->get('member_id'), $this->_oHttpRequest->post('msg_id'));

        if(!$this->_bStatus)
            $this->_sMsg = jsonMsg(0, t('Your message could not be deleted because there no exist.'));
        else
            $this->_sMsg = jsonMsg(1, t('Your message has been successfully removed!'));

        echo $this->_sMsg;
    }

    public function __destruct()
    {
        unset($this->_oSession, $this->_oHttpRequest, $this->_oMailModel, $this->_sMsg, $this->_bStatus);
    }

}

// Only for the Members and Admins.
if (UserCore::auth() || AdminCore::auth()) {
    new Mail;
}
