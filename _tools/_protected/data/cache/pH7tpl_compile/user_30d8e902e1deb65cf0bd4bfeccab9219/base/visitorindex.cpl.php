<?php 
namespace PH7;
defined('PH7') or exit('Restricted access');
/*
Created on 2020-04-04 01:19:40
Compiled file from: C:\xampp\htdocs\ph7\_protected\app/system/modules/user\views/base\tpl\visitor\index.tpl
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
?><div class="center" id="visitor_block"> <?php if($user_views_setting == PrivacyCore::NO) { ?> <div class="center alert alert-warning"><?php echo t('To see the new members who view your profile, you must first change'); ?> <a href="<?php $design->url('user','setting','privacy') ;?>"><?php echo t('your privacy settings'); ?></a>.</div> <?php } ?> <?php if(empty($error)) { ?> <h3 class="underline"><?php echo t('Recently Viewed By:'); ?></h3> <p class="italic underline"><strong><a href="<?php $design->url('user','visitor','index',$username) ;?>"><?php echo $visitor_number; ?></a></strong></p><br /> <?php foreach($visitors as $v) { ?> <div class="s_photo"> <?php $avatarDesign->get($v->username, $v->firstName, $v->sex, 64, true) ;?> </div> <?php } ?> <?php $this->display('page_nav.inc.tpl', PH7_PATH_TPL . PH7_TPL_NAME . PH7_DS); ?> <br /> <p class="center bottom"> <a class="btn btn-default btn-md" href="<?php $design->url('user','visitor','search',$username) ;?>"><?php echo t('Search for a visitor of %0%', $v->username); ?></a> </p> <?php } else { ?> <p><?php echo $error; ?></p> <?php } ?></div>