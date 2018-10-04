<?php

require_once 'app/models/calendar/schedule.php';
require_once 'lib/calendar/CalendarColumn.class.php';
require_once 'lib/calendar/CalendarWeekView.class.php';
require_once 'lib/classes/SemesterData.class.php';

class terminmodel {

    protected static $_instance = null;
    private $flash;
    private $sem_name;
    private $sem_id;
    private $start;
    private $ende;
    private $einrichtungen;
    private $raum;
    private $dozenten;

    public static function getInstance() {
        if (self::$_instance === null) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }
    
    public function getSem_name() {
        return $this->sem_name;
    }
    
    public function getSem_id() {
        return $this->sem_id;
    }
    
    public function getStart() {
        return $this->start;
    }
    
    public function getEnde() {
        return $this->ende;
    }
    
    public function getEinrichtungen() {
        return $this->einrichtungen;
    }

    public function getRaum() {
        return $this->raum;
    }
    
    public function getDozenten() {
        return $this->dozenten;
    }

    protected function __clone() {
        
    }

    protected function __construct() {
        $this->flash = Trails_Flash::instance();
    }

    public function dateToTime($date) {
        //Datum aufstueckeln
        $tag = intval($date[0] . $date[1]);
        $monat = intval($date[3] . $date[4]);
        $jahr = intval($date[6] . $date[7] . $date[8] . $date[9]);
        //in UnixTime umwandeln
        $datum = mktime(0, 0, 1, $monat, $tag, $jahr);
        $start = date("w", $datum); //Wochentag auslesen
        $this->flash->start = $datum - (($start - 1) * 86400); //Tag - Wochentag + 1 -> Datum des Montags.
        $this->flash->ende = $this->flash->start + (7 * 86400);
        $this->flash->datum = $datum;
    }

    public function getAllVlTermine() {
        $sql = "SELECT termine.termin_id as id, termine.date, termine.end_time, seminare.Name, termine.date_typ, termine.raum, termine.range_id, seminare.Seminar_id, 0 AS ex_termin  
                FROM `termine` 
                    INNER JOIN seminare ON (seminare.`Seminar_id` = termine.range_id) 
                WHERE range_id IN (
                        SELECT `Seminar_id` 
                        FROM `seminar_user` 
                        WHERE `seminar_user`.user_id = :user_id 
                            AND `seminar_user`.`status` = 'dozent'
                    )
                    AND `date` >= :start 
                    AND `end_time` <= :ende 
                UNION SELECT ex_termine.termin_id as id, ex_termine.date, ex_termine.end_time, seminare.Name, ex_termine.date_typ, ex_termine.raum, ex_termine.range_id, seminare.Seminar_id, 1 AS ex_termin  
                FROM `ex_termine` 
                    INNER JOIN seminare ON (seminare.`Seminar_id` = ex_termine.range_id) 
                WHERE range_id IN (
                        SELECT `Seminar_id` 
                        FROM `seminar_user` 
                        WHERE `seminar_user`.user_id = :user_id 
                            AND `seminar_user`.`status` = 'dozent'
                    )
                    AND `date` >= :start 
                    AND `end_time` <= :ende 
                    ";
        $db = DBManager::get()->prepare($sql);
        $db->execute(array(
            'user_id' => $this->flash->userid,
            'start' => $this->flash->start,
            'ende' => $this->flash->ende
        ));
        $result = $db->fetchAll();
        $entry = array();
        foreach ($result as $date) {
            $sql = "SELECT COUNT(*) "
                    . "FROM `termin_related_persons` WHERE range_id = ? AND user_id = ?";
            $db = DBManager::get()->prepare($sql);
            $db->execute(array($date["id"], $this->flash->userid));
            $res = $db->fetch();
            $sql = "SELECT COUNT(*) "
                    . "FROM `termin_related_persons` WHERE range_id = ?";
            $db = DBManager::get()->prepare($sql);
            $db->execute(array($date["id"]));
            $res2 = $db->fetch();
            if ($res[0] > 0 OR $res2[0] == 0) {
                $typ = $this->DateTypToHuman($date["date_typ"]);
                $name = $date["Name"] . " " . $typ["name"];
                $start = date("Hi", $date['date']);
                $ende = date("Hi", $date['end_time']);
                $weekday = date("N", $date['date']);
                $entry[$weekday][] = array(
                    'id' => md5(uniqid()),
                    'color' => $date['ex_termin'] ? "rgba(0,0,0,0.2)" : $typ["color"],
                    'start' => $start,
                    'end' => $ende,
                    'title' => $name.($date['ex_termin'] ? " ("._("fällt aus").")" : ""),
                    'onClick' => "function() { showdetails('" . $date["id"] . "'); }"
                );
            }
        }
        $this->flash->vltermine = $entry;
        return true;
    }

    public function getVl() {
        
    }

    public function renderPlan() {
        $this->getAllVlTermine();
        $plan = new CalendarView();
        $plan->setRange("6", "21");
        $t = $this->flash->start;
        $plan->addColumn(_('Montag (' . strftime("%d.%m", $t) . ')'));
        if (sizeof($this->flash->vltermine[1]) > "0") {
            foreach ($this->flash->vltermine[1] as $date) {
                $plan->addEntry($date);
            }
        }
        $t = $t + 86400;
        $plan->addColumn(_('Dienstag (' . strftime("%d.%m", $t) . ')'));
        if (sizeof($this->flash->vltermine[2]) > "0") {
            foreach ($this->flash->vltermine[2] as $date) {
                $plan->addEntry($date);
            }
        }
        $t = $t + 86400;
        $plan->addColumn(_('Mittwoch (' . strftime("%d.%m", $t) . ')'));
        if (sizeof($this->flash->vltermine[3]) > "0") {
            foreach ($this->flash->vltermine[3] as $date) {
                $plan->addEntry($date);
            }
        }
        $t = $t + 86400;
        $plan->addColumn(_('Donnerstag (' . strftime("%d.%m", $t) . ')'));
        if (sizeof($this->flash->vltermine[4]) > "0") {
            foreach ($this->flash->vltermine[4] as $date) {
                $plan->addEntry($date);
            }
        }
        $t = $t + 86400;
        $plan->addColumn(_('Freitag (' . strftime("%d.%m", $t) . ')'));
        if (sizeof($this->flash->vltermine[5]) > "0") {
            foreach ($this->flash->vltermine[5] as $date) {
                $plan->addEntry($date);
            }
        }
        if (sizeof($this->flash->vltermine[6]) > "0") {
            $t = $t + 86400;
            $plan->addColumn(_('Samstag (' . strftime("%d.%m", $t) . ')'));
            foreach ($this->flash->vltermine[6] as $date) {
                $plan->addEntry($date);
            }
        }
        $plaene["html"] = $plan->render();

        return $plaene["html"];
    }

    public function getTermin() {
        //Allgemeine Infos setzen
        $this->flash->id = $_REQUEST['id'];
        $sql_termine = "SELECT termine.raum AS raum_frei, seminare.name, seminare.VeranstaltungsNummer, seminare.Seminar_id, termine.date, termine.end_time
                FROM `termine`
                INNER JOIN seminare on seminare.Seminar_id = termine.range_id
                WHERE termine.termin_id = ?
                UNION
                SELECT ex_termine.raum AS raum_frei, seminare.name, seminare.VeranstaltungsNummer, seminare.Seminar_id, ex_termine.date, ex_termine.end_time FROM `ex_termine`
                INNER JOIN seminare on seminare.Seminar_id = ex_termine.range_id
                WHERE ex_termine.termin_id = ?";
        $statement_termine = DBManager::get()->prepare($sql_termine);
        $statement_termine->execute(array($this->flash->id, $this->flash->id));
        $result_termine = $statement_termine->fetchAll();
        $this->sem_name = utf8_encode($result_termine[0]['name']);
        $this->sem_id = $result_termine[0]['Seminar_id'];
        $this->start = date("d.m.Y, H:i", $result_termine[0]['date']);
        $this->ende = date("d.m.Y, H:i", $result_termine[0]['end_time']);
        // Raum auslesen
        $this->getRoomToDate();
        // Dozenten auslesen
        $this->dozenten = dozentmodel::getInstance()->getListDozenten();
        // Einrichtungen
        $sql_instute = "SELECT Institute.Name
                        FROM `seminar_inst`
                        INNER JOIN Institute on Institute.Institut_id = seminar_inst.Institut_id
                        WHERE Seminar_id = ?
                        ORDER BY Institute.Name";
        $statement_institute = DBManager::get()->prepare($sql_instute);
        $statement_institute->execute(array($this->sem_id));
        $result_institute = $statement_institute->fetchAll();
        //Dozenten in die VL eintragen
        foreach ($result_institute as $res) {
            $name = utf8_encode($res["Name"]);
            $this->einrichtungen .= $name . "<br />";
        }
    }

    /*
     * Gibt den Raum zu einem Termin aus bzw. speichert diesen in $this-raum
     */

    public function getRoomToDate() {
        $sql = "SELECT ro.name AS raum "
                . " FROM resources_objects as ro"
                . " INNER JOIN resources_assign as ra ON ra.resource_id = ro.resource_id"
                . " WHERE ra.assign_user_id = ?";
        $db = DBManager::get()->prepare($sql);
        $db->execute(array($this->flash->id));
        $result = $db->fetchAll();
        $this->raum = $result[0][0];
    }

    public function DateTypToHuman($typ) {
        switch ($typ) {
            case 1: $return = array("name" => _("Vorlesung/ lecture"), "sitzung" => 1, "color" => "#2D2C64");
                break;
            case 2: $return = array("name" => _("Vorbesprechung"), "sitzung" => 0, "color" => "#5C2D64");
                break;
            case 3: $return = array("name" => _("Klausur"), "sitzung" => 0, "color" => "#526416");
                break;
            case 4: $return = array("name" => _("Exkursion"), "sitzung" => 0, "color" => "#505064");
                break;
            case 5: $return = array("name" => _("Ersatztermin/ alternative date"), "sitzung" => 1, "color" => "#41643F");
                break;
            case 6: $return = array("name" => _("Ausfall / cancelled"), "sitzung" => 1, "color" => "#E60005");
                break;
            case 7: $return = array("name" => _("Sitzung"), "sitzung" => 1, "color" => "#627C95");
                break;
            case 8: $return = array("name" => _("Sondertermin"), "sitzung" => 1, "color" => "#2D2C64");
                break;
            case 9: $return = array("name" => _("Freiwillig"), "sitzung" => 1, "color" => "#24af8d");
                break;
            default: $return = array("name" => _("Vorlesung/ lecture"), "sitzung" => 1, "color" => "#2D2C64");
        }
        return $return;
    }

}
