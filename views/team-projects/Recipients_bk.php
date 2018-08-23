<?php
use app\models\Role;
$roleId             = Yii::$app->user->identity->role_id;
$roleInfo=Role::findOne($roleId);
$User_Role=explode(',',$roleInfo->role_type);
?>
<div class="custom-full-width">
        	<input type="checkbox" name="case_manager" autofocus <?php if(!empty($case_ids)) {?> checked="checked" <?php }?>  id="casemanager" onclick="$('.case_roles').prop('checked',this.checked); if(this.checked){ $('.case_roles').each(function(){ $(this).next().addClass('checked');});}else{$('.case_roles').each(function(){ $(this).next().removeClass('checked');});}"> 
        	<label for="casemanager"><strong> Case Manager Roles</strong></label>
        	<?php 
        	foreach ($role_data as $id=>$role){?>
        		<input type="checkbox" <?php if(in_array($id,$case_ids)) {?> checked="checked" <?php }?> <?php if(in_array(1,$User_Role) && $roleId==$id) {?>checked="checked"<?php }?> class="case_roles" name="case_role[<?php echo $id?>]" onclick="if($('.case_roles:checked').length >0) { $('#casemanager').attr('checked',true); $('#casemanager').next().addClass('checked'); } else { $('#casemanager').attr('checked',false); $('#casemanager').next().removeClass('checked'); }" value="<?php echo $id?>" id="case_role_<?php echo $id?>" title="<?php echo $role?>">
        		<label for="case_role_<?php echo $id?>"><?php echo $role?> </label>		
        	<?php }
        	?>
        	<input type="checkbox" name="team_member" id="team_member" <?php if(!empty($team_ids)) {?> checked="checked" <?php }?> onclick="$('.team_ids').prop('checked',this.checked); if(this.checked){ $('.team_ids').each(function(){ $(this).next().addClass('checked');});}else{$('.team_ids').each(function(){ $(this).next().removeClass('checked');});}">
        	<label for="team_member"> <strong>Team Member Roles</strong></label>
        	<?php 
        	foreach ($team_data as $team_id=>$team){
        	?>
    		<input type="checkbox" id="team_<?php echo $team_id?>"  class="team_ids" <?php if(in_array($team_id,$team_ids)) {?> checked="checked" <?php }?> <?php if($teamid==$team_id){?>checked="checked"<?php }?> name="team[<?php echo $team_id?>]" id="team_<?php echo $team_id?>" value="<?php echo $team_id?>" onclick="if($('.team_ids:checked').length >0) { $('#team_member').attr('checked',true);$('#team_member').next().addClass('checked'); } else { $('#team_member').attr('checked',false); $('#team_member').next().removeClass('checked');}" title="<?php echo $team?>">
    		<label for="team_<?php echo $team_id?>"><?php echo $team?> </label>		
			<?php 
        	}?>
</div>
<script>
$('input').customInput();
</script>
<noscript></noscript>