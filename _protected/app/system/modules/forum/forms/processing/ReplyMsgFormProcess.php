<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Forum / Form / Processing
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Mvc\Request\Http;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Header;

class ReplyMsgFormProcess extends Form
{
    public function __construct()
    {
        parent::__construct();

        $oForumModel = new ForumModel;

        $sMessage = $this->httpRequest->post('message', Http::ONLY_XSS_CLEAN);
        $sCurrentTime = $this->dateTime->get()->dateTime('Y-m-d H:i:s');
        $iTimeDelay = (int)DbConfig::getSetting('timeDelaySendForumMsg');
        $iProfileId = (int)$this->session->get('member_id');
        $iForumId = $this->httpRequest->get('forum_id', 'int');
        $iTopicId = $this->httpRequest->get('topic_id', 'int');

        if (!$oForumModel->checkWaitReply($iTopicId, $iProfileId, $iTimeDelay, $sCurrentTime)) {
            \PFBC\Form::setError('form_reply', Form::waitWriteMsg($iTimeDelay));
        } elseif ($oForumModel->isDuplicateMessage($iProfileId, $sMessage)) {
            \PFBC\Form::setError('form_reply', Form::duplicateContentMsg());
        } else {
            $oForumModel->addMessage($iProfileId, $iTopicId, $sMessage, $sCurrentTime);

            Header::redirect(
                Uri::get(
                    'forum',
                    'forum',
                    'post',
                    $this->httpRequest->get('forum_name') . ',' . $iForumId . ',' . $this->httpRequest->get('topic_name') . ',' . $iTopicId
                ),
                t('Reply posted!')
            );
        }
        unset($oForumModel);
    }
}
