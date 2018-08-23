<?php
use yii\helpers\Html;
?>
  <div class="select-location-popup">
    <div class="asign-dropdown">
      <ul class="asign-menu">
        <li class="search-box">
		  	<label for="searchUsers" style="display:none"><span class="sr-only">Assign/Transit Task User Filter List</span></label>
          <input id="searchUsers" type="text" name="search1" class="form-control" placeholder="Filter List">
          <input id="services" value="<?=$services?>" type="hidden">
          <input id="taskunits" value="<?=$taskunit_id?>" type="hidden">
          
        </li>
        <?php if(!empty($taskunit_info) && $taskunit_info->unit_assigned_to!=0){?>
        <li>
          <strong><span>Currently Assigned To: <?=$taskunit_info->assignedUser->usr_first_name." ".$taskunit_info->assignedUser->usr_lastname; ?></span></strong>
        </li>
        <?php }?>
        <li class="search-result">
		  <ul>
		  	<?php if(isset($data['team_members'])){?>
			<li id="header"><strong>Team Members <span class="pull-right"><?php /*echo count($data['team_members'])*/?></span></strong>
			  <ul>
			  	<?php foreach ($data['team_members'] as $user){ if(!empty($taskunit_info) && $taskunit_info->unit_assigned_to==$user->id){ continue;}
                                if($user->usr_first_name == '' && $user->usr_lastname == ''){
                                    $UserName = $user->usr_username;
                                }else{
                                    $UserName = ucwords($user->usr_first_name." ".$user->usr_lastname);
                                }
?>			<li class='user_li' data-id="<?=$user->id ?>"><?=Html::a($UserName,null,['href'=>'javascript:void(0)','onclick'=>'$(".user_li").removeClass("active");$(this).parent().addClass("active")']);?></li>
				<?php }?>
			  </ul>
			</li>
			<?php }?>
			<?php if(isset($data['case_members'])){?>
			<li><strong>Case Members <span class="pull-right"><?php /*echo count($data['case_members'])*/?></span></strong>
			  <ul>
			  	<?php foreach ($data['case_members'] as $user){ if(!empty($taskunit_info) && $taskunit_info->unit_assigned_to==$user->id){ continue;}
                                if($user->usr_first_name == '' && $user->usr_lastname == ''){
                                    $cUserName = $user->usr_username;
                                }else{
                                    $cUserName = ucwords($user->usr_first_name." ".$user->usr_lastname);
                                }
                                ?>
				<li class='user_li' data-id="<?=$user->id ?>"><?=Html::a($cUserName,null,['href'=>'javascript:void(0)','onclick'=>'$(".user_li").removeClass("active");$(this).parent().addClass("active")']);?></li>
				<?php }?>
			  </ul>
			</li>
			<?php }?>
			<li><strong>Both Case Managers & Team Members <span class="pull-right"><?php /*echo count($data['both_members'])*/?></span></strong>
			  <ul>
			  <?php if(!empty($data['both_members'])){foreach ($data['both_members'] as $user){ if(!empty($taskunit_info) && $taskunit_info->unit_assigned_to==$user->id){ continue;}
                          if($user->usr_first_name == '' && $user->usr_lastname == ''){
                                    $cUserName = $user->usr_username;
                                }else{
                                    $cUserName = ucwords($user->usr_first_name." ".$user->usr_lastname);
                                }
                          ?>
				<li class='user_li' data-id="<?=$user->id ?>"><?=Html::a($cUserName,null,['href'=>'javascript:void(0)','onclick'=>'$(".user_li").removeClass("active");$(this).parent().addClass("active")']);?></li>
			  <?php }}?>
			  </ul>
			</li>
		  </ul>
		</li>
      </ul>
    </div>
  </div>
<script>
$(document).ready(function($){
	jQuery("#searchUsers").keyup(function () {
	    var filter = jQuery(this).val();
	    jQuery(".search-result ul li").each(function () {
		    if(jQuery(this).attr('id')!='header'){
		        if (jQuery(this).text().search(new RegExp(filter, "i")) < 0) {
		        	jQuery(this).hide();
		        } else {
		            jQuery(this).show()
		        }
	        }
	    });
	});
});
</script>
<noscript></noscript>  
