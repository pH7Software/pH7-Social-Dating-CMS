/*
 * Author:        Pierre-Henry Soria <ph7software@gmail.com>
 * Copyright:     (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * License:       GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 */

function display_status(sUrl, sExt) {
    var sSelected = 'select#status option:selected';
    var sStatusTitle = $(sSelected).text();
    var iStatusVal = $(sSelected).val();
    var sStatusIcon = (iStatusVal == 1 ? 'online' : (iStatusVal == 2 ? 'busy' : (iStatusVal == 3 ? 'away' : 'offline')));
    $('#status_div').html('<img src="' + sUrl + sStatusIcon + '.' + sExt + '" alt="' + sStatusTitle + '" title="' + sStatusTitle + '" />');
}

function init_status() {
    display_status(pH7Url.tplImg + 'icon/', 'png');
}
