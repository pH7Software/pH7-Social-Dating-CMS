<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / User / Asset / Ajax
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Http\Http;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Parse\Emoticon;
use PH7\Framework\Parse\User as UserParser;
use PH7\Framework\Security\Ban\Ban;
use Teapot\StatusCode;

class WallAjax extends Core
{
    const MAX_ITEMS_SHOWN = 20;
    const MAX_STRING_LENGTH_SHOWN = 80;

    /** @var WallModel */
    private $oWallModel;

    /** @var AvatarDesignCore */
    private $oAvatarDesign;

    /** @var string */
    private $sMsg;

    /** @var mixed */
    private $mContents;

    /** @var bool */
    private $bStatus;

    public function __construct()
    {
        parent::__construct();

        $this->oWallModel = new WallModel;
        $this->oAvatarDesign = new AvatarDesignCore; // Avatar Design Class

        switch ($this->httpRequest->post('type')) {
            case 'show':
                $this->show();
                break;

            case 'showCommentProfile':
                $this->showCommentProfile();
                break;

            case 'add':
                $this->add();
                break;

            case 'edit':
                $this->edit();
                break;

            case 'delete':
                $this->delete();
                break;

            default:
                $this->badRequest();
        }
    }

    private function show()
    {
        $this->mContents = $this->oWallModel->get(
            $this->session->get('member_id'),
            null,
            0,
            self::MAX_ITEMS_SHOWN
        );

        if (!$this->mContents) {
            echo '<p class="alert alert-danger">', t('Oops...! No news feed available at the moment.'), '</p>';
        } else {
            foreach ($this->mContents as $oRow) {
                echo '<p>';
                $this->oAvatarDesign->get($oRow->username, $oRow->firstName, $oRow->sex, 32, DbTableName::MEMBER);
                echo '</p><p>', Emoticon::init(escape($this->str->extract(Ban::filterWord($oRow->post), self::MAX_STRING_LENGTH_SHOWN))), '</p>
                    <p class="small italic">', t('Posted on: %0%', $this->dateTime->get($oRow->createdDate)->dateTime());

                if (!empty($oRow->updatedDate)) {
                    echo ' &bull; ', t('Last Edited %0%', $this->dateTime->get($oRow->updatedDate)->dateTime());
                }
                echo '<br /></p>';
            }
        }
    }

    private function showCommentProfile()
    {
        $this->mContents = $this->oWallModel->getCommentProfile(
            null,
            0,
            self::MAX_ITEMS_SHOWN
        );

        if (!$this->mContents) {
            echo '<p class="alert alert-danger">', t('No news feed available at the moment. <a href="%0%">Start commenting some profiles</a>!', Uri::get('user', 'browse', 'index')), '</p>';
        } else {
            foreach ($this->mContents as $oRow) {
                echo '<p>';
                $this->oAvatarDesign->get($oRow->username, $oRow->firstName, $oRow->sex, 32, DbTableName::MEMBER);

                echo '</p><p>', UserParser::atUsernameToLink(escape($this->str->extract(Ban::filterWord($oRow->comment), self::MAX_STRING_LENGTH_SHOWN))), '</p>
                    <p class="small"><a href="', Uri::get('comment', 'comment', 'read', "profile,$oRow->recipient"), '#', $oRow->commentId, '">', t('Read more'), '</a> &bull; ',
                t('Posted on: %0%', $this->dateTime->get($oRow->createdDate)->dateTime());

                if (!empty($oRow->updatedDate)) {
                    echo ' &bull; ', t('Last Edited %0%', $this->dateTime->get($oRow->updatedDate)->dateTime());
                }
                echo '<br /><br /></p>';
            }
        }
    }

    private function add()
    {
        $this->bStatus = $this->oWallModel->add(
            $this->session->get('member_id'),
            $this->httpRequest->post('post')
        );

        if (!$this->bStatus) {
            $this->sMsg = jsonMsg(0, t('Oops, your post could not be sent. Please try again later.'));
        } else {
            $this->sMsg = jsonMsg(1, t('Your post has been sent successfully!'));
        }

        echo $this->sMsg;
    }

    private function edit()
    {
        $this->bStatus = $this->oWallModel->edit(
            $this->session->get('member_id'),
            $this->httpRequest->post('post')
        );

        if (!$this->bStatus) {
            $this->sMsg = jsonMsg(0, t('Oops, your post could not be saved. Please try again later.'));
        } else {
            $this->sMsg = jsonMsg(1, t('Your post has been saved successfully!'));
        }

        echo $this->sMsg;
    }

    private function delete()
    {
        $this->bStatus = $this->oWallModel->delete(
            $this->session->get('member_id'),
            $this->httpRequest->post('post')
        );

        if (!$this->bStatus) {
            $this->sMsg = jsonMsg(0, t('Your post does not exist anymore.'));
        } else {
            $this->sMsg = jsonMsg(1, t('Your post has been sent successfully!'));
        }

        echo $this->sMsg;
    }

    private function badRequest()
    {
        Http::setHeadersByCode(StatusCode::BAD_REQUEST);
        exit('Bad Request Error!');
    }
}

// Only for the members
if (User::auth()) {
    new WallAjax;
}
