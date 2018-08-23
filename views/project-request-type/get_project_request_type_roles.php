<?php 
use yii\helpers\Html;
use yii\widgets\ActiveForm;
$form = ActiveForm::begin(['id'=>$prtmodel->formName(), 'enableAjaxValidation' => false, 'enableClientValidation' => true]);
?>
<div id="role_type_container" class="mycontainer">	
	<div class="myheader">
        <a href="javascript:void(0);" class="">Select All User Roles To Have Access/Visibility</a>            
    </div>
	<div class="content borderless">
	<?php if(!empty($role_details)) { ?>
		<ul>
			<li>	
				<div class="pull-right header-checkbox">
					<input type="checkbox" id="chkselectall" class="pull-right" name="chkselectall" onclick="all_checkall_detail(this.checked);"/> 
					<label for="chkselectall" class="">&nbsp;</label> 
				</div>			
					<span class="pull-right"><b>Select All/None</b></span>				
			</li>
		<?php foreach($role_details as $key => $single){ ?>
			<li>
				<span><?php echo $single; ?></span>
				<div class="pull-right "> 
					<input type="checkbox" id="role_detail_<?php echo $key; ?>"  value="<?php echo $key; ?>" class="chk" name="role_ids[]" <?php if(isset($request_type_roles[$key]) && $request_type_roles[$key] == $project_request_type_id) echo 'checked="checked"';?> />                                     
					<label for="role_detail_<?php echo $key; ?>" class="report_details_<?php echo $key; ?>">&nbsp;</label> 
				</div>
			</li>
		 <?php } ?>
			</ul>
		<?php }?>
	</div>
</div>
<?php ActiveForm::end();?>
<style>
    #availabl-roles .mycontainer .content{display:block;}
</style>
<script>
	$(document).ready(function(){
		$('#role_type_container input').customInput();
        });
function all_checkall_detail(stat){	
	if(stat){
		$('.chk').each(function(){
			$(this).prop('checked',true);
			$(this).attr('checked',true);
			$(this).next('label').addClass('checked');
		});
	} else {
//            alert('nelson');
		$('.chk').each(function(){
//                    console.log($(this).attr('id'));
                    $(this).prop('checked',false);
//                    $(this).attr('checked',false);  
//                        $('#'+$(this).attr('id')).click();
//                    $(this).removeAttr('checked');
                    $(this).next('label').removeClass('checked');
		});
	}
}
$(document).ready(function(){
	if($('.chk:checked').length == $('.chk').length){
			$("#chkselectall").prop('checked',true);
			$("#chkselectall").attr('checked',true);			
			$("#chkselectall").next('label').addClass('checked');			
		}
	$('.chk').change(function(){
		if($('.chk:checked').length == $('.chk').length){
			$("#chkselectall").prop('checked',true);
			$("#chkselectall").attr('checked',true);			
			$("#chkselectall").next('label').addClass('checked');			
		}else{
			$("#chkselectall").prop('checked',false);
			$("#chkselectall").attr('checked',false);			
			$("#chkselectall").next('label').removeClass('checked');
		}
	});	
});
</script>
