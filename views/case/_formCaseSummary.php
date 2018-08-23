<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\components\IsataskFormFlag;

/* @var $this yii\web\View */
/* @var $model app\models\Case */
/* @var $form yii\widgets\ActiveForm */
$js = <<<JS
function SubmitForm(form_id,btn){
	var form = $('form#'+form_id);
	$.ajax({
        url    : form.attr('action'),
        cache: false,
        type   : 'post',
        data   : form.serialize(),
        beforeSend : function() {
        	showLoader();
        	$(btn).attr('disabled','disabled');
        	
        },
        success: function (response){
        	hideLoader();
        	if(response == 'OK'){
				updateCase($model->client_case_id);
			}else{
				$('#form_div').html(response);
        		$(btn).removeAttr('disabled');
        	}
        },
        error  : function (){
            console.log('internal server error');
        }
    });
}
JS;
$this->registerJs($js);
?>

<?php $form = ActiveForm::begin(['id' => $model->formName(),'enableAjaxValidation' => false,'enableClientValidation' => true]); ?>
<?= IsataskFormFlag::widget(); // change flag ?>
<fieldset class="one-cols-fieldset">
    <div class="create-form">
    	<?php // $form->field($model, 'client_case_id',['template' =>"{input}"])->hiddenInput()?>
    	<input type="hidden" name="client_case_id" id="client_case_id" value="<?= $model->client_case_id; ?>" />
    	<?= $form->field($model, 'summary',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-10'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textArea(['rows'=>6]) ?>
    	<?= $form->field($model, 'summary_note',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-10'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textArea(['rows'=>6]) ?>
    </div>	
</fieldset>
<!--<div class="button-set text-right">
    <?= Html::button('Cancel', ['title' => 'Cancel','class' => 'btn btn-primary', 'onclick'=>'clearForm("'.$model->formName().'")']); ?>
    <?= Html::button('Update', ['title' => 'Update','class' =>  'btn btn-primary','onclick'=>'SubmitForm("'.$model->formName().'",this);']) ?>
</div>-->
<?php ActiveForm::end(); ?>
<script>
function clearForm(form_id){
	$("input[type=text], textarea").val("");
}
$('textarea').bind('input', function(){ 
	$('#ClientCaseSummary #is_change_form').val('1'); 
	$('#ClientCaseSummary #is_change_form_main').val('1'); 
});
$('document').ready(function(){
	$('#active_form_name').val('ClientCaseSummary');
});
</script>
<noscript></noscript>
