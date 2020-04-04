<?php 
namespace PH7;
defined('PH7') or exit('Restricted access');
/*
Created on 2020-04-04 04:30:04
Compiled file from: C:\xampp\htdocs\ph7\_protected\app/system/modules/user\views/base\tpl\main\index.user.inc.tpl
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
?><h2><?php echo t('Hi <em>%0%</em>! How are you today?', $first_name); ?></h2><h3 class="s_bMarg"><?php echo t('Say hi to the new people and meet them!'); ?></h3><h5 class="underline vs_marg"> <?php echo t('Wall'); ?> <span class="italic">&quot;<?php echo t('The latest news'); ?>&quot;</span></h5><div class="left col-md-7" id="wall"></div><div class="right col-md-5"> <?php $userDesignModel->profilesBlock() ;?></div>