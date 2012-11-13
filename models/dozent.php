<?php
//require_once 'app/controllers/authenticated_controller.php';
class dozentmodel  {
    function __construct() {
        $this->flash = Trails_Flash::instance();
    }
    function getName($userid) {
        return "blubb";
    }
    
    function isdozent() {
        $sql = "SELECT * FROM auth_user_md5 WHERE user_id =?";
        $db = DBManager::get()->prepare($sql);
        $db->execute(array($this->flash->userid));
        $result = $db->fetchAll();
        if($result[0]["perms"] == "dozent") return true;
         else return FALSE;
    }    
}

?>
