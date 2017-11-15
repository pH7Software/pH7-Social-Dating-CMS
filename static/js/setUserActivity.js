/*
 * Author:        Pierre-Henry Soria <ph7software@gmail.com>
 * Copyright:     (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * License:       GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 */

function updateActivity() {
    $.get(pH7Url.base + 'user/asset/ajax/setActivity');
    setTimeout('updateActivity()', 10000)
}
updateActivity();
