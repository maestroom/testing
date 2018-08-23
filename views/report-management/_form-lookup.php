<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\widgets\Select2;
use kartik\widgets\DepDrop;
use yii\web\JsExpression;
/* @var $this yii\web\View */
/* @var $model app\models\ReportsFieldType */
/* @var $form yii\widgets\ActiveForm */
$form = ActiveForm::begin(['id' => $model->formName(), 'enableAjaxValidation' => false, 'enableClientValidation' => true]);
//echo "<prE>",print_r($current_table),"</prE>";
$model->type = 1;
?>
<fieldset class="one-cols-fieldset">
    <div class="create-form">
        <?= $form->field($model, 'type',['template' => "<div class='row input-field custom-full-width'><div class='col-md-3'>{label}</div><div class='col-md-9'><div class='row'>{input}\n{hint}\n{error}</div></div></div>",'labelOptions'=>['class'=>'form_label']])->radioList(['1' => ' Table Lookup', '2' => ' Custom Lookup',3=>' Field Lookup'],
							['item' => function($index, $label, $name, $checked, $value) {
								$return = '<div class="col-sm-4"><label for="'.$name.'-'.$value.'" class="form_label">';
								if($checked)
									$return .= '<input id="'.$name.'-'.$value.'" class="chk_type" checked="'.$checked.'"  type="radio" name="' . $name . '" value="' . $value . '">';
								else
									$return .= '<input id="'.$name.'-'.$value.'" class="chk_type" type="radio" name="' . $name . '" value="' . $value . '">';
									 
									$return .= ucwords($label);
								$return .= '</label></div>';
								return $return;
							}]
				);
				$model->filter_table=$table_name;
				?>
    <?= $form->field($model, 'filter_table')->hiddenInput()->label(false);?>
<?= $form->field($model, 'filter_field',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-9'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])
->widget(Select2::classname(), [
    'data' => $field_list,
    'options' => ['prompt' => false, 'id' => 'filter_field'],
    'pluginOptions' => [
        'allowClear' => true,
        'dropdownParent' => new JsExpression('$("#manage-lookup")'),
    ],
])->label('Filter Field');?>
<div id="field_lookups" style="display:<?php if($model->type == 1){ echo "display";} else{ echo "none";}?>">
<?= $form->field($model, 'lookup_table',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-9'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->widget(Select2::classname(), [
    'data' => $current_table,
    'options' => ['prompt' => false, 'id' => 'lookup_table'],
    'pluginOptions' => [
        'allowClear' => true,
        'dropdownParent' => new JsExpression('$("#manage-lookup")'),
    ],
])->label('Lookup Table');?>
<?php /*= $form->field($model, 'lookup_field',['template' => "<div class='row input-field custom-full-width'><div class='col-md-3'>{label}</div><div class='col-md-9'><div class='row'>{input}\n{hint}\n{error}</div></div></div>",'labelOptions'=>['class'=>'form_label']])->checkBoxList((isset($lookup_table_data))?$lookup_table_data:array(),
								  	['item' => function($index, $label, $name, $checked, $value) {
								  		$return = '<div class="col-sm-4"><label for="'.$name.'-'.$value.'" class="form_label">';
								  		if($checked)
											$return .= '<input id="'.$name.'-'.$value.'" class="user_type" checked="'.$checked.'"  type="checkbox" name="' . $name . '" value="' . $value . '">';
										else
											$return .= '<input id="'.$name.'-'.$value.'" class="user_type" type="checkbox" name="' . $name . '" value="' . $value . '">';
											 
											$return .= ucwords($label);
										$return .= '</label></div>';
										return $return;
									}]
							  	);*/?>
<?php echo $form->field($model, 'lookup_field',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-9'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->widget(DepDrop::classname(),[
			        	'type' => 2,
			        	'data' => (isset($lookup_table_data))?$lookup_table_data:array(),
			        	'options' => ['multiple' => false, 'title' => 'Select Lookup Table Field', 'class' => 'form-control'],
			            'pluginOptions' => [
							'multiple' => true, 
				            'allowClear' => true,
				            'depends'=>['lookup_table'],
				            'placeholder' => 'Select LookUp Field',
				            'url' => Url::toRoute(['report-management/gettablefield'])
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
					])->label('Lookup Field'); ?>
					
	<div id="field_display_map">
		<div class='row input-field'>
            <div class="form-group clearfix required">
                    <div class='col-md-3'>
                            Display Field(s)
                    </div>
                    <div class='col-md-7'>
                            <?php $operator_id = 0; ?>
                            <?= Html::Button('Add Display Fields', ['title' => 'Add Associated Display Fields','class' => 'btn btn-primary', 'id' => 'add-display-fields', 'onClick' => 'showDispalyMapPopup();'])?>
                    </div>
            </div>
        </div>
        <div class="row input-field">
            <div class="form-group clearfix">
                    <div class="col-md-3"></div>
                    <div class="col-md-9">
						<table class="table table-striped sm-table-report" id="form-fieldtype-report" width="100%" cellspacing="0" cellpadding="0" border="0">
                                            <thead>
												<tr>
													<th title="Lookup Display Fields"><a href="javascript:void(0);" title="Lookup Display Fields" class="tag-header-black"><strong>Lookup Display Fields</strong></a></th>
													<th title="Action"><a href="javascript:void(0);" title="Action" class="tag-header-black"><strong>Action</strong></a></th>
												</tr>	
                                            </thead>
                                            <tbody id="lookup_dipslay_popup_tbody">
											</tbody>
						</table>
						<div id="report-fielddisplay" class="has-error help-block"></div>
					</div>
			</div>
		</div>
	
	</div>
	<?= $form->field($model, 'lookup_field_separator',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-9'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->widget(Select2::classname(), [
    'data' => array(";"=>";(semi-colon)","-"=>"-(dash)",","=>",(comma)",":"=>":(colon)"," "=>" (space)"),
    'options' => ['prompt' => false, 'id' => 'lookup_field_separator'],
    'pluginOptions' => [
        'allowClear' => true,
    ],
])->label('Dispaly Separator');?>
	</div>
	<div id="field_map" style="display:<?php if($model->type == 2){ echo "display";} else{ echo "none";}?>;">
		<div class='row input-field'>
            <div class="form-group clearfix">
                    <div class='col-md-3'>
                            Field Map
                    </div>
                    <div class='col-md-7'>
                            <?php $operator_id = 0; ?>
                            <?= Html::Button('Add Field Map', ['title' => 'Add Associated Value For Field Database Value','class' => 'btn btn-primary', 'id' => 'pricepoint-tax-classes', 'onClick' => 'showMapPopup();']) ?>
                    </div>
            </div>
        </div>
        <div class="row input-field">
            <div class="form-group clearfix">
                    <div class="col-md-3"></div>
                    <div class="col-md-9">
                            <!-- table stripped -->
                                    <table class="table table-striped sm-table-report" id="form-fieldtype-report" width="100%" cellspacing="0" cellpadding="0" border="0">
                                            <thead>
												<tr>
													<th><a href="javascript:void(0);" title="Field Database Value"><strong>Field Database Value</strong></a></th>
													<th><a href="javascript:void(0);" title="Associated Value"><strong>Associated Value</strong></a></th>
													<th><a href="javascript:void(0);" title="Action"><strong>Action</strong></a></th>
												</tr>	
                                            </thead>
                                            <tbody id="lookup_popup_tbody">	
												<?php /*if(!empty($model->reportsLookupValues)){
													foreach($model->reportsLookupValues as $reportsLookupValues){
													?>
													<tr class="records"><td><?=$reportsLookupValues->field_value?><input value="<?=$reportsLookupValues->field_value?>" type="hidden" name="ReportsLookupValues[field_value][]" class="form-control"></td><td><?=$reportsLookupValues->lookup_value?><input value="<?=$reportsLookupValues->lookup_value?>" type="hidden" name="ReportsLookupValues[lookup_value][]" class="form-control"></td><td><a href="javascript:void(0);" title="Delete" onclick="removeTr(this)"><em class="fa fa-close text-primary"></em></a></td></tr>
												<?php } }else{?>
												<tr id="norecordfund"><td colspan="3" align="ceneter">No Records Found.</td></tr>
												<?php }*/?>
											</tbody>
									</table>
									<div id="report-fieldtypes" class="has-error help-block"></div>
					</div>
			</div>
		</div>							
	</div>
	
	<div id="field_type3_map" style="display:<?php if($model->type == 3){ echo "display";} else{ echo "none";}?>;">
		<div class='row input-field'>
            <div class="form-group clearfix">
                    <div class='col-md-3'>
                            Display Field(s)
                    </div>
                    <div class='col-md-7'>
                            <?php $operator_id = 0; ?>
                            <?= Html::Button('Add Display Fields', ['title' => 'Add Associated Display Fields','class' => 'btn btn-primary', 'id' => 'pricepoint-tax-classes_type3', 'onClick' => 'showType3DisplayField();']) ?>
                    </div>
            </div>
        </div>
        <div class="row input-field">
            <div class="form-group clearfix">
                    <div class="col-md-3"></div>
                    <div class="col-md-9">
                            <!-- table stripped -->
                                    <table class="table table-striped sm-table-report" id="form-fieldtype3-report" width="100%" cellspacing="0" cellpadding="0" border="0">
                                            <thead>
												<tr>
													<th><a href="javascript:void(0);" title="Lookup Display Fields"><strong>Lookup Display Fields</strong></a></th>
													<th><a href="javascript:void(0);" title="Action"><strong>Action</strong></a></th>
												</tr>	
                                            </thead>
                                            <tbody id="lookup_dipslay3_popup_tbody">
											</tbody>
						</table>
						<div id="report-fielddisplay3" class="has-error help-block"></div>
					</div>
			</div>
		</div>							
	<?= $form->field($model, 'lookup_field_separator2',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-9'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->widget(Select2::classname(), [
    'data' => array(";"=>";(semi-colon)","-"=>"-(dash)",","=>",(comma)",":"=>":(colon)"," "=>" (space)"),
    'options' => ['prompt' => false, 'id' => 'lookup_field_separator2'],
    'pluginOptions' => [
        'allowClear' => true,
        'dropdownParent' => new JsExpression('$("#manage-lookup").prev()'),
    ],
])->label('Dispaly Separator');?>
	</div>				
    </div>    
</fieldset>
<div class="dialog" id="availabl-field-types" title="Add Available Field Types"></div>
<?php ActiveForm::end(); ?>
<script>
    jQuery(document).ready(function () {
		$('.admin-left-module-list ul li').removeClass('active');
		//$("label[for='reportslookups-lookup_field']").hide();
		$('input').customInput();
        $('#<?= $model->formName() ?>').submit(function (e) {
            savefieldlookup('<?= $model->formName() ?>');
        });
        $('input[name="ReportsLookups[type]"]').on('click',function(){
			if(this.value==2){
				$('#field_map').show();	
				$('#field_lookups').hide()
				$('#field_type3_map').hide();	
			}
			else if(this.value==3){
				$('#field_type3_map').show();	
				$('#field_lookups').hide()
				$('#field_map').hide();	
			}
			else {
				$('#field_lookups').show()
				$('#field_map').hide();	
				$('#field_type3_map').hide();	
			}
		});
		/*$('#lookup_table').on('change',function(){
			$('#reportslookups-lookup_field').html(null);
			$.ajax({
			type : 'post',
			url:baseUrl+'report-management/gettablefield',
			data: {'depdrop_parents[0]':$(this).val()},
			beforeSend:function (data) {showLoader();},
			success:function(response){
				hideLoader();
				
				var obj=JSON.parse(response);
				var i=0;
				if(obj.output.length){
					$("label[for='reportslookups-lookup_field']").show();
				}else{
					$("label[for='reportslookups-lookup_field']").hide();
				}
				for(i=0;i<obj.output.length;i++){
					var v=obj.output[i];
					contentString='<div class="col-sm-4"><label class="form_label" for="ReportsLookups[lookup_field][]-'+v.id+'">'+v.id+'</label><input type="checkbox" value="'+v.id+'" name="ReportsLookups[lookup_field][]" class="user_type" id="ReportsLookups[lookup_field][]-'+v.id+'"></div>';
					$('#reportslookups-lookup_field').append(contentString);
				}
			},complete:function(){
				$('#reportslookups-lookup_field input').customInput();
			}
			});
		});*/
    });
    function savefieldlookup(form_id)
    {
		var no_error=true;
        /*if($('#reportslookups-lookup_name').val()==''){
                $("#reportslookups-lookup_name").next().html('Lookup Name cannot be blank.');
                $("#reportslookups-lookup_name").closest('div').parent().parent().addClass('has-error');
                no_error=false;
        }*/           
        if($('#filter_table').val()==''){
                $("#filter_table").next().next().html('Filter Table cannot be blank.');
                $("#filter_table").closest('div').parent().parent().addClass('has-error');
                no_error=false;
        }
        if($('#filter_field').val()==''){
                $("#filter_field").next().next().html('Filter Field cannot be blank.');
                $("#filter_field").closest('div').parent().parent().addClass('has-error');
                no_error=false;
        }
        if($('input[name="ReportsLookups[type]"]:checked').val()==2){                   
                if($('#lookup_popup_tbody tr.records').length == 0){
						$("#report-fieldtypes").html('Please Add Field Mapping.');                    
						$("#report-fieldtypes").parent().parent().parent().addClass('has-error');
						no_error=false;
				}
                
        }
        if($('input[name="ReportsLookups[type]"]:checked').val()==1){
			if($('#lookup_table').val()==''){
                $("#lookup_table").next().next().html('Lookup Table cannot be blank.');
                $("#lookup_table").closest('div').parent().parent().addClass('has-error');
                no_error=false;
			}
			if($('#reportslookups-lookup_field').val()==''){
                $("#reportslookups-lookup_field").next().next().html('Lookup Field cannot be blank.');
                $("#reportslookups-lookup_field").closest('div').parent().parent().addClass('has-error');
                no_error=false;
			}
			if($('#reportslookups-lookup_field').val()!='' && $('.lookup_dispaly_record').length==0){
				$("#report-fielddisplay").parent().parent().addClass('has-error');
				$("#report-fielddisplay").html('Lookup Display Field cannot be blank.');
				no_error=false;
			}
			/*if($('#lookup_table').val()!='' && $('#reportslookups-lookup_field input:checked').length==0){
				$("#reportslookups-lookup_field").next('div').addClass('clear').html('Lookup Field cannot be blank.');
                $("#reportslookups-lookup_field").closest('div').parent().parent().addClass('has-error');
                no_error=false;
			}*/
		}
		if(no_error == false){
			return false;
		}
        else{
			return true;
		}
    }
    function showType3DisplayField(){
		var display_name = $('#filter_field').val();
		var form = $('#ReportsLookups').serialize();
		relation_ship_ids="";
		$('.all_relationships').each(function(i,v){
			if(this.value!=0){
				if(relation_ship_ids==""){
					relation_ship_ids=this.value;
				}else{
					relation_ship_ids=relation_ship_ids+","+this.value;
				}
			}
		});
		//alert(relation_ship_ids);
		showLoader();
			
				if($('body').find('#filed-display-map-pop-up3').length == 0){
					$('body').append('<div class="dialog" id="filed-display-map-pop-up3" title="Add Display Fields for '+display_name+'"></div>');
				}
				$.ajax({
				type : 'post',
				url:baseUrl+'report-management/getallrelatedtablefield',
				data:{relation_ship_ids:relation_ship_ids,table_name:$('#table_name').val()},
				beforeSend:function (data) { showLoader(); },
				success:function(response){
					hideLoader();
					var obj=JSON.parse(response);
					var i=0;
					var contentString='<legend class="sr-only">Add Display Fields for '+display_name+'</legend>';
					for(i=0;i<obj.output.length;i++){
						var v=obj.output[i];
						var chk='';
						$('.lookup_dispaly3_record').each(function(){
							if(this.value==v.id){
								chk='disabled="disabled"';
								return false;
							}
						});
						if(contentString==""){
							contentString='<div class="col-sm-12 custom-full-width"><label class="form_label" for="ReportsLookups[lookup_field][]-'+v.id+'">'+v.id+'</label><input '+chk+' type="checkbox" value="'+v.id+'" name="ReportsLookups[lookup_field][]" class="user_type lookup_display_fields" id="ReportsLookups[lookup_field][]-'+v.id+'"></div>';
						}else{
							contentString=contentString+'<div class="col-sm-12 custom-full-width"><label class="form_label" for="ReportsLookups[lookup_field][]-'+v.id+'">'+v.id+'</label><input '+chk+' type="checkbox" value="'+v.id+'" name="ReportsLookups[lookup_field][]" class="user_type lookup_display_fields" id="ReportsLookups[lookup_field][]-'+v.id+'"></div>';	
						}
						
						
					}
					$('#filed-display-map-pop-up3').html('').html('<fieldset>'+contentString+'</fieldset>');
				},complete:function(){
					$('#filed-display-map-pop-up3 input').customInput();
				}
				});
				//$('#filed-display-map-pop-up').html('').html(response);		
				$('#filed-display-map-pop-up3').dialog({ 
					modal: true,
					width:'50em',
					height: 456,
					create: function(event, ui){ 
						$('#filed-display-map-pop-up3').parent().find('.ui-dialog-titlebar-close').append('<span class="ui-button-icon-primary ui-icon"></span>');
                                                $('#filed-display-map-pop-up3').parent().find('.ui-dialog-titlebar-close').attr("title", "Close");
                                                $('#filed-display-map-pop-up3').parent().find('.ui-dialog-titlebar-close').attr("aria-label", "Close");
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
							  click: function () 
							  {  
								 if($('.lookup_display_fields:checked').length >0){
									 var tablecontentString='';
									 //alert($('.lookup_display_fields:checked').length);
									 $('#ReportsLookups #is_change_form').val('1'); // change flag to 1
									 $('#is_change_form_main').val('1'); // change flag to 1
									 $('.lookup_display_fields:checked').each(function(){
										 if(tablecontentString==''){
											tablecontentString='<tr class="records"><td>'+this.value+'<input value="'+this.value+'" type="hidden" name="ReportsLookupDisplay3[field_value][]" class="form-control lookup_dispaly3_record"></td><td><a onclick="removeTr(this)" aria-label="remove Tr"><em class="fa fa-close text-primary"></em></a></td></tr>';
										 } else {
											tablecontentString=tablecontentString+'<tr class="records"><td>'+this.value+'<input value="'+this.value+'" type="hidden" name="ReportsLookupDisplay3[field_value][]" class="form-control lookup_dispaly3_record"></td><td><a onclick="removeTr(this)" aria-label="remove Tr"><em class="fa fa-close text-primary"></em></a></td></tr>';
										 }
										 //$('#lookup_dipslay_popup_tbody').append();
									 });
									// console.log(tablecontentString);
									 $('#lookup_dipslay3_popup_tbody').append(tablecontentString);
								 }
								 $(this).dialog('destroy').remove();
						      }
					  }
					]
				});	
	}
    function showDispalyMapPopup(){
		var display_name = $('#filter_field').val();
		var form = $('#ReportsLookups').serialize();
		showLoader();
			
				if($('body').find('#filed-display-map-pop-up').length == 0){
					$('body').append('<div class="dialog" id="filed-display-map-pop-up" title="Add Display Fields for '+display_name+'"></div>');
				}
				$.ajax({
				type : 'post',
				url:baseUrl+'report-management/gettablefield',
				data: {'depdrop_parents[0]':$('#lookup_table').val()},
				beforeSend:function (data) {showLoader();},
				success:function(response){
					hideLoader();
					var obj=JSON.parse(response);
					var i=0;
					var contentString='<legend class="sr-only">Add Display Fields for '+display_name+'</legend>';
					for(i=0;i<obj.output.length;i++){
						var v=obj.output[i];
						var chk='';
						$('.lookup_dispaly_record').each(function(){
							if(this.value==v.id){
								chk='disabled="disabled"';
								return false;
							}
						});
						if(contentString==""){
							contentString='<div class="col-sm-12 custom-full-width"><label class="form_label" for="ReportsLookups[lookup_field][]-'+v.id+'">'+v.id+'</label><input '+chk+' type="checkbox" value="'+v.id+'" name="ReportsLookups[lookup_field][]" class="user_type lookup_display_fields" id="ReportsLookups[lookup_field][]-'+v.id+'"></div>';
						}else{
							contentString=contentString+'<div class="col-sm-12 custom-full-width"><label class="form_label" for="ReportsLookups[lookup_field][]-'+v.id+'">'+v.id+'</label><input '+chk+' type="checkbox" value="'+v.id+'" name="ReportsLookups[lookup_field][]" class="user_type lookup_display_fields" id="ReportsLookups[lookup_field][]-'+v.id+'"></div>';	
						}
					}
					$('#filed-display-map-pop-up').html('').html('<fieldset>'+contentString+'</fieldset>');
				},complete:function(){
					$('#filed-display-map-pop-up input').customInput();
				}
				});
				//$('#filed-display-map-pop-up').html('').html(response);		
				$('#filed-display-map-pop-up').dialog({ 
					modal: true,
					width:'50em',
					height: 456,
					create: function(event, ui){ 
						$('#filed-display-map-pop-up').parent().find('.ui-dialog-titlebar-close').append('<span class="ui-button-icon-primary ui-icon"></span>');
                                                $('#filed-display-map-pop-up').parent().find('.ui-dialog-titlebar-close').attr("title", "Close");
                                                $('#filed-display-map-pop-up').parent().find('.ui-dialog-titlebar-close').attr("aria-label", "Close");
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
							  click: function () 
							  {  
								 if($('.lookup_display_fields:checked').length >0){
									 var tablecontentString='';
									 //alert($('.lookup_display_fields:checked').length);
									 $('.lookup_display_fields:checked').each(function(){
										 if(tablecontentString==''){
											tablecontentString='<tr class="records"><td>'+this.value+'<input value="'+this.value+'" type="hidden" name="ReportsLookupDisplay[field_value][]" class="form-control lookup_dispaly_record"></td><td><a onclick="removeTr(this)" aria-label="remove Tr"><em class="fa fa-close text-primary"></em></a></td></tr>';
										 } else {
											tablecontentString=tablecontentString+'<tr class="records"><td>'+this.value+'<input value="'+this.value+'" type="hidden" name="ReportsLookupDisplay[field_value][]" class="form-control lookup_dispaly_record"></td><td><a onclick="removeTr(this)" aria-label="remove Tr"><em class="fa fa-close text-primary"></em></a></td></tr>';
										 }
										 //$('#lookup_dipslay_popup_tbody').append();
									 });
									// console.log(tablecontentString);
									 $('#lookup_dipslay_popup_tbody').append(tablecontentString);
								 }
								 $('#ReportsTables #is_change_form').val('1'); // change flag to 1
								 $('#is_change_form_main').val('1'); // change flag to 1
								 $(this).dialog('destroy').remove();
						      }
					  }
					]
				});	
			
	}
    function showMapPopup(){
		var display_name = $('#reportslookups-filter_field').val();
		var form = $('#ReportsLookups').serialize();
		$.ajax({
			type : 'post',
			url:baseUrl+'report-management/filed-map-pop-up-option',
			data: form,
			beforeSend:function (data) {showLoader();},
			success:function(response){
				hideLoader();
				if($('body').find('#filed-map-pop-up').length == 0){
					$('body').append('<div class="dialog" id="filed-map-pop-up" title="Apply Filter to '+display_name+'"></div>');
				}
				$('#filed-map-pop-up').html('').html(response);		
				$('#filed-map-pop-up').dialog({ 
					modal: true,
					width:'50em',
					height: 456,
					create: function(event, ui){ 
						$('.ui-dialog-titlebar-close').append('<span class="ui-button-icon-primary ui-icon"></span>');
                                                $('.ui-dialog-titlebar-close').attr("title", "Close");
                                                $('.ui-dialog-titlebar-close').attr("aria-label", "Close");
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
						  click: function () 
						  {  
								$('.field_value').each(function(key,value){
									lookup_val = ($(this).closest('tr').find('.lookup_value').val());
									if(this.value!="" && lookup_val!=""){
										$('#norecordfund').remove();
										$('#ReportsTables #is_change_form').val('1'); // change flag to 1
										$('#is_change_form_main').val('1'); // change flag to 1
										$('#lookup_popup_tbody').append('<tr class="records"><td>'+this.value+'<input value="'+this.value+'" type="hidden" name="ReportsLookupValues[field_value][]" class="form-control"></td><td>'+lookup_val+'<input value="'+lookup_val+'" type="hidden" name="ReportsLookupValues[lookup_value][]" class="form-control"></td><td><a onclick="removeTr(this)" aria-label="remove Tr"><em class="fa fa-close text-primary"></em></a></td></tr>');
									}
								});
								$(this).dialog('destroy').remove();
						  }
						}
					]
				});	
			}
		});
	
	}
	function removeTr(obj){
		$(obj).parent().parent().remove();
		if($('#lookup_popup_tbody tr').length == 0){
			$('#lookup_popup_tbody').append('<tr id="norecordfund"><td colspan="3" align="ceneter">No Records Found.</td></tr>');
		}
	}
</script>
<noscript></noscript>
