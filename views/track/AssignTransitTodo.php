<?php
use yii\helpers\Html;

?>
  <div class="select-location-popup">
    <div class="asign-dropdown">
      <ul class="asign-menu">
        <li class="search-box">
			<label for="searchUsers" style="display:none">&nbsp;</label>
          <input id="searchUsers" type="text" name="search1" class="form-control" placeholder="Filter Users...">
        </li>
        <?php if($todo_data->assigned!=0){?>
        <li>
          <strong><span>Currently Assigned To: <?=$todo_data->assignedUser->usr_first_name." ".$todo_data->assignedUser->usr_lastname; ?></span></strong>
        </li>
        <?php }?>
		<li class="search-result">
		  <ul>
		  	<?php if(isset($data['team_members'])){?>
			<li id="header"><strong>Team Members <span class="pull-right"><?php //=count($data['team_members'])?></span></strong>
			  <ul>
			  	<?php foreach ($data['team_members'] as $user){ if($todo_data->assigned!=0 && $todo_data->assigned==$user->id){ continue;}?>
				<li class='user_li' data-id="<?=$user->id ?>"><?=Html::a(ucwords($user->usr_first_name." ".$user->usr_lastname),null,['href'=>'javascript:void(0)','onclick'=>'$(".user_li").removeClass("active");$(this).parent().addClass("active")']);?></li>
				<?php }?>
			  </ul>
			</li>
			<?php }?>
			<?php if(isset($data['case_members'])){?>
			<li id="header"><strong>Case Members <span class="pull-right"><?php //=count($data['case_members'])?></span></strong>
			  <ul>
			  	<?php foreach ($data['case_members'] as $user){ if($todo_data->assigned!=0 && $todo_data->assigned==$user->id){ continue;}?>
				<li class='user_li' data-id="<?=$user->id ?>"><?=Html::a(ucwords($user->usr_first_name." ".$user->usr_lastname),null,['href'=>'javascript:void(0)','onclick'=>'$(".user_li").removeClass("active");$(this).parent().addClass("active")']);?></li>
				<?php }?>
			  </ul>
			</li>
			<?php }?>
			<li id="header"><strong>Both Case Managers & Team Members <span class="pull-right"><?php //=count($data['both_members'])?></span></strong>
			  <ul>
			  <?php if(!empty($data['both_members'])){foreach ($data['both_members'] as $user){ if($todo_data->assigned!=0 && $todo_data->assigned==$user->id){ continue;}?>
				<li class='user_li' data-id="<?=$user->id ?>"><?=Html::a(ucwords($user->usr_first_name." ".$user->usr_lastname),null,['href'=>'javascript:void(0)','onclick'=>'$(".user_li").removeClass("active");$(this).parent().addClass("active")']);?></li>
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
