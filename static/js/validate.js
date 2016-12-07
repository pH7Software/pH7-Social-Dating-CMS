/*!
 * Author:        Pierre-Henry Soria <ph7software@gmail.com>
 * Copyright:     (c) 2012-2016, Pierre-Henry Soria. All Rights Reserved.
 * License:       GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 */

/**
 * Check if it is a valid email address with a RegEx pattern.
 *
 * @return {Void}
 */
function checkMail()
{
    var sReg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
    if(sReg.test($("#email").val()) == false)
        $("#email").css("border", "solid #cc0000 1px");
    else
        $("#email").css("border", "solid #00cc00 1px");
}

/**
 * Calculate the level of password.
 *
 * @param {String} Password.
 * @return {Void}
 */
function checkPassword(sPwd)
{
    var iPwdLength = sPwd.length, iValSecure = 0;

    if(iPwdLength<6 || iPwdLength>60)
        $("#password").css("border", "solid #cc0000 1px");
    else
        $("#password").css("border", "solid #00cc00 1px");

    // PASSWORD LENGTH

    if(iPwdLength<6)
        iValSecure++;
    else if(iPwdLength<10)
        iValSecure+=2;
    else if(iPwdLength<15)
        iValSecure+=3;
    else if(iPwdLength<20)
        iValSecure+=4;
    else if(iPwdLength<25)
        iValSecure+=5;
    else if(iPwdLength>=25)
        iValSecure+=6;

    /** LETTERS (Not exactly implemented as dictacted above because of my limited understanding of RegEx) **/

    // [verified] at least one lower case letter
    if(sPwd.match(/[a-z]/))
        iValSecure++;

    // [verified] at least one upper case letter
    if(sPwd.match(/[A-Z]/))
        iValSecure++;

    /** NUMBERS **/

    // [verified] at least one number
    if(sPwd.match(/\d+/))
        iValSecure++;

    // [verified] at least three numbers
    if(sPwd.match(/(.*[0-9].*[0-9].*[0-9])/))
        iValSecure++;

    /** COMBOS **/

    // [verified] both upper and lower case
    if(sPwd.match(/([a-z].*[A-Z])|([A-Z].*[a-z])/))
         iValSecure+=3;

    // [FAILED] both letters and numbers, almost works because an additional character is required
    if(sPwd.match(/(\d.*\D)|(\D.*\d)/))
        iValSecure+=3;

    // [verified] letters, numbers, and special characters
    if(sPwd.match(/([a-zA-Z0-9].*[!,@,#,$,%,^,&,*,?,_,~])|([!,@,#,$,%,^,&,*,?,_,~].*[a-zA-Z0-9])/))
        iValSecure+=2;


    /** SPECIAL CHAR **/

    // [verified] at least one special character
    if(sPwd.match(/.[!,@,#,$,%,^,&,*,?,_,~]/))
        iValSecure+=2;

    // [verified] at least two special characters
    if(sPwd.match(/(.*[!,@,#,$,%,^,&,*,?,_,~].*[!,@,#,$,%,^,&,*,?,_,~])/))
        iValSecure+=2;

    // CHECK LEVEL
    if(iValSecure <4)
    {
        $('.password').css("color", "#F55");
        $('.password').text(pH7LangCore.very_weak + ' (' + pH7LangCore.level + iValSecure + ')');
    }
    else if(iValSecure <8)
    {
        $('.password').css("color", "#F55");
        $('.password').text(pH7LangCore.weak + ' (' + pH7LangCore.level + iValSecure + ')');
    }
    else if(iValSecure <12)
    {
        $('.password').css("color", "#F55");
        $('.password').text(pH7LangCore.average + ' (' + pH7LangCore.level + iValSecure + ')');
    }
    else if(iValSecure <16)
    {
        $('.password').css("color", "#149541");
        $('.password').text(pH7LangCore.strong + ' (' + pH7LangCore.level + iValSecure + ')');
    }
    else if(iValSecure <20)
    {
        $('.password').css("color", "#149541");
        $('.password').text(pH7LangCore.very_strong + ' (' + pH7LangCore.level + iValSecure + ')');
    }
    else if(iValSecure >=20)
    {
        $('.password').css("color", "#149541");
        $('.password').text(pH7LangCore.very_very_string + ' (' + pH7LangCore.level + iValSecure + ')');
    }
}

/**
 * @param {String} Value.
 * @param {String} Field ID.
 * @param {String} [extra=false] Argument 1. Default: 4
 * @param {String} [extra=false] Argument 2. Default: 2000
 * @return {Void}
 */
function CValid(sInputVal, sFieldId, sParam1, sParam2)
{
    // Default Values
    if(typeof sParam1 === "undefined") var sParam1 = 4;
    if(typeof sParam2 === "undefined") var sParam2 = 2000;
    $('.' + sFieldId).hide();

    // The data to be send to the server via POST
    var sData = 'inputVal=' + sInputVal + '&fieldId=' + sFieldId + '&param1=' + sParam1 + '&param2=' + sParam2;
    $.post(pH7Url.base + 'asset/ajax/Validate', sData, function(oData)
    {
        var sMsg = oData.msg, sField = oData.fieldId;
        if(oData.status == 1)
        {
            $('.' + sFieldId).fadeIn();
            $('#' + sField).css("border", "solid #00cc00 1px");
            $('.' + sField).css("color", "#149541");
            if(typeof sMsg !== "undefined") $('.' + sField).text(sMsg);
        }
        else
        {
            $('.' + sFieldId).fadeIn();
            $('#' + sField).css("border", "1px solid #cc0000");
            $('.' + sField).css("color", "#F55");
            if(typeof sMsg !== "undefined") $('.' + sField).text(sMsg.substring(0,150));
        }
    }, 'json');
}
