<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once 'vendor/trails/trails.php';
/**
 * Description of woistmeindozent
 *
 * @author johannesstichler
 */
class woistmeindozent extends StudipPlugin implements SystemPlugin {
     function __construct()
    {
        parent::__construct();
        $navigation = new Navigation('Wo ist mein Dozemt');
	$navigation->setURL(PluginEngine::getURL('woistmeindozent/start'));
        Navigation::addItem('/start/'.get_class($this), $navigation);

    }
    
     /**
     * This method dispatches all actions.
     *
     * @param string   part of the dispatch path that was not consumed
     */
    function perform($unconsumed_path)
    {
        $trails_root = $this->getPluginPath();
        $dispatcher = new Trails_Dispatcher($trails_root, NULL, NULL);
        $dispatcher->dispatch($unconsumed_path);
    }
}

?>
