/**
 * Created by JetBrains PhpStorm.
 * User: johannesstichler
 * Date: 22.11.11
 * Time: 14:58
 * To change this template use File | Settings | File Templates.
 */

function delmodul(vlid, modulid)
{
    $.getJSON('/plugins_packages/neo/neoimporter/ajax.php', {
                    cmd: 'delVlModul',
                    modid: modulid,
                    vlid: vlid
                    }, function(data){
    });
    getVlModule(vlid);
}


function getVlModule(vlid) {
    $("#edit_module_all").empty();
    var html;
    $.getJSON('/plugins_packages/neo/neoimporter/ajax.php', {
            cmd: 'getVlModule',
            id:vlid
        },function(module){
            html = "<table width='100%'><tr><td width='20px'>Modulname</td><td width='20px'>L&ouml;schen</td></tr>"
            for(x in module)
            {
                modul = module[x];

                if(modul["id"] != "" & modul["name"] != null & modul["name"] != undefined)
                {
                    //alert(modul["name"]);
                    html = html + "<tr>" + "<td>" + modul["name"]  + "</td>"+"<td><a href='#' ><img src='../../../plugins_packages/neo/neoimporter/assets/icons/delete.png' class='show_modul_delete' onclick='delmodul(&quot;"+ vlid +"&quot;,&quot;"+ modul["id"] +"&quot;)'></a></td>"  ;
                }
            }
        html = html + "</table>";
        $('#edit_module_moduleDerVl').html(html);

        });
}

function getModule (uid){
    $("#edit_module_all").empty();
    $.getJSON('/plugins_packages/neo/neoimporter/ajax.php', {
        cmd: 'getModule',
        id:uid
    },function(module){
        for(x in module)
        {
            modul = module[x];
            if(modul["name"] != 0)   $("<option/>").val(modul["sem_tree_id"]).text(modul["name"]).appendTo("#edit_module_all");
        }
    });

}

function setModul(neu, heimat) {
    $.getJSON('/plugins_packages/neo/neoimporter/ajax.php', {
            cmd: 'setModul',
            id: $("#edit_modul_uid").val(),
            modulid: $("#edit_module_all").val(),
            neu: neu,
            heimat: heimat
        },function(module){

        });
}

function newModul (heimat) {
    $.getJSON('/plugins_packages/neo/neoimporter/ajax.php', {
               cmd: 'newModul',
               id: $("#edit_modul_uid").val(),
               name: $("#edit_modul_neu_heimat_name").val(),
               heimat: heimat
           },function(module){

           });
}

function colorTabele()
{
    $('#show_table tr:odd').css('background-color','#FFFFF');
    $('#show_table tr:even').css('background-color','#fcf4e9');
    $('#show_table tr:first').css('background-color','#f3ae00');

}

function delDozent(id, userid)
{
    $.getJSON('/plugins_packages/neo/neoimporter/ajax.php', {
                    cmd: 'delDozent',
                    id: id,
                    userid: userid
                    }, function(data){
    });
    getDozent(id);
}

function delVl(id) {
    $('#edit_sicherheitsabfrage_vl').dialog({
                minWidth: 400,
                modal: true,
                buttons: {
                    "Ok": function() {
                    $("#show_"+id).toggle();
                    $.getJSON('/plugins_packages/neo/neoimporter/ajax.php', {
                                cmd: 'delVl',
                                id: id
                                }, function(data){
                        });
                     $(this).dialog("close");
                    },
                    "Abbrechen": function() {
                    $(this).dialog("close");
                    }
                }
    });

}


function getDozent(id) {
    $.getJSON('/plugins_packages/neo/neoimporter/ajax.php', {
                cmd: 'getDozent',
                id: id
                }, function(data){
                var html;
                html = "<table width='100%'><tr><td width='20px'>Vorname</td><td width='20px'>Nachname</td><td width='20px'>L&ouml;schen</td></tr>"
                    for(x in data)
                    {
                        dozent = data[x];
                        //alert(id+" "+dozent["user_id"]);
                        var nochntest = '"'+dozent["user_id"]+'"';
                        html = html + '<tr>' + '<td>' + dozent["Vorname"] + '</td>'+ '<td>' + dozent["Nachname"] + '</td>'+ '<td><a href="#" ><img onclick="delDozent(&quot;'+ id +'&quot;,&quot;'+ dozent["user_id"] +'&quot;)" src="../../../plugins_packages/neo/neoimporter/assets/icons/delete.png" class="show_vl_delete" ></a></td>'  ;
                    }
                html = html + "</table>";
             $('#dozenten_der_vl').html(html);
            });
}

function toggleEdit(id) {
    //id = '#edit_' + id;
    //$(id).toggle('slow');
    $('#edit_uid').val(id);
    $('#edit_dozent_uid').val(id);
    $('#edit_modul_uid').val(id);
    $('#edit_sicherheitsabfrage_vl_uid').val(id);
    $.getJSON('/plugins_packages/neo/neoimporter/ajax.php', {cmd: 'getVLData',
                                                             id: id}, function(data){
        $('#edit_vl_nr').val(data["vl_nr"]);
        $('#edit_vl_name').val(data["vl_name"]);
        $("#edit_vl_art option[value='"+ data["art"] +"']").attr('selected',true);
        $("#edit_vl_heimat option[value='"+ data["heimatsemester"] +"']").attr('selected',true);
        $('#edit_vl_sws').val(data["sws"]);
        $('#edit_vl_verName').val(data["verantwortlicher"]);

    });
    $('#edit_form').dialog({
                show: "slide",
                hide: "slide",
                modal: true,
                minWidth: 800,
                buttons: {
                    "<< Dozenten bearbeiten": function() {
                        getDozent(id);
                        $(this).dialog("close");
                        $('#dozent_id_1_realvalue').val("");
                        $('#dozent_id_1').val("");
                        $('#edit_dozenten').dialog({
                                        show: "slide",
                                        hide: "slide",
                                        minWidth: 600,
                                        modal: true,
                                        buttons: {
                                            "Ok": function() {
                                            $(this).dialog("close");
                                            toggleEdit(id);
                                            }
                                        }
                        });
                    },
                    "Speichern": function() {
                        $.getJSON('/plugins_packages/neo/neoimporter/ajax.php', {
                            cmd: 'setVL',
                            id: id,
                            nr: $('#edit_vl_nr').val(),
                            name: encodeURIComponent($('#edit_vl_name').val()),
                            art: $('#edit_vl_art').val(),
                            sws: $('#edit_vl_sws').val(),
                            heimat: $('#edit_vl_heimat').val(),
                            verantwortlicher:$('#edit_vl_verName').val()
                        }, function($data){
                           if($data["status"] == "Ok") {
                                window.location.href = "show";
                            }
                        });
                        $(this).dialog("close");


                    },
                    "Abbrechen": function() {
                        $(this).dialog("close");
                    },
                    "Module bearbeiten >> ": function() {
                        $(this).dialog("close");
                        $.getJSON('/plugins_packages/neo/neoimporter/ajax.php', {
                            cmd: 'getVLData',
                            id:id
                        },function(data){
                            $('#edit_modul_home').val(data["heimatsemester"]);
                            getVlModule(id);
                            getModule(data["heimatsemester"]);
                        });
                        $('#edit_module').dialog({
                                        show: "slide",
                                        hide: "slide",
                                        modal: true,
                                        minWidth: 600,
                                        buttons: {
                                            "Ok": function() {
                                                $(this).dialog("close");
                                            },
                                            "Anderes Semester": function() {
                                                $("#edit_module_vorhanden_heimat").toggle();
                                                $("#edit_module_vorhanden_andSemester").toggle();
                                                $("#edit_module_neu_heimat").toggle();
                                                $("#edit_module_neu_andSemester").toggle();
                                            }
                                        }
                        });
                    }

                }
            });
}

$(document).ready(function(){
    //ToDo: Edit
    $('#upload').button().click(function() {
        $('#upload_button').button();
        $('#upload_form').dialog({
            minWidth: 400,
            modal: true,
            buttons: {
                "Abbrechen": function() {
                    $(this).dialog("close");
                }
            }
        });

    });

    //ToDo: Edit
    $('#copy_semester').button().click(function() {
        $('#sem_copy_button').button();
        $('#copy_semester_form').dialog({
            minWidth: 400,
            modal: true,
            buttons: {
                "Abbrechen": function() {
                    $(this).dialog("close");
                }
            }
        });

    });

    $('#show_save_edit').button();
    $('#vl_erstellen').button().click(function() {
            window.location.href = "save";
    });

    $('#vl_loeschen').button().click(function () {
        $('#edit_sicherheitsabfrage_allevl').dialog({
                        minWidth: 400,
                        modal: true,
                        buttons: {
                            "Ok": function() {
                                window.location.href = "delall";
                            },
                            "Abbrechen": function() {
                            $(this).dialog("close");
                            }
                        }
            });
    });

    $('#add_new_dozent').button().click(function(){
        $.getJSON('/plugins_packages/neo/neoimporter/ajax.php', {
            cmd: 'setDozent',
            id: $('#edit_dozent_uid').val(),
            userid: $('#dozent_id_1_realvalue').val()}, function(data){
        });

        getDozent($('#edit_dozent_uid').val());

    });

    colorTabele();

    $("#edit_vl_heimat").change(function() {
        $("#edit_vl_heimat_name").text($("#edit_vl_heimat").val());
    });

    $("#edit_module_vorhanden_andSemester_auswahl").change(function () {
        getModule($("#edit_module_vorhanden_andSemester_auswahl").val());
    });

    $("#edit_module_all").dblclick(function () {
        var heimat;
        $.getJSON('/plugins_packages/neo/neoimporter/ajax.php', {
           cmd: 'getVLData',
           id: $('#edit_modul_uid').val()
       },function(data){
            heimat = $("#edit_module_vorhanden_andSemester_auswahl").val();

            if(heimat === null)
            {
                heimat = data["heimatsemester"];
            }
            setModul("0",heimat);
            //getModule (heimat);
            getVlModule($('#edit_modul_uid').val());
       });

    });

    $('#edit_module_neu_erstellen').button().click(function () {
           var heimat;
               $.getJSON('/plugins_packages/neo/neoimporter/ajax.php', {
                  cmd: 'getVLData',
                  id: $('#edit_modul_uid').val()
              },function(data){
                   heimat = $("#edit_module_vorhanden_andSemester_auswahl").val();

                   if(heimat === null)
                   {
                       heimat = data["heimatsemester"];
                   }
                   newModul(heimat);
                   getModule (heimat);
                   getVlModule($('#edit_modul_uid').val());
              });
       });

    $('#show_back').button().click(function() {
        window.location.href = "show";
    });
});

