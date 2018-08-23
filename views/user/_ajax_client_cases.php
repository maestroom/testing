<?php
/* @var $this yii\web\View */

use yii\helpers\Html;

if (!empty($mycases)) {
    ?>
    <!--    <li class="clear custom-full-width" id="header">
            <input type="text" class="col-sm-12 form-control" id="filterClientCases" title="Filter Client Cases" placeholder="Filter List"/>
        </li>-->
    <?php
    if (count($mycases) > 0) {
        foreach ($mycases as $key => $single) {
            ?>
            <li class="clear client_case_li_from custom-full-width" data-client_case_id="<?= $single['client_case_id'] ?>" data-client_id="<?= $single['client_id'] ?>" >
                <a href="javascript:void(0)"><?= $single['client_name'] . ' - ' . $single['case_name'] ?></a>
            </li>
            <?php
        }
    }
}
?>