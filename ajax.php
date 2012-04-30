<?php
/**
 * Die Beschreibung der Datei
 *
 * Weitere Infos
 *
 * @param Art des Parameters $Variablenname Beschreibung des Parameters
 */

$STUDIP_BASE_PATH = realpath( dirname(__FILE__) . '/../../../..');
$include_path = get_include_path();
$include_path .= PATH_SEPARATOR . $STUDIP_BASE_PATH . DIRECTORY_SEPARATOR . 'public';

require_once $STUDIP_BASE_PATH . "/lib/bootstrap.php";
URLHelper::setBaseUrl($ABSOLUTE_URI_STUDIP);

page_open(array('sess' => 'Seminar_Session', 'auth' => 'Seminar_Default_Auth', 'perm' => 'Seminar_Perm', 'user' => 'Seminar_User'));
$perm->check('admin');

switch($_REQUEST['cmd']) {
    case 'renderDetails':
        //Allgemeine Termin Infos
        $sql = "SELECT termine.raum AS raum_frei, seminare.name, seminare.VeranstaltungsNummer, seminare.Seminar_id, termine.date, termine.end_time, Institute.Name as heimat, resources_objects.name AS raum FROM `termine`
        INNER JOIN seminare on seminare.Seminar_id = termine.`range_id`
        INNER JOIN Institute on Institute.Institut_id = seminare.Institut_id
        INNER JOIN resources_assign ON resources_assign.assign_user_id = termine.termin_id
        INNER JOIN resources_objects ON resources_objects.resource_id = resources_assign.resource_id
        WHERE termine.termin_id = ?";
				$id = $_REQUEST['id'];
        $db = DBManager::get()->prepare($sql);
        $db->execute(array($id));
        $result = $db->fetchAll();
        //Füllen der ersten Variabeln
        $raum = $result[0]['raum'];
				if(empty($raum)) $raum = $result[0]['raum_frei'];
        $sem_name = $result[0]['name'];
        $sem_id = $result[0]['Seminar_id'];
        $start = date("h:i",$result[0]['date']);
        $ende = date("h:i",$result[0]['end_time']);
        $dozenten = "";
        //Dozenten der VL abrufen
        $sql = "SELECT auth_user_md5.Vorname, auth_user_md5.Nachname FROM `seminar_user`
                INNER JOIN auth_user_md5 on seminar_user.user_id = auth_user_md5.user_id
                WHERE Seminar_id = ?
                AND status='dozent'";
        $db = DBManager::get()->prepare($sql);
        $db->execute(array($sem_id));
        $result = $db->fetchAll();
        //Dozenten in die VL eintragen
        foreach($result as $res) {
            $dozenten .= $res["Vorname"]." ".$res["Nachname"]."<br/>";
        }

        //Beteiligte Einrichtungen eintragen:

        $sql = "SELECT Institute.Name
                FROM `seminar_inst`
                INNER JOIN Institute on Institute.Institut_id = seminar_inst.Institut_id
                WHERE Seminar_id = ?";
        $db = DBManager::get()->prepare($sql);
        $db->execute(array($sem_id));
        $result = $db->fetchAll();
        //Dozenten in die VL eintragen
        foreach($result as $res) {
           $einrichtungen .= $res["Name"]."<br/>";
        }

        $entry = array();
        $html = "<h2>Details zum Termin</h2> <br/>
        Name der Veranstaltung: $sem_name <br/>
        <strong>Dozent</strong>: <br/> $dozenten
        <strong>Raum</strong>: $raum<br/>
        <strong>Start</strong>: $start<br/>
        <strong>Ende</strong>: $ende<br/>
        <strong>Link zu Veranstaltung</strong>: <a href='/details.php?cid=$sem_id' target='_blank'>$sem_name</a><br/>
        <strong>Liste der Beteiligten Einrichtungen</strong>: <br/>$einrichtungen
        ";
        echo $html;


        break;
    default: echo "Fehler beim Aufruf";
}