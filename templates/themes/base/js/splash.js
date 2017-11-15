/*
 * Author:        Pierre-Henry Soria <ph7software@gmail.com>
 * Copyright:     (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * License:       GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 */

// Set Default State of each portfolio piece
$(".paging").show();
$(".paging a:first").addClass("active");

// Get size of images, how many there are, then determine the size of the image reel.
var iImgWidth = $(".img_reel").width();
var iImgSum = $(".img_reel img").size();
var iImgReelWidth = iImgWidth * iImgSum;

// Adjust the image reel to its new size
$(".img_reel").css({'width': iImgReelWidth});

// Paging + Slider Function
rotate = function () {
    var iTriggerId = $active.attr("rel") - 1; // Get number of times to slide
    var iImgRealPosition = iTriggerId * iImgWidth; // Determines the distance the image reel needs to slide

    $(".paging a").removeClass('active'); // Remove all active class
    $active.addClass('active'); // Add active class (the $active is declared in the rotateSwitch function)

    //Slider Animation
    $(".img_reel").animate({
        left: -iImgRealPosition
    }, 500);

};

// Rotation + Timing Event
rotateSwitch = function () {
    play = setInterval(function () { // Set timer - this will repeat itself every 3 seconds
        $active = $('.paging a.active').next();
        if ($active.length === 0) { // If paging reaches the end...
            $active = $('.paging a:first'); //go back to first
        }
        rotate(); // Trigger the paging and slider function
    }, 7000); // Timer speed in milliseconds (3 seconds)
};

rotateSwitch(); // Run function on launch

//On Hover
$(".img_reel a").hover(function () {
        clearInterval(play); // Stop the rotation
    }, function () {
        rotateSwitch(); // Resume rotation
    }
);

// On Click
$(".paging a").click(function () {
    $active = $(this); // Activate the clicked paging
    // Reset Timer
    clearInterval(play); // Stop the rotation
    rotate(); // Trigger rotation immediately
    rotateSwitch(); // Resume rotation
    return false; // Prevent browser jump to link anchor
});
