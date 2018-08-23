<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\components\IsataskFormFlag;
?>
  <div class="right-main-container slide-open" id="maincontainer">
			<fieldset class="two-cols-fieldset">
			<div class="administration-main-cols">
			 <div class="administration-lt-cols pull-left">
			 	<button title="Expand/Collapse" id="controlbtn" class="slide-control-btn" onclick="WorkflowToggle();" aria-label="Expand or Collapse">
					<span>&nbsp;</span>
				</button>
			  <ul>
			   <li><a href="javascript:void(0);" title="System Form" class="admin-main-title"><em title="System Form" class="fa fa-folder-open text-danger"></em>System Forms</a>
			    <div class="manage-admin-left-module-list">
				<ul class="sub-links">
				 <?php //if(!empty($dataCustodianForms)) {
				 	//foreach ($dataCustodianForms as $cform){
				 ?>
				 	<li class="sfrom <?php if($sysform== 'media_form'){?>active<?php }?>" id="sfrom_meida_form"><a href="javascript:void(0);" title="Media Form" onclick="updateSystemForm('media_form');"><em title="Media Form" class="fa fa-file-o  text-danger"></em> Media Form</a></li>
				 	<li class="sfrom <?php if($sysform== 'media_check_in_out_form'){?>active<?php }?>" id="sfrom_meida_check_in_out_form"><a href="javascript:void(0);" title="Media Check In/Out Form" onclick="updateSystemForm('media_check_in_out_form');"><em title="Media Check In/Out Form" class="fa fa-file-o  text-danger"></em> Media Check In/Out Form</a></li>
				 	<li class="sfrom <?php if($sysform== 'production_form'){?>active<?php }?>" id="sfrom_production_form"><a href="javascript:void(0);" title="Production Form" onclick="updateSystemForm('production_form');"><em title="Production Form" class="fa fa-file-o  text-danger"></em> Production Form</a></li>
				 	<li class="sfrom <?php if($sysform== 'custodian_form'){?>active<?php }?>" id="sfrom_custodian_form"><a href="javascript:void(0);" title="Custodian Form" onclick="updateSystemForm('custodian_form');"><em title="Custodian Form" class="fa fa-file-o  text-danger"></em> Custodian Form</a></li>		
				 <?php //}} ?>
				</ul>
				</div>
			   </li>
			  </ul>
			 </div>
			 
			 <div class="administration-rt-cols pull-right" id="admin_right">
			  	<?php $form = ActiveForm::begin(['id' => 'form','enableAjaxValidation' => false,'enableClientValidation' => true,'options' => ['enctype'=>'multipart/form-data']]); ?>
			  	<?= IsataskFormFlag::widget(); // change flag ?>
				<div id="first">
					<div class="sub-heading"><a href="javascript:void(0);" title="Edit <?=$form_lbl[$sysform]?>" class="tag-header-black">Edit <?=$form_lbl[$sysform]?></a></div>
				 	<fieldset class="one-cols-fieldset workflow_template_new">
					<div class="template_wrkflow-main next">
					 <div class="template_wrkflow-right">
						<div class="template_wrkflow" style="top:0;bottom: 55px">
							<ul id="service_task_container" class="ui-sortable custom-inline-block-width">
								<?php if(!empty($model)){$i=0;
									foreach($model as $mod){ 
										//if($mod->grid_type==0 && $i<10) {
										//}else{
											//$mod->grid_type=1;
										//}
										?>
											<li class="li_<?=$mod->id?> clear custom-full-width" id="<?=$mod->id?>">
											<label title="<?=$mod->sys_display_name?>" class="col-sm-5 text-left" for="Service_tasks_<?=$mod->id?>"><span class="sername_div"><?=$mod->sys_display_name?></span></label>
											<?php if($sysform == 'media_check_in_out_form' && $transtype_in[$mod->id] != ''){ ?>
												<label title="<?=$mod->sys_display_name?>" class="col-sm-5 text-left" for="Service_tasks_<?=$mod->id?>">
												<span class="sername_div"><?php echo '&nbsp; ( '.$transtype_in[$mod->id].' )';?></span></label>
											<?php } ?>
											<input value="<?=$mod->grid_type?>" id="grid_type_<?=$mod->id?>" name="SystemFrom[<?=$mod->id?>][grid_type]" type="hidden">
											<input value="<?=$mod->required?>" id="required_<?=$mod->id?>" name="SystemFrom[<?=$mod->id?>][required]" type="hidden">
											<a class="icon-set handel_sort" data-id="<?=$mod->id?>" aria-label="Move" href="javascript:void(0);"><span class="fa fa-arrows text-primary" title="Move"></span></a>
											<?php if($mod->grid_only==0){ ?>
												<a class="icon-set handel_required <?php if($mod->must_required==1){?>must_required<?php }?>" data-id="<?=$mod->id?>" title="<?php if($mod->must_required==1){?> Required <?php } elseif($mod->required==1){ ?> Select for Field Not Required <?php }else{?>Select for Field Required<?php }?>" href="javascript:void(0);"><span class="fa fa-lock text-white img-circle <?php if($mod->must_required==1){?> bg-muted <?php } elseif($mod->required==1){ ?> bg-danger <?php }else{?>bg-primary<?php }?>"></span></a>
											<?php }?>
											<?php if(!in_array($mod->sys_form,array('media_check_in_out_form','production_form'))){ ?>
													<?php if(in_array($mod->sys_form,array('media_form')) && in_array($mod->sys_field_name,array('client_id','client_case_id'))){  ?>
													<a class=" icon-set " data-id="<?=$mod->id?>" title="Grid Type Select to Appear in Main Grid Display" href="javascript:void(0);"><span class="glyphicon  glyphicon-minus bg-muted text-white"></span></a>
													<?php } else{ ?>	
													<a class=" icon-set handel_grid_type" data-id="<?=$mod->id?>" title="Grid Type <?php if($mod->grid_type==0) {?> Select to Appear in Expanded Grid Display <?php $i++;}else{?> Select to Appear in Main Grid Display <?php }?>" href="javascript:void(0);"><span class="glyphicon <?php if($mod->grid_type==0) {?> glyphicon-plus bg-success <?php $i++;}else{?> glyphicon-minus bg-danger <?php }?> text-white"></span></a>

											<?php  }
											}?>
											</li>
								<?php }}?>			
							</ul>	  
						</div>
					  </div>
					  </div>
					</fieldset>
					<div class="button-set text-right">
						<?= Html::button('Cancel', ['title'=>"Cancel",'class' => 'btn btn-primary','onclick'=>'updateSystemForm("'.$sysform.'");']) ?>
						<?= Html::button('Update', ['title'=>"Update",'class' => 'btn btn-primary','onclick'=>'UpdateForm("'.$sysform.'");']) ?>
					</div>
				</div>
				<?php ActiveForm::end(); ?>
			 </div>
			</div>
			</fieldset>
		   </div>
<script type="text/javascript">
/* check flag status */
$('input').bind('input', function(){
	$("#formIDcfrom #is_change_form").val('1'); // change flag to 1
	$("#formIDcfrom #is_change_form_main").val('1'); // change flag to 1
});	
$('textarea').bind("input",function(){
	$("#formIDcfrom #is_change_form").val('1'); // change flag to 1
	$("#formIDcfrom #is_change_form_main").val('1'); // change flag to 1
});
$('#form_builder_toolbox1').click(function(){
	$("#formIDcfrom #is_change_form").val('1'); // change flag to 1
	$("#formIDcfrom #is_change_form_main").val('1'); // change flag to 1
});
function updateSystemForm(form_id){
	commonAjax(baseUrl +'/system/form&sysform='+form_id,'admin_main_container');
}
function UpdateForm(form_id){
	var form = $('#form');
	$.ajax({
        url    : baseUrl+'/system/form&sysform='+form_id,
        type   : 'post',
        data   : form.serialize(),
        beforeSend : function()    {
        	$('.btn').attr('disabled','disabled');
        },
        success: function (response){
        	if(response == 'OK'){
        		commonAjax(baseUrl +'/system/form&sysform='+form_id,'admin_main_container');
        	}else{
            	$('.btn').removeAttr("disabled");
        	}
        },
        error  : function (){
            console.log('internal server error');
        }
    });
}
jQuery(document).ready(function($) {
	$("#active_form_name").val('formIDcfrom'); // form name
	$('.handel_required').click(function(){
		var id=$(this).data('id');
		if(!$(this).hasClass('must_required')){
			if($(this).find('span').hasClass('bg-primary')){
				$(this).find('span').removeClass('bg-primary');
				$(this).find('span').addClass('bg-danger');
				$(this).find('span').attr('title','Select for Field Not Required')
				$("#required_"+id).val(1);
				
			}
			else if($(this).find('span').hasClass('bg-danger')){
				$(this).find('span').removeClass('bg-danger');
				$(this).find('span').addClass('bg-primary');
				$(this).find('span').attr('title','Select for Field Required')
				$("#required_"+id).val(0);
			}
		}
	});
	$('.handel_grid_type').click(function(){
		var id=$(this).data('id');
		var cnt_maingrid=$('span.glyphicon-plus').length;
		if($(this).find('span').hasClass('glyphicon-plus')){
			$(this).find('span').removeClass('glyphicon-plus');
			$(this).find('span').removeClass('bg-success');
			$(this).find('span').addClass('glyphicon-minus');
			$(this).find('span').addClass('bg-danger');
			$(this).find('span').attr('title','Select to Appear in Main Grid Display');
			$("#grid_type_"+id).val(1);
		}
		else if($(this).find('span').hasClass('glyphicon-minus') && cnt_maingrid<10){
			$(this).find('span').removeClass('glyphicon-minus');
			$(this).find('span').removeClass('bg-danger');
			$(this).find('span').addClass('glyphicon-plus');
			$(this).find('span').addClass('bg-success');
			$(this).find('span').attr('title','Select to Appear in Expanded Grid Display');
			$("#grid_type_"+id).val(0);
		}
	});
});
var fixHelper = function(e, ui) {
	 ui.children().each(function() {
	 	//$(this).width($(this).width());
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
