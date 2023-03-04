/*
 * Author:        Pierre-Henry Soria <hello@ph7builder.com>
 * Copyright:     (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * License:       MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

function updateActivity() {
    $.get(pH7Url.base + 'user/asset/ajax/setActivity');
    setInterval('updateActivity()', 10000)
}
updateActivity();
