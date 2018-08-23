<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\components\IsataskFormFlag;
use app\models\ReportsUserSavedFields;
/* @var $this yii\web\View */
/* @var $model app\models\Tasks */
$this->title = 'Step 1: Edit Fields, Sort & Filters';
$this->params['breadcrumbs'][] = ['label' => 'Custom Report', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->registerJsFile(Yii::$app->request->baseUrl.'/js/form-builder/admin.formbuilder.js');
$this->registerJsFile(Yii::$app->request->baseUrl.'/js/jquery.MultiFile.js');
$this->registerJsFile(Yii::$app->request->baseUrl.'/js/project.js');
?>
<div id="project_container" class="right-main-container">
	<div class="sub-heading"><a href="javascript:void(0);" title="<?= Html::encode($this->title) ?>" class="tag-header-black"><?= Html::encode($this->title) ?></a></div>
	<fieldset class="two-cols-fieldset-report section-add-new-project">
		<?php $form = ActiveForm::begin(['id' => 'report-type-format-dates','enableAjaxValidation' => false,'enableClientValidation' => true,'options' => ['enctype' => 'multipart/form-data']]);?>
		<?= IsataskFormFlag::widget(); // change flag ?>
		<!-- Step-2 -->
		<div id="tabs-step-2">
			<div id="form_div_step_2" style="display:<?php if($flag=='run'){ ?>none<?php }else{ ?>block<?php } ?>">
				<?php 
					echo $this->render('_step_2',[
						'model'=>$model,
						'form'=>$form,
						'modeReportType' => $modeReportType,
						'id'=>$id,
						'flag'=>$flag,
						'fields'=>$fields,
						'field_type_name'=> $field_type_name
					]);
				?>
			</div>
		</div>
		
		<!-- Step-3 -->
		<div id="tabs-step-3" style="display:<?php if($flag=='run'){?>block<?php }else{?>none<?php }?>;">
			<div id="form_div_step_3">
				<?= $this->render('_step_3',[
					'model'=>$model,
					'form'=>$form,
					'id'=>$id,
					'flag'=>$flag,
					'fields'=>$fields
				]); ?>
			</div>
		</div>
		<!-- Step-4 -->
		<div id="tabs-step-4" style="display:none;">
			<div id="form_div_step_4">
				<?= $this->render('_step_4',[
					'form'=>$form,
					'model'=>$model,
					'id'=>$id,
					'flag'=>$flag,
					'fields'=>$fields,
					'modeReportsChartFormat'=>$modeReportsChartFormat
				]); ?>
			</div>
		</div>	
		<!-- Step-5 -->
		<div id="tabs-step-5" style="display:none;">
			<div id="form_div_step_5">
			</div>
		</div>
		<!-- Step-6 -->
		<div id="tabs-step-6" style="display:none;">
			<div id="form_div_step_6">
			</div>
		</div>
		<?= $form->field($model, 'custom_report_name')->hiddenInput()->label(false); ?>
		<input type="hidden" id="saved-report-id" value="<?=$id?>" />
		<input type="hidden" id="flag" name='flag' value="<?=$flag?>" />
		<?php ActiveForm::end(); ?>
	</fieldset>
</div>

<script>
function run_report(){
	var form = $('#report-type-format-dates');
	form.prop('action',baseUrl+'custom-report/run-report');
	form.submit();
}
function check_allfields(){
	var chk=$('#select_all_fields').prop('checked');
	$('#avail_field_data input[type="checkbox"]').each(function(){
		$(this).prop('checked',chk);
		if(chk){
			$(this).next('label').addClass('checked');
		}else{
			$(this).next('label').removeClass('checked');
		}
	});
	}
</script>
<noscript></noscript>