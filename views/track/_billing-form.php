<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\components\IsataskFormFlag;

/* @var $this yii\web\View */
/* @var $model app\models\CaseCloseType */
/* @var $form yii\widgets\ActiveForm */
?>
<?php $form = ActiveForm::begin(['id' => $model->formName(),'enableAjaxValidation' => false,'enableClientValidation' => true]); ?>
<?= IsataskFormFlag::widget(); // change flag ?>
<fieldset class="one-cols-fieldset">
    <div class="create-form">
    <div class="form-group field-tasksunitsbilling-quantity">
	    <div class='row input-field'>
	    	<div class='col-md-3'><label class="form_label">Price Point</label></div>
	    	<div class='col-md-9'><strong><?=$model->pricing->price_point;?></strong></div>
	    </div>
    </div>
    <?= $form->field($model, 'quantity',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-9'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['class'=>'numeric-field-qu negative-key']);?>
    <?= $form->field($model, 'created',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-9 calender-group'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['maxlength'=>'10','readonly'=>'readonly'])->label('Bill Date'); ?>
    <?php if($model->pricing->is_custom==1){ ?>
    <?= $form->field($model, 'billing_desc',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-9'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textArea(['rows'=>3])->label('Custom Description');?>
    <?php }
    $model->nonbillableitem = $model->invoiced;
    ?>
    <?= $form->field($model, 'nonbillableitem',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-9'>{input}<label for='tasksunitsbilling-nonbillableitem'>Non Billable</label>\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->checkbox(['checked' => 'checked','label' => '','labelOptions'=>['class'=>'custom-full-width']]); ?>			        
    </div>
</fieldset>
<?php ActiveForm::end(); ?>
<script>
$('input').customInput();
var label_class = '<?php echo $model->invoiced;?>';
if(label_class == '2'){    
    $('#tasksunitsbilling-nonbillableitem').prop('checked', true);
    $('#tasksunitsbilling-nonbillableitem').next().addClass('checked');
}
datePickerController.createDatePicker({	                     
    formElements: { "tasksunitsbilling-created": "%m/%d/%Y"},
    callbackFunctions:{
		"datereturned":[changeflag],
	}
});
/* change event */
$('select').on('change', function(){
  $('#TasksUnitsBilling #is_change_form').val('1'); // change flag
  $('#is_change_form_main').val('1'); // change flag value
});
$('input').bind('input', function(){
  $('#TasksUnitsBilling #is_change_form').val('1'); // change flag
  $('#is_change_form_main').val('1'); // change flag value
});
$(':checkbox').change(function(){
  $('#TasksUnitsBilling #is_change_form').val('1'); // change flag
  $('#is_change_form_main').val('1'); // change flag value
});
$('textarea').bind("input",function(){
  $('#TasksUnitsBilling #is_change_form').val('1'); // change flag
  $('#is_change_form_main').val('1'); // change flag value
});


</script>
<noscript></noscript>
