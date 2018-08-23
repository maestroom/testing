<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\components\IsataskFormFlag;
?>
<?php $form = ActiveForm::begin(['id' => 'TaskCancelForm','enableAjaxValidation' => false,'enableClientValidation' => true]); ?>
	<?= IsataskFormFlag::widget(); // change flag ?>
	<?= Html::activeHiddenInput($model, 'id'); ?>
	<?= Html::activeHiddenInput($model, 'task_cancel', ['value'=>1]); ?>
	<?= $form->field($model, 'task_cancel_reason',['template' => "<div class='row input-field'><div class='col-md-12'>{label}</div><div class='col-md-12'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label','label'=>'Enter Cancel Reason']])->textArea(['rows' => 6]); ?>
<?php ActiveForm::end(); ?>
<script>
	/* Change Flag */
	$('textarea').bind("input", function(){
		$("#TaskCancelForm #is_change_form").val('1');
		$("#TaskCancelForm #is_change_form_main").val('1');
	});
</script>	    	
