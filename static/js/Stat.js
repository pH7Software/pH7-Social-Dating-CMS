/*!
 * Author:        Pierre-Henry Soria <hello@ph7builder.com>
 * Copyright:     (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * License:       MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

function Stat() {
    var oMe = this; // Self Object
    this.sUrl = 'asset/ajax/Stat/';
    this.iRefreshInterval = 60000;
    this.iTimer = null;

    this.totalUsers = function () {
        $.post(pH7Url.base + this.sUrl, {type: 'total_users'}, function (iData) {
            $('.stat_total_users').flipCounter({number: parseInt(iData)});
        });
    };

    this.start = function () {
        this.totalUsers();

        if (this.iTimer !== null) {
            clearInterval(this.iTimer);
        }

        this.iTimer = setInterval(function () {
            oMe.totalUsers();
        }, this.iRefreshInterval);
    };
}

var oStat = new Stat;
oStat.start();
