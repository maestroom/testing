<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\components\IsataskFormFlag;

/* @var $this yii\web\View */
/* @var $model app\models\ReportsFieldType */
/* @var $form yii\widgets\ActiveForm */
$form = ActiveForm::begin(['id' => $model->formName(), 'enableAjaxValidation' => false, 'enableClientValidation' => true]);
?>
<?= IsataskFormFlag::widget(); // change flag ?>
<fieldset class="one-cols-fieldset">
    <div class="create-form">
        <?= $form->field($model, 'field_type', ['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>", 'labelOptions' => ['class' => 'form_label required']])->textInput(['maxlength'=>$model_field_length['field_type']]); ?>    	            
        <div class="form-group required field-reportsfieldtype-field_type_theme">
            <div class="custom-full-width">
            <div class="row input-field">
                <div class="col-md-3">
                    <label class="form_label required">Field Type Theme</label>
                </div>
                <div class="col-md-7 radiobtnvalidation">
                    <fieldset><legend class="sr-only">Field Type Theme</legend>
                    <?php if(isset($themeList) && !empty($themeList)) {
                        $ariaPosinset = 1;
                        foreach ($themeList as $single){ ?>                    
                        <input type="radio" aria-setsize="4" aria-posinset="<?=$ariaPosinset++?>" name="ReportsFieldType[field_type_theme_id]" title="This field is required" class="fieldtype-theme" id="fieldtype-theme<?=$single['id']?>" value="<?=$single['id']?>" <?php if($model->field_type_theme_id== $single['id']){?>checked="checked"<?php }?>/>
			<label for="fieldtype-theme<?=$single['id']?>" aria-label="Field Type Theme, <?=$single['field_type_theme']?>"><?=$single['field_type_theme']?></label>                    
                    <?php } 
                            } ?>
                    </fieldset>
                    <div class="helptextforvalidate help-block col-md-12"></div>
                </div>                    
            </div>
        </div>
       </div>    
    </div>	
</fieldset>
<div class="button-set text-right">
<?= Html::button($model->isNewRecord ? 'Cancel' : 'Delete', ['title' => $model->isNewRecord ? 'Cancel' : 'Delete', 'class' => 'btn btn-primary', 'onclick' => $model->isNewRecord ? 'loadReportFieldTypesCancel();' : 'removeReportSingleData("' . $model->id . '","' . $model->field_type . '","field-type");']) ?>    
<?= Html::button($model->isNewRecord ? 'Add' : 'Update', [ 'title' => $model->isNewRecord ? 'Add' : 'Update', 'class' => 'btn btn-primary','onclick'=>'savefieltypes();']) ?>
</div>    
<?php ActiveForm::end(); ?>
<script>
	jQuery('input').bind('input', function(){
		$('#ReportsFieldType #is_change_form').val('1');
		$('#ReportsFieldType #is_change_form_main').val('1');
	});
	jQuery(':radio').change(function(){
		$('#ReportsFieldType #is_change_form').val('1');
		$('#ReportsFieldType #is_change_form_main').val('1');
	});
    jQuery(document).ready(function () {
		$('#active_form_name').val('ReportsFieldType'); // form name
        $('#<?= $model->formName() ?>').submit(function () {
            SubmitAjaxForm("<?= $model->formName() ?>", this, "loadReportFieldTypes()", "reportform_div");            
        });
        $('input').customInput();        
    });
    function savefieltypes()
    {
        if($('#reportsfieldtype-field_type').val()==''){
                $("#reportsfieldtype-field_type").next().html('Please Enter Field Type.');
                $("#reportsfieldtype-field_type").parent().parent().parent().addClass('has-error');
                return false;
        }
        if($('.fieldtype-theme:checked').length == 0) { 
            $(".radiobtnvalidation .helptextforvalidate").html('Please Select Field Type Theme.');
            $(".radiobtnvalidation").parent().parent().parent().addClass('has-error');
            return false;
        } else {            
            $(".radiobtnvalidation .helptextforvalidate").html('');
        }           
        SubmitAjaxForm("<?= $model->formName() ?>", this, "loadReportFieldTypes()", "reportform_div");
    }
</script>
<noscript></noscript>
