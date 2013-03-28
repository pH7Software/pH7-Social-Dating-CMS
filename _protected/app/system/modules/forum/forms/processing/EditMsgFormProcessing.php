<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Forum / Form / Processing
 */
namespace PH7;
defined('PH7') or exit('Restricted access');

use
PH7\Framework\Mvc\Request\HttpRequest,
PH7\Framework\Mvc\Router\UriRoute,
PH7\Framework\Url\HeaderUrl;

class EditMsgFormProcessing extends Form
{

    public function __construct()
    {
        parent::__construct();

        $iForumId = $this->httpRequest->get('forum_id', 'int');
        $iTopicId = $this->httpRequest->get('topic_id', 'int');

        (new ForumModel)->updateTopic($this->session->get('member_id'), $iTopicId, $this->httpRequest->post('title'), $this->httpRequest->post('message', HttpRequest::ONLY_XSS_CLEAN), $this->dateTime->get()->dateTime('Y-m-d H:i:s'));
        HeaderUrl::redirect(UriRoute::get('forum', 'forum', 'post', $this->httpRequest->get('forum_name').','.$iForumId.','.$this->httpRequest->post('title').','.$iTopicId), t('Your message has been updated successfully!'));
    }

}
