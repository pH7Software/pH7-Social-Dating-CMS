/*
 * Author:        Pierre-Henry Soria <hello@ph7builder.com>
 * Copyright:     (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * License:       MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

function sendUserActivityHeartbeat() {
    $.get(pH7Url.base + 'user/asset/ajax/setActivity');
}

function startUserActivityTracking() {
    var TEN_SECONDS_IN_MILLISECONDS = 10000;

    sendUserActivityHeartbeat();
    setInterval(sendUserActivityHeartbeat, TEN_SECONDS_IN_MILLISECONDS);
}

startUserActivityTracking();
