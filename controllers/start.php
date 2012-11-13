<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once 'app/controllers/authenticated_controller.php';
/**
 * Description of woistmeindozent
 *
 * @author johannesstichler
 */
class startController extends StudipController {
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
    }
    
    function index_action($parm1 = false, $parm2 = false) {
        PageLayout::addStylesheet($GLOBALS["plugin_pfad"].'/assets/dozentenplan.css');
        PageLayout::addScript($GLOBALS["plugin_pfad"]. '/assets/wimd.js');
        $this->message = "Das ist ein test";
        $this->data = "Blubb";
        
    }
}

?>
