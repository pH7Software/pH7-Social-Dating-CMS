<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Comment / Form / Processing
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Security\Spam\Spam;
use PH7\Framework\Url\Header;

class EditCommentFormProcess extends Form
{
    const MAX_ALLOWED_LINKS = 1;
    const MAX_ALLOWED_EMAILS = 0;

    /** @var int */
    private $iMemberId;

    /** @var int */
    private $iRecipientId;

    /** @var int */
    private $iSenderId;

    public function __construct()
    {
        parent::__construct();

        $this->iMemberId = (int)$this->session->get('member_id');
        $this->iRecipientId = $this->httpRequest->get('recipient', 'int');
        $this->iSenderId = $this->httpRequest->get('sender', 'int');

        $oCommentModel = new CommentModel;

        $sTable = $this->httpRequest->get('table');
        $iCommentId = $this->httpRequest->get('id', 'int');
        $sComment = $this->httpRequest->post('comment');

        if (!$oCommentModel->idExists($this->iRecipientId, $sTable)) {
            \PFBC\Form::setError('form_edit_comment', t("The comment recipient doesn't exists."));
        } elseif (Spam::areUrls($sComment, self::MAX_ALLOWED_LINKS)) {
            \PFBC\Form::setError('form_edit_comment', Form::tooManyUrlsMsg());
        } elseif (Spam::areEmails($sComment, self::MAX_ALLOWED_EMAILS)) {
            \PFBC\Form::setError('form_edit_comment', Form::tooManyEmailsMsg());
        } else {
            if ($this->isEditEligible()) {
                if ($oCommentModel->update(
                    $iCommentId,
                    $this->iRecipientId,
                    $this->iSenderId,
                    $sComment,
                    '1',
                    $this->dateTime->get()->dateTime('Y-m-d H:i:s'),
                    $sTable
                )) {
                    CommentCore::clearCache();

                    Header::redirect(
                        Uri::get(
                            'comment',
                            'comment',
                            'read',
                            $sTable . ',' . $this->iRecipientId
                        ),
                        t('Comment updated!')
                    );
                } else {
                    \PFBC\Form::setError('form_edit_comment', t('Oops! Error occurred when updated comment.'));
                }
            } else {
                \PFBC\Form::setError('form_edit_comment', t("Oops! You don't have the permission to update the comment."));
            }
        }
        unset($oCommentModel);
    }

    /**
     * @return bool
     */
    private function isEditEligible()
    {
        return $this->iMemberId === $this->iRecipientId || $this->iMemberId === $this->iSenderId;
    }
}
