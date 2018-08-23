<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\components\IsataskFormFlag;
/* @var $this yii\web\View */
/* @var $model app\models\UnitPrice */
/* @var $form yii\widgets\ActiveForm */
$js = <<<JS
function showNext(){
	if($('#taskstemplates-temp_name').val()==''){
		$("#taskstemplates-temp_name").trigger('blur');
	}
	else{
		$(".first").hide();
		$(".next").show();
	}
}
function showPrev(){
	$(".first").show();
	$(".next").hide();
}
function teamplateAddTask(){
		$.ajax({
			url:baseUrl+'workflow/teamplatestask',
			beforeSend:function (data) {showLoader();},
			success:function(response){
			hideLoader();
			if($('#availabl-service-tasks').length == 0){
				$('#admin_right').append('<div class="dialog" id="availabl-service-tasks" title="Add Available Service Tasks"></div>');
			}
			$('#availabl-service-tasks').html('').html(response);		
					$('#availabl-service-tasks').dialog({ 
					modal: true,
			        width:'50em',
			        height:456,
			        create: function(event, ui) { 
						  
						 $('.ui-dialog-titlebar-close').append('<span class="ui-button-icon-primary ui-icon"></span>');
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
										  $("#wftree").dynatree("getRoot").visit(function(node) {
											selKeys = $.map(node.tree.getSelectedNodes(), function(node){
												if(node.childList===null)
													return node.data.key.toString();
											});
											if(node.isSelected()) {
												if(node.data.isFolder == false){
													var service_task_id=node.data.servicetask_id;
													var service_location=node.data.loc;
													var service = node.data.title;
													var teamservice_task = node.data.teamservice;
													if($('#service_task_container').find('#'+service_task_id).length == 0){
														// change form flag 
														$("#is_change_form").val("1"); $("#is_change_form_main").val("1"); 
														$('#service_task_container').append('<li class="li_'+service_task_id+' clear" id="'+service_task_id+'"><label title="ServiceTask" class="pull-left" for="Service_tasks_'+service_task_id+'"><span class="sername_div">'+teamservice_task+' - '+service+'</span></label><input type="hidden" value="'+service_location+'" id="stl_'+service_task_id+'" name="ServiceteamLoc1['+service_task_id+'][]"><a class="icon-set handel_sort" href="javascript:void(0);"><span class="fa fa-arrows text-primary pull-right" title="Move"></span></a><a class="icon-set" href="javascript:removestask('+service_task_id+');"><span class="fa fa-close text-primary pull-right" title="Delete"></span></a></li>');
													}
												}
											}
										});
										$(this).dialog('destroy').remove();
										return false;
										/*$('.service_checkbox:checked').each(function(){
											service_task_id=$(this).attr('rel');
											service_location=$(this).data('loc');
											var service = $(this).data('service');
											var teamservice_task = $(this).data('teamservice');
											if($('#service_task_container').find('#'+service_task_id).length == 0){
												// change form flag 
												$("#is_change_form").val("1"); $("#is_change_form_main").val("1"); 
												$('#service_task_container').append('<li class="li_'+service_task_id+' clear" id="'+service_task_id+'"><label title="ServiceTask" class="pull-left" for="Service_tasks_'+service_task_id+'"><span class="sername_div">'+teamservice_task+' - '+service+'</span></label><input type="hidden" value="'+service_location+'" id="stl_'+service_task_id+'" name="ServiceteamLoc1['+service_task_id+'][]"><a class="icon-set handel_sort" href="javascript:void(0);"><span class="fa fa-arrows text-primary pull-right" title="Move"></span></a><a class="icon-set" href="javascript:removestask('+service_task_id+');"><span class="fa fa-close text-primary pull-right" title="Delete"></span></a></li>');
											}
										});
										$('input').customInput();*/
										
				                	  }
			                  }
			        ],
			    });	
			}
		});

}
function removestask(servicetask_id){
	//$('.wrkservice_task:checked').each(function(){
			//id=this.value;
			id=servicetask_id;
			$('#service_task_container').find('.li_'+id).remove();
	//});
	$('#is_change_form').val('1'); $('#is_change_form_main').val('1'); // change status of form change
}
function saveServiceTemplate(form_id,btn){
	if($('ul#service_task_container li').length == 0){
		alert('Please insert service task in workflow.');	
	}else{
		var form = $('form#'+form_id);
		$.ajax({
	        url    : form.attr('action'),
	        cache: false,
	        type   : 'post',
	        data   : form.serialize(),
	        beforeSend : function()    {
	        	$(btn).attr('disabled','disabled');
	        },
	        success: function (response){
	        	if(response == 'OK'){
					commonAjax(baseUrl +'/workflow/templates','admin_main_container');
				}else{
					$(btn).removeAttr("disabled");
	        	}
	        },
	        error  : function (){
	            console.log('internal server error');
	        }
	    });
	}
}
function ClearServices(){
		$('#service_task_container').empty();
}

function removeTemplate(template_id){
	var template_name = $('#taskstemplates-temp_name').val();
	if (confirm('Are you sure you want to Delete '+template_name+'?')) {
		jQuery.ajax({
		url: baseUrl +'/workflow/removetemplate&id='+template_id,
		type: 'get',
		beforeSend:function (data) {showLoader();},
		success: function (data) {
				hideLoader();
				commonAjax(baseUrl +'/workflow/templates','admin_main_container');
			}
		
	});
	}
}
JS;
$this->registerJs($js);
?>
<?php 

if($model->isNewRecord){
$form = ActiveForm::begin(['action'=> Url::to(['workflow/addtemplate']),'id' => $model->formName(),'enableAjaxValidation' => false,'enableClientValidation' => true]); 
}else{
$form = ActiveForm::begin(['id' => $model->formName(),'enableAjaxValidation' => false,'enableClientValidation' => true]); 
}
?>
<?= IsataskFormFlag::widget(); // change flag ?>
<fieldset class="one-cols-fieldset workflow_template_new work-flow-template-height">
    <div class="create-form first">
    	<?= $form->field($model, 'temp_name',['template' => "<div class='row input-field'><div class='col-md-4'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['maxlength'=>$model_field_length['temp_name']])->label('Template Name'); ?>
    	<?= $form->field($model, 'temp_desc',['template' => "<div class='row input-field'><div class='col-md-4'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textArea(['rows'=>3,'maxlength'=>$model_field_length['temp_desc']])->label('Template Description'); ?>
    	<div class='row input-field'>
            <div class="form-group clearfix">
                    <div class='col-md-4'>
                            Associate Project Request Types
                    </div>
                    <div class='col-md-7'>
                            <?php $template_id = $model->isNewRecord?0:$model->id; ?>
                            <?= Html::Button('Add Request Types', ['title' => 'Associate to Project Request Types','class' => 'btn btn-primary', 'id' => 'project-request-type', 'onClick' => 'getallrequesttypes('.$template_id.');']) ?>
                    </div>
            </div>
        </div>
        <div class="row input-field">
            <div class="form-group clearfix">
                    <div class="col-md-4"></div>
                    <div class="col-md-8">
                            <!-- table stripped -->
                                    <table class="table table-striped sm-table-report" id="form-request-type" width="100%" cellspacing="0" cellpadding="0" border="0">
                                            <thead>
                                                    <tr>
                                                            <th><a href="javascript:void(0);" class="tag-header-black" title="Associated Field Types"><strong>Project Request Type</strong></a></th>
                                                            <th><a href="javascript:void(0);" class="tag-header-black"  title="Action"><strong>Action</strong></a></th>
                                                    </tr>	
                                            </thead>
                                            <tbody>	
                                                    <?php  
                                                    $fieldtypes_ids = '';
                                                    if(isset($all_request_types) && !empty($all_request_types)){
                                                        ?>
                                                            <?php foreach($all_request_types as $key => $fieldtype){
                                                                if(empty($fieldtypes_ids)) { $fieldtypes_ids = $key;} else{$fieldtypes_ids .= ','.$key; }?>
                                                                    <tr class="requestType_<?php echo $key; ?>">
                                                                            <input type="hidden" name="request_type[]" class="request_type" value="<?php echo $key; ?>" />
                                                                            <td class="inner_text"><?php echo $fieldtype; ?></td>
                                                                            <td><a href="javascript:void(0);" onClick="remove_dialog_single_request('form-request-type','requestType_','<?php echo $key; ?>');" aria-label="Delete"><em class="fa fa-close text-primary" title="Delete" ></em></a></td>
                                                                    </tr>
                                                            <?php }?>
                                                    <?php } ?>
                                                    <input type="hidden" value="<?=$fieldtypes_ids?>" class="total_request_type_ids" >                                                    
                                            </tbody>
                                    </table>
                            <!-- End table -->
                    <div id="report-fieldtypes" class="has-error help-block"></div>
                    </div>
                    
            </div>
        </div>
    </div>	
	<div class=" button-set text-right first">
  	 <?= Html::button('Next', ['title' => 'Next','class' =>  'btn btn-primary','onclick'=>'showNext("'.$model->formName().'",this);']) ?>
	</div>
	
	
	
	
	
	
	
	
	
    <div class="template_wrkflow-main next" style="display: none;">

    	<div class="template_wrkflow-right">
    			<div class="head-title">Add Tasks to Template 
					<a href="javascript:void(0);" title="Add Tasks" class="fa fa-plus text-primary pull-right" onClick="javascript:teamplateAddTask();"></a>
    			</div>
    			<div class="template_wrkflow">
					     	<ul id="service_task_container" class="ui-sortable custom-inline-block-width">
								<?php if(isset($services)){
									foreach ($services as $service_task){?>
										<li class="li_<?=$service_task['servicetask_id']?> clear custom-full-width" id="<?=$service_task['servicetask_id']?>">
											<label title="ServiceTask" class="pull-left" for="Service_tasks_<?=$service_task['servicetask_id']?>"><span class="sername_div"><?php echo $service_task['service_name']; if($service_task['team_loc']) { echo ' - '.$service_task['team_location_name'];} echo ' - '.$service_task['service_task']; ?></span></label>
											<input type="hidden" value="<?=$service_task['team_loc']?>" id="stl_<?=$service_task['servicetask_id']?>" name="ServiceteamLoc1[<?=$service_task['servicetask_id']?>][]">
											<a class="icon-set handel_sort" aria-label="Move" href="javascript:void(0);"><span class="fa fa-arrows text-primary" title="Move"></span></a>
											<a class="icon-set pull-right" aria-label="Delete" href="javascript:removestask('<?php echo $service_task['servicetask_id']; ?>');" aria-label="Remove task"><span class="fa fa-close text-primary" title="Delete"></span></a>
											
										</li>
									<?php }
								} ?>
							</ul>	  
				</div>
    	</div>
    </div>
	
	
	<div class=" button-set text-right next" style="display: none;">
  <?= Html::button('Previous', ['title' => 'Previous','class' =>  'btn btn-primary','onclick'=>'showPrev();']) ?>
  <?= Html::button('Cancel', ['title' => 'Cancel','class' => 'btn btn-primary','id'=>'teamplateformcancel']) ?>
  <?php if(!$model->isNewRecord){ ?>
	  <?= Html::button('Delete', ['title' => 'Delete','class' =>  'btn btn-primary','onclick'=>'removeTemplate("'.$model->id.'");']) ?>
  <?php } ?>	  
  <?= Html::button($model->isNewRecord ?'Add':'Update', ['title' => $model->isNewRecord?'Add':'Update','class' =>  'btn btn-primary','onclick'=>'saveServiceTemplate("'.$model->formName().'",this);']) ?>
</div>
	
	
	
	
	
</fieldset>


<div class="dialog" id="availabl-service-tasks" title="Add Available Service Tasks"></div>
<?php ActiveForm::end(); ?>
<script type="text/javascript">
/** Tasks Templates **/	
$('input').bind('input', function(){
	$('#TasksTemplates #is_change_form').val('1'); 
	$('#TasksTemplates #is_change_form_main').val('1');
}); 
$('textarea').bind('input', function(){ 
	$('#TasksTemplates #is_change_form').val('1'); 
	$('#TasksTemplates #is_change_form_main').val('1'); 
});
$('document').ready(function(){
	$('#active_form_name').val('TasksTemplates'); // change form 
});
$('#teamplateformcancel').click(function(event){
	var chk_status = checkformstatus(event,'');
	if(chk_status==true)
		commonAjax(baseUrl +"/workflow/templates","admin_main_container");
});
$('input').customInput();
 var fixHelper = function(e, ui) {
	 ui.children().each(function() {
	 	// $(this).width($(this).width());
	 });
	 return ui;
  };
$("#service_task_container").sortable({
	handle:'.handel_sort',
	helper: fixHelper,
	change: function(event) {
		$('#is_change_form').val('1'); $('#is_change_form_main').val('1'); // form status change
	},
	stop: function(e,ui) { 
	}
}).disableSelection(); 
</script>
<noscript></noscript>
