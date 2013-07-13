$(document).ready(function(){
    //var html;
    $('#index_pic_div').append("<div class='sprechblase links pfeillinksoben'> " +
        "<p>Sie haben eine gute Idee oder ärgern sich über etwas, aber keine Zeit, dies in ein Gremium einzubringen? Dann sind Sie hier richtig! </p>" +
        "<p>Es ist uns wichtig, dass Sie als Studierende die Hochschule mitgestalten, sei es im Gespräch mit den Lehrenden und Mitarbeiterinnen direkt oder in Gremien wie Studienkommission, Fakultätsrat, Senat. Ergänzend möchten wir eine weitere Möglichkeit eröffnen, Verbesserungsvorschläge einzubringen. </p> " +
        "<p>Sie können Ihre Vorschläge auf diesem Weg bei uns im Qualitätsmanagement entweder unter Angabe Ihres Namens oder anonym einbringen. Die Vorschläge werden im QM-Team auf Umsetzbarkeit geprüft und an die entsprechende Stelle weitergeleitet. Wenn Sie Ihren Namen angegeben haben, erhalten Sie in jedem Fall ein Feedback über den Stand.</p> " +
        "<form class='neo_Form' action='plugins.php/neolook/sendmail' method='post'>"+
            "<div><label for='betreff'>Betreff:</label><br />" +
            "<input type='text' name='Betreff' id='betreff' value='' /></div>"+
            "<div><br /><label for='content'>Inhalt:</label><br />" +
            "<textarea cols='32' rows='7' name='content' id='content'>" +
                "..." +
            "</textarea></p>"+
            "<label for='sendAnonym'>Nachricht Anonym versenden</label><br />"+
            "<div id='radio'><input type='radio' id='ja' name='radio' /><label for='ja'>Ja</label>"+
            "<input type='radio' id='nein' name='radio' checked='checked' /><label for='nein'>Nein</label></div>"+
            "<div><br/><input type='submit' name='submit' id='Lobsubmit' value='Absenden' /></div>"+
        "</form>" +
        "</div>");

    $('#Lobsubmit').button();

    $('#radio').buttonset();


    $('.sprechblase').toggle();

    $('#index_head').append("<img style='' alt='' src='assets/images/icons/16/white/arr_2left.png' class='index_head_status'>");
    $('.index_head_status').toggle();
    $('#index_head').append("<img style='' alt='' src='assets/images/icons/16/white/arr_2down.png' class='index_head_status'>");

   if ($('.index_container_plugin').text() == "") {
        $('this').toggle();
    }

    $('#index_head').click(function() {
        $('#index_navigation').toggle();
        $('#index_pic_div').toggle();
        $('.index_head_status').toggle();
    });

    $('#kummerkasten_bild').click(function() {
        $('.sprechblase').toggle();
    });
});