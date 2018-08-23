<?php 
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\components\IsataskFormFlag;

/* @var $this yii\web\View */
/* @var $model app\models\Pricing */
/* @var $form yii\widgets\ActiveForm */
$model->rate_type = $model->isNewRecord?1:$model->rate_type;
?>
<style>
table.display td a:focus{outline: 2px solid;}
table.display td a:focus .fa{background:transparent;}
</style>
<?php $form = ActiveForm::begin(['id' => $model->formName(),'enableAjaxValidation' => false,'enableClientValidation' => true]); ?>
<?= IsataskFormFlag::widget(); // change flag ?>
<fieldset class="one-cols-fieldset">
    <div class="create-form">
    	<?= $form->field($model, 'pricing_id')->hiddenInput(['value'=>$pricing_id])->label(false); ?>
        <fieldset>
    	<?= $form->field($model, 'rate_type',['template' => '<div class="row input-field custom-full-width"><div class="col-md-3">{label}</div><div class="col-md-8"><legend class="sr-only">Rate Type</legend><div class="row">{input}{error}{hint}</div></legend></div></div>','labelOptions'=>['class'=>'form_label']])->radioList([1=>'Single',2=>'Tier'],
			['item' => function($index, $label, $name, $checked, $value){    			
				$return = '<div class="col-sm-4">'; 
				if($checked) 
					$return .= '<input aria-setsize="2" aria-posinset="'.($index+1).'" id="'.$name.'-'.$value.'" checked="'.$checked.'"  type="radio" name="' . $name . '" value="' . $value . '"><label for="'.$name.'-'.$value.'" class="form_label">'.ucwords($label).'</label>';
				else
					$return .= '<input  aria-setsize="2" aria-posinset="'.($index+1).'" id="'.$name.'-'.$value.'"   type="radio" name="' . $name . '" value="' . $value . '"><label for="'.$name.'-'.$value.'" class="form_label">'.ucwords($label).'</label>';
				$return .= '</div>';
				return $return;
			}]
		) ?>
            </fieldset>
		<?= $form->field($model, 'rate_amount',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['class'=>'form-control numeric-field-qu','maxlength' => $pricing_rates_length['rate_amount']]) ?>
		<?= $form->field($model, 'cost_amount',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['class'=>'form-control numeric-field-qu','maxlength' => $pricing_rates_length['cost_amount']]) ?>
		<fieldset>
		<?= $form->field($model, 'team_loc',['template' => '<div class="row input-field"><div class="col-md-3">{label}</div><div class="col-md-8"><legend class="sr-only">Location(s)</legend>{input}{hint}{error}</div></div>','labelOptions'=>['class'=>'form_label']])->checkboxList($teamLocation,
		['item' => function($index, $label, $name, $checked, $value) {
			$return = '<div class="col-sm-12">';
			if($checked)
				$return .= '<input aria-labelledby="team_loc_lbl_chk_'.$index.'" id="'.$name.'-'.$value.'" checked="'.$checked.'"  type="checkbox" name="' . $name . '" value="' . $value . '" aria-required="true" title="This field is required" ><label for="'.$name.'-'.$value.'" class="form_label" id="team_loc_lbl_chk_'.$index.'">'.ucwords($label).'</label>';
			else
				$return .= '<input aria-labelledby="team_loc_lbl_chk_'.$index.'" id="'.$name.'-'.$value.'"  type="checkbox" name="' . $name . '" value="' . $value . '" aria-required="true" title="This field is required" ><label for="'.$name.'-'.$value.'" class="form_label" id="team_loc_lbl_chk_'.$index.'">'.ucwords($label).'</label>';
			$return .= '</div>';
			return $return;
		},'class'=>'custom-full-width']);?>
		</fieldset>
		<div class="form-group">
			<div class="row input-field custom-full-width">
				<div class="col-md-3"><label for="pricingrates-tiered" class="form_label field-pricingrates-tiered">Tier</label></div>
				<div class="col-md-8">
					<div class="row">
						<div class="col-md-4 field-pricingrates-tiered">
							<?= $form->field($model, 'tier_from',['template' => "<div class='row input-field'><div class='col-md-12'>{label}</div><div class='col-md-12'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['class'=>'form-control numeric-field-qu','maxlength' => $pricing_rates_length['tier_from']]) ?>
						</div>
						<div class="col-md-4 field-pricingrates-tiered">
							<?= $form->field($model, 'tier_to',['template' => "<div class='row input-field'><div class='col-md-12'>{label}</div><div class='col-md-12'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['class'=>'form-control numeric-field-qu','maxlength' => $pricing_rates_length['tier_to']]) ?>
						</div>
						<div class="col-md-4">
							<div class="form-group field-pricingrates-tier_to">
								<div class="row input-field">
									<div class="col-md-12">
										<label for="pricingrates-tier_to" class="form_label field-pricingrates-tiered">
											&nbsp;
										</label>
									</div>
									<div class="col-md-12">
										<?= Html::button('Add', ['title'=> 'Add', 'class' => 'btn btn-primary', 'onclick'=>'AddPricingRange();']) ?>
									</div>		
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="form-group table-pricingrates-tiered">
			<div class="row input-field custom-full-width">
				<div class="col-md-12">
					<div class="table-responsive table-tbody-scroll">
						<div class="dataTables_wrapper no-footer" id="example_wrapper">
								<table id="pricing_rate_table" class="display dataTable no-footer table table-striped" summary="Added Pricing Rates">
									<?php 
										if($data!=''){
											echo $data;
										} else {
									?>
									<thead> 
										<tr>
											<th  class="sorting_disabled price-location-width" scope="col"><a href="javascript:void(0);" title="Location" class="tag-header-black">Location</a></th>
											<th  class="sorting_disabled price-bill-width" scope="col"><a href="javascript:void(0);" title="Bill" class="tag-header-black">Bill</a></th>
											<th  class="sorting_disabled price-cost-width" scope="col"><a href="javascript:void(0);" title="Cost" class="tag-header-black">Cost</a></th>
											<th  class="sorting_disabled ifistiered price-range-width" scope="col"><a href="javascript:void(0);" title="Range" class="tag-header-black">Range</a></th>
											<th  class="sorting_disabled text-center" scope="col"><input type="hidden" name="rate_type_added" id="rate_type_added" value=""/><a href="javascript:void(0);" title="Action" class="tag-header-black">Action</a></th>
										</tr>
									</thead>
									<tbody class="tbodyClass"><tr class="odd no-rows"><td colspan="4">No Rates added yet</td></tr></tbody>
								<?php } ?>
							</table>
						</div>
					</div>	
				</div>
			</div>
		</div>	
    </div>	
</fieldset>
<?php ActiveForm::end(); ?>
<script>

$('input').customInput();
$('.field-pricingrates-tiered').hide();
if($('form#PricingRates #rate_type_added').val() != 2){
	$('form#PricingRates .ifistiered').hide();
}
/* Event Changed */
$('document').ready(function(){
	$('#active_form_name').val('PricingRates'); // change form name
});
$('input').bind("input", function(){ 
	$('#PricingRates #is_change_form').val('1');
	$('#PricingRates #is_change_form_main').val('1');
});
$(':checkbox').change(function(){
	$('#PricingRates #is_change_form').val('1');
	$('#PricingRates #is_change_form_main').val('1');
});
$(':radio').change(function(){
	$('#PricingRates #is_change_form').val('1');
	$('#PricingRates #is_change_form_main').val('1');
});
</script>
<noscript></noscript>
