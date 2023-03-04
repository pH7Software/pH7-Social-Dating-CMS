/*
 * Author:        Pierre-Henry Soria <ph7software@gmail.com>
 * Copyright:     (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * License:       MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

function display_status() {
    var sSelected = 'select#status option:selected';
    var sStatusTitle = $(sSelected).text();
    var iStatusVal = $(sSelected).val();
    var sCssClass = (iStatusVal == 1 ? 'green' : (iStatusVal == 2 ? 'orange' : (iStatusVal == 3 ? 'red' : 'gray')));
    $('.user_status#dropdown_setting').html('<span class="' + sCssClass + '" title="' + sStatusTitle + '">•</span>');
}

function init_status() {
    display_status();
}
