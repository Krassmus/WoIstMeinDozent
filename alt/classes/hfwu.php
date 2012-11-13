<?php
/**
 * Created by JetBrains PhpStorm.
 * User: johannesstichler
 * Date: 21.03.12
 * Time: 11:32
 * To change this template use File | Settings | File Templates.
 */
class hfwu
{
    public function DateTypToHuman($typ) {
        switch($typ) {
            case 1: $return = array("name"=>_("Vorlesung"), "sitzung"=>1, "color"=>"#2D2C64"); break;
            case 2: $return = array("name"=>_("Vorbesprechung"), "sitzung"=>0, "color"=>"#5C2D64"); break;
            case 3: $return = array("name"=>_("Klausur"), "sitzung"=>0, "color"=>"#526416"); break;
            case 4: $return = array("name"=>_("Exkursion"), "sitzung"=>0, "color"=>"#505064"); break;
            case 5: $return = array("name"=>_("Neuer Termin / Ersatztermin"), "sitzung"=>1, "color"=>"#41643F"); break;
            case 6: $return = array("name"=>_("Ausfall"), "sitzung"=>0, "color"=>"#E60005"); break;
            case 7: $return = array("name"=>_("Sitzung"), "sitzung"=>1, "color"=>"#627C95"); break;
            case 8: $return = array("name"=>_("Sondertermin"), "sitzung"=>1, "color"=>"#2D2C64"); break;
            case 9: $return = array("name"=>_("Freiwillig"), "sitzung"=>1,  "color"=>"#6c6c6c"); break;
            default: $return = array("name"=>_("Vorlesung"), "sitzung"=>1, "color"=>"#2D2C64");
        }
        return $return;
    }


}
