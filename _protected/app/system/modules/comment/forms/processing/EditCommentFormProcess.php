<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Comment / Form / Processing
 */

namespace PH7;
defined('PH7') or exit('Restricted access');

use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Header;

class EditCommentFormProcess extends Form
{

    public function __construct()
    {
        parent::__construct();

        $oCommentModel = new CommentModel;

        $sTable = $this->httpRequest->get('table');
        $iCommentId = $this->httpRequest->get('id', 'int');
        $iMemberId = (int) $this->session->get('member_id');
        $iRecipientId = $this->httpRequest->get('recipient', 'int');
        $iSenderId = $this->httpRequest->get('sender', 'int');

        if (!$oCommentModel->idExists($iRecipientId, $sTable))
        {
            \PFBC\Form::setError('form_comment', t('The comment recipient does not exists.'));
        }
        else
        {
            if (($iMemberId == $iRecipientId) || ($iMemberId == $iSenderId))
            {
                if ($oCommentModel->update($iCommentId, $iRecipientId, $iSenderId, $this->httpRequest->post('comment'), 1, $this->dateTime->get()->dateTime('Y-m-d H:i:s'), $sTable))
                {
                    /* Clean All Data of CommentModel Cache */
                    (new Framework\Cache\Cache)->start(CommentCoreModel::CACHE_GROUP, null, null)->clear();

                    Header::redirect(Uri::get('comment','comment','read', $sTable . ',' . $iRecipientId), t('The comment has been updated successfully!'));
                }
                else
                {
                    \PFBC\Form::setError('form_comment', t('Oops! Error when updated comment.'));
                }
            }
        }
        unset($oCommentModel);
    }

}
