<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use kartik\widgets\Select2;
use app\components\IsataskFormFlag;
// form -tax- codes
?>
<style>
<!--
form#TaxCode table thead th{
	font-size:12px;
}
-->
</style>
<?php $form = ActiveForm::begin(['id' => $model->formName(),'enableAjaxValidation' => false,'enableClientValidation' => true]); ?>
	<?= IsataskFormFlag::widget(); // change flag ?>
	<fieldset class="one-cols-fieldset">
	<div class="email-confrigration-table sla-bus-hours">
	    <input type="hidden" value="<?php echo $model->id; ?>" name="id">
	    <?= $form->field($model, 'tax_code',['template' => "<div class='row'><div class='col-md-2'>{label}<span class='text-danger'>*</span></div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['maxlength'=>$tax_code_length['tax_code']])->label('Code Name'); ?>
	    <div class='row input-field'>
	    	<div class="form-group">
		    	<div class='col-md-2'>
		    		<label for="taxcode-tax_class_id">Tax Class</label><span class='text-danger'>*</span>
		    	</div>
			    <div class='col-md-7'>
				<?php
				   echo Select2::widget([
						'model' => $model,
						'attribute' => 'tax_class_id',
						'data' => $tax_classes,
						'options' => [
							'prompt' => 'Select Tax Class', 
							'id' => 'taxcode-tax_class_id', 
							'class' => 'form-control', 
							'aria-required' => 'true',
							'nolabel'=>true
						],
						'pluginOptions' => [
							 'allowClear' => false,
							 
							 
						]
					]);
				?>
				<div id="taxcode-tax_class_id_error" class="help-block"></div>
				</div>
			</div>
		</div>
	    <?= $form->field($model, 'tax_rate',['template' => "<div class='row'><div class='col-md-2'>{label}<span class='text-danger'>*</span></div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['placeholder' => '%','maxlength'=>$tax_code_length['tax_rate']])->label('Code Rate (%)'); ?>
	    <?= $form->field($model, 'tax_code_desc',['template' => "<div class='row'><div class='col-md-2'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textArea(['rows' => 5,'colm' => 5,'maxlength'=>$tax_code_length['tax_code_desc']])->label('Code Description'); ?>
			<div class='row input-field'>
				<div class="form-group">
					<div class='col-md-2'>
						Clients<span class='text-danger'>*</span>
					</div>
					<div class='col-md-7'>
						<?php $tax_code_id = $model->isNewRecord?0:$model->id; ?>
						<?= Html::Button('Add Clients', ['aria-label'=>"Required",'title' => 'Add Clients','class' => 'btn btn-primary', 'id' => 'pricepoint-tax-classes', 'onClick' => 'selectedclients('.$tax_code_id.');']) ?>
					</div>
				</div>
			</div>

			<div class="row input-field">
				<div class="form-group">
					<div class="col-md-2"></div>
					<div class="col-md-7">
						<!-- table stripped -->
							<table class="table table-striped table-inner-table" id="form-tax-codes" width="100%" cellspacing="0" cellpadding="0" border="0">
								<thead>
									<tr>
										<th class="client-name-th"><a href="javascript:void(0);" class="tag-header-black" title="Client Name"><strong>Client Name</strong></a></th>
										<th class="third-th text-center"><a href="javascript:void(0);" class="tag-header-black" title="Actions"><strong>Actions</strong></a></th>
									</tr>
								</thead>
								<tbody>
									<?php if(isset($taxcodeclients) && !empty($taxcodeclients)){ ?>
										<?php foreach($taxcodeclients as $taxcode){ ?>
											<tr class="clients_<?php echo $taxcode['id']; ?>">
												<input type="hidden" name="clients[]" class="clients" value="<?php echo $taxcode['id']; ?>" />
												<td class="client-name-td"><?php echo $taxcode['client_name']; ?></td>
												<td class="third-td text-center "><a href="javascript:void(0);" aria-label="Delete" onClick="remove_clientcode('<?php echo $taxcode['id']; ?>');" class="icon-fa"><em class="fa fa-close text-primary" title="Delete"></em></a></td>
											</tr>
										<?php } ?>
									<?php } ?>
								</tbody>
							</table>
						<!-- End table -->
					</div>
					<div id="taxcode-clients" class="has-error"></div>
				</div>
			</div>
		</div>
	</fieldset>
    <div class="button-set text-right">
    	<?= Html::Button('Cancel', ['title' => 'Cancel','class' => 'btn btn-primary', 'id' => 'cancel-tax-classes']) ?>
        <?= Html::Button($model->isNewRecord ? 'Add' : 'Update', ['title' => $model->isNewRecord ? 'Add' : 'Update','class' => 'btn btn-primary','onClick' => 'savetaxcodes("'.$model->formName().'");']) ?>
    </div>
<?php ActiveForm::end(); ?>
<style>#taxcode-clients{height:150px;}</style>
<script>
/* Change Flag */
$('input').bind("input", function(){
	$('#TaxCode #is_change_form').val('1');
	$('#TaxCode #is_change_form_main').val('1');
});
$('select').on('change',function(){
	$('#TaxCode #is_change_form').val('1');
	$('#TaxCode #is_change_form_main').val('1');
});
$('textarea').bind('input', function(){
	$('#TaxCode #is_change_form').val('1');
	$('#TaxCode #is_change_form_main').val('1');
});
$('document').ready(function(){
	$('#active_form_name').val('TaxCode');
});
$('#cancel-tax-classes').click(function(){
	loadercodepage();
	//showLoader();
	//location.href = baseUrl +'/billing-taxes/tax-codes';
});

function savetaxcodes(form_id)
{
	var form = $('form#'+form_id);
	/*if($('#taxcode-tax_code').val()=='') {
		$("#taxcode-tax_code").next().html('Please Enter Tax Code.');
		$("#taxcode-tax_code").parent().parent().parent().addClass('has-error');
		return false;
	}
	if($('#taxcode-tax_class_id').val()==''){
		$("#taxcode-tax_class_id").next().html('Please Select Class Name.');
		$("#taxcode-tax_class_id").parent().parent().parent().addClass('has-error');
		return false;
	}
	if($('#taxcode-tax_rate').val()==''){
		$("#taxcode-tax_rate").next().html('Please Select Tax Rate.');
		$("#taxcode-tax_rate").parent().parent().parent().addClass('has-error');
		return false;
	}*/

	/*if($('.clients').length==0 || $('.clients').length==''){
		$("#taxcode-clients").next().html('Please Select Client.');
		$("#taxcode-clients").parent().parent().parent().addClass('has-error');
		return false;
	}*/

	$.ajax({
        url    : form.attr('action'),
        cache: false,
        type   : 'post',
        data   : form.serialize(),
        beforeSend : function()    {
			showLoader();
        },
        success: function (response){
			hideLoader();
		 	if(response == 'OK'){
				 loadercodepage();
 				//location.href = baseUrl +'/billing-taxes/tax-codes';
 			} else if(response == 'FAIL'){
 				$("#taxcode-clients").html('Please Select Client.');
				$("#taxcode-clients").addClass('has-error');
				$("#taxcode-clients").addClass('text-danger');
				return false;
 			} else {
 				hideLoader();
				for (var key in response) {
			 		if(key == 'taxcode-tax_class_id') {
						$("#"+key).next().next().html(response[key]);
					} else {
						$("#"+key).next().html(response[key]);
					}
					$("#"+key).parent().parent().parent().addClass('has-error');
				}

			}
        },
        error  : function (){
            console.log('internal server error');
        }
    });
}
</script>
<noscript></noscript>
