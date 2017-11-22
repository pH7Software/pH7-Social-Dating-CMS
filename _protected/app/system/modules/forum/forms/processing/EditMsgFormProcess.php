<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Forum / Form / Processing
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Mvc\Request\Http;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Header;

class EditMsgFormProcess extends Form
{
    public function __construct()
    {
        parent::__construct();

        $iForumId = $this->httpRequest->get('forum_id', 'int');
        $iTopicId = $this->httpRequest->get('topic_id', 'int');
        $sTopicTitle = trim($this->httpRequest->post('title'));

        (new ForumModel)->updateTopic(
            $this->session->get('member_id'),
            $iTopicId,
            $sTopicTitle,
            $this->httpRequest->post('message', Http::ONLY_XSS_CLEAN),
            $this->dateTime->get()->dateTime('Y-m-d H:i:s')
        );

        Header::redirect(
            Uri::get(
                'forum',
                'forum',
                'post',
                $this->httpRequest->get('forum_name') . ',' . $iForumId . ',' . $sTopicTitle . ',' . $iTopicId
            ),
            t('Message updated!')
        );
    }
}
