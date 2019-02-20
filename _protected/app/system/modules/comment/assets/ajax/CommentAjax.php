<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Comment / Asset / Ajax
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Http\Http;
use PH7\Framework\Mvc\Request\Http as HttpRequest;
use PH7\Framework\Security\CSRF\Token;
use PH7\Framework\Session\Session;
use Teapot\StatusCode;

class Comment
{
    /** @var Session */
    private $oSession;

    /** @var HttpRequest */
    private $oHttpRequest;

    /** @var CommentModel */
    private $oCommentModel;

    /** @var string */
    private $sMsg;

    /** @var bool */
    private $bStatus;

    public function __construct()
    {
        if (!(new Token)->check('comment')) {
            exit(jsonMsg(0, Form::errorTokenMsg()));
        }

        /** Instance objects for the class **/
        $this->oSession = new Session;
        $this->oHttpRequest = new HttpRequest;
        $this->oCommentModel = new CommentModel;

        switch ($this->oHttpRequest->post('type')) {
            case 'delete':
                $this->delete();
                break;

            default:
                Http::setHeadersByCode(StatusCode::BAD_REQUEST);
                exit('Bad Request Error!');
        }
    }

    protected function delete()
    {
        if (CommentCore::isRemovalEligible($this->oHttpRequest, $this->oSession)) {
            $this->bStatus = $this->oCommentModel->delete(
                $this->oHttpRequest->post('id'),
                $this->oHttpRequest->post('recipient_id'),
                $this->oHttpRequest->post('sender_id'),
                $this->oHttpRequest->post('table')
            );

            if ($this->bStatus) {
                CommentCore::clearCache();

                $this->sMsg = jsonMsg(1, t('Your comment has been successfully removed!'));
            } else {
                $this->sMsg = jsonMsg(0, t('Your comment does not exist anymore.'));
            }
        } else {
            $this->sMsg = jsonMsg(0, t('Whoops! The comment could not be removed!'));
        }
        echo $this->sMsg;
    }
}

// Only for members
if (UserCore::auth()) {
    new Comment;
}
