/*
 * Title:           Chat Messenger
 * Description:     This Chat Messenger allows users to instantly communicate via messages and smileys.
 *                  It also has a warning system to alert the arrival of new messages.
 *
 * Author:          Pierre-Henry Soria <ph7software@gmail.com>
 * Copyright:       (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * License:         GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * Version:         1.4
 */

// This feature is only for members!


// Global variables
var sOriginalTitle = '', bWindowFocus = true;

var Messenger = {

    // Properties
    sUsername: null,
    sBoxTitle: '',
    iHeartbeatCount: 0,
    iMinHeartbeat: 1000,
    iMaxHeartbeat: 40000,
    iBlinkOrder: 0,
    sMessage: '',

    aNewMessages: new Array,
    aNewMessagesWin: new Array,
    aBoxes: new Array,
    aBoxFocus: new Array,
    aMinimizedBoxes: new Array,

    // Constructor
    Messenger: function () {
        this.iHeartbeatTime = this.iMinHeartbeat;
        oMe = this; // Self Object

        return this;
    },

    // Methods
    restructureBoxes: function () {
        var iAlign = 0;

        for (x in this.aBoxes) {
            this.sBoxTitle = this.aBoxes[x];

            if ($("#chatbox_" + this.sBoxTitle).css('display') != 'none') {
                if (iAlign == 0) {
                    $("#chatbox_" + this.sBoxTitle)
                        .css('right', '20px');
                }
                else {
                    iWidth = (iAlign) * (225 + 7) + 20;
                    $("#chatbox_" + this.sBoxTitle).css('right', iWidth + 'px');
                }
                ++iAlign;
            }
        }
    },

    chatWith: function (sUser) {
        if (this._check(sUser)) {
            this.createBox(sUser);
            $("#chatbox_" + sUser + " .chatboxtextarea").focus();
        }
        else {
            $('.msg').addClass('alert alert-danger').text(pH7LangIM.cannot_chat_yourself).fadeOut(5000);
        }
    },

    createBox: function (sBoxTitle, iMinimizeBox) {
        if (!this._check(sBoxTitle)) return;

        if ($("#chatbox_" + sBoxTitle).length > 0) {
            if ($("#chatbox_" + sBoxTitle).css('display') == 'none') {
                $("#chatbox_" + sBoxTitle).css('display', 'block');
                this.restructureBoxes();
            }
            $("#chatbox_" + sBoxTitle + " .chatboxtextarea").focus();
            return;
        }

        $("<div />")
            .attr("id", "chatbox_" + sBoxTitle)
            .addClass("chatbox")
            .html('<div class="chatboxhead"><div class="chatboxtitle"></div><div class="chatboxoptions"><a href="javascript:void(0)" onclick="Messenger.toggleBoxGrowth(\'' + sBoxTitle + '\')">-</a> <a href="javascript:void(0)" onclick="Messenger.closeBox(\'' + sBoxTitle + '\')">X</a></div><br clear="all"/></div><div class="chatboxcontent"></div><div class="chatboxinput"><textarea class="chatboxtextarea" onkeydown="return Messenger.checkBoxInputKey(event,this,\'' + sBoxTitle + '\');"></textarea></div>')
            .appendTo($("body"));

        $("#chatbox_" + sBoxTitle).css('bottom', '0px');

        $.post(pH7Url.base + 'user/asset/ajax/Api', {type: 'profile_link', param: sBoxTitle}, function (sUserLink) {
            $("#chatbox_" + sBoxTitle + ' .chatboxhead .chatboxtitle').html('<a href="' + sUserLink + '" target="_blank">' + sBoxTitle + '</a>');
        });

        iBoxesLength = 0;

        for (x in this.aBoxes) {
            if ($("#chatbox_" + this.aBoxes[x]).css('display') != 'none')
                ++iBoxesLength;
        }

        if (iBoxesLength == 0) {
            $("#chatbox_" + sBoxTitle).css('right', '20px');
        }
        else {
            iWidth = (iBoxesLength) * (225 + 7) + 20;
            $("#chatbox_" + sBoxTitle).css('right', iWidth + 'px');
        }

        this.aBoxes.push(sBoxTitle);

        if (iMinimizeBox == 1) {
            if ($.cookie('chatbox_minimized')) {
                this.aMinimizedBoxes = $.cookie('chatbox_minimized').split(/\|/);
            }

            var iMinimize = 0;
            for (j = 0; j < this.aMinimizedBoxes.length; j++) {
                if (this.aMinimizedBoxes[j] == sBoxTitle) {
                    iMinimize = 1;
                }
            }

            if (iMinimize == 1) {
                $('#chatbox_' + sBoxTitle + ' .chatboxcontent').css('display', 'none');
                $('#chatbox_' + sBoxTitle + ' .chatboxinput').css('display', 'none');
            }
        }

        this.aBoxFocus[sBoxTitle] = false;

        $("#chatbox_" + sBoxTitle + " .chatboxtextarea").blur(function () {
            oMe.aBoxFocus[sBoxTitle] = false;
            $("#chatbox_" + sBoxTitle + " .chatboxtextarea").removeClass('chatboxtextareaselected');
        }).focus(function () {
            oMe.aBoxFocus[sBoxTitle] = true;
            oMe.aNewMessages[sBoxTitle] = false;
            $('#chatbox_' + sBoxTitle + ' .chatboxhead')
                .removeClass('chatboxblink');
            $("#chatbox_" + sBoxTitle + " .chatboxtextarea")
                .addClass('chatboxtextareaselected');
        });

        $("#chatbox_" + sBoxTitle).click(function () {
            if ($('#chatbox_' + sBoxTitle + ' .chatboxcontent').css('display') != 'none') {
                $("#chatbox_" + sBoxTitle + " .chatboxtextarea").focus();
            }
        });

        $("#chatbox_" + sBoxTitle).show();
    },
    heartbeat: function () {
        var iItemsFound = 0;

        if (bWindowFocus == false) {
            var iBlinkNumber = 0, iTitleChanged = 0;

            for (x in this.aNewMessagesWin) {
                if (this.aNewMessagesWin[x] == true) {
                    ++iBlinkNumber;
                    if (iBlinkNumber >= this.iBlinkOrder) {
                        document.title = x + ' ' + pH7LangIM.say;
                        iTitleChanged = 1;
                        break;
                    }
                }
            }

            if (iTitleChanged == 0) {
                document.title = sOriginalTitle;
                this.iBlinkOrder = 0;
            }
            else {
                ++this.iBlinkOrder;
            }

        }
        else {
            for (x in this.aNewMessagesWin)
                this.aNewMessagesWin[x] = false;
        }

        for (x in this.aNewMessages) {
            if (this.aNewMessages[x] == true) {
                if (this.aBoxFocus[x] == false) {
                    oMe.soundAlert();
                    //TODO: Add toggle all or none policy, otherwise it looks awkward.
                    $('#chatbox_' + x + ' .chatboxhead').toggleClass('chatboxblink');
                }
            }
        }

        $.ajax(
            {
                url: pH7Url.base + "im/asset/ajax/Messenger/?act=heartbeat",
                type: 'POST',
                cache: false,
                dataType: "json",

                success: function (oData) {
                    $.each(oData.items, function (i, oItem) {
                        oMe.sBoxTitle = oItem.user;

                        if ($("#chatbox_" + oMe.sBoxTitle).length <= 0)
                            oMe.createBox(oMe.sBoxTitle);

                        if ($("#chatbox_" + oMe.sBoxTitle).css('display') == 'none') {
                            $("#chatbox_" + oMe.sBoxTitle).css('display', 'block');
                            oMe.restructureBoxes();
                        }

                        if (oItem.status == 1) {
                            oItem.user = oMe.sUsername;
                        }

                        if (oItem.status == 2) {
                            $("#chatbox_" + oMe.sBoxTitle + " .chatboxcontent").append('<div class="chatboxmessage"><span class="chatboxinfo">' + oItem.msg + '</span></div>');
                        }
                        else {
                            oMe.aNewMessages[oMe.sBoxTitle] = true;
                            oMe.aNewMessagesWin[oMe.sBoxTitle] = true;
                            $("#chatbox_" + oMe.sBoxTitle + " .chatboxcontent").append('<div class="chatboxmessage"><span class="chatboxmessagefrom">' + oItem.user + ':&nbsp;&nbsp;</span><span class="chatboxmessagecontent">' + oItem.msg + '</span></div>');
                        }

                        $("#chatbox_" + oMe.sBoxTitle + " .chatboxcontent").scrollTop($("#chatbox_" + oMe.sBoxTitle + " .chatboxcontent")[0].scrollHeight);
                        iItemsFound += 1;
                    });

                    ++oMe.iHeartbeatCount;

                    if (iItemsFound > 0) {
                        oMe.iHeartbeatTime = oMe.iMinHeartbeat;
                        oMe.iHeartbeatCount = 1;
                    }
                    else if (oMe.iHeartbeatCount >= 10) {
                        oMe.iHeartbeatTime *= 2;
                        oMe.iHeartbeatCount = 1;
                        if (oMe.iHeartbeatTime > oMe.iMaxHeartbeat) {
                            oMe.iHeartbeatTime = oMe.iMaxHeartbeat;
                        }
                    }

                    setTimeout(function () {
                        oMe.heartbeat()
                    }, oMe.iHeartbeatTime);
                }
            });
    },

    closeBox: function (sBoxTitle) {
        $('#chatbox_' + sBoxTitle).css('display', 'none');
        this.restructureBoxes();

        $.post(pH7Url.base + "im/asset/ajax/Messenger/?act=close", {box: sBoxTitle});
    },

    toggleBoxGrowth: function (sBoxTitle) {
        if ($('#chatbox_' + sBoxTitle + ' .chatboxcontent').css('display') == 'none') {
            if ($.cookie('chatbox_minimized'))
                this.aMinimizedBoxes = $.cookie('chatbox_minimized').split(/\|/);

            var sNewCookie = '';

            for (i = 0; i < this.aMinimizedBoxes.length; i++) {
                if (this.aMinimizedBoxes[i] != sBoxTitle) {
                    sNewCookie += sBoxTitle + '|';
                }
            }

            sNewCookie = sNewCookie.slice(0, -1);


            $.cookie('chatbox_minimized', sNewCookie);
            $('#chatbox_' + sBoxTitle + ' .chatboxcontent').css('display', 'block');
            $('#chatbox_' + sBoxTitle + ' .chatboxinput').css('display', 'block');
            $("#chatbox_" + sBoxTitle + " .chatboxcontent")
                .scrollTop($("#chatbox_" + sBoxTitle + " .chatboxcontent")[0].scrollHeight);
        }
        else {
            var sNewCookie = sBoxTitle;

            if ($.cookie('chatbox_minimized'))
                sNewCookie += '|' + $.cookie('chatbox_minimized');

            $.cookie('chatbox_minimized', sNewCookie);
            $('#chatbox_' + sBoxTitle + ' .chatboxcontent').css('display', 'none');
            $('#chatbox_' + sBoxTitle + ' .chatboxinput').css('display', 'none');
        }
    },

    checkBoxInputKey: function (oEvent, oBoxTextarea, sBoxTitle) {
        if (oEvent.keyCode == 13 && oEvent.shiftKey == 0) {
            this.sMessage = $(oBoxTextarea).val();
            this.sMessage = this.sMessage.replace(/^\s+|\s+$/g, "");

            $(oBoxTextarea).val('');
            $(oBoxTextarea).focus();
            $(oBoxTextarea).css('height', '44px');
            if (this.sMessage != '') {
                $.post(pH7Url.base + "im/asset/ajax/Messenger/?act=send", {
                    to: sBoxTitle,
                    message: this.sMessage
                }, function (oData) {
                    oMe.sMessage = oMe.sMessage.replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/\"/g, "&quot;");
                    $("#chatbox_" + sBoxTitle + " .chatboxcontent")
                        .append('<div class="chatboxmessage"><span class="chatboxmessagefrom">' + oData.user + ':&nbsp;&nbsp;</span><span class="chatboxmessagecontent">' + oData.msg + '</span></div>');
                    $("#chatbox_" + sBoxTitle + " .chatboxcontent").scrollTop($("#chatbox_" + sBoxTitle + " .chatboxcontent")[0].scrollHeight);
                });
            }
            this.iHeartbeatTime = this.iMinHeartbeat;
            this.iHeartbeatCount = 1;

            return false;
        }

        var iAdjustedHeight = oBoxTextarea.clientHeight;
        var iMaxHeight = 94;

        if (iMaxHeight > iAdjustedHeight) {
            iAdjustedHeight = Math.max(oBoxTextarea.scrollHeight, iAdjustedHeight);
            if (iMaxHeight) iAdjustedHeight = Math.min(iMaxHeight, iAdjustedHeight);
            if (iAdjustedHeight > oBoxTextarea.clientHeight) $(oBoxTextarea).css('height', iAdjustedHeight + 8 + 'px');
        }
        else {
            $(oBoxTextarea).css('overflow', 'auto');
        }
    },

    startSession: function () {
        $.ajax(
            {
                url: pH7Url.base + "im/asset/ajax/Messenger/?act=startsession",
                type: 'POST',
                cache: false,
                dataType: "json",
                success: function (oData) {
                    oMe.sUsername = oData.user;

                    $.each(oData.items, function (i, oItem) {
                        oMe.sBoxTitle = oItem.user;

                        if ($("#chatbox_" + oMe.sBoxTitle).length <= 0) {
                            oMe.createBox(oMe.sBoxTitle, 1);
                        }

                        if (oItem.status == 1) {
                            oItem.user = oMe.sUsername;
                        }

                        if (oItem.status == 2) {
                            $("#chatbox_" + oMe.sBoxTitle + " .chatboxcontent").append('<div class="chatboxmessage"><span class="chatboxinfo">' + oItem.msg + '</span></div>');
                        }
                        else {
                            $("#chatbox_" + oMe.sBoxTitle + " .chatboxcontent").append('<div class="chatboxmessage"><span class="chatboxmessagefrom">' + oItem.user + ':&nbsp;&nbsp;</span><span class="chatboxmessagecontent">' + oItem.msg + '</span></div>');
                        }
                    });

                    for (i = 0; i < oMe.aBoxes.length; i++) {
                        oMe.sBoxTitle = oMe.aBoxes[i];
                        $("#chatbox_" + oMe.sBoxTitle + " .chatboxcontent").scrollTop($("#chatbox_" + oMe.sBoxTitle + " .chatboxcontent")[0].scrollHeight);
                    }

                    setTimeout(function () {
                        oMe.heartbeat()
                    }, oMe.iHeartbeatTime);
                }
            });
    },

    soundAlert: function () {
        $.sound.play(pH7Url.stic + 'sound/purr.mp3');
    },

    _check: function (sUser) {
        if (sUser == this.sUsername) {
            return false;
        }
        return true;
    }

};

$(document).ready(function () {
    sOriginalTitle = document.title;

    Messenger.Messenger().startSession();

    $([window, document]).blur(function () {
        bWindowFocus = false;
    }).focus(function () {
        bWindowFocus = true;
        document.title = sOriginalTitle;
    });
});
