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
     
    public function __construct()
    { 
        parent::__construct();       
        
        $navigation = new Navigation('Wo ist mein Dozent');
        $navigation->setURL(PluginEngine::getURL('woistmeindozent/start'));
        Navigation::addItem('/start/'.get_class($this), $navigation);
         unset($GLOBALS["plugin_pfad"]);
        $GLOBALS["wimd_pfad"] = $this->getPluginPath().'/assets/';//        $GLOBALS["plugin_pfad"] = ;
    }
    
     /**
     * This method dispatches all actions.
     *
     * @param string   part of the dispatch path that was not consumed
     */
    public function perform($unconsumed_path)
    {
        $trails_root = $this->getPluginPath();
        $dispatcher = new Trails_Dispatcher($trails_root, NULL, NULL);
        $dispatcher->dispatch($unconsumed_path);
    }
}
    
?>
