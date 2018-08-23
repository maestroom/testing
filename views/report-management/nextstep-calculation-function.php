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
?>
<?= IsataskFormFlag::widget(); // change flag ?>
<fieldset class="one-cols-fieldset">
    <div class="create-form">
	<div class="form-group">
		<div class="row input-field">
			<div class="col-md-4">Create Function Definition (Params)</div>
			<div class="col-md-8">
				<a href="javascript:void(0);" onclick="addFnParams();" class="btn btn-primary">Select Field Params</a>
			</div>
		</div>	
	</div>
	 <div class="form-group">
		<div class="row input-field">
			<div class="col-xs-12">
				<div class="table-responsive marlr2">
					<table class="table table-striped calculation-function-params" id="claculation_function_params" width="100%" cellspacing="0" cellpadding="0" border="0">
					<thead>
						<tr><th scope="col"><a href="javascript:void(0);" class="tag-header-black" title="Related Table">Related Table</a></th><th scope="col"><a href="javascript:void(0);" class="tag-header-black" title="Field Params">Field Params</a></th><th scope="col"><a href="javascript:void(0);" class="tag-header-black" title="Type">Type</a></th><th><a href="javascript:void(0);" class="tag-header-black" title="Action">Action</a></th></tr>
					</thead>
				   	<tbody id="tbodyid">
						<?php 
							if(!empty($tableFieldParamsList)){
								$i=0;
									foreach($tableFieldParamsList as $fn_paramsfields){ ?>
										<tr id="trfnparams_<?=$i?>">
											<td class="tble"><?=$fn_paramsfields->reportsTables->table_name?></td>
											<td><span class="filed"><?=$fn_paramsfields->field_name?></span><input type="hidden" name="params[]" value="<?=$fn_paramsfields->id?>" /></td>
											<td class="type"><?=$fn_paramsfields->reportsFieldType->field_type?></td>
											<td><a href="javascript:void(0);" aria-label="Delete" aria-label="Delete" onclick="RemoveParams('<?=$i?>');"><em class="fa fa-close text-primary"></em></a></td>
										</tr>
									<?php $i++; 
								}
							}
						?>
					</tbody>
					</table>
				</div>
			</div>	
		</div>
	</div>		
	<?= $form->field($model, 'function_params', ['template' => "<div class='row input-field'><div class='col-xs-12'>{label}</div><div class='col-xs-12'>{input}\n{hint}\n{error}</div></div>", 'labelOptions' => ['class' => 'form_label required']])->textArea(['rows' => '6','readonly'=>'readonly'])->label(false); ?>    			
	<?= $form->field($model, 'mysql_function_code', ['template' => "<div class='row input-field'><div class='col-xs-12'>{label}</div><div class='col-xs-12'>{input}\n{hint}\n{error}</div></div>", 'labelOptions' => ['class' => 'form_label required']])->textArea(['rows' => '12']); ?>		
    <?= $form->field($model, 'mssql_function_code', ['template' => "<div class='row input-field'><div class='col-xs-12'>{label}</div><div class='col-xs-12'>{input}\n{hint}\n{error}</div></div>", 'labelOptions' => ['class' => 'form_label required']])->textArea(['rows' => '12']); ?>
    <?= $form->field($model, 'function_name', ['template' => "<div class='row input-field hidden'><div class='col-xs-12'>{label}</div><div class='col-xs-12'>{input}\n{hint}\n{error}</div></div>", 'labelOptions' => ['class' => 'form_label required']])->hiddenInput()->label(false); ?>    	
	<?= $form->field($model, 'function_display_name', ['template' => "<div class='row input-field hidden'><div class='col-xs-12'>{label}</div><div class='col-xs-12'>{input}\n{hint}\n{error}</div></div>", 'labelOptions' => ['class' => 'form_label required']])->hiddenInput()->label(false); ?>    	
    <?= $form->field($model, 'function_desc', ['template' => "<div class='row input-field hidden'><div class='col-xs-12'>{label}</div><div class='col-xs-12'>{input}\n{hint}\n{error}</div></div>", 'labelOptions' => ['class' => 'form_label required']])->hiddenInput()->label(false); ?>    	
    <?= $form->field($model, 'id', ['template' => "<div class='row input-field hidden'><div class='col-xs-12'>{label}</div><div class='col-xs-12'>{input}\n{hint}\n{error}</div></div>", 'labelOptions' => ['class' => 'form_label required']])->hiddenInput()->label(false); ?>
    </div>	    
</fieldset>
<div class="button-set text-right">
<input type="hidden" value="<?php if(isset($tables) && !empty($tables)){ echo implode("|",$tables);}?>" name="table_names" id="table_names" />	
<?= Html::button('Previous', [ 'title' =>'Previous', 'class' => 'btn btn-primary', 'style' => 'margin-right:3px','onclick'=>'PreviousCalFn("'.$model->formName().'");']) ?>
<?= Html::button($model->isNewRecord ? 'Cancel' : 'Delete', ['title' => $model->isNewRecord ? 'Cancel' : 'Delete', 'class' => 'btn btn-primary', 'onclick' => $model->isNewRecord ? 'loadReportCalculationFunctionCancel();' : 'removeReportSingleData("' . $model->id . '","' . $model->function_name . '","calculation-function");']) ?>    
<?= Html::button('Clear', ['title' => 'Clear', 'class' => 'btn btn-primary', 'onclick' => 'ClearFnData();']) ?>    
<?= Html::button($model->isNewRecord ? 'Add' : 'Update', [ 'title' => $model->isNewRecord ? 'Add' : 'Update', 'class' => 'btn btn-primary','onclick'=>'SubmitForm("'.$model->formName().'",this,"loadReportCalculationFunction()","reportform_div");'])?>
</div>    
<?php ActiveForm::end(); ?>
<script>
jQuery(document).ready(function () {
	$('document').ready(function(){ $("#active_form_name").val('ReportsCalculationFunction'); });
	$('#<?= $model->formName() ?>').submit(function () {
		SubmitAjaxForm("<?= $model->formName() ?>", this, "loadReportCalculationFunction()", "reportform_div");            
	});
}); 
function ClearFnData(){
	$("#tbodyid").empty();
	$("#reportscalculationfunction-function_params").val(null);
	$("#reportscalculationfunction-mysql_function_code").val(null);
	$("#reportscalculationfunction-mssql_function_code").val(null);
}
function SubmitForm(from_id,obj,callbak,replaceDiv){
	$.ajax({
		url:baseUrl+'report-management/validate-nextcalfn&flag=all',
		type:'post',
		data:$("#"+from_id).serialize(),
		success:function(response){
			if(response.length==0){
				<?php if($model->isNewRecord){?>
					$("#"+from_id).attr('action',baseUrl+'report-management/create-calculation-function');
				<?php } else {?>
					$("#"+from_id).attr('action',baseUrl+'report-management/update-calculation-function&id='+$('#reportscalculationfunction-id').val());
				<?php }?>
				SubmitAjaxForm('<?php echo $model->formName()?>',obj,"loadReportCalculationFunction()","reportform_div");
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
function PreviousCalFn(from_name){
	$.ajax({
		url:baseUrl+'report-management/previouscalfn',
		type:'post',
		data:$("#"+from_name).serialize(),
		success:function(response){
			$('#reportform_div').html(response);
		}
	});
}
function addFnParams(){
	$.ajax({
		url:baseUrl+'report-management/functionparams',
		type:'post',
		beforeSend:function (data) {showLoader();},
		success:function(response){
			hideLoader();
			if($('body').find('#availabl-field-with-types').length == 0){
				$('body').append('<div class="dialog" id="availabl-field-with-types" title="Select Field Params"></div>');
			}
			$('#availabl-field-with-types').html('').html(response);							
			$('#availabl-field-with-types').dialog({ 
			modal: true,
			width:'40em',
			create: function(event, ui){ 
				$('.ui-dialog-titlebar-close').append('<span class="ui-button-icon-primary ui-icon"></span>');
				$('.ui-dialog-titlebar-close').prop('title', 'Close');
				$('.ui-dialog-titlebar-close').prop('aria-label', 'Close');
			},
			close:function(){
				$(this).dialog('destroy').remove();
			},
			buttons: [
						{ 
						  text: "Cancel", 
						  "class": 'btn btn-primary',
						  "title": 'Cancel',
						  click: function () { 
							  $(this).dialog('destroy').remove();
						  } 
					   },
					   { 
						  text: "Update", 
							"class": 'btn btn-primary',
							"title": 'Update',
							click: function () { 	
								if($('#availabl-field-with-types .inner_check:checked').length > 0){
									$('#availabl-field-with-types .inner_check:checked').each(function(i){
										var tbl   = $(this).data('tablename');
										var field = $(this).data('fieldname');
										var field_id = $(this).data('fieldid');
										var type  = $(this).data('fieldtype');
										$('#claculation_function_params tbody').append('<tr id="trfnparams_'+i+'"><td class="tble">'+tbl+'</td><td><span class="filed">'+field+'</span><input type="hidden" name="params[]" value="'+field_id+'" /></td><td class="type">'+type+'</td><td><a href="javascript:void(0);" aria-label="Remove Params" onclick="RemoveParams('+i+');"><em class="fa fa-close text-primary"></em></a></td></tr>');
									});
									/* change flag */
									$("#ReportsCalculationFunction #is_change_form").val('1');
									$("#is_change_form_main").val('1');
									/* end */
									prepareFunDefinition();
									$(this).dialog('destroy').remove();
								}else{
									alert('Please Select Field Params');
									return false;
								}
							}
					  }
				]
			});		
			$('#availabl-field-with-types').find('input').customInput();
		}
	});
	/*var param = $('#reportscalculationfunction-fn_params').val();
	var type = $('#reportscalculationfunction-fn_params_type').val();
	var i=0;
	if($('#claculation_function_params tbody tr:last').length){
		i=parseInt($('#claculation_function_params tbody tr:last').attr('id').replace(/[^0-9\.]/g, '')) + 1;
	}
	if(param != '' && type !=''){
		$('#claculation_function_params tbody').append('<tr id="trfnparams_'+i+'"><td>'+param+'<input type="hidden" name="params[]" value="'+param+'" /></td><td>'+type+'<input type="hidden" name="params_type[]" value="'+type+'" /></td><td><a href="javascript:void(0);" onclick="RemoveParams('+i+');"><em class="fa fa-close text-primary"></em></a></td></tr>');
	}*/
}
function RemoveParams(i){
	$('#trfnparams_'+i).remove();
	/* Change Flag */
	$("#ReportsCalculationFunction #is_change_form").val('1');
	$("#is_change_form_main").val('1');
	/* end */
	prepareFunDefinition();
}
function prepareFunDefinition(){
	var fn_name=$('#reportscalculationfunction-function_name').val();
	var params = "";
	if($('#claculation_function_params tbody tr').length > 0){
		$('#claculation_function_params tbody tr').each(function(){
			if(params == "")
				params = $(this).find('td.tble').html()+'_'+$(this).find('span.filed').html()+' '+$(this).find('td.type').html();
			else	
				params =  params + ', '+$(this).find('td.tble').html()+'_'+$(this).find('span.filed').html()+' '+$(this).find('td.type').html();
		});
		$('#reportscalculationfunction-function_params').val(fn_name+'('+params+')');
	}else{
		$('#reportscalculationfunction-function_params').val(null);
	}
}
</script>
<noscript></noscript>
