<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\Select2;
use app\components\IsataskFormFlag;

/* @var $this yii\web\View */
/* @var $model app\models\ReportsFieldCalculations */
/* @var $form yii\widgets\ActiveForm */
$form = ActiveForm::begin(['id' => $model->formName(), 'enableAjaxValidation' => false, 'enableClientValidation' => true]);
$returnval = $model->calculation_field_name!=''?true:false;
if(isset($model->calculation_type) && $model->calculation_type!=""){}else{
$model->calculation_type = $model->isNewRecord ? 1 :$model->calculation_type;
}
if(!$model->isNewRecord){
	if(!isset($formula))
	$formula=$model->select_sql;
}
?>
<?= IsataskFormFlag::widget(); // change flag ?>
<fieldset class="one-cols-fieldset">
    <div class="create-form">
	<div id="fieldcalstep1">
		<?= $form->field($model, 'calculation_field_name', ['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>", 'labelOptions' => ['class' => 'form_label required']])->textInput(['readonly'=>$returnval,'maxlength'=>$model_field_length['calculation_field_name']]); ?>
		<?= $form->field($model, 'calculation_name', ['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>", 'labelOptions' => ['class' => 'form_label required']])->textInput(['maxlength'=>$model_field_length['calculation_name']]); ?>
		<?= $form->field($model, 'calculation', ['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>", 'labelOptions' => ['class' => 'form_label required']])->textArea(['rows' => '6']); ?>
		<?php if(!$model->isNewRecord) { ?>
				<div class="form-group field-reportsfieldcalculations-calculation">
					<div class="row input-field">
						<div class="col-md-3">
							<label class="form_label required" for="reportsfieldcalculations-calculation">Calculation Type</label>
						</div>

						<div class="col-md-7">
							<?php echo ($model->calculation_type==1?'Expression':'Function');?>
							<input type="hidden" class="form-control" id="reportsfieldcalculations-calculation_type" name="ReportsFieldCalculations[calculation_type]" value="<?=$model->calculation_type?>">
							<div class="help-block"></div>
						</div>
				</div>
			</div>
		<?php }else{?>
		<?= $form->field($model, 'calculation_type',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->widget(Select2::classname(), [
			'data' => [1=>'Expression',2=>'Function'],
			'options' => ['multiple' => false,'prompt' => false,'aria-label'=>'Calculation Type','nolabel'=>true],
			'pluginOptions' => [
				'allowClear' => false,
			],
			]);?>
		<?php }?>
	</div>
	<div id="fieldcalstep2" style="display:none;">
		<div id="fieldcalstep2_fun" style="display:none;">
		<?php if(!$model->isNewRecord && $model->calculation_type==2){?>
		<?= $form->field($model, 'calculation_primary',['template' => "<div class='row input-field required'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label required']])->widget(Select2::classname(), [
			'data' => $functions,
			'options' => ['multiple' => false,'prompt' => false,'nolabel'=>true, 'aria-required'=>'true'],
			'pluginOptions' => [
				'allowClear' => false,
			],
		])->label('Select Function');

		}elseif($model->isNewRecord){?>
			<?= $form->field($model, 'calculation_primary',['template' => "<div class='row input-field required'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label required']])->widget(Select2::classname(), [
			'data' => $functions,
			'options' => ['multiple' => false,'prompt' => false,'nolabel'=>true],
			'pluginOptions' => [
				'allowClear' => false,
			],
			])->label('Select Function');
		}?>
		</div>
		<div id="fieldcalstep2_exp" style="display:none;">
			<?php if(!$model->isNewRecord && $model->calculation_type==1){?>
				<?= $form->field($model, 'calculation_primary',['template' => "<div class='row input-field required'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label required']])->widget(Select2::classname(), [
					'data' => $tableList,
					'options' => ['multiple' => false,'prompt' => false,'id' => 'calculation_primary_tables','nolabel'=>true],
					'pluginOptions' => [
						'allowClear' => false,
					],
					'pluginEvents' => [
							"change" => "function() { getRelatedTables(this.value); }",
					]
				])->label('Select Primary Table');
			}elseif($model->isNewRecord){?>
				<?= $form->field($model, 'calculation_primary',['template' => "<div class='row input-field required'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label required']])->widget(Select2::classname(), [
					'data' => $tableList,
					'options' => ['multiple' => false,'prompt' => false,'id' => 'calculation_primary_tables','nolabel'=>true],
					'pluginOptions' => [
						'allowClear' => false,
					],
					'pluginEvents' => [
							"change" => "function() { getRelatedTables(this.value); }",
					]
				])->label('Select Primary Table');
			}?>
				<div id="exp_builder">
				<div class="row buildexpressin-row form-group">
					<div class="col-sm-6">
						<label><a href="javascript:void(0);" class="tag-header-black" title="Select Primary Table Related Attributes">Select Primary Table Related Attributes</a></label>
						<div class="buildexpressin-col">
							<div class="mycontainer">
								<div id="tables_list" class="myheader">
									<a href="javascript:void(0);" onclick="$('#table_list').toggle();$('#table-content').toggle();" title="Field Relationships">Field Relationships</a>
								</div>
								<div class="content" id="table-content">
									<ul style="display:none;" id="table_list"></ul>
								</div>
								<div class="myheader"><a href="javascript:void(0);" onclick="$('.fileds').hide();$('#function_list').toggle();$('#sp_function_field').show();" title="Field `Function">Field Function</a></div>

							</div>
						</div>
					</div>
					<div class="col-sm-6">
						<label><a href="javascript:void(0);" class="tag-header-black" title="Fields, Functions , Sp">List Of Fields or Functions</a></label>
						<div class="mycontainer" id="sp_function_field" style="display:none;">
							<ul style="display:none;" id="function_list">
							</ul>
							<div class="col-sm-12" id="table_field_list">

							</div>
						</div>
					</div>
			</div>
			<div class="row buildexpressin-row form-group">
				<div class="col-sm-12">
					<label><a href="javascript:void(0);" class="tag-header-black" title="Operators">Select Operators</a></label>
					<div class="opt-buttonset">
					<?php foreach(Yii::$app->params['exp'] as $exp){ if($exp==""){ continue; }?>
						<button value="<?=$exp?>" onclick="addOp(this);" type="button"><?=$exp?></button>
					<?php }?>
					</div>
				</div>
			</div>
			<div class="row buildexpressin-row">
				<div class="form-group col-sm-12 <?php if(isset($flag) && $flag=='next' && $formula==""){?>form-group required has-error<?php }?>">
                                    <label for="formula"><a href="javascript:void(0);" class="tag-header-black" title="Formula">Build Formula</a></label>
					<textarea id="formula" class="form-control" name="formula"  rows="6" aria-required="true" placeholder="Build Formula"><?=$formula?></textarea>
					<div class="help-block"><?php if(isset($flag) && $flag=='next' && $formula==""){?>Build Formula cannot be blank.<?php }?></div><br />
				</div>
			</div>
		</div>

		</div>
	</div>
	<input type="hidden" value="<?=$model->calculation_primary?>" name="function_id" id="function_id"/>
	<input type="hidden" value="<?=$model->calculation_primary?>" name="table_id" id="table_id"/>
	<?php /*?>


    <?= $form->field($model, 'calculation_type',['template' => '<div class="row custom-full-width"><div class="col-md-3">{label}</div><div class="col-md-7"><div class="row">{input}{error}{hint}</div></div></div>'])->radioList([1=>'Function',2=>'Store Procedure',3=>'Expression'],
						['item' => function($index, $label, $name, $checked, $value) use($radio_data) {
							$return = '<div class="col-sm-6"><label for="'.$name.'-'.$value.'">';
							if($checked)
								$return .= '<input aria-label="'. $radio_data[$value].'" id="'.$name.'-'.$value.'" checked="'.$checked.'"  type="radio" name="' . $name . '" value="' . $value . '">';
							else
								$return .= '<input aria-label="'. $radio_data[$value].'" id="'.$name.'-'.$value.'"  type="radio" name="' . $name . '" value="' . $value . '">';

							$return .= ucwords($label);
							$return .= '</label></div>';

							return $return;
						}]
						)->label('Type', ['class'=>'form_label']); ?>


	<?= $form->field($model, 'primary_tables',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-9'><div class='col-md-8'>{input}\n{hint}\n{error}</div><div class='col-md-1'><a href='javascript:addPrimaryTable();' class='add_primary_table' title='Add Primary Table'><em class='fa fa-plus'></em></a></div></div></div>",'labelOptions'=>['class'=>'form_label']])->widget(Select2::classname(), [
    'data' => $tableList,
    'options' => ['multiple' => false,'prompt' => false, 'id' => 'primary_tables'],
    'pluginOptions' => [
        'allowClear' => false,
    ],
	])->label('Primary Table');?>
	<div class="form-group">
		<div class="row input-field">
			<div class="col-md-3"></div>
			<div class="col-md-7">
				<div class="table-responsive">
				   <table class="table table-striped" id="claculation_table" width="100%" cellspacing="0" cellpadding="0" border="0">
					<thead>
						<tr><th scope="col"><a href="javascript:void(0);" class="tag-header-black" title="Table">Table</a></th><th scope="col"><a href="javascript:void(0);" class="tag-header-black" title="Actions">Actions</a></th></tr>
					</thead>
				   	<tbody>
				   	<?php if(!empty($tables)){
						foreach($tables as $tbl){?>
							<tr>
								<td>
									<?php echo $tableList[$tbl]?><input class="cal_table_<?php echo $tbl?>" name="calc_table[]" value="<?php echo $tbl?>" type="hidden">
								</td>
								<td>
									<a aria-label="Related Table" href="javascript:AddRelatedTable(<?php echo $tbl?>);" class="icon-fa" title="Add Related Table"><em class="fa fa-sitemap text-danger"></em></a>&nbsp;&nbsp;
									<a aria-label="Remove" href="javascript:RemoveRelationalTable(<?php echo $tbl?>);" class="icon-fa" title="Delete Table"><em class="fa fa-close text-primary"></em></a>
								</td>
							</tr>
						<?php }
					}?>
				   	</tbody>
				   </table>
				</div>
			</div>
		</div>
	</div>
    <?php /*?><div class="form-group">
		<div class="row input-field">
			<div class="col-md-3">
				<label class="form_label " for="reportsfieldcalculations-exp_build">Build Exp</label>
			</div>
			<div class="col-md-7">
				<?php echo $form->field($model, 'table',['template' => "<div class='row input-field'><div class='col-md-12'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])
			->widget(Select2::classname(), [
				'model' => $modelReportTables,
				'attribute' => 'table_name',
				'data' => $tableList,
				'options' => ['placeholder' => 'Select Table', 'id' => 'table_name','title'=>'Select Table'],
				'pluginOptions' => [
					'allowClear' => false,
				],
			])->label(false);?>
				<div class="help-block"></div>
			</div>
		</div>
	</div>

    <div class="form-group">
		<div class="row input-field">
			<div class="col-md-3">
				<label class="form_label " for="tables">Select Table</label>
			</div>
			<div class="col-md-7">
				<select id="tables" name="tables" class="form-control"></select>
			</div>

		</div>
	</div>

    <div class="form-group">
		<div class="row input-field">
			<div class="col-md-3">
				<label class="form_label " for="opt">Select Operator</label>
			</div>
			<div class="col-md-7">
					<select id="opt" name="opt"  class="form-control">
							<?php foreach(Yii::$app->params['exp'] as $exp){?>
								<option value="<?=$exp?>"><?=$exp?></option>
							<?php }?>
					</select>
			</div>
		</div>
	</div>

	 <div class="form-group">
		<div class="row input-field">
			<div class="col-md-3">
				<label class="form_label " for="table_fields">Select Field</label>
			</div>
			<div class="col-md-7">
				<select id="table_fields" name="table_fields"  class="form-control"></select>
				<a href="javascript:addExp();" class="btn btn-primary"><em class="fa fa-plus"></em></a>
			</div>
		</div>
	</div><?php */?>
    <?php /*?>
    <?= $form->field($model, 'select_sql', ['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>", 'labelOptions' => ['class' => 'form_label required']])->textArea(['rows' => '6','readonly'=>'readonly']);
    */?>

    </div>
</fieldset>
<div class="button-set text-right">
<?= Html::button('Previous', ['title' => 'Previous','id'=>'prev_field_calc', 'style'=>'display:none;','class' => 'btn btn-primary', 'onclick' => 'prevFieldCalc();']) ?>
<?php if(!$model->isNewRecord ) { echo Html::button('Cancel', ['title' => 'Cancel', 'class' => 'btn btn-primary', 'onclick' => 'loadReportFieldCalculationCancel();']); }?>
<?= Html::button($model->isNewRecord ? 'Cancel' : 'Delete', ['title' => $model->isNewRecord ? 'Cancel' : 'Delete', 'class' => 'btn btn-primary', 'onclick' => $model->isNewRecord ? 'loadReportFieldCalculationCancel();' : 'checkFieldCalculation("' . $model->id . '","' . $model->calculation_name . '","field-calculation");']) ?>
<?= Html::button('Next', ['title' => 'Next','id'=>'next_field_calc', 'class' => 'btn btn-primary', 'onclick' => 'nextFieldCalc();']) ?>
<?= Html::button('Clear', ['title' => 'Clear','id'=>'btn_clear', 'style'=>'display:none;','class' => 'btn btn-primary', 'onclick' => 'clearFn();']) ?>
<?= Html::button($model->isNewRecord ? 'Add' : 'Update', [ 'title' => $model->isNewRecord ? 'Add' : 'Update','id'=>'btn_add_edit','style'=>'display:none;', 'class' => 'btn btn-primary','onclick'=>'submitFo("'.$model->formName().'",this,"loadReportFieldCalculation()","reportform_div");']) ?>
<?php //= Html::button('Build Expression', [ 'title' => 'Build Expression', 'class' => 'btn btn-primary pull-left','onclick'=>'BuildExp();']) ?>
</div>

<?php ActiveForm::end(); ?>
<script>
	$('document').ready(function(){
		$('#active_form_name').val('ReportsFieldCalculations');
	});
	function submitFo(frm_name,obj,callback,replace_div){
		if($('#reportsfieldcalculations-calculation_type').val() == 1){ //expression
			if($("#calculation_primary_tables").val()==""){
				$("#calculation_primary_tables").closest('div').find('div.help-block').html('Primary Table cannot be blank.');
				$("#calculation_primary_tables").closest('div').parent().parent().addClass('required has-error');
			}else{
				SubmitAjaxForm(frm_name,obj,callback,replace_div);
			}
		}else{
			if($("#reportsfieldcalculations-calculation_primary").val()==""){
				$("#reportsfieldcalculations-calculation_primary").closest('div').find('div.help-block').html('Function cannot be blank.');
				$("#reportsfieldcalculations-calculation_primary").closest('div').parent().parent().addClass('required has-error');
			}else{
				SubmitAjaxForm(frm_name,obj,callback,replace_div);
			}
		}
	}
	$('#calculation_primary_tables').on('change',function(){
		$('#table_id').val($(this).val());
	});
	$('#reportsfieldcalculations-calculation_primary').on('change',function(){
		$('#function_id').val($(this).val());
	});
	function clearFn(){
		if($('#reportsfieldcalculations-calculation_type').val() == 1){
			$('#calculation_primary_tables').val(null).change();
			$('#formula').val(null);
			$('#table_list').empty();
			$('#function_list').empty();
			$('#table_field_list').html(null);
		}else{
			$('#reportsfieldcalculations-calculation_primary').val(null).change();
		}
	}
	<?php if(isset($flag) && $flag=='next'){?>
		nextFieldCalc();
	<?php }?>
	function prevFieldCalc(){
		$('#fieldcalstep2').hide();
		$('#fieldcalstep2_fun').hide();
		$('#fieldcalstep2_exp').hide();
		$('#prev_field_calc').hide();
		$('#btn_add_edit').hide();
		$('#btn_clear').hide();
		$('#fieldcalstep1').show();
		$('#next_field_calc').show();
		if($('#reportsfieldcalculations-calculation_type').val() == 1){
			if($("#calculation_primary_tables").val()!=""){
				getRelatedTables($("#calculation_primary_tables").val());
			}
		}
		var action_name="Add";
		<?php if(!$model->isNewRecord ) {?>
			var action_name="Update";
		<?php }?>
		$('.sub-heading').html('<a href="javascript:void(0);" class="tag-header-black" title="'+action_name+' Field Calculation">'+action_name+' Field Calculation</a>');
	}
	function nextFieldCalc(){
		var from_name='<?=$model->formName()?>';
		$.ajax({
			url:baseUrl+'report-management/validate-nextfieldcalc',
			type:'post',
			data:$("#"+from_name).serialize(),
			success:function(response){
				if(response.length==0){
					$('#fieldcalstep1').hide();
					$('#next_field_calc').hide();
					$('#fieldcalstep2').show();
					var action_name="Add";
					<?php if(!$model->isNewRecord ) { ?>
						var action_name="Update";
					<?php } ?>
					if($('#reportsfieldcalculations-calculation_type').val() == 1){
						$('#fieldcalstep2_exp').show();

						$('.sub-heading').html('<a href="javascript:void(0);" class="tag-header-black" title="'+action_name+' Field Calculation">'+action_name+' Field Calculation</a><span class="pull-right">Expression</span>');
						if($("#calculation_primary_tables").val()!=""){
							getRelatedTables($("#calculation_primary_tables").val());
						}
					}else{
						$('#fieldcalstep2_fun').show();
						$('.sub-heading').html('<a href="javascript:void(0);" class="tag-header-black" title="'+action_name+' Field Calculation">'+action_name+' Field Calculation</a><span class="pull-right">Function</span>');
					}
					$('#prev_field_calc').show();
					$('#btn_add_edit').show();
					$('#btn_clear').show();
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

	/* jQuery */
	jQuery('input').keyup('input', function(){
		$('#ReportsFieldCalculations #is_change_form').val('1');
		$('#ReportsFieldCalculations #is_change_form_main').val('1');
	});
	jQuery('textarea').bind('keyup', function(){
		$('#ReportsFieldCalculations #is_change_form').val('1');
		$('#ReportsFieldCalculations #is_change_form_main').val('1');
	});
	jQuery('select').on('change', function() {
		$('#field-calculation_list_dropdown').val();
		$('#ReportsFieldCalculations #is_change_form').val('1');
		$('#ReportsFieldCalculations #is_change_form_main').val('1');
	});
	/* End */


    jQuery(document).ready(function () {
		<?php if(!empty($tables)){?>
			$('.add_primary_table').hide();
		<?php }?>
		jQuery('input[type="radio"]').customInput();
		$('#<?= $model->formName() ?>').submit(function () {
            SubmitAjaxForm("<?= $model->formName() ?>", this, "loadReportFieldCalculation()", "reportform_div");
        });
        $('#tables').select2({
		});
		$('#table_fields').select2({
		});
		$('#opt').select2({
		});

		$('#table_name').on('change',function(){
			jQuery.ajax({
                type: "POST",
                url: baseUrl + "report-management/get-related-table-lists",
                data:{'primary_table_name':$(this).val()},
                dataType: 'html',
                cache: false,
                success: function (data) {
					$('#tables').html(data);
					$("#tables").select2("destroy");
					$("#tables").select2();
               }
            });
		});
		$('#tables').on('change',function(){
			jQuery.ajax({
                type: "POST",
                url: baseUrl + "report-management/get-table-fields",
                data:{'primary_table_name':$(this).val(),'selected_table':$(this).val()},
                dataType: 'html',
                cache: false,
                success: function (data) {
					$('#table_fields').html(data);
					$("#table_fields").select2("destroy");
					$("#table_fields").select2();
               }
            });
		});
    });
function addExp(){
	var table_id = $('#tables').val();
	var table_name = $("#tables option:selected").text();
	var opt = $("#opt").val();
	var field=$("#table_fields option:selected").text();
	var exp_val= $('#reportsfieldcalculations-select_sql').text();
	var tablename = $("#table_names").val();
	if(opt==""){
		if($.trim(exp_val).length>0){
			opt= "<<Exp>>";
		}
	}
	if(table_id != "" && field!=""){
		if(tablename == ""){
			$("#table_names").val(table_name);
		}else{
			tablename = tablename+'|'+table_name;
			$("#table_names").val(tablename);
		}

		if($.trim(exp_val).length>0){
			exp_val =  exp_val + ' ' +opt+ ' ' + table_name+'.'+field;
		}else{
			exp_val = exp_val + table_name+'.'+field;
		}
		$('#reportsfieldcalculations-select_sql').text(exp_val);
	}
}
function addPrimaryTable(){
	var table_id = $('#primary_tables').val();
	if(table_id!="" && table_id!=0){
		if($('.cal_table_'+table_id).length == 0){
			var table_name = $("#primary_tables option:selected").text();
			$htmlTr="<tr><td>"+table_name+"<input type='hidden' class='cal_table_"+table_id+"' name='calc_table[]' value='"+table_id+"'></td><td><a aria-label='Related Table' href='javascript:AddRelatedTable("+table_id+");' class='icon-fa' title='Add Related Table'><em class='fa fa-sitemap text-danger'></em></a>&nbsp;&nbsp;<a aria-label='Remove' href='javascript:RemoveRelationalTable("+table_id+");' class='icon-fa' title='Delete Table' aria-label='Delete Table'><em class='fa fa-close text-primary'></em></a></td></tr>";
			$('#claculation_table tbody').append($htmlTr);
			$('.add_primary_table').hide();
		}
	}
}
function RemoveRelationalTable(table_id){
	$('.cal_table_'+table_id).closest('tr').remove();
	$('#is_change_form').val('1'); $('#is_change_form_main').val('1'); // change flag to 1
	if($('#claculation_table tbody tr').length == 0){
		$('.add_primary_table').show();
	}
}
/*
 * Function to add fields from primary table
 */
function AddRelatedTable(table_id){
		$.ajax({
			url:baseUrl+'report-management/get-related-table-lists',
			beforeSend:function (data) {showLoader();},
			type:'post',
			data:{'primary_table_name':table_id},
			success:function(response){
			hideLoader();
			if($('body').find('#availabl-primary-tables').length == 0){
				$('body').append('<div class="dialog" id="availabl-primary-tables" title="Add Relational Table"></div>');
			}
			$('#availabl-primary-tables').html('').html('<select id="tables" class="form-control">'+response+'</select>');
			$('#availabl-primary-tables').dialog({
					modal: true,
			        width:'50em',
			        height: 256,
			        title:'Add Relational Table',
			        close: function(){
						$(this).dialog('destroy').remove();
					},
			        create: function(event, ui) {
						 $('.ui-dialog-titlebar-close').html('<span class="ui-button-icon-primary ui-icon"></span>');
                                                 $('.ui-dialog-titlebar-close').attr("title", "Close");
                                                $('.ui-dialog-titlebar-close').attr("aria-label", "Close");
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
			                	  text: "Add",
			                	  "class": 'btn btn-primary',
									"title": 'Add',
				                	  click: function () {
										var table_id = $('#tables').val();
										var table_name = $("#tables option:selected").text();
										if(table_id!="" && table_id!=0){
											if($('.cal_table_'+table_id).length == 0){
												$htmlTr="<tr><td>"+table_name+"<input type='hidden' class='cal_table_"+table_id+"' name='calc_table[]' value='"+table_id+"'></td><td><a aria-label='Related Table' href='javascript:AddRelatedTable("+table_id+");' class='icon-fa' title='Add Related Table'><em class='fa fa-sitemap text-danger'></em></a>&nbsp;&nbsp;<a aria-label='Remove' href='javascript:RemoveRelationalTable("+table_id+");' class='icon-fa' title='Delete Table' aria-label='Delete Table'><em class='fa fa-close text-primary'></em></a></td></tr>";
												$('#claculation_table tbody').append($htmlTr);
											}
											$(this).dialog('destroy').remove();
										}
				                	  }
			                  }
			        ]
			    });
			},complete:function(){
				$('#availabl-primary-tables #tables').select2({
					allowClear: false,
					placeholder: 'Select Table',
					dropdownParent: $('#availabl-primary-tables')
				});
			}
		});
}
function BuildExp(){
	if($('#claculation_table tbody tr').length > 0){
		$.ajax({
			url:baseUrl+'report-management/build-express',
			beforeSend:function (data) {showLoader();},
			type:'post',
			data:$("#ReportsFieldCalculations").serialize(),
			success:function(response){
				hideLoader();
				if($('body').find('#build-calculation-exp').length == 0){
					$('body').append('<div class="dialog" id="build-calculation-exp" title="Build Exprssion"></div>');
				}
				$('#build-calculation-exp').html('').html(response);
				$('#build-calculation-exp').dialog({
					modal: true,
			        width:'60em',
			        height: 500,
			        title:'Build Exprssion',
			        close: function(){
						$(this).dialog('destroy').remove();
					},
			        create: function(event, ui) {
						 $('.ui-dialog-titlebar-close').html('<span class="ui-button-icon-primary ui-icon"></span>');
                                                 $('.ui-dialog-titlebar-close').attr("title", "Close");
                                        $('.ui-dialog-titlebar-close').attr("aria-label", "Close");
					},
			        buttons: [
								{
			                	  text: "Cancel",
			                	  "class": 'btn btn-primary',
								  "title": 'Cancel',
			                	  click: function () {
									  trigger = 'Cancel';
									  $(this).dialog('close');
			                	  }
			                  },
			                  {
			                	  text: "Add",
									"class": 'btn btn-primary',
									"title": 'Add',
				                	click: function () {
										trigger = 'Add';
										var formula = $('#formula').val();
										$("#reportsfieldcalculations-select_sql").val(formula);
										$('#exp_builder #is_change_form').val('1');
										$('#is_change_form_main').val('1');
										$(this).dialog('destroy').remove();
									}
			                  }
			        ],
			        beforeClose: function(event){
						if(event.keyCode == 27)	trigger = '';
						if(trigger != 'Add') checkformstatus(event,"exp_builder");
					}
			    });
			}
		});
	}
}
function checkFieldCalculation(calc_id,calculation_name,updateid){
	$.ajax({
			url:baseUrl+'report-management/check-isdelcalcfield',
			beforeSend:function (data) {showLoader();},
			type:'post',
			data:{calc_id:calc_id},
			success:function(response){
				hideLoader();
				if(response == 'OK'){
					removeReportSingleData(calc_id,calculation_name,updateid);
				}else {
					alert(calculation_name +" Calcuation Field is used in Report type. You can't delete it");
					return false;
				}
			}
		});
}
function addOp(obj){
	var formula = $('#formula').val() + ' ' + $(obj).val();
	$('#ReportsFieldCalculations #is_change_form').val('1'); // change flag
	$('#is_change_form_main').val('1'); // change flag
	$('#formula').val(formula);
}
function addField(filed){
	var formula = $('#formula').val() +  filed;
	$('#ReportsFieldCalculations #is_change_form').val('1'); // change flag
	$('#is_change_form_main').val('1'); // change flag
	$('#formula').val(formula);
}
function addFn(fn_id){
	$.ajax({
			url:baseUrl+'report-management/get-fnwithparams',
			type:'post',
			data:{fn_id:fn_id},
			success:function(response){
				if(response!=""){
					var formula = $('#formula').val() +  response;
					$('#exp_builder #is_change_form').val('1');
					$('#is_change_form_main').val('1');
					$('#formula').val(formula);
				}
			}
	});
}
function getRelatedTables(table_id){
	if(table_id!=""){
	$.ajax({
			url:baseUrl+'report-management/get-related-table-and-fn',
			beforeSend:function (data) {showLoader();},
			type:'post',
			data:{table_id:table_id},
			success:function(response){
				hideLoader();
				if(response.length > 0){
					var response = jQuery.parseJSON(response);
					if(response.tables.length > 0){
						var table_data="";
						var table_field_data="";
						$.each(response.tables, function(key,value){
							var table_fields=response.table_fields;
							table_data += '<li><a href="javascript:void(0);" data-name="'+value.table_name+'" data-id="'+value.id+'" onclick=$("#function_list").hide();$(".fileds").hide();$("#field_list_'+value.id+'").toggle();$("#sp_function_field").show(); >'+value.table_display_name+'</a></li>';
							if(table_fields[value.id]){
								table_field_data+="<ul style='display:none;' id='field_list_"+value.id+"' class='fileds'>";
								var table_name = value.table_name;
								$.each(table_fields[value.id], function(field_key,field){
									table_field_data+="<li><a style='cursor: pointer;' data-name='"+field+"' data-id='"+field_key+"' onclick=addField('"+table_name+"."+field+"')>"+table_name+"."+field+"</a></li>";
								});
								table_field_data+="</ul>";
							}
						});
						$("#table_list").html(table_data);
						$("#table_field_list").html(table_field_data);
					}
					if(response.functions){
						var function_list="";
						$.each(response.functions, function(key,fn){
							function_list+="<li><a data-name='"+fn+"' data-id='"+key+"' onclick='addFn("+key+")'>"+fn+"</a></li>";
						});
						$("#function_list").html(function_list);
					}
				}
			}
		});
	}
}
</script>
<noscript></noscript>
