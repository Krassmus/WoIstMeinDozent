/**
 * Created by JetBrains PhpStorm.
 * User: johannesstichler
 * Date: 16.04.12
 * Time: 15:26
 * To change this template use File | Settings | File Templates.
 */
function toggle(id) {
    $('#'+id).toggle();
}

function delTermin(id) {
    $.ajax({
        type: "POST",
        url: '/plugins_packages/neo/neocheck/termineindenferien/tif-ajax.php',
        data: {
                cmd: 'delTermin',
                id: id
            }
      }).done(function(data){
          if(data == 'Ok') {
              $('#neo_termin_'+id).toggle();
          }
      });
}

