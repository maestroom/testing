<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\Select2;
use app\components\IsataskFormFlag;

/* @var $this yii\web\View */
/* @var $model app\models\Pricing */
/* @var $form yii\widgets\ActiveForm */
$mode = $model->isNewRecord?"Add":"Edit";
$id = $model->isNewRecord?0:$model->id;
$pricing_type = $model->pricing_type;
$team_id = $pricing_type==0?$team_id:0;

?>
<?php $form = ActiveForm::begin(['id' => $model->formName(),'enableAjaxValidation' => false,'enableClientValidation' => true]); ?>
<?= IsataskFormFlag::widget(); // change flag ?>
<fieldset class="one-cols-fieldset">
    <div class="create-form sla-bus-hours">
    	<?= $form->field($model, 'team_id')->hiddenInput(['value' => $team_id])->label(false); ?>
    	<?= $form->field($model, 'pricing_type')->hiddenInput(['value' => $pricing_type])->label(false); ?>
    	<?= $form->field($model, 'remove')->hiddenInput(['value'=>0])->label(false); ?>
    	<?= $form->field($model, 'price_point',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-9'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['maxlength' =>  $pricing_length['price_point']]) ?>
    	<?php if($pricing_type == 0){ ?>
	    	<?= $form->field($model, 'pricing_rate',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-9'>".Html::button('Rate', ['title'=> 'Rate','aria-label'=>"Required", 'class' => 'btn btn-primary', 'onclick'=>'managePricingRate('.$id.',"'.$team_id.'");'])."{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->hiddenInput(); ?>
	    	<div class="form-group field-pricing-rate-table">
				<div class="row input-field">
					<div class="col-md-3">&nbsp;</div>
					<div class="col-md-9">
						<table id="pricing_rate_table" class="display dataTable no-footer table table-striped" summary="Added Pricing Rates">
							<?php 
								if($model->pricing_rate!='') {
									echo $model->pricing_rate;
								} else {
							?>
							<thead> 
								<tr>
									<th class="sorting_disabled price-location-width" scope="col"><a href="javascript:void(0);" title="Location" class="tag-header-black">Location</a></th>
									<th class="sorting_disabled price-bill-width" scope="col"><a href="javascript:void(0);" title="Bill" class="tag-header-black">Bill</a></th>
									<th class="sorting_disabled price-cost-width" scope="col"><a href="javascript:void(0);" title="Cost" class="tag-header-black">Cost</a></th>
									<th class="sorting_disabled ifistiered price-range-width" style="display:none;"; scope="col"><a href="javascript:void(0);" title="Range" class="tag-header-black">Range</a></th>
									<th class="sorting_disabled text-center third-th" scope="col"><input type="hidden" name="rate_type_added" id="rate_type_added" value=""/><a href="javascript:void(0);" title="Actions" class="tag-header-black">Actions</a></th>
								</tr>
							</thead>
							<tbody class="tbodyClass">
								<tr class="odd no-rows"><td colspan="4" style="text-align:center;">No Rates added yet</td></tr>
							</tbody>
								<?php 
								}
							?>
						</table>
					</div>
				</div>
			</div>
		<?php } else { ?>
			<?= $form->field($model, 'pricing_rate',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-9'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['maxlength' => $pricing_length['price_point']]) ?>
		<?php } ?>
    	<?php //echo $form->field($model, 'unit_price_id',['template' => "<div class='row input-field'><div class='col-md-3'>{label}<span class='text-danger'>*</span></div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->dropDownList($listunitType,['prompt'=>'-Select Rate Unit-']); ?>
    	<?= $form->field($model, 'unit_price_id',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-9'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->widget(Select2::classname(), [
	    'data' => $listunitType,
                'options' => ['prompt' => 'Select Rate Unit', 'id' => 'pricing-unit_price_id','aria-label'=>'Rate Unit,','nolabel'=>true],
	    /*'pluginOptions' => [
	        'allowClear' => true
	    ],*/
	])->label('Rate Unit'); ?>
    	<?php if($pricing_type == 0) { ?>
    		<?php // $form->field($model, 'service_task',['template' => "<div class='row input-field'><div class='col-md-3'>{label}<span class='text-danger'>*</span></div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->dropDownList($serviceList,['multiple' => true]); ?>
    		<?php 
                    if(empty($serviceList)) {
                        $serviceList = ['norow' => 'First add Rate(s), to then see and select Service Task(s).'];
                    }
                    echo $form->field($model, 'service_task',['template' => "<div class='row input-field'><div class='col-md-3'>{label}<span class='text-danger'>*</span></div><fieldset><legend class='sr-only'>service tasks</legend><div class='col-md-9'>{input}\n{hint}\n{error}</div></fieldset></div>",'labelOptions'=>['class'=>'form_label','id'=>'lbl-service-tasks']])->checkboxList($serviceList,
                        ['item' => function($index, $label, $name, $checked, $value) {
                            $return = '<div class="col-sm-12">';
                                if($label != 'First add Rate(s), to then see and select Service Task(s).') {
                                    if($checked)
                                        $return .= '<input aria-required="true" title="This field is required" id="'.$name.'-'.$value.'" checked="'.$checked.'"  type="checkbox" name="' . $name . '" value="' . $value . '" aria-label="'.$label.'"><label for="'.$name.'-'.$value.'" class="form_label">'.ucwords($label).'</label>';
                                    else
                                        $return .= '<input aria-required="true" title="This field is required" id="'.$name.'-'.$value.'" type="checkbox" name="' . $name . '" value="' . $value . '"><label for="'.$name.'-'.$value.'" class="form_label">'.ucwords($label).'</label>';
                                } else {
                                    $return .= '<label class="form_label text-muted">'.$label.'</label>';
                                }
                            $return .= '</div>';
                            return $return;
                        },'class'=>'custom-full-width']);
                    ?>
    	<?php } ?>
    	<?= $form->field($model, 'description',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-9'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textArea(['rows'=>3,'maxlength'=>$pricing_length['description']]) ?>
    	<div class="form-group field-pricing-cust-description clearfix">			
					<?= $form->field($model, 'is_custom',['template' => "<div class='row input-field'><div class='col-md-2 col-md3-chk pr-0'>{input}<label for='pricing-is_custom' class='chkbox-global-design'> Custom Description</label>\n{hint}\n{error}</div><div class='col-md-7 pt-1 pl-0'>(Add Additional Description Notes To Billable Items) </div></div>"])->checkbox(['label' => null]); ?>	
            </div>
        <div class="form-group field-pricing-cust-description clearfix">
            <div class="col-md-3"></div>
            <div class="col-md-9">
					<?= $form->field($model, 'cust_desc_template',['template' => "<div class='row input-field'><div class='col-md-2 col-md3-chk pr-0' id='lbl-adesc-tmp'>Add Description Template</div><div class='col-md-12'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textArea(['rows'=>3,'maxlength'=>$pricing_length['cust_desc_template'],'aria-labelledby'=>'lbl-adesc-tmp']) ?>                
		</div>
		</div>
		<?php if($pricing_type == 0) { ?>
			<div class="form-group field-pricing-accumcost">
                            <div class="row input-field">                                                                
                                        <?= $form->field($model, 'accum_cost',['template' => "<div class='row input-field'><div class='col-md-2 col-md3-chk pr-0'>{input} <label for='pricing-accum_cost' class='chkbox-global-design'> Accumulated Cost</label>\n{hint}\n{error}</div><div class='col-md-7 pt-1 pl-0'>(Recurring Cost That Sums Up All Prior And Current Billable Items)</div></div>"])->checkbox(['label' => null]); ?>                               
                            </div>
			</div>	
		<?php } ?>
		<?php if($pricing_type == 1) { ?>
        <div class="form-group field-pricing-cust-description clearfix">                    
                        <fieldset>
                            <?= $form->field($model, 'display_teams_type',['template' => '<div class="row input-field custom-full-width"><div class="col-md-3">{label}</div><div class="col-md-8">{input}{error}{hint}</div></div>','labelOptions'=>['class'=>'form_label']])->radioList([1=>'All (Default)',2=>'Selected'],
                                    ['item' => function($index, $label, $name, $checked, $value) {
                                            if($index == 0)
                                                $return = '<legend class="sr-only">Display Across Teams</legend>';
                                            else
                                                $return = '';
                                            $return .= '<div class="col-sm-3">';
                                            if($checked)
                                                    $return .= '<input aria-required="true" title="This field is required" aria-setsize="2" aria-posinset="'.($index+1).'" id="'.$name.'-'.$value.'" checked="'.$checked.'"  type="radio" name="' . $name . '" value="' . $value . '"><label for="'.$name.'-'.$value.'" class="form_label" >'.ucwords($label).'</label>';
                                            else
                                                    $return .= '<input aria-setsize="2" aria-posinset="'.($index+1).'" aria-required="true" title="This field is required" id="'.$name.'-'.$value.'"  type="radio" name="' . $name . '" value="' . $value . '"><label for="'.$name.'-'.$value.'" class="form_label" >'.ucwords($label).'</label>';
                                            $return .= '</div>';

                                            return $return;
                                    }]
                            ) ?>
                        </fieldset>                  
                </div>
				<?php // $form->field($model, 'display_teams',['template' => "<div class='row input-field'><div class='col-md-3'>{label}<span class='text-danger'>*</span></div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->dropDownList($teamList,['multiple' => true]);?>
	<div class="form-group field-pricing-cust-description  clearfix">					
        <fieldset><?php echo $form->field($model, 'display_teams',['template' => "<div class='row input-field'><legend class='sr-only'>Teams</legend><div class='col-md-3'>{label}</div><div class='col-md-9'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label','id'=>'teams-container']])->checkboxList($teamList,
	    		['item' => function($index, $label, $name, $checked, $value) {
                            $return = '<div class="col-sm-12">';
                            if($checked)
                                    $return .= '<input id="'.$name.'-'.$value.'" checked="'.$checked.'"  type="checkbox" name="' . $name . '" value="' . $value . '" aria-label="'.$label.'"><label for="'.$name.'-'.$value.'" class="form_label">'.ucwords($label).'</label>';
                            else
                                    $return .= '<input id="'.$name.'-'.$value.'"  type="checkbox" name="' . $name . '" value="' . $value . '" aria-label="'.$label.'"><label for="'.$name.'-'.$value.'" class="form_label">'.ucwords($label).'</label>';
                            $return .= '</div>';
				
                            return $return;
                        },'class'=>'custom-full-width']); ?></fieldset>
            </div>
		<?php } else { ?>
			<?= $form->field($model, 'display_teams_type')->hiddenInput(['value' => 0])->label(false); ?>
		<?php } ?>
		<?php //echo "<pre>",print_r($pricingUtmbsCodes),"</pre>";// $form->field($model, 'utbms_code_id',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->dropDownList($pricingUtmbsCodes,['prompt'=>'-Select UTBMS Code-']); ?>
                <?= $form->field($model, 'utbms_code_id',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-9'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->widget(
                    Select2::classname(), [
                            'attribute' => 'utbms_code_id',
                        'data' => $pricingUtmbsCodes,
                        'options' => ['prompt' => 'Select UTBMS Code', 'id' => 'pricing-utbms_code_id','aria-label'=>'UTBMS Code, '],
                        /*'pluginOptions' => [
                            'allowClear' => true
                        ],*/
                    ])->label('UTBMS Code'); ?>
    </div>	
</fieldset>
<div class="button-set text-right">
	<?php 
		$destination = 'loadTeamPricing('.$team_id.')';
		//$destination_new = 'loadTeamPricingBilling('.$team_id.')';
	
		if($pricing_type == 1){
			$destination = 'loadSharedPricing();';
			//$destination_new = 'loadTeamPricingBilling(0)';
		}
		//echo $destination," - ",$destination_new;
	?>
    <?= Html::button('Cancel', ['title'=> 'Cancel','class' => 'btn btn-primary', 'onclick'=>$destination]) ?>
    <?= Html::button($model->isNewRecord ? 'Add' : 'Update', ['title'=> $model->isNewRecord?'Add':'Update','class' =>  'btn btn-primary','onclick'=>$model->isNewRecord ? 'SubmitAjaxForm("'.$model->formName().'",this,"'.$destination.'","'.$sourceDiv.'");' : 'UpdatePricepoint("'.$model->formName().'",this,"'.$destination.'","'.$sourceDiv.'","'.$team_id.'","'.$id.'")']) ?>
</div>
<?php ActiveForm::end(); ?>
<script>
	var form_type_name = "<?=$sourceDiv?>";	
	//alert("<?=$model->formName()?>");
	//'sourceDiv'=>'sharedpricing_div',
/* Event Changed */
$('document').ready(function(){
	$("#active_form_name").val('Pricing');
});
$('input').bind("input", function(){ 
	$('#Pricing #is_change_form').val('1');
	$('#Pricing #is_change_form_main').val('1');
});
$('textarea').bind('input',function(){
	$('#Pricing #is_change_form').val('1');
	$('#Pricing #is_change_form_main').val('1');
});
$('select').on('change',function(){
	$('#Pricing #is_change_form').val('1');
	$('#Pricing #is_change_form_main').val('1');
});
$(':checkbox').change(function(){
	$('#Pricing #is_change_form').val('1');
	$('#Pricing #is_change_form_main').val('1');
});
<?php if($model->is_custom == 0){ ?>
$('.field-pricing-cust_desc_template').hide();
$('.field-pricing-cust_desc_template textarea').text('');
<?php } ?>
$('#pricing-accum_cost').customInput();
$('#pricing-is_custom').customInput();
$('#pricing-is_custom').on('change',function(){
	if($(this).prop('checked') == true){
		$('.field-pricing-cust_desc_template').show();
	} else {
		$('.field-pricing-cust_desc_template').hide();
		$('.field-pricing-cust_desc_template textarea').text('');
	}
});
$('input').customInput();
<?php if($model->display_teams_type == 1){ ?>
$('.field-pricing-display_teams').hide();
<?php } ?>
$('input[name="Pricing[display_teams_type]"]').on('change',function(){
	//$('#pricing-display_teams:option:selected').attr('selected',false);
	if($(this).val() == 2){
		$('.field-pricing-display_teams').show();
	} else {
		$('.field-pricing-display_teams').hide();
		
	}
});
</script>
<noscript></noscript>
