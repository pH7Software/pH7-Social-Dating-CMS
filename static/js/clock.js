/*
 * Author:        Pierre-Henry Soria <ph7software@gmail.com>
 * Copyright:     (c) 2012-2016, Pierre-Henry Soria. All Rights Reserved.
 * License:       GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 */

function getTime(){$.get(pH7Url.base+"asset/ajax/clock",function(a){$("#clock").html(a)});setTimeout("getTime()",1E3)}getTime();
