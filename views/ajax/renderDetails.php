<table>
    <tbody>
        <tr>
            <th>Name der Veranstaltung:</th>
            <td><?= terminmodel::getInstance()->getSem_name() ?></td>
        </tr>
        <tr>
            <th>Dozent:</th>
            <td><?= terminmodel::getInstance()->getDozenten() ?></td>
        </tr>
        <tr>
            <th>Raum:</th>
            <td><?= terminmodel::getInstance()->getRaum() ?></td>
        </tr>
        <tr>
            <th>Start:</th>
            <td><?=  terminmodel::getInstance()->getStart() ?></td>
        </tr>
        <tr>
            <th>Ende:</th>
            <td><?=  terminmodel::getInstance()->getEnde() ?></td>
        </tr>
        <tr>
            <th>Link zu Veranstaltung:</th>
            <td><a href="/details.php?cid=<?= terminmodel::getInstance()->getSem_id() ?>" target="_blank"><?= terminmodel::getInstance()->getSem_name() ?></a></td>
        </tr>
        <tr>
            <th>Liste der beteiligten Einrichtungen:</th>
            <td><?= terminmodel::getInstance()->getEinrichtungen() ?></td>
        </tr>
    </tbody>
</table>