<?php
use yii\helpers\Html;
use kartik\widgets\Select2;
use yii\widgets\ListView;
use yii\widgets\Pjax;
?>
<div class="right-main-container slide-open uma-main-container" id="maincontainer">
			<fieldset class="two-cols-fieldset workflow-management">
			<div class="administration-main-cols">
			 <div class="administration-lt-cols pull-left">
			  <button id="controlbtn"  aria-label="Expand or Collapse" class="slide-control-btn" title="Expand/Collapse" onclick="jQuery('#maincontainer').toggleClass('slide-close');"><span>&nbsp;</span></button>
			  <div id="role_wise_user">
				<ul>
				   <li><a href="javascript:void(0);" class="admin-main-title" title="Users"><em title="Users" class="fa fa-folder-open text-danger"></em>Users</a>
				   <div class="select-items-dropdown">
                                       <div class="left-header-upm">
                                           <input type="text" id="filterUserMain" name="filterUser" class="form-control margin-bottom-5 filter-manage-user-inp" title="Filter Users" placeholder="Filter Users" value=""/>
                                       <span title="Clear" class="clear_text" data-idname="filterUserMain">&times;</span>
                                       </div>
				   <?php echo Select2::widget([
								'name' => 'select_role_user',
								'attribute' => 'select_box',
								'data' => $role_details,
								'options' => ['prompt' => 'Select Role to Apply Bulk Access','title' => 'Select Role to Apply Bulk Access', 'class' => 'form-control','onchange'=>'javascript:getRoleUser();','id'=>'nolabel-2'],
					]);?>
				  </div>
				   <fieldset>
						<legend class="sr-only">User </legend>
				    <div id="sub-links-user" class="workflow-manage-user-access-list">
                        <div class="admin-left-module-list">
							<?php Pjax::begin([
							'enablePushState' => false,
							'enableReplaceState' => false
							]);?>
							<?= 
							ListView::widget([
								'dataProvider' => $dataProvider,
								'options' => [
									'tag' => 'ul',
									'class' => 'sub-links checkbox-with-sub-link',
									'id' => 'list-wrapper',
								],
								'itemView' => function ($model, $key, $index, $widget) {
									return $this->render('_list_item',['model' => $model,'i'=>$key,'from'=>'useraccess']);
								},
								'layout' => "{items}\n{pager}",
								'pager'=>[
									'maxButtonCount'=>3
								],
							]); 
							Pjax::end();
							?>
						</div>
					</div>
					</fieldset>
					<div id="role_wise_select">
						<input type="hidden" id="txtaccess" name="txtaccess" value="0"/>
						<ul>
							<li id="no_access"><?= Html::button('No Access', ['title'=>"No Access",'class' => 'btn btn-primary','id' => 'bulk_access_action', 'onClick' => "$('input[name=\"txtaccess\"]').val(1);getRoleUser()"]) ?></li>
							<li id="all_user" style="display:none"><?= Html::button('All Users', ['title'=>"All Users",'class' => 'btn btn-primary','id' => 'bulk_access_action', 'onClick' => "$('input[name=\"txtaccess\"]').val(0);getRoleUser()"]) ?></li>
							<li style="display:none;" class="role_wise_select_access"><?= Html::button('Bulk Access', ['title'=>"Bulk Access",'class' => 'btn btn-primary','id' => 'bulk_access_action', 'onClick' => "userBulkAccess()"]) ?></li>
							<li style="display:none;" class="user_last role_wise_select_access"><input type="checkbox" name="userAll" class="selectAll" id="userAll" value="all" title="Select All" />Select All <label for="userAll" title="Select All"><span class="sr-only">Select All</span></label></li>
						</ul>
					  </div>
					</div>
				   </li>
				</ul>
			 </div>
			<div class="administration-rt-cols pull-right" id="admin_right">
				
			</div>
		</div>
	</fieldset>
</div>
<script>
$('#role_wise_select :checkbox').customInput();
/**
 * Get Role wise user details in Usermanagement Manage user
 */
var currentUserRoleAccessRequest = null;
function getRoleUser(){

	var access = $('input[name="txtaccess"]').val();
	var role_id = $('select[name="select_role_user"]').val();
	var filterText = $('input[name="filterUser"]').val();
	
	if(access == 1) {
		$('#no_access').hide();
		$('#all_user').show();
	} else {
		$('#no_access').show();
		$('#all_user').hide();
	}
	
	if(role_id!=''){
		$('input[name="txtaccess"]').val(0);
		access=0;
	}
	
	currentUserRoleAccessRequest = jQuery.ajax({
       url: baseUrl +'/user/ajax-role-user&role_id='+role_id+'&filteruser='+filterText+'&from=useraccess&access='+access,
       type: 'get',
       beforeSend:function (data) {
			if(currentUserRoleAccessRequest != null) {
				currentUserRoleAccessRequest.abort();
			}
		},
       success: function (data) {
		   
    	   //hideLoader();
    	   $('#admin_right').html('');
    	   if(role_id!=''){
				$('#all_user').hide();
				$('#no_access').hide();
				$('.role_wise_select_access').show();
		   } else {
				if(access != 1){
					$('#no_access').show();
				}
				$('.role_wise_select_access').hide();
		   }
		   jQuery('#sub-links-user').html(data);
       }
	});
}

/**
 * User All checkbox for bulk role 
 */
$("#userAll").change(function () {
	if ($('#userAll').is(':checked')){	
		$(".usersChk").addClass('checked');
		$(".userschkall").prop('checked',true);
   }else{
		$('.usersChk').removeClass('checked');
		$(".userschkall").prop('checked',false);
    }	
});

function userBulkAccess(){
	var role_id =$('select[name="select_role_user"]').val();
	var userIDs = new Array(); 
	$.each($("input[name='users[]']:checked"), function(){userIDs.push($(this).val());});
	if(userIDs.length < 1){alert("Please select a User to perform this action.");return false;}   
	$.ajax({
	    type:'POST',
		url:baseUrl+'user/bulk-user-access',
		data: {ids:userIDs,'role_id':role_id},
		beforeSend:function (data) {showLoader();},
		success:function(response){
			hideLoader();
			if($('#availabl-user-access').length == 0){
                            $('#admin_right').append('<div class="dialog" id="availabl-user-access" title="Bulk User Access"></div>');
			}
			$('#availabl-user-access').html('').html(response);		
			$('#availabl-user-access').dialog({
                        modal: true,
		        width:'80em',
		        height:573,
		        create: function(event, ui) { 
					$('.ui-dialog-titlebar-close').append('<span class="ui-button-icon-primary ui-icon"></span>');
                                        $('.ui-dialog-titlebar-close').attr("title", "Close");
                                        $('.ui-dialog-titlebar-close').attr("aria-label", "Close");

				},
				open: function(){
					$(this).find('input').customInput();
                                        $('#bulk_no_client_cases').prop('checked',true);
					var role_type = $('#availabl-user-access input[name="txt_role_type"]').val();
					if(role_type == '1,2') {
						$( "#availabl-user-access #user-access-tabs" ).tabs('enable');
					}
					else if(role_type == '2,1'){
						$( "#availabl-user-access #user-access-tabs" ).tabs('enable');
					}
					else if(role_type == '1'){
						$( "#availabl-user-access #user-access-tabs" ).tabs("option","disabled", [1]);
						$( "#availabl-user-access #user-access-tabs" ).tabs("option","active",[0]);
					}
					else if(role_type == '2'){
						$( "#availabl-user-access #user-access-tabs" ).tabs("option","disabled", [0]);
						$( "#availabl-user-access #user-access-tabs" ).tabs("option","active",[1]);
					}
				},
				close: function(){
					$(this).dialog('destroy').remove();
				},
		    	buttons: [
		              { 
	                	  text: "Cancel", 
	                	  "class": 'btn btn-primary',
	                	  click: function () { 
	                		  $(this).dialog('close');
 	                	  } 
	                  },
		    		  { 
	                	  text: "UnAssign", 
	                	  "class": 'btn btn-primary',
	                	  click: function () { 
                                        if(confirm("You are about to UnAssign the Project Access from the\nselected user(s), are you sure?")){
											var formdata = $('form#User_access').serialize();
											jQuery.ajax({
												url: baseUrl +'/user/bulk-user-access-update',
												type: 'post',
												data: formdata+'&btn=unassign',
												beforeSend:function(){showLoader();},
												success: function(response) {
													hideLoader();
													if(response == 'OK'){
														$('#availabl-user-access').dialog('close',true);                                                                
														commonAjax(baseUrl +'/user/manage-user-access','admin_main_container');
													} 
												}
											});
										}
		                  }
	                  },
	                  { 
	                	  text: "Assign", 
	                	  "class": 'btn btn-primary',
	                	  click: function () {
                                            var formdata = $('form#User_access').serialize();
                                            jQuery.ajax({
		                	        url: baseUrl +'/user/bulk-user-access-update',
		                	        type: 'post',
		                	        data: formdata+'&btn=assign',
		                	        beforeSend:function(){showLoader();},
		                	        success: function(response) {
		                	        	hideLoader();
			                	    	if(response == 'OK'){
			                	    		$('#availabl-user-access').dialog('close',true);
			                	    		commonAjax(baseUrl +'/user/manage-user-access','admin_main_container');
			                	    	} 
		                	        }
		                	 	});
						  }
					  }
	             ]
		    });	
		}
	});
}
</script>
<noscript></noscript>
