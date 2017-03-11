<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / User / Asset / Ajax
 */
namespace PH7;
defined('PH7') or exit('Restricted access');

use PH7\Framework\Mvc\Router\Uri;

class WallAjax extends Core
{

    private $_oWallModel, $_oAvatarDesign, $_sMsg, $_mContents, $_bStatus;

    public function __construct()
    {
        parent::__construct();

        $this->_oWallModel = new WallModel;
        $this->_oAvatarDesign = new AvatarDesignCore; // Avatar Design Class
        switch ($this->httpRequest->post('type'))
        {
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
                Framework\Http\Http::setHeadersByCode(400);
                exit('Bad Request Error!');
        }
    }

    protected function show()
    {
        $this->_mContents = $this->_oWallModel->get($this->session->get('member_id'), null, 0, 20);
        if (!$this->_mContents)
        {
            echo '<p class="alert alert-danger">', t('Oops...! No news feed available at the moment.'), '</p>';
        }
        else
        {
            foreach ($this->_mContents as $oRow)
            {
                echo '<p>';
                $this->_oAvatarDesign->get($oRow->username, $oRow->firstName, $oRow->sex, 32, 'Members');
                echo '</p><p>', Framework\Parse\Emoticon::init(escape($this->str->extract(Framework\Security\Ban\Ban::filterWord($oRow->post), 0, 80))), '</p>
                <p class="small italic">', t('Posted on: %0%', $this->dateTime->get($oRow->createdDate)->dateTime());
                if (!empty($oRow->updatedDate)) echo ' &bull; ', t('Last Edited %0%', $this->dateTime->get($oRow->updatedDate)->dateTime());
                echo '<br /></p>';
            }
        }
    }

    protected function showCommentProfile()
    {
        $this->_mContents = $this->_oWallModel->getCommentProfile(null, 0, 20);
        if (!$this->_mContents)
        {
            echo '<p class="alert alert-danger">', t('No news feed available at the moment. Start commenting some profiles!'), '</p>';
        }
        else
        {
            foreach ($this->_mContents as $oRow)
            {
                echo '<p>';
                $this->_oAvatarDesign->get($oRow->username, $oRow->firstName, $oRow->sex, 32, 'Members');
                echo '</p><p>', Framework\Parse\User::atUsernameToLink(escape($this->str->extract(Framework\Security\Ban\Ban::filterWord($oRow->comment), 0, 80))), '</p>
                <p class="small"><a href="', Uri::get('comment', 'comment', 'read', "profile,$oRow->recipient"), '#', $oRow->commentId, '">', t('Read more'), '</a> &bull; ',
                t('Posted on: %0%', $this->dateTime->get($oRow->createdDate)->dateTime());
                if (!empty($oRow->updatedDate)) echo ' &bull; ', t('Last Edited %0%', $this->dateTime->get($oRow->updatedDate)->dateTime());
                echo '<br /><br /></p>';
            }
        }
    }

    protected function add()
    {
        $this->_bStatus = $this->_oWallModel->add($this->session->get('member_id'), $this->httpRequest->post('post'));
        if (!$this->_bStatus)
            $this->_sMsg = jsonMsg(0, t('Oops, your post could not be sent. Please try again later.'));
        else
            $this->_sMsg = jsonMsg(1, t('Your post has been sent successfully!'));


        echo $this->_sMsg;
    }

    protected function edit()
    {
        $this->_bStatus = $this->_oWallModel->edit($this->session->get('member_id'), $this->httpRequest->post('post'));
        if (!$this->_bStatus)
            $this->_sMsg = jsonMsg(0, t('Oops, your post could not be saved. Please try again later.'));
        else
            $this->_sMsg = jsonMsg(1, t('Your post has been saved successfully!'));


        echo $this->_sMsg;
    }

    protected function delete()
    {
        $this->_bStatus = $this->_oWallModel->delete($this->session->get('member_id'), $this->httpRequest->post('post'));
        if (!$this->_bStatus)
            $this->_sMsg = jsonMsg(0, t('Your post does not exist anymore.'));
        else
            $this->_sMsg = jsonMsg(1, t('Your post has been sent successfully!'));


        echo $this->_sMsg;
    }

    public function __destruct()
    {
        parent::__destruct();

        unset($this->_oWallModel, $this->_oAvatarDesign, $this->_sMsg, $this->_bStatus);
    }

}

// Only for the members
if (User::auth())
    new WallAjax;
