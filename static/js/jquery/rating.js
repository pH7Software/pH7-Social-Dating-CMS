/************************************************************************
 *************************************************************************
 pHS pH Star

 @Name:           jRating - jQuery Plugin (modified in pHRating by PH Soria)
 @Revison:        2.2
 @Date:           26/01/2011
 @Author:         ALPIXEL - (www.myjqueryplugins.com - www.alpixel.fr)
 @License:        Open Source - MIT License : http://www.opensource.org/licenses/mit-license.php
 @modified:       This code has been modified by PH (Pierre-Henry Soria).

 **************************************************************************
 *************************************************************************/
(function ($) {
    $.fn.pHRating = function (op) {
        var defaults = {
            /** String vars **/
            url: pH7Url.base + 'asset/ajax/Rating/',
            bigStarUrl: pH7Url.stic + 'img/icon/m-star.png',
            smallStarUrl: pH7Url.stic + 'img/icon/s-star.png',
            type: 'big', // can be set to 'small' or 'big'

            /** Boolean vars **/
            step: false, // if true,  mouseover binded star by star,
            isDisabled: false,
            showRateInfo: true,

            /** Integer vars **/
            length: 5, // number of star to display
            decimalLength: 0, // number of decimals.. Max 3, but you can complete the function 'getNote'
            rateMax: 20, // maximal rate - integer from 0 to 9999 (or more)
            rateInfosX: -45, // relative position in X axis of the info box when mouseover
            rateInfosY: 5, // relative position in Y axis of the info box when mouseover

            /** Functions **/
            onSuccess: null,
            onError: null
        };

        if (this.length > 0)
            return this.each(function () {
                var opts = $.extend(defaults, op),
                    newWidth = 0,
                    starWidth = 0,
                    starHeight = 0,
                    bgUrl = '';

                if ($(this).hasClass('jDisabled') || opts.isDisabled)
                    var jDisabled = true;
                else
                    var jDisabled = false;

                getStarWidth();
                $(this).height(starHeight);

                var average = parseFloat($(this).attr('id').split('_')[0]),
                    id = parseInt($(this).attr('id').split('_')[1]), // get the id of the box for database
                    table = $(this).attr('id').split('_')[2], // Table
                    widthRatingContainer = starWidth * opts.length, // Width of the Container
                    widthColor = average / opts.rateMax * widthRatingContainer, // Width of the color Container

                    quotient =
                        $('<div>',
                            {
                                'class': 'pHRatingColor',
                                css: {
                                    width: widthColor
                                }
                            }).appendTo($(this)),

                    average =
                        $('<div>',
                            {
                                'class': 'pHRatingAverage',
                                css: {
                                    width: 0,
                                    top: -starHeight
                                }
                            }).appendTo($(this)),

                    pHStar =
                        $('<div>',
                            {
                                'class': 'pHStar',
                                css: {
                                    width: widthRatingContainer,
                                    height: starHeight,
                                    top: -(starHeight * 2),
                                    background: 'url(' + bgUrl + ') repeat-x'
                                }
                            }).appendTo($(this));

                $(this).css({width: widthRatingContainer, overflow: 'hidden', zIndex: 1, position: 'relative'});

                if (!jDisabled)
                    $(this).bind({
                        mouseenter: function (e) {
                            var realOffsetLeft = findRealLeft(this);
                            var relativeX = e.pageX - realOffsetLeft;
                            if (opts.showRateInfo)
                                var tooltip =

                                    $('<p>', {
                                        'class': 'pHRatingInfo',
                                        html: getNote(relativeX) + ' <span class="maxRate">/ ' + opts.rateMax + '</span>',
                                        css: {
                                            top: (e.pageY + opts.rateInfosY),
                                            left: (e.pageX + opts.rateInfosX)
                                        }
                                    }).appendTo('body').show();
                        },
                        mouseover: function (e) {
                            $(this).css('cursor', 'pointer');
                        },
                        mouseout: function () {
                            $(this).css('cursor', 'default');
                            average.width(0);
                        },
                        mousemove: function (e) {
                            var realOffsetLeft = findRealLeft(this);
                            var relativeX = e.pageX - realOffsetLeft;
                            if (opts.step) newWidth = Math.floor(relativeX / starWidth) * starWidth + starWidth;
                            else newWidth = relativeX;
                            average.width(newWidth);
                            if (opts.showRateInfo)
                                $("p.pHRatingInfo")
                                    .css({
                                        left: (e.pageX + opts.rateInfosX)
                                    })
                                    .html(getNote(newWidth) + ' <span class="maxRate">/ ' + opts.rateMax + '</span>');
                        },
                        mouseleave: function () {
                            $("p.pHRatingInfo").remove();
                        },
                        click: function (e) {
                            $(this).unbind().css('cursor', 'default').addClass('jDisabled');
                            if (opts.showRateInfo) $("p.pHRatingInfo").fadeOut('fast', function () {
                                $(this).remove();
                            });
                            e.preventDefault();
                            var rate = getNote(newWidth);
                            average.width(newWidth);

                            $.post(opts.url, {
                                    id: id,
                                    table: table,
                                    score: rate,
                                    action: 'rating'
                                },
                                function (data) {
                                    var txt_class = '.pHS' + id + table + '_txt';

                                    if (data.status == 1)
                                        $(txt_class).html(data.txt).show('slow');
                                    else
                                        $(txt_class).addClass('alert alert-danger').html(data.txt).delay(3000).fadeOut();
                                },
                                'json'
                            );
                        }
                    });

                function getNote(relativeX) {
                    var noteBrut = parseFloat((relativeX * 100 / widthRatingContainer) * opts.rateMax / 100);
                    switch (opts.decimalLength) {
                        case 1 :
                            var note = Math.round(noteBrut * 10) / 10;
                            break;
                        case 2 :
                            var note = Math.round(noteBrut * 100) / 100;
                            break;
                        case 3 :
                            var note = Math.round(noteBrut * 1000) / 1000;
                            break;
                        default :
                            var note = Math.round(noteBrut * 1) / 1;
                    }
                    return note;
                };

                function getStarWidth() {
                    switch (opts.type) {
                        case 'small' :
                            starWidth = 12; // width of the picture small.png
                            starHeight = 10; // height of the picture small.png
                            bgUrl = opts.smallStarUrl;
                            break;
                        default :
                            starWidth = 23; // width of the picture m-star.png
                            starHeight = 20; // height of the picture m-star.png
                            bgUrl = opts.bigStarUrl;
                    }
                };

                function findRealLeft(obj) {
                    if (!obj) return 0;
                    return obj.offsetLeft + findRealLeft(obj.offsetParent);
                };
            });

    }
})(jQuery);
