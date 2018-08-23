<?php 
use app\assets\ManageUserAccessAsset;
ManageUserAccessAsset::register($this);
/*if(!empty($list_casecreatedbyuser)){foreach ($list_casecreatedbyuser as $casecrtmgr_id=>$lccm) {?><li><input type="checkbox"  name='casecreatedmanagers[]' id="casecreatedmanager_<?=$casecrtmgr_id;?>" value="<?=$casecrtmgr_id;?>" class="casecreatemanager" aria-label="<?=$lccm?>"><label for="casecreatedmanager_<?=$casecrtmgr_id;?>" class="casecreatemanager"><?=$lccm?></label></li><?php }}else{?>No Record Found...<?php }*/
if(!empty($casecreatedList)){
?>
<div>
    <div id="tree6" class="tree-class"></div>
    <textarea name="casecreatedmanagers" id="casecreatedmanagers" style="visibility:hidden;height: 0px;margin: 0px;padding: 0px;" ></textarea>
</div>
<script>
var treeData = <?= json_encode($casecreatedList); ?>;
    
    $(function(){
        $("#tree6").dynatree({
			checkbox: true,
			selectMode: 3,
			children: treeData,
			onSelect: function(select, node) {
                var clientcaseAr = [];
				var selKeys = $.map(node.tree.getSelectedNodes(), function(node){
                    if(node.childList===null)
					    return node.data.key.toString();
                });
                //console.log(selKeys);
                mystring = JSON.stringify(selKeys);
                newTemp = mystring.replace(/"/g, "'");
                $('#casecreatedmanagers').val(newTemp);
                
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