<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2015, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Comment / Asset / Ajax
 */
namespace PH7;
defined('PH7') or exit('Restricted access');

use PH7\Framework\Mvc\Request\Http, PH7\Framework\Session\Session;

class Comment
{

    private $_oSession, $_oHttpRequest, $_oCommentModel, $_sMsg, $_bStatus;

    public function __construct()
    {
        if (!(new Framework\Security\CSRF\Token)->check('comment'))
            exit(jsonMsg(0, Form::errorTokenMsg()));

        /** Instance objects for the class * */
        $this->_oSession = new Session;
        $this->_oHttpRequest = new Http;
        $this->_oCommentModel = new CommentModel;

        switch ($this->_oHttpRequest->post('type'))
        {
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
        if ((($this->_oSession->get('member_id') == $this->_oHttpRequest->post('recipient_id')) || ($this->_oSession->get('member_id') == $this->_oHttpRequest->post('sender_id'))) || AdminCore::auth())
        {
            $this->_bStatus = $this->_oCommentModel->delete($this->_oHttpRequest->post('id'), $this->_oHttpRequest->post('recipient_id'), $this->_oHttpRequest->post('sender_id'), $this->_oHttpRequest->post('table'));

            if ($this->_bStatus)
            {
                /* Clean All Data of CommentModel Cache */
                (new Framework\Cache\Cache)->start(CommentCoreModel::CACHE_GROUP, null, null)->clear();

                $this->_sMsg = jsonMsg(1, t('Your comment has been successfully removed!'));
            }
            else
            {
                $this->_sMsg = jsonMsg(0, t('Your comment could not be deleted because there no exist.'));
            }
        }
        else
        {
            $this->_sMsg = jsonMsg(0, t('Whoops! The comment could not be removed!'));
        }
        echo $this->_sMsg;
    }

    public function __destruct()
    {
        unset(
            $this->_oSession,
            $this->_oHttpRequest,
            $this->_oCommentModel,
            $this->_sMsg,
            $this->_bStatus
        );
    }

}

// Only for members
if (UserCore::auth())
    new Comment;
