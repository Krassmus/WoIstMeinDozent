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
        $hfwu = new hfwu();
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
      echo $sql;
        $db = DBManager::get()->prepare($sql);
        $db->execute(array($this->flash->userid, $this->flash->start, $this->flash->ende)); 
        echo $this->flash->userid ." -> ". $this->flash->start ." -> ". $this->flash->ende ." -> ".$this->flash->vlbeginn;
        $result = $db->fetchAll();
        $entry = array();
        foreach($result as $date) {
            $typ = $hfwu->DateTypToHuman($date["date_typ"]);
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
        echo "<pre>";
        print_r($this->flash->vltermine);
        echo "</pre>";
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
        $sql = "SELECT termine.raum AS raum_frei, seminare.name, seminare.VeranstaltungsNummer, seminare.Seminar_id, termine.date, termine.end_time FROM `termine`
        INNER JOIN seminare on seminare.Seminar_id = termine.`range_id`
        WHERE termine.termin_id = ?";
        /*
         * INNER JOIN resources_assign ON resources_assign.assign_user_id = termine.termin_id resources_objects.name AS raum
         * INNER JOIN resources_objects ON resources_objects.resource_id = resources_assign.resource_id
         */
        $id = $_REQUEST['id'];
        $db = DBManager::get()->prepare($sql);
        $db->execute(array($id));
        $result = $db->fetchAll();
        
        $this->sem_name = $result[0]['name'];
        $this->sem_id = $result[0]['Seminar_id'];
        $this->start = date("d.m.Y, h:i",$result[0]['date']);
        $this->ende = date("d.m.Y, h:i",$result[0]['end_time']);
        
        // Raum auslesen
        
        // Dozenten auslesen
         $sql = "SELECT auth_user_md5.Vorname, auth_user_md5.Nachname FROM `seminar_user`
                INNER JOIN auth_user_md5 on seminar_user.user_id = auth_user_md5.user_id
                WHERE Seminar_id = ?
                AND status='dozent'";
        $db = DBManager::get()->prepare($sql);
        $db->execute(array($this->sem_id));
        $result = $db->fetchAll();
        //Dozenten in die VL eintragen
        foreach($result as $res) {
            $this->dozenten .= $res["Vorname"]." ".$res["Nachname"]."<br/>";
        }
        // Einrichtungen
        //Beteiligte Einrichtungen eintragen:

        $sql = "SELECT Institute.Name
                FROM `seminar_inst`
                INNER JOIN Institute on Institute.Institut_id = seminar_inst.Institut_id
                WHERE Seminar_id = ?";
        $db = DBManager::get()->prepare($sql);
        $db->execute(array($this->sem_id));
        $result = $db->fetchAll();
        //Dozenten in die VL eintragen
        foreach($result as $res) {
           $this->einrichtungen .= $res["Name"]."<br/>";
        }
    }
}
?>
