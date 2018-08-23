<?php
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\grid\GridView;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

$fullUrl=Yii::$app->request->hostInfo.Yii::$app->request->baseUrl.'/index.php?'.Yii::$app->request->queryString;
?>
<?php $form = ActiveForm::begin(['id' => 'client-preferred-pricing','enableAjaxValidation' => false,'enableClientValidation' => true]); ?>
<div class="row">
	<div class="col-sm-12">
		<?php if($case_id == 0) {?>
		<?= Html::dropDownList('client_id', [], $clientList, ['class'=>'form-control billing-dropdown-filterlist']) ?>
		<?php } else { ?>
		<?= Html::dropDownList('client_case_id', [], $clientCaseList, ['class'=>'form-control billing-dropdown-filterlist']) ?>
		<?php } ?>
	</div>
	<div class="col-sm-12 mycontainer">
		<?php 
		if(!empty($model)){ 
			foreach ($model as $data){
		?>	
			<div class="myheader">
				<!--  <a href="javascript:void(0);" onClick="getremainingpricepointbyteamandclient(<?php /* $client_id ?>,<?= $case_id ?>,<?= $data->team_id; */ ?>);">-->
				<a href="javascript:void(0);">
					<?= $data->pricing_type == 1? 'Shared': $data->team->team_name ;?>
				</a>
				<div class="pull-right header-checkbox">
					<input type="checkbox" id="chk_<?= $data->team_id;?>" value="<?= $data->team_id ?>" class="parent_<?=$data->team_id;?>" name="pricing-team[]" aria-label="pricing team" />
					<label for="chk_<?= $data->team_id ?>">&nbsp;</label>
				</div>
			</div>
			<div class="content" id="container_<?= $data->team_id ?>" style="padding:0px;">
				<table class="table table-striped table-hover">
					<thead>
						<tr>
				    		<th class="text-left" width="92%"><a href="#" title="Price Point">Price Point</a></th>
							<th>&nbsp;</th>
						</tr>
					</thead>
					<tbody>
					<?php 	       
						foreach ($data as $content){
					?>
						<tr>
						   <td class="text-left" width="80%"><?= $data->price_point; ?></td>
					       <td class="pull-right">
					         	<input class="child_<?=$data->id;?>" type="checkbox" name="team_pricepoint[<?= $team_id ?>][]" id="team_pricepoint_<?=$data->id;?>" value="<?= $data->id; ?>" onChange="selectedParentTeam(<?=$team_id;?>);" aria-label="team pricepoint" >
					       		<label for="team_pricepoint_<?=$data->id;?>">&nbsp;</label>
					       </td>
					    </tr>
					<?php } ?>
					</tbody>
				</table>
			</div>
		<?php 
			}
		}
		?>
	</div>
</div>	
<?php ActiveForm::end(); ?>
<script>
$('input').customInput();
$('#module-url').val('<?=$fullUrl ?>');
</script>
<noscript></noscript>