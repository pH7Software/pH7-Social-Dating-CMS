<?php 
namespace PH7;
defined('PH7') or exit('Restricted access');
/*
Created on 2020-04-04 01:19:42
Compiled file from: C:\xampp\htdocs\ph7\_protected\app/system/modules/friend\views/base\tpl\main\index.tpl
Template Engine: PH7Tpl version 1.4.0 by Pierre-Henry Soria
*/
/***************************************************************************
 *     pH7CMS Web Engineer: Pierre-Henry Soria
 *               --------------------
 * @since      Mon Mar 21 2011
 * @author     Pierre-Henry Soria
 * @email      hello@ph7cms.com
 * @link       https://ph7cms.com
 * @copyright  (c) 2011-2020, Pierre-Henry Soria. All Rights Reserved.
 * @license    Creative Commons Attribution 3.0 License - http://creativecommons.org/licenses/by/3.0/
 ***************************************************************************/
?><div class="center" id="friend_block"> <?php if(empty($error)) { ?> <p class="italic underline"> <strong><a href="<?php $design->url('friend','main','index',$username) ;?>"><?php echo $friend_number; ?></a></strong> </p> <br /> <?php foreach($friends as $f) { ?> <div class="s_photo" id="friend_<?php echo $f->fdId ;?>"> <?php $avatarDesign->get($f->username, $f->firstName, $f->sex, 64, true) ;?> <em><abbr title="<?php echo $f->firstName ;?>"><?php echo $f->username ;?></abbr></em><br /> <?php if($is_user_auth AND $sess_member_id == $member_id) { ?> <?php if($sess_member_id == $f->friendId AND $f->pending == FriendCoreModel::PENDING_REQUEST) { ?> <small><?php echo t('Pending...'); ?></small> <a href="javascript:void(0)" onclick="friend('approval',<?php echo $f->fdId ;?>,'<?php echo $csrf_token; ?>')"><?php echo t('Approve'); ?></a> <?php } ?> <a href="javascript:void(0)" onclick="friend('delete',<?php echo $f->fdId ;?>,'<?php echo $csrf_token; ?>')"> <?php echo t('Delete'); ?> </a> <?php } ?> </div> <?php } ?> <?php $this->display('page_nav.inc.tpl', PH7_PATH_TPL . PH7_TPL_NAME . PH7_DS); ?> <br /> <p class="center bottom"> <a class="btn btn-default btn-md" href="<?php $design->url('friend','main','search',"$username,$action") ;?>"> <?php echo t('Search for a friend of %0%', $username); ?> </a> </p> <?php } else { ?> <p><?php echo $error; ?></p> <?php } ?></div>