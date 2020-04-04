<?php 
namespace PH7;
defined('PH7') or exit('Restricted access');
/*
Created on 2020-04-04 01:21:08
Compiled file from: C:\xampp\htdocs\ph7\_protected\app/system/modules/admin123\views/base\tpl\main\index.tpl
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
?><?php if($show_get_started_section) { ?> <?php $this->display($this->getCurrentController() . PH7_DS . 'get_started_intro.inc.tpl', $this->registry->path_module_views . PH7_TPL_MOD_NAME . PH7_DS); } $this->display($this->getCurrentController() . PH7_DS . 'stat.tpl', $this->registry->path_module_views . PH7_TPL_MOD_NAME . PH7_DS); if($is_news_feed) { ?> <br /><hr /><br /> <?php $this->display($this->getCurrentController() . PH7_DS . 'news.inc.tpl', $this->registry->path_module_views . PH7_TPL_MOD_NAME . PH7_DS); } ?>