<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\widgets\Select2;
use kartik\widgets\DepDrop;
use app\components\IsataskFormFlag;

/* @var $this yii\web\View */
/* @var $model app\models\ReportsFieldCalculations */
/* @var $form yii\widgets\ActiveForm */
$form = ActiveForm::begin(['id' => $model->formName(), 'enableAjaxValidation' => false, 'enableClientValidation' => true]);
if(isset($tables) && !empty($tables)){
	$model->primary_tables=$tables;
}   
?>
<?= IsataskFormFlag::widget(); // change flag ?>
<fieldset class="one-cols-fieldset">
    <div class="create-form">
	
	<?= $form->field($model, 'function_name', ['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-9'>{input}\n{hint}\n{error}</div></div>", 'labelOptions' => ['class' => 'form_label required']])->textInput(['maxlength'=>$model_field_length['function_name']]); ?>    	
	<?= $form->field($model, 'function_display_name', ['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-9'>{input}\n{hint}\n{error}</div></div>", 'labelOptions' => ['class' => 'form_label required']])->textInput(['maxlength'=>$model_field_length['function_display_name']]); ?>    	
    <?= $form->field($model, 'function_desc', ['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-9'>{input}\n{hint}\n{error}</div></div>", 'labelOptions' => ['class' => 'form_label required']])->textArea(['rows' => '6','maxlength'=>$model_field_length['function_desc']]); ?>    	
    <?php /*= $form->field($model, 'primary_tables',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-9'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->widget(Select2::classname(), [
    'data' => $tableList,
    'options' => ['multiple' => true,'prompt' => false, 'id' => 'primary_tables'],
    'pluginOptions' => [
        'allowClear' => false,
    ],
])->label('Select Tables Required To Run Reports');;?>
    
    <?php /*echo $form->field($model, 'related_table',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-9'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->widget(DepDrop::classname(),[
			        	'type' => 2,
			        	'data' => (isset($lookup_table_data))?$lookup_table_data:array(),
			        	'options' => ['multiple' => true, 'title' => 'Select Related Tables', 'class' => 'form-control'],
			            'pluginOptions' => [
							'multiple' => true, 
				            'allowClear' => true,
				            'depends'=>['primary_tables'],
				            'placeholder' => 'Select Related Tables',
				            'url' => Url::toRoute(['report-management/getrelatedtable'])
			            ],
			            'pluginEvents' => [
			            	"depdrop.change"=>"function(event, id, value, count) { 
			            		if(value != ''){
				            		$(this).closest('div.form-group').removeClass('has-error');
									$(this).closest('div.form-group').removeClass('has-success'); 
									$(this).parent().parent().parent().find('.help-block').html('');
								}
							}",
			            ]
					])->label('Related Table'); */?>
	<?php /*= $form->field($model, 'function_params', ['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-9'>{input}\n{hint}\n{error}</div></div>", 'labelOptions' => ['class' => 'form_label required']])->textArea(['rows' => '6']); ?>    	
	<?php if(Yii::$app->db->driverName == 'mysql'){ ?>
		<?= $form->field($model, 'mysql_function_code', ['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-9'>{input}\n{hint}\n{error}</div></div>", 'labelOptions' => ['class' => 'form_label required']])->textArea(['rows' => '12']); ?>		
    <?php }else{ ?>
		<?= $form->field($model, 'mssql_function_code', ['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-9'>{input}\n{hint}\n{error}</div></div>", 'labelOptions' => ['class' => 'form_label required']])->textArea(['rows' => '12']); ?>
    <?php }?>
    
    <div class="form-group">
		<div class="row input-field">
			<div class="col-md-3">Function Params</div>
			<div class="col-md-9">
				   <div class="col-md-5">
						<input id="reportscalculationfunction-fn_params" class="form-control col-md-3" name="ReportsCalculationFunction[fn_params]" value="" aria-required="true" type="text" placeholder="Enter Function Params Name.">
				   </div>
				   <div class="col-md-5">
						<input id="reportscalculationfunction-fn_params_type" class="form-control col-md-3" name="ReportsCalculationFunction[fn_params_type]" value="" aria-required="true" type="text" placeholder="Enter Function Params Type.">
				   </div>
				   <div class="col-md-2">
						<a href="javascript:void(0);" onclick="addFnParams();"><em class="fa fa-plus"></em></a>
				   </div>
			</div>
		</div>	
	</div>			
    <div class="form-group">
		<div class="row input-field">
			<div class="col-md-3">&nbsp;</div>
			<div class="col-md-9">
				<div class="table-responsive">
					<table class="table table-striped" id="claculation_function_params" width="100%" cellspacing="0" cellpadding="0" border="0">
					<thead>
						<tr><th scope="col"><a href="javascript:void(0);" class="tag-header-black" title="Params">Params</a></th><th scope="col"><a href="javascript:void(0);" class="tag-header-black" title="Type">Type</a></th><th><a href="javascript:void(0);" class="tag-header-black" title="Action">Action</a></th></tr>
					</thead>
				   	<tbody>
						<?php if(!empty($fnparams)){
								$i=0;
								foreach($fnparams as $fnpa){?>
								<tr id="trfnparams_<?php echo $i?>">
									<td><?php echo $fnpa->params?>
									<input type="hidden" name="params[]" value="<?php echo $fnpa->params?>" />
									</td>
									<td><?php echo $fnpa->type?>
									<input type="hidden" name="params_type[]" value="<?php echo $fnpa->type?>" />
									</td>
									<td><a href="javascript:void(0);" onclick="RemoveParams(<?php echo $i?>);"><em class="fa fa-close text-primary"></em></a></td>
								</tr>	
								<?php $i++;}
						}?>
				   	</tbody>
				   </table>
				</div>
			</div>				
		</div>
	</div><?php */?>
	<?php 
	if(isset($req_params) && $req_params!=""){
			foreach($req_params as $params_id){?>
			<input type="hidden" name="params[]" value="<?=$params_id?>" />
	<?php }}?>
	<?= $form->field($model, 'function_params', ['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-9'>{input}\n{hint}\n{error}</div></div>", 'labelOptions' => ['class' => 'form_label required','aria-label'=>'Function Paramas']])->hiddenInput()->label(false); ?>    			
	<?= $form->field($model, 'mysql_function_code', ['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-9'>{input}\n{hint}\n{error}</div></div>", 'labelOptions' => ['class' => 'form_label required']])->hiddenInput()->label(false); ?>		
    <?= $form->field($model, 'mssql_function_code', ['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-9'>{input}\n{hint}\n{error}</div></div>", 'labelOptions' => ['class' => 'form_label required']])->hiddenInput()->label(false); ?>
    <?= $form->field($model, 'id', ['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-9'>{input}\n{hint}\n{error}</div></div>", 'labelOptions' => ['class' => 'form_label required']])->hiddenInput()->label(false); ?>    	
	</div>
</fieldset>
<div class="button-set text-right">
<input type="hidden" value="<?php if(isset($tables) && !empty($tables)){ echo implode("|",$tables);}?>" name="table_names" id="table_names" />	
<?= Html::button($model->isNewRecord ? 'Cancel' : 'Delete', ['title' => $model->isNewRecord ? 'Cancel' : 'Delete', 'class' => 'btn btn-primary', 'onclick' => $model->isNewRecord ? 'loadReportCalculationFunctionCancel();' : 'removeReportSingleData("' . $model->id . '","' . $model->function_name . '","calculation-function");']) ?>    
<?= Html::button('Next', [ 'title' =>'Next', 'class' => 'btn btn-primary','onclick'=>'NextCalFn("'.$model->formName().'");']) ?>
</div>    
<?php ActiveForm::end(); ?>
<script>
/* input change event */	
jQuery('input').bind('input', function(){
	$('#ReportsCalculationFunction #is_change_form').val('1');
	$('#ReportsCalculationFunction #is_change_form_main').val('1');
});
jQuery('textarea').bind('input', function(){
	$('#ReportsCalculationFunction #is_change_form').val('1');
	$('#ReportsCalculationFunction #is_change_form_main').val('1');
});
jQuery('select').on('change',function(){
	$('#ReportsCalculationFunction #is_change_form').val('1');
	$('#ReportsCalculationFunction #is_change_form_main').val('1');
});

jQuery(document).ready(function () {
	$('#active_form_name').val('ReportsCalculationFunction'); // form name
	$('#<?= $model->formName() ?>').submit(function () {
		SubmitAjaxForm("<?= $model->formName() ?>", this, "loadReportCalculationFunction()", "reportform_div");            
	});
}); 
function NextCalFn(from_name){
	$.ajax({
		url:baseUrl+'report-management/validate-nextcalfn',
		type:'post',
		data:$("#"+from_name).serialize(),
		success:function(response){
			if(response.length==0){
					$.ajax({
						url:baseUrl+'report-management/nextcalfn',
						type:'post',
						data:$("#"+from_name).serialize(),
						success:function(response){
							$('#reportform_div').html(response);
						}
					});
			}else{
				for (var key in response) {
					$("#"+key).parent().find('.help-block').html(response[key]);
					$("#"+key).closest('div.form-group').addClass('has-error');
				}
				return false;
			}
		}
	});
}
</script>
<noscript></noscript>
