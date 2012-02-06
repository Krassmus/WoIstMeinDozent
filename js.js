jQuery(window).resize(function () {
    if (parseInt(jQuery(window).width(), 10) < 600) {
        jQuery("#barTopStudip").hide();
    } else {
        jQuery("#barTopStudip").show();
    }
});
jQuery(function () {
    jQuery(window).trigger("resize");
});