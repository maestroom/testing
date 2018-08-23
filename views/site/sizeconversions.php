<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\grid\GridView;

$this->title = 'Unit Conversions';
$this->params['breadcrumbs'][] = $this->title;
$fullUrl=Yii::$app->request->hostInfo.Yii::$app->request->baseUrl.'/index.php?'.Yii::$app->request->queryString;
?>
<div class="right-main-container slide-open" id="maincontainer">
	<fieldset class="two-cols-fieldset">
                <div class="administration-main-cols">
			<div class="administration-lt-cols pull-left">
				<button id="controlbtn" aria-label="Expand or Collapse" title="Expand/Collapse" class="slide-control-btn" onclick="WorkflowToggle();"><span>&nbsp;</span></button>
				<ul>
					<li>
						<a href="#" title="Unit Conversions" class="admin-main-title"><em title="Unit Conversions" class="fa fa-folder-open text-danger"></em>Unit Conversions</a>
						<div class="manage-admin-left-module-list">
							<ul class="sub-links">
								<li class="conversion active" id="SizeConversion"><a href="javascript:void(0);" onclick="SelectUnitConversion('Size');" class="conversion" title="Size Conversion"><em title="Size Conversion" class="fa fa-calculator"></em> Size Conversion</a></li>
								<li class="conversion" id="PaperConversion"><a href="javascript:void(0);" onclick="SelectUnitConversion('Paper');" class="conversion" title="Paper Conversion"><em title="Paper Conversion" class="fa fa-calculator"></em> Paper Conversion</a></li>
								<li class="conversion" id="TimeConversion"><a href="javascript:void(0);" onclick="SelectUnitConversion('Time');" class="conversion" title="Time Conversion"><em title="Time Conversion" class="fa fa-calculator"></em> Time Conversion</a></li>
							</ul>
						</div>
				   </li>
				</ul>
			</div>
			<div class="administration-rt-cols pull-right" id="admin_right">
				<?= $this->render('units', [
					'type' => $type,
					'unitMaster' => $unitMaster
				]); ?>
			</div>
		</div>
	</fieldset>
</div>
<script>
function SelectUnitConversion(typeConversion)
{
	$.ajax({
		url:baseUrl +'/site/units-view&typeconversion='+typeConversion,
		type:'get',
		beforeSend:function(){showLoader();$('.conversion').removeClass('active');},
		success:function(response){
			$('#admin_right').html(response);
			$('#'+typeConversion+'Conversion').addClass('active');
		},
		complete:function(){hideLoader();}
	});
}
</script>
<noscript></noscript>
