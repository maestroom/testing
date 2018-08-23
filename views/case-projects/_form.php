<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use beastbytes\wizard\WizardMenu;
?>

<?php 
echo WizardMenu::widget(['step' => $event->step, 'wizard' => $event->sender]);
$form = ActiveForm::begin(['action'=> $model->isNewRecord ?Url::to(['case-projects/add']):Url::to(['case-projects/edit']),'id' => $model->formName(),'enableAjaxValidation' => false,'enableClientValidation' => true,'options' => ['enctype'=>'multipart/form-data','onsubmit'=>'return validateproject();'],]); ?>
<fieldset class="one-cols-fieldset">
    <div class="email-confrigration-table sla-bus-hours">
    
    <?= $form->field($instruct_model, 'project_name',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(); ?>             
    
    <div class="form-group" >
     <div class='row input-field'>
        <div class='col-md-2'>Project Priority&nbsp;<span class="required-field">*</span></div>
        <div class='col-md-8'>      
                     <?php  
                   echo Select2::widget([
                    'model' => $instruct_model,
                    'attribute' => 'task_priority',
                    'data' => ArrayHelper::map($priorityList, 'id', 'priority'),
                    'options' => ['prompt' => false, 'id' => 'priority', 'class' => 'form-control'],
                    /*'pluginOptions' => [
                      'allowClear' => true
                    ]*/
                    ]); ?>
            </div>
     </div>
    </div>
    <?= $form->field($instruct_model, 'requestor',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(); ?>        
    <div class="form-group" >
     <div class='row input-field'>
        <div class='col-md-2'>Project Request Type</div>
        <div class='col-md-8'>      
                     <?php  
						echo Select2::widget([
							'model' => $instruct_model,
							'attribute' => 'task_projectreqtype',
							'data' => array('internal','external'),
							'options' => ['prompt' => false, 'id' => 'id', 'class' => 'form-control'],
							/*'pluginOptions' => [
							  'allowClear' => true
							]*/
						]); 
                    ?>
            </div>
     </div>
    </div>
</fieldset>

<div class="button-set text-right">
        <?= $form->field($model, 'id')->hiddenInput()->label(false); ?>
        <?= Html::button('Cancel', ['class' =>  'btn btn-primary','onclick'=>'location.href="index.php?r=case-projects/index&case_id='.$case_id.'";','title'=>'Cancel']) ?>
        <?= Html::submitButton($model->isNewRecord ? 'Add' : 'Update', ['class' =>'btn btn-primary','title'=>$model->isNewRecord ? 'Add' : 'Update']) ?>
</div>
    <?php ActiveForm::end(); ?>
<script>
    function validateproject()
    {
        $('div.help-block').remove();
        $('div.has-error').removeClass('has-error');
        if($('#priority').val() == ''){
            if($(this).parent().find("div.help-block").length==0)
            {
                $('#priority').parent().append('<div class="help-block">Project Priority cannot be blank.</div>');
                $('#priority').parent().parent().parent().addClass('has-error');
            }  
            return false;
        }
        return true;
    }
    $('#priority').change(function() {
        if($(this).val()!="")
        {
            if($(this).parent().find("div.help-block").length>0)
            {
                $(this).parent().find("div.help-block").remove();
                $(this).parent().parent().parent().removeClass("has-error");
            }
        }  
        else
        {
            if($(this).parent().find("div.help-block").length==0)
            {
                $(this).parent().append('<div class="help-block">Project Priority cannot be blank.</div>');
                $(this).parent().parent().parent().addClass('has-error');
            }
        }
    });
</script>
<noscript></noscript>
