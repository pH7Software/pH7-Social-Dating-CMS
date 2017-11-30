/*
 * Title:           AjPH
 * Description:     Loading pages in Ajax with HTML5 pushState.
 *
 * Author:          Pierre-Henry Soria <ph7software@gmail.com>
 * Copyright:       (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * License:         GNU General Public License <http://www.gnu.org/licenses/gpl.html>
 * Version:         1.3
 */

$(document).ready(function () {
    if (window.history && history.pushState) {
        bHistoryEdited = false;
        $(window).bind('popstate', function () {
            if (bHistoryEdited) {
                loadPage(location.pathname + location.search);
            }
        });
        doPager();
    }
});

function doPager() {
    // Loading the ajax pages
    $('a[data-load=ajax]').click(function (oE) {
        oE.preventDefault();
        $('#ajph').html("<div id='loading'>Loading...</div>");
        loadPage($(this).attr('href'));
        history.pushState(null, null, $(this).attr('href'));
        bHistoryEdited = true;
    });
}

/**
 * Load page.
 *
 * @param {String} Link
 * @return {Void}
 */
function loadPage(sLink) {
    $.ajax({
        url: sLink,
        processData: true,
        dataType: 'html',
        success: function (oData) {
            var oContent = $(oData).find("#sub_ajph"); // Get the new Contents
            var oTitle = $(oData).filter('title'); // Get the new Title tag
            var oHeadings = $(oData).find('#lang'); // Get the Lang Menu
            $('title').text(oTitle.text()); // Set Title
            $('#lang').html(oHeadings.html()); // Set the Lang Menu
            $('#ajph').fadeOut(200, function () {
                $(this).html(oContent.html()).fadeIn(200); // Set Contents
            });
        }
    });
    doPager();
}
