<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use app\components\IsataskFormFlag;
// form-tax-classes
?>
<style>
<!--
form#TaxClass table thead th{
	font-size:12px;
}
-->
</style>
<?php $form = ActiveForm::begin(['id' => $model->formName(),'enableAjaxValidation' => false,'enableClientValidation' => true]); ?>
	<?= IsataskFormFlag::widget(); // change flag ?>
	<fieldset class="one-cols-fieldset">
		<div class="email-confrigration-table sla-bus-hours">
			<input type="hidden" value="<?php echo $model->id; ?>" name="id">
			<?= $form->field($model, 'class_name',['template' => "<div class='row'><div class='col-md-2'>{label}<span class='text-danger'>*</span></div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['maxlength'=>$tax_class_length['class_name']]) ?>
			<?= $form->field($model, 'tax_class_desc',['template' => "<div class='row'><div class='col-md-2'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textArea(['rows' => '5','cols' => '5','maxlength'=>$tax_class_length['tax_class_desc']])->label('Class Description'); ?>
			<div class='row input-field'>
				<div class="form-group">
					<div class='col-md-2'>
						Price Points<span class='text-danger'>*</span>
					</div>
					<div class='col-md-7'>
						<?php $tax_id = $model->isNewRecord?0:$model->id; ?>
						<?= Html::Button('Add Price Points', ['aria-label'=>"Required",'title' => 'Add Price Points','class' => 'btn btn-primary', 'id' => 'pricepoint-tax-classes', 'onClick' => 'selectedpricepoint('.$tax_id.');']) ?>
					</div>
				</div>
			</div>
			
			<div class="row input-field">
				<div class="form-group">
					<div class="col-md-2"></div>
					<div class="col-md-7">
						<div class="table-responsive">
							<!-- table stripped -->
							<table class="table table-striped" id="form-tax-classes" width="100%" cellspacing="0" cellpadding="0" border="0">
								<thead>
									<tr>
										<th class="tax-team-name-th"><a href="javascript:void(0);" class="tag-header-black" title="Team Name"><strong>Team Name</strong></a></th>
										<th class="tax-pricepoint-th"><a href="javascript:void(0);" class="tag-header-black" title="Price Point"><strong>Price Point</strong></a></th>
										<th class="third-th text-center"><a href="javascript:void(0);" class="tag-header-black" title="Actions"><strong>Actions</strong></a></th>
									</tr>	
								</thead>
								<tbody>	
									<?php if(isset($pricePoint_data) && !empty($pricePoint_data)){ ?>
										<?php foreach($pricePoint_data as $key => $teams){ ?>
											<?php foreach($teams as $innerkey => $teamsval){ ?>
												<?php foreach($teamsval as $ks=>$val){ ?>
													<tr class="teamspricepoint_<?php echo $key; ?>_<?php echo $ks; ?>">
														<input type="hidden" name="pricepointlist[]" class="pricepointlist" value="<?= $ks; ?>" />
														<td class="tax-team-name-td"><?= $innerkey; ?></td>
														<td class="tax-pricepoint-td"><?= $val; ?></td>
														<td class="third-td text-center">
															<a href="javascript:void(0);" class="icon-fa" aria-label="Delete" onClick="remove_pricepoint('<?php echo $key; ?>','<?php echo $ks; ?>');"><em title="Delete" class="fa fa-close text-primary"></em></a>
														</td>
													</tr>
												<?php } ?>
											<?php } ?>
										<?php } ?>
									<?php } ?>
								</tbody>
							</table>
						<!-- End table -->
						</div>
					</div>
					<div id="taxclass-pricepointlist" class="has-error"></div>
				</div>
			</div>
		</div>
	</fieldset>
    <div class="button-set text-right">
    	<?= Html::Button('Cancel', ['title' => 'Cancel','class' => 'btn btn-primary', 'id' => 'cancel-tax-classes']) ?>
        <?= Html::Button($model->isNewRecord ? 'Add' : 'Update', ['title' => $model->isNewRecord ? 'Add' : 'Update','class' => 'btn btn-primary','onClick' => 'savetaxclasses("'.$model->formName().'");']) ?>
    </div>
<div class="dialog" id="availabl-price-points" title="Add Available Price Points"></div>
<?php ActiveForm::end(); ?>

<style>#taxclass-pricepoint{height:200px;}
.mycontainer .content ul {word-wrap: break-word;}
</style>
<script>
/* checkflag */
$('document').ready(function(){
	$("#active_form_name").val('TaxClass');
});
$('input').bind("input", function(){ 
	$('#TaxClass #is_change_form').val('1');	
	$('#TaxClass #is_change_form_main').val('1');	
});
$('textarea').bind('input', function(){
	$('#TaxClass #is_change_form').val('1');	
	$('#TaxClass #is_change_form_main').val('1');	
});

$('#cancel-tax-classes').click(function(){
	loaderclasspage();
	//location.href = baseUrl +'/billing-taxes/tax-classes';
});
function savetaxclasses(form_id)
{
	var form = $('form#'+form_id);
	/* if($('#taxclass-class_name').val()==''){
		$("#taxclass-class_name").next().html('Please Select Class Name.');
		$("#taxclass-class_name").parent().parent().parent().addClass('has-error');
		return false;
	} if($('.pricepointlist').length==0 || $('.pricepointlist').length==''){
		$("#taxclass-pricepointlist").next().html('Please Select Class Name.');
		$("#taxclass-pricepointlist").parent().parent().parent().addClass('has-error');
		return false;
	} */
	
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
				 loaderclasspage();
 				//location.href = baseUrl +'/billing-taxes/tax-classes';
 			} else if(response == 'FAIL'){
 				$("#taxclass-pricepointlist").html('Please Select Price Points.');
				$("#taxclass-pricepointlist").addClass('has-error');
				$("#taxclass-pricepointlist").addClass('text-danger');
				return false;
 			} else {
 				hideLoader();
			 	for (var key in response) {
			 		$("#"+key).next().html(response[key]);
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
