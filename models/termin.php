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
        return $datum;
    }
    
    function getAllVlTermine()
    {
        $hfwu = new hfwu();

        $sql = "SELECT dates.seminar_id, seminare.VeranstaltungsNummer, seminare.Name, seminare.Ort, dates.start_time, dates.`end_time` , dates.`weekday`
                 FROM  `seminar_cycle_dates` AS dates
                 INNER JOIN seminare ON seminare.seminar_id = dates.seminar_id
                 WHERE dates.`seminar_id`
                 IN (
                     SELECT seminar_id
                     FROM  `seminar_user`
                     WHERE  `user_id` LIKE  ?
                 )
                 AND seminare.start_time =  '1330556400'";

        $db = DBManager::get()->prepare($sql);
        $db->execute(array($this->flash->userid));
        $result = $db->fetchAll();
        $entry = array();
        foreach($result as $date) {
            $typ = $hfwu->DateTypToHuman($date["date_typ"]);
            $name = $date["Name"]." ".$typ["name"];
            $entry[$date["weekday"]][] = array(
                'id' => md5(uniqid()),
                'color' => $typ["color"],
                'start' => substr(str_replace(":", "", $date['start_time']), 0, 4),
                'end' => substr(str_replace(":", "", $date['end_time']), 0, 4),
                'title' => $name,
                'onClick' => "function() { showdetails('id'); }"
            );
        }
        $this->flash->vltermine = $entry;
    }
    
    function renderPlan() {
        terminmodel::getAllVlTermine();
        //print_r( $this->flash->vltermine);
        $plan = new CalendarView();
        //$plan->setOnClick($jsbefehl);
        $plan->setRange("6","21");
        //Maontags
        
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
}
?>
