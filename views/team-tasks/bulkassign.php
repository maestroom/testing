<?php
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $searchModel app\models\search\EvidenceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>
<form id="frmbulkassigntasks" name="bulkassigntasks" action="" method="post">
                                    <fieldset>
                    <legend class="sr-only">Bulk Assign Tasks</legend>
				<div class="custom-full-width">
				<input type="radio" aria-setsize="2" aria-posinset="1" name="bulkassigntask" class="bulkassigntask" id="bulkassignselectedtask" value="selectedtask"><label for="bulkassignselectedtask">Selected <span id="assignselectedtask">0</span> Tasks in Grid</label>
                </div>
                <div class="clr"></div>
                <div class="custom-full-width">
                    <input type="radio" name="bulkassigntask" aria-setsize="2" aria-posinset="2" class="bulkassigntask" value="alltask" id="bulkassignalltask" checked="checked"/><label for="bulkassignalltask">All Filtered <span id="assignalltask"><?php echo $totalCount; ?></span> Tasks in Grid</label>
                </div>
                </fieldset> 
                <div class="clr">&nbsp;</div>
                <div class="clsusersdata">
                <?php if(!empty($data)) { ?>
					<div class="asign-dropdown">
					  <ul class="asign-menu">
						<li class="search-box">
						  <label for="searchUsers" style="display:none">&nbsp;</label>
						  <input id="searchUsers" type="text" name="search1" class="form-control" placeholder="Filter List">
						  <label for="services" style="display:none">&nbsp;</label>
<!--						  <input id="services" value="<?=$services?>" type="hidden">-->
						  <label for="taskunits" style="display:none">&nbsp;</label>
<!--						  <input id="taskunits" value="<?=$taskunit_id?>" type="hidden">-->
						  
						</li>
						<li class="search-result">
							<ul>
								<?php if(isset($data['team_members'])){ ?>
									<li data-id="header"><strong>Team Members <span class="pull-right"></span></strong>
									<ul>
										<?php foreach ($data['team_members'] as $user){ ?>
											<li class='user_li' data-id="<?=$user->id ?>"><?=Html::a(ucwords($user->usr_first_name." ".$user->usr_lastname),null,['href'=>'javascript:void(0)','onclick'=>'$(this).parent().toggleClass("active");']);?></li>
										<?php } ?>	
										
									</ul>
									
								<?php } ?>	
								<?php if(isset($data['case_members'])){ ?>
									<li data-id="header"><strong>Case Members <span class="pull-right"></span></strong>
									<ul>
										<?php foreach ($data['case_members'] as $user){ ?>
											<li class='user_li' data-id="<?=$user->id ?>"><?=Html::a(ucwords($user->usr_first_name." ".$user->usr_lastname),null,['href'=>'javascript:void(0)','onclick'=>'$(this).parent().toggleClass("active");']);?></li>
										<?php } ?>	
										
									</ul>
									
								<?php } ?>	
								<li data-id="header"><strong>Both Case Managers & Team Members <span class="pull-right"></span></strong>
									<ul>
										<?php foreach ($data['both_members'] as $user){ ?>
											<li class='user_li' data-id="<?=$user->id ?>"><?=Html::a(ucwords($user->usr_first_name." ".$user->usr_lastname),null,['href'=>'javascript:void(0)','onclick'=>'$(this).parent().toggleClass("active");']);?></li>
										<?php } ?>	
										
									</ul>
							</ul>
						</li>
                	</ul>
                	</div>
                	<?php } else {
                		echo "No User have permission to this Team and Location"; 
                	}
                	?>                </div>
</form>
<script>
jQuery("#searchUsers").keyup(function () {
	var filter = jQuery(this).val();
	jQuery(".search-result ul li").each(function () {
		if(jQuery(this).data('id')!='header'){
			if (jQuery(this).text().search(new RegExp(filter, "i")) < 0) {
				jQuery(this).hide();
			} else {
				jQuery(this).show()
			}
		}
	});
});
</script>