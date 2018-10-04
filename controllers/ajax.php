<?php

/**
 * Description of ajax
 *
 * @author johannesstichler
 */
require_once 'app/controllers/plugin_controller.php';
require_once dirname(__FILE__) . '/../model/dozentmodel.class.php';
require_once dirname(__FILE__) . '/../model/terminmodel.class.php';

class ajaxController extends PluginController {

    function before_filter(&$action, &$args) {
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
        terminmodel::getInstance()->getTermin();
    }

}
