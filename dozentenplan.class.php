<?php
/**
 * User: Johannes Stichler
 * Date: 02.09.11
 * Time: 09:25
 * Was macht die Klasse:
 */
//require_once 'app/controllers/authenticated_controller.php';
require_once 'app/models/calendar/schedule.php';
require_once 'lib/calendar/CalendarColumn.class.php';
require_once 'lib/calendar/CalendarWeekView.class.php';
require_once 'lib/classes/SemesterData.class.php';
require_once('plugins_packages/neo/neoprint/classes/hfwu.php');
require_once('plugins_packages/neo/neoprint/classes/terminplan.php');

class dozentenplan extends StudIPPlugin implements SystemPlugin {

    public function __construct() {
        parent::__construct();
        //Fügt eine neues Headerelement hinzu
        PageLayout::addHeadElement('script',
            array('src' => $this->getPluginURL()."/js.js"),
            "");
        //Navigationselement AutoNavigation 
        $navigation = new AutoNavigation("Wo ist der Dozent", PluginEngine::getURL($this, array(), "show"));
        //Punkt an dem das Elements eingesetzt werden soll
        Navigation::addItem('/start/'.get_class($this), $navigation);
        //Wichtig das Element clonen sonst kann es zu einer Schleife kommen -> nur bei AutoNavigation
        //Navigation::addItem('/'.get_class($this), clone $navigation);


    }


    public function show_action() {
        $plan = array();

        if(!empty($_REQUEST["user_id"])) {
            $terminplanner = new terminplan();
            $jsbefehl = "function() { showdetails('id'); }";
            $terminplanner->setOnClick($jsbefehl);
            $terminplanner->toggleabschneiden();
            $terminplanner->setRange("6","21");
            if(empty($_REQUEST["i"])) $i=0; //Vorlauf 0 = diese Woche / 1 = nächste Woche usw...
            else $i = $_REQUEST["i"];
            //$woche = date("W", time());

					if(!empty($_REQUEST["datum"])) {
								$datum = $_REQUEST["datum"];
                //Datum aufstückeln
                $tag = $datum[0].$datum[1];
                $monat = $datum[3].$datum[4];
                $jahr = $datum[6].$datum[7].$datum[8].$datum[9];
                //in UnixTime umwandeln
                $datum = mktime(0,0,1,$monat,$tag,$jahr);
                $start = date("w", $datum); //Wochentag auslesen
                $start = $datum - (($start-1)*86400); //Tag - Wochentag + 1 -> Datum des Montags.
            } else {
							$start = date("w", time()); //Wochentag auslesen
							$start = time() - (($start-1)*86400); //Tag - Wochentag + 1 -> Datum des Montags.
            }
            $start = mktime(0,0,1,date("m",  $start),date("d",  $start),date("Y",  $start));
            //if($i == 0) $start = $terminplanner->calcStarttime($start, $i);
            $termine = $terminplanner->getUserTermineWeek($start, $_REQUEST["user_id"], false);
            $plan = $terminplanner->renderPlan($termine);
            $woche = date("W",  $start)." (".date("d.m.Y",  $start)."-".date("d.m.Y",  $start+518399).")"; //
            $plan["start"] = $woche;
            $plan["letztewoche"] = date("d.m.Y", mktime(0,0,1,date("m",  $start),date("d",  $start)-7,date("Y",  $start)));
            $plan["naechstewoche"] = date("d.m.Y", mktime(0,0,1,date("m",  $start),date("d",  $start)+7,date("Y",  $start)));
        }

        PageLayout::addStylesheet('../../plugins_packages/neo/dozentenplan/dozentenplan.css');
        $template = $this->getTemplate("dozentenplan.php", "without_infobox");
        if(isset($_REQUEST["user_id"])) $template->set_attribute("stundenplan", $plan["html"]);
        if(isset($_REQUEST["user_id"])) $template->set_attribute("woche", $plan["start"]);
        if(isset($_REQUEST["user_id"])) $template->set_attribute("zurueck", $plan["letztewoche"]);
        if(isset($_REQUEST["user_id"])) $template->set_attribute("vor", $plan["naechstewoche"]);
        if(isset($_REQUEST["user_id"])) $template->set_attribute("userid", $_REQUEST["user_id"]);

        echo $template->render();
    }


    /**
     * Gibt die Termine so aus damit eine Wochenübersicht erstellt werden kann
     *
     * @param string $uid die eindeutige StudIP ID des Users
     * @return array $termine die Termine direkt zum Malen des Terminkalenders
     */
    public function getAllVlTermine($uid)
    {
        $hfwu = new hfwu();

        $sql = "SELECT dates.seminar_id, seminare.VeranstaltungsNummer, seminare.Name, seminare.Ort, dates.start_time, dates.`end_time` , dates.`weekday`
                 FROM  `seminar_cycle_dates` AS d«ates
                 INNER JOIN seminare ON seminare.seminar_id = dates.seminar_id
                 WHERE dates.`seminar_id`
                 IN (
                     SELECT seminar_id
                     FROM  `seminar_user`
                     WHERE  `user_id` LIKE  ?
                 )
                 AND seminare.start_time =  '1330556400'";

        $db = DBManager::get()->prepare($sql);
        $db->execute(array($uid));
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
                'title' => $name
            );
        }
        return $entry;
    }


    //Beginn Standard Funktionen:

    protected function getDisplayName() {
        return "Dozentenplan";
    }

    protected function getTemplate($template_file_name, $layout = "without_infobox") {
        if ($layout) {
            if (method_exists($this, "getDisplayName")) {
                PageLayout::setTitle($this->getDisplayName());
            } else {
                PageLayout::setTitle(get_class($this));
            }
        }
        if (!$this->template_factory) {
            $this->template_factory = new Flexi_TemplateFactory(dirname(__file__)."/templates");
        }
        $template = $this->template_factory->open($template_file_name);
        if ($layout) {
            $template->set_layout($GLOBALS['template_factory']->open($layout === "without_infobox" ? 'layouts/base_without_infobox' : 'layouts/base'));
        }
        return $template;
    }

}
