<h2>Dozentenplan</h2>

    <form action="?" method="GET">
    <? $qs = QuickSearch::get("user_id", new PermissionSearch("user", "Nach Dozenten suchen", "user_id", array('permission' => "dozent", 'exclude_user' => array())));
        if (Request::option("user_id")) {
            $qs->defaultValue(Request::get("user_id"), get_fullname(Request::get("user_id")));
        }    
        echo $qs->render() ?>
    <input type="image" src="<?= Assets::image_path("icons/16/blue/search.png") ?>">
    </form>
    <? if ($stundenplan) : ?>
        <?= $stundenplan->render() ?>
    <? endif ?>