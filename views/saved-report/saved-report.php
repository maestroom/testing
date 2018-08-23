<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\grid\GridView;
use yii\web\JsExpression;
use app\models\Options;

/* @var $this yii\web\View */
/* @var $model app\models\ReportsUserSaved */
$this->title = "Report Saved User View";
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="sub-heading"><a href="javascript:void(0);" title="<?= Html::encode($this->title) ?>" class="tag-header-black"><?= Html::encode($this->title) ?></a></div>
<div class="table-responsive">
	<fieldset class="two-cols-fieldset section-add-new-project" style="padding: 7px;top: 38px;">
		<h3>Report Preview</h3>
			<?php $form = ActiveForm::begin(['id' => 'report-type-user-saved','enableAjaxValidation' => false,'enableClientValidation' => true,'options' => ['enctype' => 'multipart/form-data']]);?>
			<input type="hidden" name="post_data" id="post_data" value='<?= json_encode($post_data); ?>'; />
			<?= $this->render('saved-report-view', ['report_data' => $report_data, 'column_data' => $column_data,'reportTypeFields' => reportTypeFields,'column_display_data' => $column_display_data]);  ?>
			
			<!-- button -->
			<div class="button-set text-right">
				<?= Html::button('Cancel', ['title' => 'Previous','class' => 'btn btn-primary', 'id' => 'report-saved-user-view-cancel']) ?>
				<?= Html::button('Run', ['title' => 'Run','class' =>  'btn btn-primary', 'id' => 'run3','onclick'=>'run_report();']) ?>
			</div>
			<?php ActiveForm::end(); ?>
	</fieldset>
</div>
<script>
	$('#report-saved-user-view-cancel').click(function(){
		location.href = baseUrl +'/saved-report/index';
	});
	function run_report(){
		var form = $('#report-type-user-saved');
		form.prop('action',baseUrl+'saved-report/run-file-report');
		form.submit();
	}
</script>
