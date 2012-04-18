<h2>Dozentenplan</h2>
<div class="neo_dozenten_suche"> <h3>Name des Dozenten:</h3>
    <form action="?" method="GET">
    <? $qs = QuickSearch::get("user_id", new PermissionSearch("user", "Nach Dozenten suchen", "user_id", array('permission' => "dozent", 'exclude_user' => array())));
        if (Request::option("user_id")) {
            $qs->defaultValue(Request::get("user_id"), get_fullname(Request::get("user_id")));
        }    
        echo $qs->render() ?>
    <input type="image" src="<?= Assets::image_path("icons/16/blue/search.png") ?>">
    </form>
</div>
<div class="neo_platzhalter"></div>
<div class="neo_wochenwechsel">
    <div id="neo_wochenwechsel_minus">
        <? if($zurueck >= 0): ?>
            <a href="?user_id=<?= $userid ?>&i=<?= $zurueck ?>" ><img src="/assets/images/icons/16/yellow/arr_2left.png" alt=""></a>
         <? endif ?>
    </div>
    <div id="neo_wochenwechsel_datum">KW: <?= $woche ?> </div>
    <a href="?user_id=<?= $userid ?>&i=<?= $vor ?>" >
        <div id="neo_wochenwechsel_plus"><img src="/assets/images/icons/16/yellow/arr_2right.png" alt=""></div>
    </a>
</div>

        <?= $stundenplan ?>

<div id="neo_termin_details">

</div>
