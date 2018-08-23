<?php 
	/*if(!empty($list_teams))
	{
		foreach ($list_teams as $teamid=>$lt) 
		{
			foreach ($lt as $keys => $lts) 
			{
			?>
				<li>
					<input type="checkbox"   name='teams[]' id="team_<?=$teamid;?>" value="<?=$teamid;?>" class="teams" onclick='display_block("<?= $teamid ?>");' aria-label="<?= $keys; ?>">
					<label for="team_<?=$teamid;?>" class="teams"><?= $keys; ?></label>
						<fieldset><legend class="sr-only">By Team, <?= $keys ?></legend><ul class="filter_hide_all" style="display:none;" id='<?php echo 'team_loc_'.$teamid ?>'>
							<?php foreach($lts as $innerkey => $val){ ?>
								<li class='by_teamloc' ><input type="checkbox" name='teamloc[]' id="teamloc_<?=$teamid;?>_<?= $val['team_loc']; ?>" value="<?= $val['team_loc']; ?>" class="by_client_case" aria-label="<?php echo $val['team_location_name'] ?>"> <label for="teamloc_<?=$teamid;?>_<?= $val['team_loc']; ?>" class="by_teamloc"><?php echo $val['team_location_name'] ?></label></li>
							<?php }	?>
                                                    </ul></fieldset>	
				</li><?php 
			}
		}
	}else{ ?>
		No Record Found...
<?php } ?>
<script>$('input').customInput();
function display_block(teamid){
	var chk = $('#team_'+teamid).is(':checked');
	if(chk) 
		$("#team_loc_"+teamid).show(); 
	else 
		$("#team_loc_"+teamid).hide();
}
</script>
<noscript></noscript>
<?php */
use app\assets\ManageUserAccessAsset;
ManageUserAccessAsset::register($this);
if(!empty($teamList)){
?>
<div>
    <div id="tree4" class="tree-class"></div>
    <textarea name="teamLocs" id="teamLocsToInput" style="visibility: hidden; height: 0px; margin: 0px; padding: 0px;"></textarea>
</div>
<script>
var treeData = <?= json_encode($teamList); ?>;
$(function(){
	$("#tree4").dynatree({
		checkbox: true,
		selectMode: 3,
		children: treeData,
		onSelect: function(select, node) {
			var clientcaseAr = [];
			var selKeys = $.map(node.tree.getSelectedNodes(), function(node){
				if(node.childList===null)
					return node.data.key.toString();
			});
			mystring = JSON.stringify(selKeys);
            newTemp = mystring.replace(/"/g, "'");
			$('#teamLocsToInput').val(newTemp);
		},
		onDblClick: function(node, event) {
			node.toggleSelect();
		},
		onKeydown: function(node, event) {
			if( event.which == 32 ) {
				node.toggleSelect();
				return false;
			}
		},
	});
});
</script>
<?php } else {?>No Record Found...<?php }?>