<?php

class woistmeindozent extends StudipPlugin implements SystemPlugin {

    public function __construct() {
        parent::__construct();
        $nav_title = "Wo ist mein Dozent?";
        $nav = new Navigation($nav_title, PluginEngine::getURL($this, array(), "start"));
        Navigation::addItem("/search/woistmeindozent", $nav);

        $navigation = new Navigation($nav_title);
        $navigation->setURL(PluginEngine::getURL('woistmeindozent/start'));
        Navigation::addItem('/start/' . get_class($this), $navigation);
    }

}
