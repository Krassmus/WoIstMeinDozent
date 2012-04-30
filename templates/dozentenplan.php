<h2>Dozentenplan</h2>
<div class="neo_dozenten_suche"> Name des Dozenten:
    <form action="?" method="GET">
    <? $qs = QuickSearch::get("user_id", new PermissionSearch("user", "Nach Dozenten suchen", "user_id", array('permission' => "dozent", 'exclude_user' => array())));
        if (Request::option("user_id")) {
            $qs->defaultValue(Request::get("user_id"), get_fullname(Request::get("user_id")));
        }    
        echo $qs->render() ?>
        <br/>Datum (falls gew&uuml;nscht):
        <br/><input id="datepicker" type="text" name="datum" value="<?= Request::get('datum') ?>"><br/>
    <input type="image" src="<?= Assets::image_path("icons/16/blue/search.png") ?>">
		<a href="/plugins.php/dozentenplan/"><img src="/../../assets/images/icons/16/blue/refresh.png" alt="Neue Suche"></a>
    </form>
</div>
<div class="neo_platzhalter"></div>
<div class="neo_wochenwechsel">

    <div id="neo_wochenwechsel_datum">
        <div id="neo_wochenwechsel_minus">
            <? if($zurueck >= 0): ?>
                <a href="?user_id=<?= $userid ?>&datum=<?= $zurueck ?>" ><img src="/assets/images/icons/16/yellow/arr_2left.png" alt=""></a>
             <? endif ?>
        </div>
        KW: <?= $woche ?>
        <a href="?user_id=<?= $userid ?>&datum=<?= $vor ?>" >
                <div id="neo_wochenwechsel_plus"><img src="/assets/images/icons/16/yellow/arr_2right.png" alt=""></div>
        </a>
    </div>

</div>

        <?= $stundenplan ?>

<div id="neo_termin_details">

</div>
