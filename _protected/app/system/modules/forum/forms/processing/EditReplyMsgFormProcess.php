<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Forum / Form / Processing
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Mvc\Request\Http;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Header;

class EditReplyMsgFormProcess extends Form
{
    public function __construct()
    {
        parent::__construct();

        $iForumId = $this->httpRequest->get('forum_id', 'int');
        $iTopicId = $this->httpRequest->get('topic_id', 'int');
        $iMessageId = $this->httpRequest->get('message_id', 'int');

        (new ForumModel)->updateMessage(
            $this->session->get('member_id'),
            $iMessageId, $this->httpRequest->post(
            'message',
            Http::ONLY_XSS_CLEAN
        ),
            $this->dateTime->get()->dateTime('Y-m-d H:i:s')
        );

        $this->redirectUserToTopicPost($iForumId, $iTopicId);
    }

    /**
     * @param int $iForumId
     * @param int $iTopicId
     *
     * @throws Framework\File\IOException
     */
    private function redirectUserToTopicPost($iForumId, $iTopicId)
    {
        Header::redirect(
            Uri::get(
                'forum',
                'forum',
                'post',
                $this->httpRequest->get('forum_name') . ',' . $iForumId . ',' . $this->httpRequest->get('topic_name') . ',' . $iTopicId
            ),
            t('Reply updated!')
        );
    }
}
