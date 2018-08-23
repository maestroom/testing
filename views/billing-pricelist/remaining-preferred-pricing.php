<?php
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\grid\GridView;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

$fullUrl=Yii::$app->request->hostInfo.Yii::$app->request->baseUrl.'/index.php?'.Yii::$app->request->queryString;

$data = $type == 'client' ? $clientList : $clientCaseList ;
?>
<?php $form = ActiveForm::begin(['id' => 'reamining-preferred-pricing','enableAjaxValidation' => false,'enableClientValidation' => true]); ?>
<div class="row">
	<div class="col-sm-12 mycontainer">
		<div class="myheader">
			<a href="javascript:void(0);" onclick="loadclientcasestoclonefrom()">
				<?= $type == 'client'? 'Select Client to Clone From': 'Select Case to Clone From'; ?>
			</a>
		</div>
		<div class="content" id="container_clientcase_toclonefrom">
			<fieldset>
				<legend class="sr-only"><?= $type == 'client'? 'Select Client to Clone From': 'Select Case to Clone From'; ?></legend>
				<ul id="service_task_container">
					<?php 
						$idCounter = 0;
						foreach ($data as $key => $content){ ?>
						<li><span id="lbl-cc-<?=$idCounter;?>"><?= $content; ?></span>
							<div class="pull-right"> 
								<input class="clientcasecheckbox" type="checkbox" name="<?= $type=='client'?'clone_client_id':'clone_client_case_id'; ?>" id="clientcase_<?=$key;?>_<?=$content;?>" value="<?= $key; ?>" onchange="isClientCaseSelectedtoClone();" aria-label="<?= $content; ?>" >
								<label for="clientcase_<?=$key;?>_<?=$content;?>" aria-labelledby="lbl-cc-<?=$idCounter++;?>">&nbsp;&nbsp;<span class="sr-only"><?= $content; ?></span></label>
							</div>
						</li>
					<?php } ?>    
				</ul>
           </fieldset>
		</div>
		<?php 
		if(!empty($modelTeam)){
			$hederCounter = 0;
			foreach ($modelTeam as $key => $data){
		?>	
			<div class="myheader">
				<!-- new -->
				<a href="javascript:void(0);" onclick="loadreamainingpricepoints(<?= $key; ?>)" id="header-ot-<?=$hederCounter?>"><?= $key == 0? 'Shared Price Points': $data['team_name'].' Price Points' ;?></a>
				<div class="pull-right header-checkbox">
					<input  aria-labelledby="header-ot-<?=$hederCounter++?>" type="checkbox" id="parent_<?= $key;?>" value="<?= $key ?>" class="" name="team[]" onchange="selectChildContent(<?=$key;?>, this.checked)" />
					<label for="parent_<?= $key;?>">&nbsp;</label>
				</div>
			</div>
			<div class="content" id="container_<?= $key ?>">
				<fieldset>
				<legend class="sr-only"><?= $key == 0? 'Shared Price Points': $data['team_name'].' Price Points' ;?></legend>
				<ul id="service_task_container">
					<?php foreach ($model[$key] as $content){ ?>
						<li><span  id="lbl-cc-<?=$idCounter;?>"><?= $content['price_point']; ?></span>
							<div class="pull-right"> 
								<input class=""  aria-labelledby="lbl-cc-<?=$idCounter++;?>" type="checkbox" name="pricepoint[<?= $key ?>][]" id="child_<?=$key;?>_<?=$content['id'];?>" value="<?= $content['id']; ?>" onchange="selectParentTeam(<?=$key;?>,<?=$content['id'];?>, this.checked);" aria-label="<?= $content['price_point']; ?>" >
								<label for="child_<?=$key;?>_<?=$content['id'];?>"><span class="sr-only"><?= $content['price_point']; ?></span></label>
							</div>
						</li>
					<?php } ?>    
				</ul>
				</fieldset>
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
