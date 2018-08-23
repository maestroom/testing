<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\components\IsataskFormFlag;
/* @var $this yii\web\View */
/* @var $model app\models\Tasks */
//$this->title = 'Step1 : Select Report Type, Format & Dates';
$this->params['breadcrumbs'][] = ['label' => 'Custom Report', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->registerJsFile(Yii::$app->request->baseUrl.'/js/form-builder/admin.formbuilder.js');
$this->registerJsFile(Yii::$app->request->baseUrl.'/js/jquery.MultiFile.js');
$this->registerJsFile(Yii::$app->request->baseUrl.'/js/project.js');
?>
<div id="project_container" class="right-main-container">
	<div class="sub-heading"><a href="javascript:void(0);" class="tag-header-black" title="<?php echo Html::encode("Step 1: Select Report Type & Format") ?>"><?= Html::encode("Step 1: Select Report Type") ?></a></div>
	<fieldset class="two-cols-fieldset section-add-new-project two-cols-fieldset-report">
		<?php $form = ActiveForm::begin(['id' => 'report-type-format-dates','enableAjaxValidation' => false,'enableClientValidation' => true,'options' => ['enctype' => 'multipart/form-data']]);?>
		<?= IsataskFormFlag::widget(); // change flag ?>
		<!-- Step-1 -->
		<div id="tabs-step-1">
			<div id="form_div_step_1">
			<?= 
                            $this->render('_step_1',[
                                'teamserviceList'=>$teamserviceList,
                                'team_loc_data' => $team_loc_data,
                                'client_data'=> $client_data,
                                'client_case_data' => $client_case_data,
                                'model' => $model,
                                'modeReportType' => $modeReportType,
                                'modeReportFormat' => $modeReportFormat,
                                'modeReportsChartFormat' => $modeReportsChartFormat,
                                'modelReportFields' => $modelReportFields,
                                'form' => $form,
                                'id' => $id,
                                'flag' => $flag,
                                'fields' => $fields
                            ]);
			?>
			</div>	
		</div>
		
		<!-- Step-2 -->
		<div id="tabs-step-2" style="display:none;">
			<div id="form_div_step_2">
				<?php echo $this->render('_step_2',[
						'form'=>$form,
						'modeReportType' => $modeReportType,
						'id'=>$id,
						'flag'=>$flag,
						'fields'=>$fields
					]); 
				?>
			</div>
		</div>
		
		<!-- Step-3 -->
		<div id="tabs-step-3" style="display:none;">
			<div id="form_div_step_3">
				<?php echo $this->render('_step_3',[
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
				<?php echo $this->render('_step_4',[
					'form'=>$form,
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
		<input type="hidden" id="saved-report-flag1" value="<?=$flag?>" />
		<input type="hidden" id="saved-report-id1" value="<?=$id?>" />
		<?php ActiveForm::end(); ?>
	</fieldset>
</div>
<script>
/* change flag */
$('#reportsusersaved-report_type_id').on('change', function(){
	$('#report-type-format-dates #is_change_form').val('1');
	$('#is_change_form_main').val('1');
});
<?php if(isset($flag) && ($flag=='edit' || $flag=='run')){ ?>
	showLoader();	
	validateReportSteps(1);
<?php } ?>
function run_report(){
	var form = $('#report-type-format-dates');
	form.prop('action',baseUrl+'custom-report/run-report');
	form.submit();
}
</script>
<noscript></noscript>
