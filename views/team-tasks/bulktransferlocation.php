<?php
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\widgets\Select2;
/* @var $this yii\web\View */
/* @var $searchModel app\models\search\EvidenceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>
<fieldset>
	<div class="custom-inline-block-width">
		<input type="radio" name="bulktransfertask" class="bulkreopen" value="selectedtask" id="rdo_selectedtransfertask">
        <label for="rdo_selectedtransfertask">Selected <span id="transferselectedtask">0</span> Tasks in Grid</label>
		<input type="radio" name="bulktransfertask" class="bulkreopen" value="alltask" checked="checked" id="rdo_bulktransfertask"/>
        <label for="rdo_bulktransfertask">All Filtered<span id="allbulktransfertask">0</span> Tasks in Grid</label>
	</div>
    <div class="form-group field-pricing-price_point required">
        <div class="row input-field">
            <div class="col-md-3">
                 <label class="form_label" for="team_location">Select Location</label>
            </div>
            <div class="col-md-9">
            <?php 
            echo Select2::widget([
                'id'   => 'team_location',
                'name' => 'team_location',
                'data' => $teamLocation,
                'options' => ['prompt' => 'Select Location', 'title' => 'Select Location', 'id' => 'team_location_dropdown', 'class' => 'form-control'],
            ]); ?>
            <div class="help-block"></div>
            </div>
        </div>
    </div>
</fieldset>
<script>
$(document).ready(function($){
    $('#applytransferLocation input').customInput();
});
</script>
<noscript></noscript>

