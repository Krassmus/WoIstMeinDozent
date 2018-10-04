<?php

//Benötigte Dateien
require_once 'app/controllers/plugin_controller.php';
require_once dirname(__FILE__) . '/../model/dozentmodel.class.php';
require_once dirname(__FILE__) . '/../model/terminmodel.class.php';

/**
 * Description of woistmeindozent
 *
 * @author johannesstichler
 */
class startController extends PluginController {

    var $assetspfad;

    /**
     * Common code for all actions: set default layout and page title.
     */
    function before_filter(&$action, &$args) {
        parent::before_filter($action, $args);
        $this->flash = Trails_Flash::instance();
        // set default layout
        //$layout = $GLOBALS['template_factory']->open('layouts/base');
        //$this->set_layout($layout);

        PageLayout::setTitle('Wo Ist mein Dozent');
    }

    /*
     * Gibt die UI aus.
     */

    function index_action($parm1 = false, $parm2 = false) {
        Navigation::activateItem("/search/woistmeindozent");
        $this->assetspfad = $this->plugin->getPluginPath() . '/assets/';
        PageLayout::addStylesheet("../../" . $this->assetspfad . 'dozentenplan.css');
        PageLayout::addScript("../../" . $this->assetspfad . 'wimd.js');
        //Ueberpruefen ob ein Dozent uebergeben wurde und ob der User Dozent ist
        if (isset($_REQUEST["user_id"])) {
            $this->flash->userid = $_REQUEST["user_id"];
            if (!dozentmodel::getInstance()->isDozent($_REQUEST["user_id"])) {
                $this->message = _("User ist kein Dozent");
                return false; // Sofortiger Abbruch
            }
        }
        //Ueberpruefen ob eine Datum ausgewaehlt wurde
        if (!empty($_REQUEST["datum"])) {
            terminmodel::getInstance()->dateToTime($_REQUEST["datum"]); //Umwandlung in ein Maschinen Datum
        } else {
            terminmodel::getInstance()->dateToTime(date("j") . date("n") . date("Y")); //Wenn keinD Datum gesetzt ist das Aktuelle Datum übergeben
        }
        //Datum fuer naechste Woche generieren
        $this->vor = date("d.m.Y", $this->flash->datum + (7 * 86400));
        ;
        //Datum von letzte Woche generieren
        $this->zurueck = date("d.m.Y", $this->flash->datum - (7 * 86400));
        //Welche Kalenderwoche man sich befindet
        $this->woche = date("W", $this->flash->datum);
        //Den Plan ausgeben
        $this->stundenplan = terminmodel::getInstance()->renderPlan();
        $this->asset = "../../" . $this->assetspfad;
    }

}
