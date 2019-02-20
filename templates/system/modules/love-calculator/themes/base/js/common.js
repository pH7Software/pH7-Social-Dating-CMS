/*
 * Author:        Pierre-Henry Soria <ph7software@gmail.com>
 * Copyright:     (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * License:       GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 */

function loveCounter(iNumber, iMax) {
    if (iNumber < iMax) {
        iNumber++;
        $('.heart span').text(iNumber);
        var iSizeTxt = (iNumber > 10 ? iNumber : 10);
        $('.love_txt').css('font-size', iSizeTxt);
        setTimeout('loveCounter(' + iNumber + ',' + iMax + ')', 100);
    }
}
