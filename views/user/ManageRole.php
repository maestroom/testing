<?php
	/* @var $this yii\web\View */
	use yii\helpers\Html;
	use yii\helpers\Url;
	use yii\widgets\ActiveForm;
	use kartik\widgets\Select2;
?>
<div class="right-main-container slide-open" id="maincontainer">
	<fieldset class="two-cols-fieldset workflow-management">
		<div class="administration-main-cols">
		 <div class="administration-lt-cols pull-left">
		  <button id="controlbtn" aria-label="Expand or Collapse" class="slide-control-btn" title="Expand/Collapse" onclick="jQuery('#maincontainer').toggleClass('slide-close');"><span>&nbsp;</span></button>
		  	
			  <ul>
			   <li><a href="javascript:void(0);" title="User Roles" class="admin-main-title" onclick="userRole();"><em class="fa fa-folder-open text-danger" title="User Roles"></em>User Roles</a>
			    <div class="select-items-dropdown">
					<?php if(!empty($role_details)){ foreach ($role_details as $role){
							$role_dropdown[$role->id] = Html::encode($role->role_name);
							}
						   }
						  echo Select2::widget([
									'name' => 'select_box',
									'attribute' => 'select_box',
									'data' => $role_dropdown,
									'options' => ['prompt' => 'Select Role','class' => 'form-control','onchange'=>'javascript:RoleDetails(this.value);','id'=>'nolabel-2'],
									/*'pluginOptions' => [
									  'allowClear' => true
									]*/
								]); 
						 ?>
			   		
			 	</div>
			    <div class="left-dropdown-list">
				    <div class="admin-left-module-list">
					<fieldset>
						<legend class="sr-only">User </legend>
						<ul class="sub-links">
							<?php if(!empty($role_details)){ foreach ($role_details as $role){ ?>
							 	<li id="role_<?=$role->id ?>"><a href="javascript:RoleDetails(<?=$role->id?>);" title="<?=Html::encode($role->role_name); ?>"><em class="fa fa-folder-o text-danger" title="<?=Html::encode($role->role_name); ?>"></em><?=Html::encode($role->role_name); ?></a></li>
							<?php }}?>
						</ul>
					</fieldset>
					</div>
				</div>
			   </li>
			  </ul>
			 </div>
 	 		<!-- admin Right first -->
			<div class="administration-rt-cols pull-right" id="admin_right">
				<div id="form_div"><?= $this->render('_roleForm', [
					'model' => $model,
					'security_features' => $security_features,
					'model_field_length' => $model_field_length
				]) ?></div>
			</div>
	 		<!-- End Right first -->
		</div>
	</fieldset>
</div>

<script type = "text/javascript" >
/**
 * Selected li 
 */
var selector = '.sub-links li';
$(selector).on('click', function(){
    $(selector).removeClass('active');
    $(this).addClass('active');
});

/**
 * Clear OR Delete button Role Form Action
 * Javascript Method RoleAction 
 */
 function RoleAction(role_id){
	 if(role_id != ''){
		var role_name = $('#role-role_name').val(); 
		var ans = confirm("Are you sure you want to Delete "+role_name+"?");
		if(ans == true){
			role_name = $('#role-role_name').val();
		 	$.ajax({
		      	url    : baseUrl +'/user/role-delete',
		        cache: false,
		        type   : 'post',
		        data   : 'role_id='+role_id,
		        success: function (response){
			       if(response == 'OK'){
		        	   commonAjax(baseUrl +'/user/manage-role','admin_main_container');
		           } 
		           if(response == 'Fail'){
			           alert(role_name+" cannot be Deleted as it is associated to 1+ User.");
			           return false;
		           }    
			    },
		        error  : function (){
		            console.log('internal server error');
		        }
	    	}); 
		}	
	 }else{
		 $("input[name='Role[role_type][]']:checkbox").prop('checked',false);
		 $('#role-role_name').val('');
		 $('#role-role_description').val('');
	 }
 }
 function userRole(){
	var chk_status = checkformstatus("event"); // check form edit status 
	if(chk_status==true) commonAjax(baseUrl +'/user/manage-role','admin_main_container');
 }
</script>
<noscript></noscript>
