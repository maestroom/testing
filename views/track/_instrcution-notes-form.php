<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\components\IsataskFormFlag;

/* @var $this yii\web\View */
/* @var $model app\models\CaseCloseType */
/* @var $form yii\widgets\ActiveForm */
$this->registerJsFile(Yii::$app->request->baseUrl.'/js/jquery.form.min.js');
$this->registerJsFile(Yii::$app->request->baseUrl.'/js/jquery.MultiFile.js');
$js = <<<JS
// get the form id and set the event
$(function() {
 $('#T7').MultiFile({
  list: '#T7-list',
  STRING: {
		remove:'<em class="fa fa-close text-danger" title="Remove"></em>',
  },
  maxsize:102400
 });
});
function remove_image(id,obj){
	removed = $("#remove_attachments").val();
	if(removed == ""){
	  removed = id;
	}else{
	  removed = removed + ','+ id;
	}	
	$("#remove_attachments").val(removed);
	$(obj).parent().remove();
}
JS;
$this->registerJs($js);
$model->notes=Html::decode($model->notes);
?>
<?php $form = ActiveForm::begin(['id' => $model->formName(),'enableAjaxValidation' => false,'enableClientValidation' => true]); ?>
<?= IsataskFormFlag::widget(); // change flag ?>
<fieldset class="one-cols-fieldset">
    <div class="create-form">
    <?= $form->field($model, 'notes',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-9'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textArea(['rows'=>3,'maxlength'=>$tasks_units_notes_length['notes']]);?>
    
    <?= $form->field($model, 'attachment[]',['template' => "<div class='row input-field'><div class='col-md-3'><label class='form_label' for=''>{label}</label></div><div class='col-md-9'>{input}\n{hint}\n{error}<span>Tip: File size cannot exceed 100 MB.</span><div id='T7-list'></div></div></div>",'labelOptions'=>['class'=>'form_label']])->fileInput(['multiple' => true,"id"=>"T7",'title'=>'Choose File']) ?>
    
    <?php if (!$model->isNewRecord) { ?>
   <div class="form-group field-evidence-cont" >
	<div class="row input-field">
            <div class="col-md-3"><label for="evidence-cont" class="form_label">&nbsp;</label></div>
            <div class="col-md-7">
            <?php
           		if (!empty($model->instructionattachments)) {
               		foreach ($model->instructionattachments as $filename) {
               	?>
               	<div class="MultiFile-label" style="margin-left:7px;">
                   <a href="#Task_attachments_wrap" class="MultiFile-remove" onclick="remove_image('<?php echo $filename->id; ?>', this);">x</a>
                   <span title="File selected: " class="MultiFile-title">
                       <?php echo $filename->fname;?>
                   </span>
               	</div>
               	<?php
                   } 
               	}
               	?>  
            </div> 
        </div> 
    </div> 
    <?php } ?> 
    <?= $form->field($model, 'servicetask_id',['template' => "{input}"])->hiddenInput(); ?>
    <?= $form->field($model, 'task_id',['template' => "{input}"])->hiddenInput(); ?>
    <input type="hidden" name="remove_attachments" id="remove_attachments" value="">
    </div>
</fieldset>
<?php ActiveForm::end(); ?>
<script>
	/* Change Flag */
	$('textarea').bind('input', function(){ 
		$('#TaskInstructNotes #is_change_form').val('1'); 
		$('#is_change_form_main').val('1'); 
	}); 
	$('select').on('change', function() {
		$('#TaskInstructNotes #is_change_form').val('1');
		$('#is_change_form_main').val('1'); 
	});
	$('input[type=file]').change(function() {
		$('#TaskInstructNotes #is_change_form').val('1'); 
		$('#is_change_form_main').val('1');
	});
</script>
