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

        //$GLOBALS['CURRENT_PAGE'] = 'TrailsDemo';
        //Navigation::activateItem('/trails/demo');
    }
    
    function index_action($parm1 = false, $parm2 = false) {
        $this->message = "Das ist ein test";
        //$this->data("index", "Das ist ein test");
    }
}

?>
