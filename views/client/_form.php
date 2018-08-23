<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\Select2;
use app\components\IsataskFormFlag;
/* @var $this yii\web\View */
/* @var $model app\models\Client */
/* @var $form yii\widgets\ActiveForm */
?>
<?php $form = ActiveForm::begin(['id' => $model->formName(),'enableAjaxValidation' => false,'enableClientValidation' => true]); ?>
<?= IsataskFormFlag::widget(); // change flag ?>
<fieldset class="one-cols-fieldset">
    <div class="create-form">
    	<?= $form->field($model, 'client_name',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['maxlength'=>$model_field_length['client_name']]); ?>
    	<?= $form->field($model, 'description',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['maxlength'=>$model_field_length['description']]); ?>
    	
    	<?= $form->field($model, 'industry_id',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->widget(Select2::classname(), [
					'data' => $industryList,
					'options'=>['prompt'=>'Select Client Industry', 'nolabel' => true],
					/*'pluginOptions' => [
						'allowClear' => true
					],*/]);?>
    	
    	<?= $form->field($model, 'address1',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n<span>Street Address, P.O. Box, Company Name, C/O</span>\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['maxlength'=>$model_field_length['address1']]); ?>
    	
    	<?= $form->field($model, 'address2',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n<span>Apartment, Suite, Unit, Building, Floor, Etc</span>\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['maxlength'=>$model_field_length['address2']]); ?>
    	
    	<?= $form->field($model, 'city',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['maxlength'=>$model_field_length['city']]); ?>
    	
    	<?= $form->field($model, 'state',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['maxlength'=>$model_field_length['state']]); ?>
    	
    	<?= $form->field($model, 'zip',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['maxlength'=>$model_field_length['zip']]); ?>
    	
    	<?= $form->field($model, 'country_id',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->widget(Select2::classname(), [
					'data' => $countryList,
					'options'=>['prompt'=>'Select Country', 'nolabel' => true],
					/*'pluginOptions' => [
						'allowClear' => true
					],*/]);?>
    	
    	<?= $form->field($model, 'phone',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['maxlength'=>$model_field_length['phone']]); ?>
    	
    	<?= $form->field($model, 'fax',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['maxlength'=>$model_field_length['fax']]); ?>
    	
    	<?= $form->field($model, 'website',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['maxlength'=>$model_field_length['website']]); ?>
    	
    </div>	
</fieldset>
<div class="button-set text-right">
    <?= Html::button($model->isNewRecord ? 'Cancel' : 'Delete', ['title'=> $model->isNewRecord?'Cancel':'Delete','class' => 'btn btn-primary', 'onclick'=> $model->isNewRecord ? 'loadClient();' : 'removeClient('.$model->id.');']) ?>
    <?= Html::button($model->isNewRecord ? 'Add' : 'Update', ['title'=> $model->isNewRecord?'Add':'Update','class' =>  'btn btn-primary','onclick'=>'SubmitAjaxForm("'.$model->formName().'",this,"loadClient()","clientform_div");']) ?>
</div>
<?php ActiveForm::end(); ?>
<script>
	/** TeamForm **/
        $('input').bind('input', function(){
            $('#Client #is_change_form').val('1'); 
            $('#Client #is_change_form_main').val('1');
	}); 
        var count = 0;
        var frm_type = '<?= $model->isNewRecord ?>';
        $('#Client select').on('change', function() {
            if(count != 0 && frm_type == 1) {
                $('#Client #is_change_form').val('1');
                $('#Client #is_change_form_main').val('1'); 
            }
            if(frm_type != 1) {
                $('#Client #is_change_form').val('1');
                $('#Client #is_change_form_main').val('1'); 
            }
            count++;
	});
	$('document').ready(function(){
            $('#active_form_name').val('Client');
	});
</script>
