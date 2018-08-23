<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\widgets\Select2;
use kartik\widgets\DepDrop;

/* @var $this yii\web\View */
/* @var $model app\models\ReportsFieldCalculations */
/* @var $form yii\widgets\ActiveForm */
$form = ActiveForm::begin(['id' => $model->formName(), 'enableAjaxValidation' => false, 'enableClientValidation' => true]);
if(isset($tables) && !empty($tables)){
	$model->primary_tables=$tables;
}
?>
<fieldset class="one-cols-fieldset">
    <div class="create-form">

	<?= $form->field($model, 'sp_name', ['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-9'>{input}\n{hint}\n{error}</div></div>", 'labelOptions' => ['class' => 'form_label required']])->textInput(); ?>    	
    <?= $form->field($model, 'sp_desc', ['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-9'>{input}\n{hint}\n{error}</div></div>", 'labelOptions' => ['class' => 'form_label required']])->textArea(['rows' => '6']); ?>    	
    <?= $form->field($model, 'primary_tables',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-9'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->widget(Select2::classname(), [
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
	<?php //$form->field($model, 'sp_params', ['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-9'>{input}\n{hint}\n{error}</div></div>", 'labelOptions' => ['class' => 'form_label required']])->textArea(['rows' => '6','id'=>'reportscalculationsp-sp_definition']); ?>    	
	<?php if(Yii::$app->db->driverName == 'mysql'){ ?>
		<?= $form->field($model, 'mysql_sp_code', ['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-9'>{input}\n{hint}\n{error}</div></div>", 'labelOptions' => ['class' => 'form_label required']])->textArea(['rows' => '12']); ?>		
    <?php }else{ ?>
		<?= $form->field($model, 'mssql_sp_code', ['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-9'>{input}\n{hint}\n{error}</div></div>", 'labelOptions' => ['class' => 'form_label required']])->textArea(['rows' => '12']); ?>
    <?php }?>
    <div class="form-group">
		<div class="row input-field">
			<div class="col-md-3">Store Procedure Params</div>
			<div class="col-md-9">
				   <div class="col-md-5">
						<input id="reportscalculationsp-sp_params" class="form-control col-md-3" name="ReportsCalculationSp[sp_params]" value="" aria-required="true" type="text" placeholder="Enter Store Procedure Params Name.">
				   </div>
				   <div class="col-md-5">
						<input id="reportscalculationsp-sp_params_type" class="form-control col-md-3" name="ReportsCalculationSp[sp_params_type]" value="" aria-required="true" type="text" placeholder="Enter Store Procedure Params Type.">
				   </div>
				   <div class="col-md-2">
						<a href="javascript:void(0);" onclick="addSpParams();"><em class="fa fa-plus"></em></a>
				   </div>
			</div>
		</div>	
	</div>			
    <div class="form-group">
		<div class="row input-field">
			<div class="col-md-3">&nbsp;</div>
			<div class="col-md-9">
				<div class="table-responsive">
					<table class="table table-striped" id="claculation_sp_params" width="100%" cellspacing="0" cellpadding="0" border="0">
					<thead>
						<tr><th scope="col"><a href="javascript:void(0);" class="tag-header-black" title="Params">Params</a></th><th scope="col"><a href="javascript:void(0);" class="tag-header-black" title="Type">Type</a></th><th><a href="javascript:void(0);" class="tag-header-black" title="Action">Action</a></th></tr>
					</thead>
				   	<tbody>
						<?php if(!empty($spparams)){
								$i=0;
								foreach($spparams as $sppa){?>
								<tr id="trspparams_<?php echo $i?>">
									<td><?php echo $sppa->params?>
									<input type="hidden" name="params[]" value="<?php echo $sppa->params?>" />
									</td>
									<td><?php echo $sppa->type?>
									<input type="hidden" name="params_type[]" value="<?php echo $sppa->type?>" />
									</td>
									<td><a href="javascript:void(0);" onclick="RemoveSpParams(<?php echo $i?>);" aria-label="Remove"><em class="fa fa-close text-primary"></em></a></td>
								</tr>	
								<?php $i++;}
						}?>
				   	</tbody>
				   </table>
				</div>
			</div>				
		</div>
	</div>
	
    </div>	
</fieldset>
<div class="button-set text-right">
<?= Html::button($model->isNewRecord ? 'Cancel' : 'Delete', ['title' => $model->isNewRecord ? 'Cancel' : 'Delete', 'class' => 'btn btn-primary', 'onclick' => $model->isNewRecord ? 'loadReportCalculationSp();' : 'removeReportSingleData("' . $model->id . '","' . $model->sp_name . '","calculation-sp");']) ?>    
<?= Html::button($model->isNewRecord ? 'Add' : 'Update', [ 'title' => $model->isNewRecord ? 'Add' : 'Update', 'class' => 'btn btn-primary','onclick'=>'SubmitAjaxForm("'.$model->formName().'",this,"loadReportCalculationSp()","reportform_div");']) ?>
</div>    
<?php ActiveForm::end(); ?>
<script>
jQuery(document).ready(function () {
	$('#<?= $model->formName() ?>').submit(function () {
		SubmitAjaxForm("<?= $model->formName() ?>", this, "loadReportCalculationSp()", "reportform_div");            
	});
}); 
function addSpParams(){
	var param = $('#reportscalculationsp-sp_params').val();
	var type = $('#reportscalculationsp-sp_params_type').val();
	var i=0;
	if($('#claculation_sp_params tbody tr:last').length){
		i=parseInt($('#claculation_sp_params tbody tr:last').attr('id').replace(/[^0-9\.]/g, '')) + 1;
	}
	if(param != '' && type !=''){
		$('#claculation_sp_params tbody').append('<tr id="trspparams_'+i+'"><td>'+param+'<input type="hidden" name="params[]" value="'+param+'" /></td><td>'+type+'<input type="hidden" name="params_type[]" value="'+type+'" /></td><td><a href="javascript:void(0);" onclick="RemoveParams('+i+');" aria-label="Remove Params"><em class="fa fa-close text-primary"></em></a></td></tr>');
	}
}
function RemoveSpParams(i){
	$('#trspparams_'+i).remove();
}
</script>
<noscript></noscript>
