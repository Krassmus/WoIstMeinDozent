<?php
require_once 'app/models/calendar/schedule.php';
require_once 'lib/calendar/CalendarColumn.class.php';
require_once 'lib/calendar/CalendarWeekView.class.php';
require_once 'lib/classes/SemesterData.class.php';

class terminmodel {
    
    function __construct() {
        $this->flash = Trails_Flash::instance();
    }
    
    function dateToTime($date) {
        //Datum aufstueckeln
        $tag = $date[0].$date[1];
        $monat = $date[3].$date[4];
        $jahr = $date[6].$date[7].$date[8].$date[9];
        //in UnixTime umwandeln
        $datum = mktime(0,0,1,$monat,$tag,$jahr);
        $start = date("w", $datum); //Wochentag auslesen
        $this->flash->start = $datum - (($start-1)*86400); //Tag - Wochentag + 1 -> Datum des Montags.
        $this->flash->ende = $this->flash->start + (7*86400);
        $this->flash->datum = $datum;
    }
    
    function getAllVlTermine()
    { 
        $sql = "SELECT termine.termin_id as id,termine.date, termine.end_time, seminare.Name, termine.date_typ, termine.raum, termine.range_id "
              ."FROM `termine` "
              ."INNER JOIN seminare ON seminare.`Seminar_id` = termine.range_id "
              ."WHERE range_id "
              ."IN (SELECT `Seminar_id` "
              ."    FROM `seminar_user` AS user "
              ."    WHERE user.user_id = ? ) "
              ."AND `date` >=? "
              ."AND `end_time` <=? "
             ;
        $db = DBManager::get()->prepare($sql);
        $db->execute(array($this->flash->userid, $this->flash->start, $this->flash->ende)); 
        $result = $db->fetchAll();
        $entry = array();
        foreach($result as $date) {
            $typ = terminmodel::DateTypToHuman($date["date_typ"]);
            $name = $date["Name"]." ".$typ["name"];
            $start = date("Hi", $date['date']);
            $ende = date("Hi", $date['end_time']);
            $weekday = date("N", $date['date']);
            $entry[$weekday][] = array(
                'id' => md5(uniqid()),
                'color' => $typ["color"],
                'start' => $start,
                'end' => $ende,
                'title' => $name,
                'onClick' => "function() { showdetails('".$date["id"]."'); }"
            );
        }
        $this->flash->vltermine = $entry;
        return true;
    }
    
    function getVl() {
        
    }


    function renderPlan() {
        terminmodel::getAllVlTermine();
        $plan = new CalendarView();
        $plan->setRange("6","21");

        $plan->addColumn(_('Montag'));
        if(sizeof($this->flash->vltermine[1]) > "0") {
        foreach($this->flash->vltermine[1] as $date) $plan->addEntry($date);
        }
        $plan->addColumn(_('Dienstag'));
        if(sizeof($this->flash->vltermine[2]) > "0") {
        foreach($this->flash->vltermine[2] as $date) $plan->addEntry($date);
        }
        $plan->addColumn(_('Mittwoch'));
        if(sizeof($this->flash->vltermine[3]) > "0") {
        foreach($this->flash->vltermine[3] as $date) $plan->addEntry($date);
        }
        $plan->addColumn(_('Donnerstag'));
        if(sizeof($this->flash->vltermine[4]) > "0") {
            foreach($this->flash->vltermine[4] as $date) 
                $plan->addEntry($date);
        }
        $plan->addColumn(_('Freitag'));
        if(sizeof($this->flash->vltermine[5]) > "0") {
            foreach($this->flash->vltermine[5] as $date) {
                $plan->addEntry($date);
            }
        }
        if(sizeof($this->flash->vltermine[6]) > "0") {
            $plan->addColumn(_('Samstag'));
            foreach($this->flash->vltermine[6] as $date) $plan->addEntry($date);
        }
        $plaene["html"] =  $plan->render();

        return $plaene["html"];
    }
    
    function getTermin() {
        //Allgemeine Infos setzen
        $this->flash->id = $_REQUEST['id'];
        $sql = "SELECT termine.raum AS raum_frei, seminare.name, seminare.VeranstaltungsNummer, seminare.Seminar_id, termine.date, termine.end_time FROM `termine`
        INNER JOIN seminare on seminare.Seminar_id = termine.`range_id`
        WHERE termine.termin_id = ?";
        $db = DBManager::get()->prepare($sql);
        $db->execute(array($this->flash->id));
        $result = $db->fetchAll();
        
        $this->sem_name = $result[0]['name'];
        $this->sem_id = $result[0]['Seminar_id'];
        $this->start = date("d.m.Y, H:i",$result[0]['date']);
        $this->ende = date("d.m.Y, H:i",$result[0]['end_time']);
        
        // Raum auslesen
        terminmodel::getRoomToDate();
        // Dozenten auslesen
        dozentmodel::getListDozenten();
        // Einrichtungen

        $sql = "SELECT Institute.Name "
              ."FROM `seminar_inst` "
              ."INNER JOIN Institute on Institute.Institut_id = seminar_inst.Institut_id "
              ."WHERE Seminar_id = ?"; 
        $db = DBManager::get()->prepare($sql);
        $db->execute(array($this->sem_id));
        $result = $db->fetchAll();
        //Dozenten in die VL eintragen
        foreach($result as $res) {
           $this->einrichtungen .= $res["Name"]."<br/>";
        }
    }
    /*
     * Gibt den Raum zu einem Termin aus bzw. speichert diesen in $this-raum
     */
    function getRoomToDate() {
         $sql =  "SELECT ro.name AS raum "
               ." FROM resources_objects as ro"
               ." INNER JOIN resources_assign as ra ON ra.resource_id = ro.resource_id"
               ." WHERE ra.assign_user_id = ?";
        $db = DBManager::get()->prepare($sql);
        $db->execute(array($this->flash->id));
        $result = $db->fetchAll();
        $this->raum = $result[0][0];
    }
    
    function DateTypToHuman($typ) {
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
?>
