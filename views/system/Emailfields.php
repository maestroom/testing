<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
\app\assets\SystemCustomWordingAsset::register($this);
?>
<style>#icon-plus{ margin:8px 0 0;padding:0;width:auto; }</style>
<?php if(!empty($selectFields)) { ?>
	<div class="mycontainer custom-full-width email-custom-fields">
		<?php $i=1; ?>
			<?php foreach($selectFields as $key => $val){ ?>
				<?php if(isset($key)){ ?>
					<div class="myheader">
                                            <a href="javascript:void(0);"><?= $key ?></a>
                                            <div class="col-xs-6 pull-right header-checkbox">
                                                <?php if($key != 'Application Fields') { // IRT 446 ?>
                                                    <div class="pull-left custom-checkbox" style="width: auto;">
                                                        <input type="checkbox" name="select_all_none" title="Select All/None"  id="select_all_none_<?= $i ?>" class="select_all_none_chk" value="1" onChange="select_all_none('<?= $i; ?>');" />
                                                        <label for="select_all_none_<?= $i ?>" class="select_all_none_chk">Select All/None</label>
                                                    </div>s                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 
                                                    <a class="col-xs-6 pull-right icon-set" id="icon-plus" href="javascript:AddFieldsToTemp('<?= $key ?>');">
                                                        <img title="Add More Field" src="<?=Url::base()?>/images/plus-sm-icon.png" alt="Add More Field">                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       
                                                    </a>
                                                <?php } ?>
                                            </div>
					</div>
					<div class="content">
                                            <fieldset>
                                               <legend class="sr-only">Application Fields</legend>     
                                                    <ul>
							<?php 
							if(!empty($val)){
                                                            // if($key=='Application Fields'){	
								$j=1; foreach($val as $keys => $value) { ?>
								<li>
                                                                    <?php if($value['display_name']=='[project_instruction_form_fields]' || $value['display_name']=='[task_outcome_form_fields]') { ?>
                                                                        <a class="icon-set" href="javascript:void(0);" title="Edit" onclick="UpdateTempField(<?=$value['id']?>,<?=$value['field_id']?>);" aria-label="edit <?= $value['display']; ?> Field"></a>
                                                                    <?php } else { ?>
                                                                        <a class="icon-set" href="javascript:void(0);" title="Edit" onclick="UpdateTempField(<?=$value['id']?>,<?=$value['field_id']?>);" aria-label="edit <?= $value['display']; ?> Field"><em title="Edit" class="fa fa-pencil text-primary"></em></a>	
                                                                    <?php } ?>
                                                                    
                                                                    <?php /*if($value['is_default']!=1) { ?>
                                                                        <a class="icon-set" href="javascript:DeleteTempField(<?=$value['id']?>,<?=$value['field_id']?>);" title="Remove" aria-label="Remove"><em class="fa fa-close text-primary"></em></a>
                                                                    <?php }*/ ?>
                                                                    <!--<span><?php // $value['display']; ?></span>-->
                                                                    <label class="chkbox-global-design m-0" for="inner_field_<?= $i ?>_<?= $j ?>" class="innerfield_<?= $i ?> fieldschk_infld"><?= $value['display']; ?></label>
                                                                    <div class="pull-right"> 
                                                                        <input type="checkbox" aria-label="<?= $value['display']; ?>" id="inner_field_<?= $i ?>_<?= $j ?>" class="fieldschk innerfield_<?= $i ?> fieldschk_infld" name="teamservice_<?= $i ?>_<?= $j ?>" value="<?= ($key != 'Application Fields')?'['.$value['display_name'].']':$value['display_name']; ?>" />
                                                                  </div>
								</li>
								<?php $j++; } /*} else{ ?>
								<?php $j=1; foreach($val as $keys => $value){ ?>
									<li><span><?= $value['display_name']; ?></span>
										<div class="pull-right"> 
											<input type="checkbox" id="inner_field_<?= $i ?>_<?= $j ?>" class="fieldschk innerfield_<?= $i ?>" name="teamservice_<?= $i ?>_<?= $j ?>" value="<?= '['.$value['display_name'].']'; ?>" />
											<label for="inner_field_<?= $i ?>_<?= $j ?>" class="innerfield_<?= $i ?>">&nbsp;</label>
										</div>
									</li>
								<?php $j++; } ?>
                                                            <?php } */
							}?>
                                                    </ul>
                                            </fieldset>    
					</div>
				<?php } ?>
		<?php $i++; } ?>
	</div>		
<?php } ?>
<script>
							
/* inner select all */
function select_all_none(loop){
	if($('#select_all_none_'+loop).is(':checked')){
		$('.innerfield_'+loop).prop('checked',true);
		$('.innerfield_'+loop).addClass('checked');
	} else{
		$('.innerfield_'+loop).prop('checked',false);
		$('.innerfield_'+loop).removeClass('checked');
	}
}

function AddFieldsToTemp(key){
	/*
		if(key == 'Application Fields'){
			add_report_table_fields(0,'');
		}else
	*/
	if(key == 'Instruction Form Fields'){
		add_instruction_fields();
	}
	else if(key == 'Task Outcome Form Fields'){
		add_data_fields();
	}
	else if(key == 'Calculation Fields'){
		add_calculation_fields();
	}
}
function add_calculation_fields(){
	$.ajax({
			url:baseUrl + "system/calculation-field",
			type:"get",
			beforeSend:function(){
				showLoader();
			},
			success:function(mydata){
				if($('#copy-field-dialog').length == 0) {
					$('body').append("<div id='copy-field-dialog'></div>");
				}
				
				$('#copy-field-dialog').html(mydata);
				
				$( "#copy-field-dialog" ).dialog({
					title:"Select Fields",
					autoOpen: true,
					resizable: false,
					width: "50em",
					height:456,
					modal: true,
					open: function (){
						hideLoader();
					},
					buttons: [
						{
							text: "Cancel",
							"title":"Cancel",
							"class": 'btn btn-primary',
							click: function() {
								$( this ).dialog( "close" );
							}
						},
						{
							text: "Add",
							"title":"Add",
							"class": 'btn btn-primary',
							click: function() {
								var element_id = $('input[name="form_field[]"]:checkbox:checked').map(function(){
									return this.value;
								}).get().join(',');
								if(element_id==''){
									alert('Please select any field to copy');
									return false;
								}
								var temp_sort= '<?=$email_sort?>';
								$.ajax({
									url:baseUrl + "system/calculation-field",
									data:{element_pkid:element_id,'email_sort':temp_sort},
									type:"post",
									beforeSend:function(){
										showLoader();
									},
									success:function(response){
										if(response=='OK') {
											$( "#field-dialog" ).dialog('close');
											fieldsDialog(temp_sort);
											$("#copy-field-dialog").dialog('destroy').remove();
										}
									}
								});
							}
						}
					],
					close: function() {
						$(this).dialog('destroy').remove();
						// Close code here (incidentally, same as Cancel code)
					}
				});
		}
	});
}
function add_data_fields()
{
		$.ajax({
			url:baseUrl + "system/get-fields-by-types",
			data:{formtype:'formtype',email_teamp:'data'},
			type:"get",
			beforeSend:function(){
				showLoader();
			},
			success:function(mydata){
				if($('#copy-field-dialog').length == 0){
					$('body').append("<div id='copy-field-dialog'></div>");
				}
				
				$('#copy-field-dialog').html(mydata);
				
				$( "#copy-field-dialog" ).dialog({
					title:"Select Fields",
					autoOpen: true,
					resizable: false,
					width: "50em",
					height:456,
					modal: true,
					open: function (){
						hideLoader();
					},
					buttons: [
						{
							text: "Cancel",
							"title":"Cancel",
							"class": 'btn btn-primary',
							click: function() {
								$( this ).dialog( "close" );
							}
						},
						{
							text: "Add",
							"title":"Add",
							"class": 'btn btn-primary',
							click: function() {
								var element_id = $('input[name="form_field[]"]:checkbox:checked').map(function(){
									return this.value;
								}).get().join(',');
								if(element_id==''){
									alert('Please select any field to copy');
									return false;
								}
								var temp_sort= '<?=$email_sort?>';
								$.ajax({
									url:baseUrl + "system/data-field",
									data:{element_pkid:element_id,'email_sort':temp_sort},
									type:"post",
									beforeSend:function(){
										showLoader();
									},
									success:function(response){
										if(response=='OK'){
											$( "#field-dialog" ).dialog('close');
											fieldsDialog(temp_sort);
											$("#copy-field-dialog").dialog('destroy').remove();
										}
									}
								});
							}
						}
					],
					close: function() {
						$(this).dialog('destroy').remove();
						// Close code here (incidentally, same as Cancel code)
					}
				});
		}
	});
}
function add_instruction_fields(){
					$.ajax({
						url:baseUrl + "system/get-fields-by-types",
						data:{formtype:'formtype',email_teamp:'instruction'},
						type:"get",
						beforeSend:function(){
							showLoader();
						},
						success:function(mydata){
							if($('#copy-field-dialog').length == 0){
								$('body').append("<div id='copy-field-dialog'></div>");
							}
							
							$('#copy-field-dialog').html(mydata);
							
							$( "#copy-field-dialog" ).dialog({
								title:"Select Fields",
								autoOpen: true,
								resizable: false,
								width: "50em",
								height:456,
								modal: true,
								open: function (){
									hideLoader();
								},
								buttons: [
									{
										text: "Cancel",
										"title":"Cancel",
										"class": 'btn btn-primary',
										click: function() {
											$( this ).dialog( "close" );
										}
									},
									{
										text: "Add",
										"title":"Add",
										"class": 'btn btn-primary',
										click: function() {
											var element_id = $('input[name="form_field[]"]:checkbox:checked').map(function(){
												return this.value;
											}).get().join(',');
											if(element_id==''){
												alert('Please select any field to copy');
												return false;
											}
											var temp_sort= '<?=$email_sort?>';
											$.ajax({
												url:baseUrl + "system/instruction-field",
												data:{element_pkid:element_id,'email_sort':temp_sort},
												type:"post",
												beforeSend:function(){
													showLoader();
												},
												success:function(response){
													if(response=='OK'){
														$( "#field-dialog" ).dialog('close');
														fieldsDialog(temp_sort);
														$("#copy-field-dialog").dialog('destroy').remove();
													}
												}
											});
										}
									}
								],
								close: function() {
									$(this).dialog('destroy').remove();
									// Close code here (incidentally, same as Cancel code)
								}
							});
					}
});
}
function add_report_table_fields(primary_table_name, flag){	
		$.ajax({
			url:baseUrl+'report-management/get-table-lists',
			beforeSend:function (data) {showLoader();},
			type:'post',
			success:function(response){
			hideLoader();						
			if($('body').find('#availabl-primary-tables').length == 0){
				$('body').append('<div class="dialog" id="availabl-primary-tables" title="Add Report Fields"></div>');
			}		
			$('#availabl-primary-tables').html('').html(response);	
			$('#availabl-primary-tables').dialog({ 
					modal: true,
			        width:'50em',
			        height: 456,
			        title:'Add Report Fields',
			        close: function(){
						$(this).dialog('destroy').remove();
					},
			        create: function(event, ui) { 						  
						 $('#availabl-primary-tables .ui-dialog-titlebar-close').html('<span class="ui-button-icon-primary ui-icon"></span>');
                                                 $('#availabl-primary-tables .ui-dialog-titlebar-close').attr("title", "Close");
                                        $('#availabl-primary-tables .ui-dialog-titlebar-close').attr("aria-label", "Close");
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
				                  		  var counter = parseInt(Math.random()*1000000); 
										  var tbl_name	= $('#my_primaryheader').val(); 
										  var checked_length = 	$('.column-fields:checked').length;
										  var filter_data = $('#filter_data').val();
										  var temp_sort= '<?=$email_sort?>';
										  // check if checkbox not selected
										  if(checked_length==0){
											alert("Select Table and related fields"); 
											return false;
										  }
										  
										  if(checked_length > 0){
										  	jQuery.ajax({
												url: baseUrl +'/system/add-table-field',
												type: 'POST',
												data: $('#availabl-primary-tables input, select').serialize()+'&temp_sort='+temp_sort,
												success: function (data) {
													if(data=='OK'){
														$( "#field-dialog" ).dialog('close');
														fieldsDialog(temp_sort);
														$("#availabl-primary-tables").dialog('destroy').remove();
													}
												}
											});	
										  }
									 	
				                  }
			                  }
			        ]
			    });
			},complete:function(){
				$('#availabl-primary-tables input').customInput();
				$('#availabl-primary-tables .custom-table-list').select2({
					allowClear: false,
					placeholder: 'Select Table',
					dropdownParent: $('#availabl-primary-tables')
				});
			}
		});
}
function DeleteTempField(temp_id,field_id){
	if(confirm('Are You sure you want to remove this field from email template?')){
		var temp_sort= '<?=$email_sort?>';
		jQuery.ajax({
			url: baseUrl +'/system/delete-temp-field&temp_id='+temp_id+'&field_id='+field_id,
			type: 'get',
			beforeSend:function (data) {showLoader();},
			success: function (data) {
				$( "#field-dialog" ).dialog('close');
				fieldsDialog(temp_sort);
			}
  		});
	}
}
function UpdateTempField(temp_id,field_id){
	if(!$('#updatetempfield').length){
		$('body').append("<div id='updatetempfield'></div>");
	}
 	var $custodianDialogContainer = $('#updatetempfield');
 	 $custodianDialogContainer.dialog({
     	 title:"Edit Email Template Field",
         autoOpen: false,
         resizable: false,
         height:456,
         width:"50em",
         modal: true,
         beforeClose: function(event){
			if(event.keyCode==27) trigger = 'esc';
			if(trigger!='Update') checkformstatus(event);
		 },
         buttons: {
             'Cancel': {
                     text: 'Cancel',
                     "title":"Cancel",
                     "class": 'btn btn-primary',
                     'aria-label': "Cancel Edit Custodian",
                     click:  function (event) {
						 trigger = 'Cancel';
						$custodianDialogContainer.dialog('close');
                     }
             },
             "Update":  {
                         text: 'Update',
                         "title":"Update",
                         "class": 'btn btn-primary',
                         'aria-label': "Edit Custodian",
                         click: function () {
                         	email_sort = $('#template_id').val();
                         	trigger = 'Update';
                        	 var form = $("#updatetempfield #frm-updatetempfield");
								jQuery.ajax({
									url : form.attr('action'),
									data:form.serialize(),
									type: 'post',
									beforeSend:function (data) {showLoader();},
									success: function (data) {
										hideLoader();
										if(data=='OK'){
											$custodianDialogContainer.dialog('destroy').remove();
											$( "#field-dialog" ).dialog('close');
											fieldsDialog(email_sort);
										}else{
											$("#updatetempfield").html(data);
										}
								}
                           	});
						 }
             }
         },
         close: function(event) {
			$custodianDialogContainer.dialog('destroy').remove();
		 }
     });
    $custodianDialogContainer.dialog("open");
 	jQuery.ajax({
		url: baseUrl +'/system/update-temp-field&temp_id='+temp_id+'&field_id='+field_id,
		type: 'get',
		beforeSend:function (data) {showLoader();},
		success: function (data) {
			hideLoader();
			$custodianDialogContainer.html(data);
			
		}
  	});
}
$(function() {
	$('input').customInput();
});
$('.myheader').on('click',function(){
	if($(this).hasClass('myheader-selected-tab')){
		$(this).removeClass('myheader-selected-tab');
	} else {
		$(this).addClass('myheader-selected-tab');
	}	
});

$(".myheader a").click(function () {
    $header = $(this).parent();
    $content = $header.next();
    $content.slideToggle(500, function () {
        $header.text(function () {
            // change text based on condition
            // return $content.is(":visible") ? "Collapse" : "Expand";
        });
    });

});

/* checkall email inner fields */
function checkall_emailfields(loop) 
{
	if($(".select_all").is(':checked')){
		$('.innerfield_'+loop).prop('checked',true);
		$('.innerfield_'+loop).addClass('checked');
	} else {
		$('.innerfield_'+loop).prop('checked',false);
		$('.innerfield_'+loop).removeClass('checked');
	}
}
</script>
<noscript></noscript>
