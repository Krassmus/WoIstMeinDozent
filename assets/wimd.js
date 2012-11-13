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
function showdetails(id) {
    $.ajax({
      type: "POST",
      url: "/plugins_packages/neo/dozentenplan/ajax.php",
      data: { cmd: "renderDetails", id: id }
    }).done(function(data) {
            $('#neo_termin_details').html(data)
    });


$('#neo_termin_details').dialog({
               show: "slide",
               hide: "slide",
               modal: true,
               minWidth: 800,
               buttons: {
                   'Ok': function() {
                       $(this).dialog("close");
                   }
               }
        });
}

$(document).ready(function(){
    $( "#datepicker" ).datepicker();
});