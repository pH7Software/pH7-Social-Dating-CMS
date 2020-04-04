<?php 
namespace PH7;
defined('PH7') or exit('Restricted access');
/*
Created on 2020-04-04 03:29:10
Compiled file from: C:\xampp\htdocs\ph7\_protected\app/system/modules/mail\views/base\tpl\main\compose.tpl
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
?><div class="msg-menu"> <a href="<?php $design->url('mail','main','compose') ;?>">Compose</a> | <a href="<?php $design->url('mail','main','inbox') ;?>">Inbox(<?php echo $count_unread_mail; ?>) </a> | <a href="<?php $design->url('mail','main','outbox') ;?>">Sent</a> | <a href="<?php $design->url('mail','main','trash') ;?>">Trash</a> | <a href="<?php $design->url('user','setting','index') ;?>#p=notification">Settings</a> </div><div class="col-md-8"> <?php MailForm::display() ;?></div><div class="col-md-4 ad_336_280"> <?php $this->designModel->ad(336, 280) ?></div>