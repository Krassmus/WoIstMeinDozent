<?php
/**
 * User: Johannes Stichler
 * Date: 02.09.11
 * Time: 09:25
 * Was macht die Klasse:
 */
 require_once 'lib/calendar/CalendarView.class.php';

class dozentenplan extends StudIPPlugin implements SystemPlugin {

    public function __construct() {
        parent::__construct();
        //Fügt eine neues Headerelement hinzu
        PageLayout::addHeadElement('script',
            array('src' => $this->getPluginURL()."/js.js"),
            "");
        //Navigationselement AutoNavigation 
        $navigation = new AutoNavigation(get_class($this), PluginEngine::getURL($this, array(), "show_action"));
        //Punkt an dem das Elements eingesetzt werden soll
        Navigation::addItem('/start/'.get_class($this), $navigation);
        // Wichtig das Element clonen sonst kann es zu einer Schleife kommen -> nur bei AutoNavigation
        Navigation::addItem('/'.get_class($this), clone $navigation);
        Navigation::addItem('/'.get_class($this).'/show', clone $navigation);
        Navigation::addItem('/'.get_class($this).'/show/show2', clone $navigation);


    }

    public function show_action() {

        if (Request::option('user_id')) {
            $stundenplan = new CalendarView();
            $stundenplan->addColumn(get_fullname(Request::get('user_id')));
            $db = DBManager::get();
            $termine = $db->query(
                "SELECT seminar_cycle_dates.*, seminare.Name " .
                "FROM seminar_user " .
                    "INNER JOIN seminar_cycle_dates ON (seminar_cycle_dates.Seminar_id = seminar_user.Seminar_id) " .
                    "INNER JOIN seminare ON (seminare.Seminar_id = seminar_user.Seminar_id) " .
                "WHERE seminar_user.user_id = ".$db->quote(Request::get("user_id"))." " .
                    "AND seminar_cycle_dates.weekday = ".$db->quote(date("N"))." " .
            "")->fetchAll(PDO::FETCH_ASSOC);

            foreach ($termine as $termin) {
                $stundenplan->addEntry(array(
                    'id' => md5(uniqid()),
                    'color' => "#5C2D64",
                    'start' => substr(str_replace(":", "", $termin['start_time']), 0, 4),
                    'end'   => substr(str_replace(":", "", $termin['end_time']), 0, 4),
                    'title' => $termin['Name'],
                    'content' => $termin['description']
                ));
            }
        }

        $template = $this->getTemplate("dozentenplan.php", "without_infobox");
        $template->set_attribute("stundenplan", $stundenplan);
        echo $template->render();
    }

    protected function getDisplayName() {
        return "Dozentenplan";
    }

    protected function getTemplate($template_file_name, $layout = "without_infobox") {
        if ($layout) {
            if (method_exists($this, "getDisplayName")) {
                PageLayout::setTitle($this->getDisplayName());
            } else {
                PageLayout::setTitle(get_class($this));
            }
        }
        if (!$this->template_factory) {
            $this->template_factory = new Flexi_TemplateFactory(dirname(__file__)."/templates");
        }
        $template = $this->template_factory->open($template_file_name);
        if ($layout) {
            $template->set_layout($GLOBALS['template_factory']->open($layout === "without_infobox" ? 'layouts/base_without_infobox' : 'layouts/base'));
        }
        return $template;
    }

}
