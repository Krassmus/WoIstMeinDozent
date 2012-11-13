<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once 'app/controllers/authenticated_controller.php';
require_once dirname(__FILE__).'/../models/dozent.php';
require_once dirname(__FILE__).'/../models/termin.php';
/**
 * Description of woistmeindozent
 *
 * @author johannesstichler
 */
class startController extends StudipController {
    var $userid;
    var $datum;
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
        $this->assetspfad = $GLOBALS["plugin_pfad"].'/assets/';
        
    }
    
    /*
     * 
     */
    function index_action($parm1 = false, $parm2 = false) {
        PageLayout::addStylesheet($this->assetspfad .'dozentenplan.css');
        PageLayout::addScript($this->assetspfad .'wimd.js');
        //Ueberpruefen ob ein Dozent uebergeben wurde und ob der User Dozent ist 
        if(isset($_REQUEST["user_id"]) ) {
            $this->flash->userid = $_REQUEST["user_id"];
            if(!dozentmodel::isDozent($_REQUEST["user_id"])) { 
                $this->message = _("User ist kein Dozent");
                return false; // Sofortiger Abbruch
            }
        }
        //Ueberpruefen ob eine Datum ausgewaehlt wurde
        if(isset($_REQUEST["datum"])) {
            terminmodel::dateToTime($_REQUEST["datum"]);
        } else {
            $this->flash->datum = time();
        }
        $this->vor = date("d.m.Y", $this->flash->datum + (7*86400));;
        $this->zurueck = date("d.m.Y", $this->flash->datum - (7*86400));
        $this->woche = date("W", $this->flash->datum);
        $this->stundenplan = terminmodel::renderPlan();
    }
}

?>
