<?php
/* @var $this yii\web\View */

use yii\helpers\Html;
if (!empty($myteams)) {
    foreach ($myteams as $key => $val) {
        foreach ($val as $key1 => $val1) {
            foreach ($val1 as $val2 => $team_location_name) {
                ?>
                <li class="clear teams_li_from custom-full-width" data-team_id="<?= $key1 ?>" data-team_loc="<?= $val2 ?>" >
                    <a href="javascript:void(0)"><?= $key . ' - ' . $team_location_name ?></a>
                </li>
                <?php
            }
        } $i++;
    }
}
?>