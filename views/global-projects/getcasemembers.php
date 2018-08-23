<?php 
use app\assets\ManageUserAccessAsset;
ManageUserAccessAsset::register($this);
if(!empty($casemanagerList)){
    /*foreach ($list_casemanager as $casemgr_id=>$lcm) {?><li><input type="checkbox"  name='casemanagers[]' id="casemanager_<?=$casemgr_id;?>" value="<?=$casemgr_id;?>" class="casemanager_projectsubmitted" aria-label="<?=$lcm?>"><label for="casemanager_<?=$casemgr_id;?>" class="casemanager_projectsubmitted"><?=$lcm?></label></li><?php }}else{?>No Record Found...<?php }*/


?>
<div>
    <div id="tree7" class="tree-class"></div>
    <textarea name="casemanagers" id="casemanagers" style="visibility:hidden;height: 0px;margin: 0px;padding: 0px;" ></textarea>
</div>
<script>
var treeData = <?= json_encode($casemanagerList); ?>;
    
    $(function(){
        $("#tree7").dynatree({
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
                $('#casemanagers').val(newTemp);
                
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
