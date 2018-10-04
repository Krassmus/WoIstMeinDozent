<?php

//require_once 'app/controllers/authenticated_controller.php';
class dozentmodel {

    protected static $_instance = null;
    private $flash;

    public static function getInstance() {
        if (self::$_instance === null) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    protected function __clone() {
        
    }

    protected function __construct() {
        $this->flash = Trails_Flash::instance();
    }

    public function isdozent() {
        $sql = "SELECT * FROM auth_user_md5 WHERE user_id =?";
        $db = DBManager::get()->prepare($sql);
        $db->execute(array($this->flash->userid));
        $result = $db->fetchAll();
        if ($result[0]["perms"] == "dozent") {
            return true;
        } else {
            return FALSE;
        }
    }

    public function getListDozenten() {
        $dozenten = "";
        $sql = "SELECT auth_user_md5.Vorname, auth_user_md5.Nachname FROM `seminar_user`
                INNER JOIN auth_user_md5 on seminar_user.user_id = auth_user_md5.user_id
                WHERE Seminar_id = ?
                AND status='dozent'";
        $db = DBManager::get()->prepare($sql);
        //$db->execute(array($this->sem_id));
        $db->execute(array(terminmodel::getInstance()->getSem_id()));
        $result = $db->fetchAll();
        //Dozenten in die VL eintragen
        foreach ($result as $res) {
            $vorname = utf8_encode($res["Vorname"]);
            $nachname = utf8_encode($res["Nachname"]);
            $dozenten .= $vorname . " " . $nachname . "<br />";
        }
        return $dozenten;
    }

}
