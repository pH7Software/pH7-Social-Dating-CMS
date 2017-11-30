/*!
 * Author:        Pierre-Henry Soria <ph7software@gmail.com>
 * Copyright:     (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * License:       GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * Version:       0.3
 */

function Stat() {
    var oMe = this; // Self Object
    this.sUrl = 'asset/ajax/Stat/';

    this.totalUsers = function () {
        $.post(pH7Url.base + this.sUrl, {type: 'total_users'}, function (iData) {
            $('.stat_total_users').flipCounter({number: parseInt(iData)});
        });
        setTimeout(function () {
            oMe.totalUsers()
        }, 1000);
    };
}

var oStat = new Stat;
oStat.totalUsers();
