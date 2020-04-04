<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Comment / Form / Processing
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Security\Spam\Spam;
use PH7\Framework\Url\Header;

class CommentFormProcess extends Form
{
    const MAX_ALLOWED_LINKS = 1;
    const MAX_ALLOWED_EMAILS = 0;

    public function __construct()
    {
        parent::__construct();

        $oCommentModel = new CommentModel;

        $sComment = $this->httpRequest->post('comment');
        $sCurrentTime = $this->dateTime->get()->dateTime('Y-m-d H:i:s');
        $iTimeDelay = (int)DbConfig::getSetting('timeDelaySendComment');
        $sTable = $this->httpRequest->get('table');
        $iRecipientId = $this->httpRequest->get('recipient', 'int');
        $iSenderId = (int)$this->session->get('member_id');

        if (!$oCommentModel->idExists($iRecipientId, $sTable)) {
            \PFBC\Form::setError('form_comment', t("The comment recipient doesn't exists."));
        } elseif (!$oCommentModel->checkWaitSend($iSenderId, $iTimeDelay, $sCurrentTime, $sTable)) {
            \PFBC\Form::setError('form_comment', Form::waitWriteMsg($iTimeDelay));
        } elseif ($oCommentModel->isDuplicateContent($iSenderId, $sComment, $sTable)) {
            \PFBC\Form::setError('form_comment', Form::duplicateContentMsg());
        } elseif (Spam::areUrls($sComment, self::MAX_ALLOWED_LINKS)) {
            \PFBC\Form::setError('form_comment', Form::tooManyUrlsMsg());
        } elseif (Spam::areEmails($sComment, self::MAX_ALLOWED_EMAILS)) {
            \PFBC\Form::setError('form_comment', Form::tooManyEmailsMsg());
        } else {
            if (!$oCommentModel->add($sComment, $iRecipientId, $iSenderId, '1', $sCurrentTime, $sTable)) {
                \PFBC\Form::setError('form_comment', t('Oops! Error occurred when adding comment.'));
            } else {
                CommentCore::clearCache();

                Header::redirect(
                    Uri::get('comment', 'comment', 'read', $sTable . ',' . $iRecipientId),
                    t('Comment posted!')
                );
            }
        }
        unset($oCommentModel);
    }
}
