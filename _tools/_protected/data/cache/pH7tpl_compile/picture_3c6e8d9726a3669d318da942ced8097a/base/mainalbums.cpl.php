<?php 
namespace PH7;
defined('PH7') or exit('Restricted access');
/*
Created on 2020-04-04 01:19:41
Compiled file from: C:\xampp\htdocs\ph7\_protected\app/system/modules/picture\views/base\tpl\main\albums.tpl
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
?><div class="center" id="picture_block"> <?php if(empty($error)) { ?> <?php foreach($albums as $album) { ?> <?php $absolute_url = Framework\Mvc\Router\Uri::get('picture','main','album',"$album->username,$album->name,$album->albumId") ;?> <div class="thumb_photo"> <h4><?php echo Framework\Security\Ban\Ban::filterWord($album->name) ;?></h4> <p> <a href="<?php echo $absolute_url; ?>"> <img src="<?php echo PH7_URL_DATA_SYS_MOD?>picture/img/<?php echo $album->username ;?>/<?php echo $album->albumId ;?>/<?php echo $album->thumb ;?>" alt="<?php echo $album->name ;?>" title="<?php echo $album->name ;?>" /> </a> </p> <p><?php echo nl2br(Framework\Security\Ban\Ban::filterWord($album->description)) ;?></p> <p class="italic"><?php echo t('Views:'); ?> <?php echo Framework\Mvc\Model\Statistic::getView($album->albumId,DbTableName::ALBUM_PICTURE) ;?></p> <?php if($is_user_auth AND $member_id == $album->profileId) { ?> <div class="small"> <a href="<?php $design->url('picture', 'main', 'editalbum', $album->albumId) ;?>"><?php echo t('Edit'); ?></a> | <?php LinkCoreForm::display(t('Delete'), 'picture', 'main', 'deletealbum', array('album_id'=>$album->albumId)) ;?> </div> <?php } ?> <p> <?php RatingDesignCore::voting($album->albumId,DbTableName::ALBUM_PICTURE) ;?> <?php $design->like($album->username,$album->firstName,$album->sex,$absolute_url) ;?> | <?php $design->report($album->profileId, $album->username, $album->firstName, $album->sex) ;?> </p> </div> <?php } ?> <?php $this->display('page_nav.inc.tpl', PH7_PATH_TPL . PH7_TPL_NAME . PH7_DS); ?> <?php } else { ?> <p><?php echo $error; ?></p> <?php } ?> <?php if($is_add_album_btn_shown) { ?> <p class="bottom"> <a class="btn btn-default btn-md" href="<?php $design->url('picture', 'main', 'addalbum') ;?>"> <?php echo t('Add a new album'); ?> </a> </p> <?php } ?></div>