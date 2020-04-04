<?php defined('PH7') or exit('Restricted access');
/*
Created on 2020-04-04 01:15:17
File ID: mailcheck
*/
/***************************************************************************
 *     pH7CMS Web Engineer: Pierre-Henry Soria
 *               --------------------
 * @since      Mon Oct 14 2011
 * @author     Pierre-Henry Soria
 * @email      hello@ph7cms.com
 * @link       https://ph7cms.com
 * @copyright  (c) 2011-2020, Pierre-Henry Soria. All Rights Reserved.
 * @license    GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 ***************************************************************************/
$_mData = <<<'EOF'
s:1366:"
var domains=['# emails domain names suggestion for mailcheck JS plugin #','yahoo.com','yahoo.fr','google.com','hotmail.com','hotmail.co.il','hotmail.co.jp','hotmail.com.ar','hotmail.com.br','hotmail.com.tr','hotmail.co.th','hotmail.co.uk','hotmail.de','hotmail.es','hotmail.jp','hotmail.se','hotmail.it','hotmail.fr','hotmail.be','live.com','live.at','live.ca','live.cl','live.cn','live.co.kr','live.com.ar','live.com.au','live.com.mx','live.com.my','live.com.sg','live.co.za','live.de','live.dk','live.hk','live.ie','live.in','live.jp','live.nl','live.no','live.ru','live.se','live.it','live.fr','live.be','gmail.com','me.com','aol.com','mac.com','comcast.net','googlemail.com','msn.com','yahoo.co.uk','facebook.com','verizon.net','sbcglobal.net','att.net','gmx.com','mail.com','mail.ru','yandex.ru','orange.fr','free.fr'];$('input[id^=email]').blur(function(){var input=$(this);var parent=input.parents('pfbc-textbox');input.mailcheck({domains:domains,suggested:function(element,suggestion){input.next('span').remove();$('<span class="warn_msg"/>').fadeIn('slow').insertAfter(input).append('Did you mean <a href="#">'+suggestion.address.substring(0,120)+'@'+'<strong>'+suggestion.domain+'</strong></a>?').find('a').click(function(e){e.preventDefault();input.val($(this).text());input.trigger('blur');});},empty:function(element){input.next('span').remove();}})});";
EOF;
