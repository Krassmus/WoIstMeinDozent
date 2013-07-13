<?php
//Benötigte Dateien
require_once 'app/controllers/authenticated_controller.php';
require_once dirname(__FILE__).'/../model/dozent.php';
require_once dirname(__FILE__).'/../model/termin.php';
/**
 * Description of woistmeindozent
 *
 * @author johannesstichler
 */
class startController extends StudipController {
    var $assetspfad;
    /**
     * Common code for all actions: set default layout and page title.
     */
    function before_filter(&$action, &$args)
    {
        $this->flash = Trails_Flash::instance();
        // set default layout
        $layout = $GLOBALS['template_factory']->open('layouts/base');
        $this->set_layout($layout);
            
        $GLOBALS['CURRENT_PAGE'] = 'Wo Ist mein Dozent';
        $this->flash->assetspfad = $GLOBALS["wimd_pfad"]; //$GLOBALS["plugin_pfad"].'/assets/';
    }
    
    /*
     * Gibt die UI aus.
     */
    function index_action($parm1 = false, $parm2 = false) {
        $this->assetspfad = $this->flash->assetspfad;
        PageLayout::addStylesheet("../../".$this->assetspfad .'dozentenplan.css');
        PageLayout::addScript("../../".$this->assetspfad .'wimd.js');
        //Ueberpruefen ob ein Dozent uebergeben wurde und ob der User Dozent ist 
        if(isset($_REQUEST["user_id"]) ) {
            $this->flash->userid = $_REQUEST["user_id"];
            if(!dozentmodel::isDozent($_REQUEST["user_id"])) { 
                $this->message = _("User ist kein Dozent");
                return false; // Sofortiger Abbruch
            }
        }
        //Ueberpruefen ob eine Datum ausgewaehlt wurde
        if(!empty($_REQUEST["datum"])) {
            terminmodel::dateToTime($_REQUEST["datum"]); //Umwandlung in ein Maschinen Datum
        } else {
            terminmodel::dateToTime(date("j").date("n").date("Y")); //Wenn keinD Datum gesetzt ist das Aktuelle Datum übergeben
        }
        //Datum fuer naechste Woche generieren
        $this->vor = date("d.m.Y", $this->flash->datum + (7*86400));;
        //Datum von letzte Woche generieren
        $this->zurueck = date("d.m.Y", $this->flash->datum - (7*86400));
        //Welche Kalenderwoche man sich befindet
        $this->woche = date("W", $this->flash->datum);
        //Den Plan ausgeben
        $this->stundenplan = terminmodel::renderPlan();
        $this->asset = "../../".$this->assetspfad;
    }
}
?>
