<?php

use yii\helpers\Html;

\app\assets\CustomInputAsset::register($this);
/* @var $this yii\web\View */
/* @var $searchModel app\models\EvidenceTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
<style>
<!--
.mycontainer .content {
    display: none;
    padding : 5px;
}
.mycontainer .myheader a {
    cursor: pointer;
}
-->
</style>
<div class="mycontainer">
<fieldset>
    <legend class='sr-only'>Select Team to Share Summary Comment</legend>
    <?php 
        if(!empty($dropdown_widget)){
            foreach($dropdown_widget as $team_id=>$team_name) {
            if(is_array($team_name)){ ?> 
        <div class="myheader">
            <a href="javascript:void(0);" id="team_name_label_<?=$team_id?>"><?=key($team_name)?></a>
            <div class="pull-right header-checkbox">
                <input type="checkbox" aria-labelledby="team_name_label_<?= $team_id ?>" <?php if(in_array($team_id,$selected_teams)) {?> checked="checked" <?php }?> id="teams_<?=$team_id?>" onclick=" $('.child_<?=$team_id?>').prop('checked',this.checked); if(this.checked){ $('.child_<?=$team_id?>').each(function(){ $(this).next().addClass('checked');});}else{$('.child_<?=$team_id?>').each(function(){ $(this).next().removeClass('checked');});} " name="teams[<?=$team_id?>]" value="<?=$team_id; ?>" class="parent_<?=$team_id?>" /> 
                <label for="teams_<?=$team_id?>">&nbsp;</label> 
            </div>
        </div>
        <div class="content">
            <fieldset>
            <legend class='sr-only'>Select Team to Share Summary Comment, <?=key($team_name)?></legend>
            <ul id="search-result-<?=$team_id?>">
            <li class="search-box">
              <label for="searchUsers_<?=$team_id?>" style="display:none"><span class="sr-only"><?=key($team_name)?> Filter List</span></label>
              <input id="searchUsers_<?=$team_id?>" aria-label="<?=key($team_name)?>" data-teamid="<?=$team_id?>" class="searchUsers form-control" name="search_<?=$team_id?> " class="form-control" placeholder="Filter List" type="text">
            </li>
            <?php foreach ($team_name[key($team_name)] as $loc=>$loc_name){ ?>
                <li><span id="team_loc_label_<?=$team_id."_".$loc;?>"><?=$loc_name; ?></span>
                            <div class="pull-right "> 
                                    <input aria-labelledby="team_loc_label_<?=$team_id."_".$loc;?>" rel="<?=$loc_name; ?>" <?php if(in_array($team_id."_".$loc,$selected_teams_locs)) {?> checked="checked" <?php }?> onclick="$('.parent_<?=$team_id?>').prop('checked',this.checked);if(this.checked){$('.parent_<?=$team_id?>').next().addClass('checked');}else{if($('.child_<?=$team_id?>:checked').length == 0){ $('.parent_<?=$team_id?>').next().removeClass('checked');}}" data-name="<?=key($team_name).' - '.$loc_name;?>" data-teamloc="<?=$team_id."_".$loc;?>" id="chk_<?=$team_id."_".$loc;?>" type="checkbox" class="child_<?=$team_id?> service_checkbox chk_<?=$team_id."_".$loc?>"  name="locs[<?=$team_id?>][<?=$loc?>]" value="<?=$loc?>">
                                    <label for="chk_<?=$team_id."_".$loc;?>">&nbsp;</label>
                            </div>
                    </li>
            <?php }?>    
            </ul>
            </fieldset>
        </div>  
    <?php } } } ?>
</fieldset>
</div>
<script>
$(function() {
	$('input').customInput(); 
    jQuery(".searchUsers").keyup(function () {
        var filter  = jQuery(this).val();
        var team_id = jQuery(this).data('teamid');
	    jQuery("#search-result-"+team_id+" li").each(function () {
            console.log(this);
		    if(jQuery(this).attr('class')!='search-box'){
		        if (jQuery(this).text().search(new RegExp(filter, "i")) < 0) {
		        	jQuery(this).hide();
		        } else {
		            jQuery(this).show()
		        }
	        }
	    });
	});  
});

$('.myheader').on('click',function(){
	if($(this).hasClass('myheader-selected-tab')){
		$(this).removeClass('myheader-selected-tab');
	} else {
		$(this).addClass('myheader-selected-tab');
	}	
});

$(".myheader a").click(function () {
    $header = $(this).parent();
    $content = $header.next();
    $content.slideToggle(500, function () {
        $header.text(function () {
            //change text based on condition
            //return $content.is(":visible") ? "Collapse" : "Expand";
        });
    });

});
</script>
<noscript></noscript>
