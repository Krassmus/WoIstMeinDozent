<?php
/**
 * Created by JetBrains PhpStorm.
 * User: johannesstichler
 * Date: 14.03.12
 * Time: 14:48
 * To change this template use File | Settings | File Templates.
 */
class terminplan
{

    public function getInstTermineWeek($start, $instid) {
        $hfwu = new hfwu();
        $ende = $start + (86400*5); //86400 sec = 1 Tag
        $termine = array();
        //Termine der Vorlesungen
        $sql = "SELECT termine.date, termine.end_time, seminare.Name, termine.date_typ
                FROM `termine`
                INNER JOIN seminare ON seminare.`Seminar_id` = termine.range_id
                WHERE range_id
                IN (SELECT  `Seminar_id`
                    FROM  `seminar_inst` AS inst
                    WHERE inst.institut_id = ?
                )
                AND `date` >=?
                AND `end_time` <=?";
        $db = DBManager::get()->prepare($sql);
        $db->execute(array($instid, $start, $ende, $ende));
        $terplan = $db->fetchAll();
        $i = 0;

        foreach($terplan as $termin)
        {
            $typ = $hfwu->DateTypToHuman($termin["date_typ"]);
            $name = $termin["Name"]." ".$typ["name"];
            $termine[date("w", $termin["date"])][] = array(
                'id' => md5(uniqid()),
                'color' => $typ["color"],
                'start' => date("Gi", $termin["date"]),
                'end' => date("Gi", $termin["end_time"]),
                'title' => $name
            );
            $i++;
        }
        return $termine;
    }

    public function getUserTermineWeek($start, $userid = "") {
        $hfwu = new hfwu();
        if($userid == "") $userid = $GLOBALS['user']->id;
        $ende = $start + (86400*5); //86400 sec = 1 Tag
        $termine = array();
        //Persönliche Termin
        $sql = "SELECT summary, wdays, start, end, description, expire, category_intern AS date_typ
        FROM `calendar_events`
        WHERE `autor_id` = ?
        AND ((`start` >= ? AND `end` <= ?) OR (expire >= ? AND rtype != 'SINGLE'))";
        $db = DBManager::get()->prepare($sql);
        $db->execute(array($userid, $start, $ende, $ende));
        $ptplan = $db->fetchAll();
        //Termine aus der Vorlesung
        $sql = "SELECT termine.date, termine.end_time, seminare.Name, termine.date_typ
                FROM `termine`
                INNER JOIN seminare ON seminare.`Seminar_id` = termine.range_id
                WHERE range_id
                IN (SELECT `Seminar_id`
                    FROM `seminar_user` AS user
                    WHERE user.`user_id` = ?
                )
                AND `date` >=?
                AND `end_time` <=?";

        $db = DBManager::get()->prepare($sql);
        $db->execute(array($userid, $start, $ende, $ende));
        $terplan = $db->fetchAll();

        //Beides in eine Einheitliche Form bringen
        $i = 0;

        foreach($ptplan as $termin)
        {
            if(!empty($termin["wdays"])) {
                $wday = $termin["wdays"];
            } else {
                $wday = date("w", $termin["start"]);
            }
            $typ = $hfwu->DateTypToHuman($termin["date_typ"]);
            $name = $termin["summary"];
            $termine[$wday][]  = array(
                'id' => md5(uniqid()),
                'color' => $typ["color"],
                'start' => date("Gi", $termin["start"]),
                'end' => date("Gi", $termin["end"]),
                'title' => $name
            );
            $i++;
        }

        foreach($terplan as $termin)
        {
            $typ = $hfwu->DateTypToHuman($termin["date_typ"]);
            $name = $termin["Name"]." ".$typ["name"];
            $termine[date("w", $termin["date"])][] = array(
                'id' => md5(uniqid()),
                'color' => $typ["color"],
                'start' => date("Gi", $termin["date"]),
                'end' => date("Gi", $termin["end_time"]),
                'title' => $name
            );
            $i++;
        }
        return $termine;
    }

    public function renderPlan($termine) {

        $plan = new CalendarView();
        $plan->setRange("8","20");
        $plan->addColumn(_('Montag'));
        foreach($termine[1] as $date) $plan->addEntry($date);
        $plan->addColumn(_('Dienstag'));
        foreach($termine[2] as $date) $plan->addEntry($date);
        $plan->addColumn(_('Mittwoch'));
        foreach($termine[3] as $date) $plan->addEntry($date);
        $plan->addColumn(_('Donnerstag'));
        foreach($termine[4] as $date) $plan->addEntry($date);
        if(sizeof($termine[5]) > "0") {
            $plan->addColumn(_('Freitag'));
            foreach($termine[5] as $date) {
                $plan->addEntry($date);
            }
        }
        if(sizeof($termine[6]) > "0") {
            $plan->addColumn(_('Samstag'));
            foreach($termine[6] as $date) $plan->addEntry($date);
        }
        $plaene["html"] =  $plan->render();

        return $plaene;

    }

    public function calcStarttime($start, $i) {
        $start = date("w", time()); //Wochentag auslesen
        $start = time()+(604800*$i) - (($start-1)*86400);
        $start = mktime(0,0,1,date("m",  $start),date("d",  $start),date("Y",  $start));
        return $start;
    }


}
