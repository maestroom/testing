<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\grid\GridView;
use yii\helpers\Url;


	echo GridView::widget([                        
		'id'=>'get-report-field-relationship-grid',
		'dataProvider' => $dataprovider,
		'layout' => '{items}',
		'columns' => [
				['class' => '\kartik\grid\ExpandRowColumn', 'detail' => function($dataprovider, $key, $index, $column) use($post_data, $field_list, $relations, $lookup,$model_field_length){return $this->render('table-fields-relation-lookup', ['dataprovider'=>$dataprovider, 'post_data'=>$post_data, 'field_list'=>$field_list, 'relations'=>$relations, 'lookup'=>$lookup,'keyValue'=>$key,'model_field_length'=>$model_field_length]);},'headerOptions'=>['title'=>'Expand/Collapse All','class'=>'first-th'],'contentOptions'=>['title'=>'Expand/Collapse Row','class' => 'first-td text-center'], 'expandIcon' => '<a href="javascript:void(0);" aria-label="Expand Raw"><span class="glyphicon glyphicon-plus"></span></a>', 'collapseIcon' => '<a href="javascript:void(0);" aria-label="Collapse Raw"><span class="glyphicon glyphicon-minus"></span></a>', 'expandTitle' => 'Expand', 'collapseTitle' => 'Collapse', 'expandAllTitle' => 'Expand All', 'collapseAllTitle' => 'Collapse All', 'mergeHeader'=>true, 'value' => function ($model) { return 1;}],
				['attribute' => 'fields', 'header'=> '<a href="javascript:void(0);" title="'.$table_display_name.'" class="tag-header-black">'.$table_display_name.'</a>', 'contentOptions' => ['class' => ''], 'format' => 'raw', 'value'=>function($model){ return '<a href="javascript:void(0);" class="tag-header-black" title="'.$model.'">'.$model.'</a>'; }],
			],
			'export'=>false,
			'floatHeader'=>false,
			'pjax'=>true,
			'responsive'=>false,
			'floatHeaderOptions' => ['top' => 'auto'],
			'persistResize'=>false,
			'resizableColumns'=>false,
			'pjaxSettings'=>[
			'options'=>['id'=>'get-report-field-relationship-grid-pajax','enablePushState' => false],
			'neverTimeout'=>true,
			'beforeGrid'=>'',
			'afterGrid'=>'',
		],
		'rowOptions'=>['class'=>'sort'],
	]);
?>				 
<script>
	function addRelation()
    {
		if($('body').find('#add-relationships').length == 0){
			$('body').append('<div class="dialog" id="manage-relationships" title="Add Relationships"></div>');
		}
		jQuery.ajax({
			url: baseUrl +'/report-management/manage-relationships&table_name='+$('#table_name').val(),
			type: 'post',
			data: $('form#ReportsTables').serialize(),
			beforeSend:function (data) {showLoader();},
			success: function (data) {
				hideLoader();
				$('#manage-relationships').dialog({ 
					modal: true,
					width:'50em',
					height: 456,
					title:'Add Relationships',
					create: function(event, ui) { 						  
						 $('.ui-dialog-titlebar-close').append('<span class="ui-button-icon-primary ui-icon"></span>');
                                                 $('.ui-dialog-titlebar-close').attr("title", "Close");
                                                $('.ui-dialog-titlebar-close').attr("aria-label", "Close");
					},
					close:function(){
						$(this).dialog('destroy').remove();
					},
					open:function(){
						$('#manage-relationships').html(data);
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
								var primary_tbl = $('#select-primary-table').val();
								var primary_tbl_field = $('#select-primary-table-field').val();
								var join_type = $('#join-type').val();
								var secondary_tbl = $('#select-secondary-table-list').val();
								var secondary_tbl_field = $('#select-secondary-table-field-list').val();
								if(primary_tbl_field!='' && join_type!='' && secondary_tbl != '' && secondary_tbl_field != ''){
									
									var CONTENTS_STRING='<input type="hidden" name="related_table_name['+primary_tbl_field+'][]" id="related_table_name_'+primary_tbl_field+'" value="'+secondary_tbl+'" />'+
									'<input type="hidden" name="related_base_table['+primary_tbl_field+'][]" id="related_base_table_'+primary_tbl_field+'" value="'+primary_tbl+'" />'+
									'<input type="hidden" name="related_field_name['+primary_tbl_field+'][]" id="related_field_name_'+primary_tbl_field+'" value="'+secondary_tbl_field+'" />'+
									'<input type="hidden" name="related_type['+primary_tbl_field+'][]" id="related_type_'+primary_tbl_field+'" value="'+join_type+'" />';
									console.log(CONTENTS_STRING);
									$('#related_tr_'+primary_tbl_field).append(CONTENTS_STRING);
									jQuery.ajax({
									   url: baseUrl +'/report-management/nextstep-field-relationship&table_name='+$('#table_name').val()+'&table_display_name='+$('#reportstables-table_display_name').val(),
									   type: 'post',
									   data:$('form#ReportsTables').serialize(),
									   beforeSend:function (data) {showLoader();},
									   success: function (data) {
										   hideLoader();
										   $("#main_fields").hide();
										   $("#btn_show_prev").show();
										   /* change form flag */
										   jQuery("#ReportsTables #is_change_form").val('1');
										   jQuery("#is_change_form_main").val('1');
										   /* End */
										   jQuery('#nextstep-fieldrelationship').html(data);	    	   
									   } 
									});
									
									$(this).dialog('destroy').remove();
								} else {
									alert("Please Fillup all Fields."); return false;
								}
							}
						}
					]
				});
			} 
		});
				
}
function addLookup(){
	if($('body').find('#manage-lookup').length == 0){
			$('body').append('<div class="dialog" id="manage-lookup" title="Add Lookup"></div>');
		}
		jQuery.ajax({
			url: baseUrl +'/report-management/manage-lookup&table_name='+$('#table_name').val(),
			type: 'post',
			data: $('form#ReportsTables').serialize(),
			beforeSend:function (data) {showLoader();},
			success: function (data) {
				hideLoader();
				var dialog = $('#manage-lookup').dialog({ 
					modal: true,
					width:'50em',
					height: 556,
					title:'Add Lookup',
					create: function(event, ui) { 						  
						 $('.ui-dialog-titlebar-close').append('<span class="ui-button-icon-primary ui-icon"></span>');
                                                 $('.ui-dialog-titlebar-close').attr("title", "Close");
                                                $('.ui-dialog-titlebar-close').attr("aria-label", "Close");
					},
					close:function(){
						$(this).dialog('destroy').remove();
					},
					open:function(){
						$('#manage-lookup').html(data);
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
								if(savefieldlookup('ReportsLookups')){
									var primary_tbl_field = $('#filter_field').val();
									var type=$('.chk_type:checked').val();
									var lookup_table = $('#lookup_table').val();
									
									var lookup_field = $('.lookup_dispaly_record').map(function () {return this.value;}).get().join(",");
									
									$('#lookup_'+primary_tbl_field).show();
									$('#lookup_type_'+primary_tbl_field).val(type);
									
									$('#lookup_filter_table_'+primary_tbl_field).val($('#table_name').val());
									$('#lookup_filter_field_'+primary_tbl_field).val(primary_tbl_field);
									if(type==1){
										var lookup_field_separator = $('#lookup_field_separator').val();
										$('#lookup_table_'+primary_tbl_field).val(lookup_table);
										$('#lookup_field_'+primary_tbl_field).val(lookup_field);
										$('#lookup_field_separator_'+primary_tbl_field).val(lookup_field_separator);
										var lookup_org_field=$('#reportslookups-lookup_field').val();
										$('#related_table_name_'+primary_tbl_field).val(lookup_table);
										$('#related_field_name_'+primary_tbl_field).val(lookup_org_field);
										$('#related_type_'+primary_tbl_field).val(1);
										
									}else if(type==2){
										var field_value = $('input[name="ReportsLookupValues[field_value][]"]').map(function () {return this.value;}).get();
										var lookup_value = $('input[name="ReportsLookupValues[lookup_value][]"]').map(function () {return this.value;}).get();
										var lookup_custom=[];
										
										$.each(field_value,function(i,val){
											console.log(val);
											var obj={'field_value':val,'lookup_value':lookup_value[i]};
											lookup_custom.push(obj);
										});
										var lookup_custom_str = JSON.stringify(lookup_custom);
										$('#lookup_custom_'+primary_tbl_field).val(lookup_custom_str);
									}else if(type==3){
										var lookup_field_separator = $('#lookup_field_separator2').val();
										var lookup_field = $('.lookup_dispaly3_record').map(function () {return this.value;}).get().join(",");
										$('#lookup_custom_field_'+primary_tbl_field).val(lookup_field);
										$('#lookup_field_separator_'+primary_tbl_field).val(lookup_field_separator);
									}
									
									jQuery.ajax({
									   url: baseUrl +'/report-management/nextstep-field-relationship&table_name='+$('#table_name').val()+'&table_display_name='+$('#reportstables-table_display_name').val(),
									   type: 'post',
									   data:$('form#ReportsTables').serialize(),
									   beforeSend:function (data) {showLoader();},
									   success: function (data) {
										   hideLoader();
										   $('#ReportsTables #is_change_form').val('1'); // change flag to 1
										   $('#is_change_form_main').val('1'); // change flag to 1
										   $("#main_fields").hide();
										   $("#btn_show_prev").show();
										   jQuery('#nextstep-fieldrelationship').html(data);	    	   
									   } 
									});
									$(this).dialog('destroy').remove();
								}
								
							}
						}
					]
				});
			} 
		});
}
</script>
<noscript></noscript>
