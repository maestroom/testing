<?php
use yii\helpers\Html;
use kartik\widgets\Select2;
use yii\widgets\ListView;
use yii\widgets\Pjax;
?>
<div class="right-main-container slide-open" id="maincontainer">
			<fieldset class="two-cols-fieldset workflow-management">
			<div class="administration-main-cols">
			 <div class="administration-lt-cols pull-left">
			  <button id="controlbtn" aria-label="Expand or Collapse" class="slide-control-btn" title="Expand/Collapse" onclick="jQuery('#maincontainer').toggleClass('slide-close');"><span>&nbsp;</span></button>
			  <div id="role_wise_user">
				<ul>
					<li><a href="javascript:void(0);" class="admin-main-title" title="Users" onclick="UserAdd();"><em title="Users" class="fa fa-folder-open text-danger"></em>Users</a>
					<div class="select-items-dropdown">
						<?php 
							/*echo Select2::widget([
								'name' => 'select_box',
								'attribute' => 'select_box',
								'data' => $role_details,
								'options' => ['prompt' => 'Select Role','title' => 'Select Role', 'class' => 'form-control','onchange'=>'javascript:getRoleUser(this.value);','id'=>'nolabel-2'],
							]);*/
						?>
                                            <div class="left-header-upm">
                                           <input type="text" id="filterUserMain" name="filterUser" class="form-control margin-bottom-5 filter-manage-user-inp manage-user-inp" title="Filter Users" placeholder="Filter Users" value=""/>
                                       <span title="Clear" class="clear_text" data-idname="filterUserMain">&times;</span>
                                       </div>
<!--						<input type="text" name="filterUser" class="form-control filter-user-inp" title="Filter Users" placeholder="Filter Users" onkeyup="getFilteredUsers(this.value);" value=""/>                                                -->
					</div>
				   <fieldset>
						<legend class="sr-only">User </legend>
				    <div id="sub-links-user" class="workflow-manage-user-list">
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
									return $this->render('_list_item_user',['model' => $model,'i'=>$key,'from'=>'user']);
								},
								'layout' => '{items}{pager}',
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
						<ul>
							<li><?= Html::button('Bulk Role', ['title'=>"Bulk Role",'class' => 'btn btn-primary','id' => 'bulk_role_action', 'onClick' => "userBulkRole()"]) ?></li>
                                                        <li class="user_last"><input type="checkbox" name="userAll" class="selectAll" id="userAll" value="all" title="Select All" />Select All <label for="userAll"><span class="sr-only">Select All</span></label></li>
						</ul>
					  </div>
					</div>
				   </li>
				</ul>
			 </div>
				<div class="administration-rt-cols pull-right" id="admin_right"></div>
			</div>
	</fieldset>
</div>
<script>
UserAdd();
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

function userBulkRole(){
   var userIDs = new Array(); 
   $.each($("input[name='users[]']:checked"), function(){userIDs.push($(this).val());});
   if(userIDs.length < 1){alert("Please select a User to perform this action.");return false;}   
   $.ajax({
	    type:'POST',
		url:baseUrl+'user/bulkedituser',
		data: {ids:userIDs},
		beforeSend:function (data) {showLoader();},
		success:function(response){
			hideLoader();
			if($('#availabl-service-tasks').length == 0){
				$('#admin_right').append('<div class="dialog" id="availabl-service-tasks" title="Bulk Edit User Role"></div>');
			}
			$('#availabl-service-tasks').html('').html(response);		
			$('#availabl-service-tasks').dialog({ 
				modal: true,
		        width:'50em',
		        height:302,
		        create: function(event, ui) { 
					$('.ui-dialog-titlebar-close').append('<span class="ui-button-icon-primary ui-icon"></span>');
                                        $('.ui-dialog-titlebar-close').attr("title", "Close");
                                        $('.ui-dialog-titlebar-close').attr("aria-label", "Close");
				},
		    	buttons: [
		              { 
	                	  text: "Cancel", 
	                	  "class": 'btn btn-primary',
	                	  click: function () { 
	                		  $(this).dialog('destroy').remove();
 
	                	  } 
	                  },
		    		  { 
	                	  text: "Update", 
	                	  "class": 'btn btn-primary',
	                	  click: function () { 
		                	  	var role_id = $('#bulk_edit').val();
		                	  	jQuery.ajax({
		                	        url: baseUrl +'/user/updatebulkuserrole',
		                	        type: 'post',
		                	        data   : 'role_id='+role_id+'&userId='+userIDs,
		                	        beforeSend:function (response) {showLoader();},
		                	        success: function (response) {
		                	        	hideLoader();
			                	    	if(response == 'OK'){
			                	    		$('#availabl-service-tasks').dialog('close');
			                	    		commonAjax(baseUrl +'/user/manage-user','admin_main_container');
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
