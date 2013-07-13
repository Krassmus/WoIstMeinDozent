<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once 'app/controllers/authenticated_controller.php';
require_once dirname(__FILE__).'/../model/dozent.php';
require_once dirname(__FILE__).'/../model/termin.php';
/**
 * Description of ajax
 *
 * @author johannesstichler
 */
class ajaxController extends StudipController {
    function before_filter(&$action, &$args)
    {
        $this->flash = Trails_Flash::instance();
        // set default layout
        //$layout = $GLOBALS['template_factory']->open('layouts/base');
        //$this->set_layout($layout);
            
        //$GLOBALS['CURRENT_PAGE'] = 'Wo Ist mein Dozent';
    }
    
    function index_action() {
        echo "fehler";
    }
    
    function renderDetails_action() {
        terminmodel::getTermin();
    }
    
}

?>
