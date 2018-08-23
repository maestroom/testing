<?php /*if(!empty($list_teammanager)){foreach ($list_teammanager as $teammgr_id=>$ltm) {?>
<li><input type="checkbox"  name='teammanagers[]' id="teammanager_<?=$teammgr_id;?>" value="<?=$teammgr_id;?>" class="teammanager" aria-label="<?=$ltm?>"><label for="teammanager_<?=$teammgr_id;?>" class="teammanager"><?=$ltm?></label></li>
<?php }}else{?>No Record Found...<?php }*/
use app\assets\ManageUserAccessAsset;
ManageUserAccessAsset::register($this);
if(!empty($teammemList)){
?>
<div>
    <div id="tree5" class="tree-class"></div>
    <textarea name="teammanagers" id="teammanagers" style="visibility: hidden; height: 0px; margin: 0px; padding: 0px;"></textarea>
</div>
<script>
var treeData = <?= json_encode($teammemList); ?>;
$(function(){
	$("#tree5").dynatree({
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
			$('#teammanagers').val(newTemp);
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
<?php }else{?>No Record Found...<?php }?>