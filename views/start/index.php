<? if (isset($message)): ?>
  <?= MessageBox::success($message) ?>
<? endif ?>
<h2>Wo ist mein Dozent?</h2>
<form action="?" method="GET">
    <table class="neo_dozenten_suche">
        <tr>
            <td>
                <label>Name des Dozenten:</label>
            </td>
            <td>
                <?
                    $qs = QuickSearch::get("user_id", 
                        new PermissionSearch("user", 
                            "Nach Dozenten suchen", 
                            "user_id", 
                            array('permission' => "dozent", 'exclude_user' => array())
                        )
                    );
                    if (Request::option("user_id")) {
                        $qs->defaultValue(Request::get("user_id"), get_fullname(Request::get("user_id")));
                    }    
                    echo $qs->render()
                ?>
            </td>
        </tr>
        <tr>
            <td>
                <label>Datum:</label>
            </td>
            <td>
                <input id="datepicker" type="text" name="datum" value="<?= ( Request::get('datum') ? (Request::get('datum')) : (date('d.m.Y')) ) ?>" />
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <!--<input type="image" src="<?= Assets::image_path("icons/16/blue/search.png") ?>" />-->
                <?= Icon::create('search', 'clickable')->asInput() ?>
                <a href="./start">
                    <?= Icon::create('refresh', 'clickable')->asImg() ?>
                </a>
            </td>
        </tr>
    </table>
</form>
<div class="neo_platzhalter"></div>
<div class="neo_wochenwechsel">
    <div id="neo_wochenwechsel_datum">
        <div id="neo_wochenwechsel_minus">
            <? if($zurueck >= 0): ?>
                <a href="?user_id=<?= Request::get("user_id")?>&datum=<?= $zurueck ?>" >
                    <?= Icon::create('arr_2left', 'clickable')->asImg() ?>
                </a>
             <? endif ?>
        </div>
        <span>KW: <?= $woche ?></span>
        <div id="neo_wochenwechsel_plus">
            <a href="?user_id=<?= Request::get("user_id") ?>&datum=<?= $vor ?>" >
                <?= Icon::create('arr_2right', 'clickable')->asImg() ?>
            </a>
        </div>
    </div>
</div>
<?= $stundenplan ?>
<div id="neo_termin_details">
</div>