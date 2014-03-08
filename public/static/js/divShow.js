/*
 * Author:        Pierre-Henry Soria <ph7software@gmail.com>
 * Copyright:     (c) 2012-2014, Pierre-Henry Soria. All Rights Reserved.
 * License:       GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 */

function pH7DivShow(a){"none"==$(a).css("display")?$(a).show("slow"):$(a).hide(1E3)}$(".divShow a").click(function(){$(this).attr("href",pH7DivShow($(this).attr("href")))});
