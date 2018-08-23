<?php 
use app\assets\ManageUserAccessAsset;
ManageUserAccessAsset::register($this);
/*use yii\helpers\Html;
if(!empty($list_casemanager)){foreach ($list_casemanager as $lcm) {?><li><input type="checkbox"  name='requestor[]' id="requestor_<?=Html::encode($lcm);?>" value="<?=Html::encode($lcm);?>" class="casemanager_projectrequestor" aria-label="<?=Html::encode($lcm)?>"><label for="requestor_<?=Html::encode($lcm);?>" class="casemanager_projectrequestor"><?=Html::encode($lcm)?></label></li><?php }}else{?>No Record Found...<?php }
*/
if(!empty($requestedList)){
?>
<div>
    <div id="tree8" class="tree-class"></div>
    <textarea name="requestor" id="requestor" style="visibility:hidden;height: 0px;margin: 0px;padding: 0px;" ></textarea>
</div>
<script>
var treeData = <?= json_encode($requestedList); ?>;
    
    $(function(){
        $("#tree8").dynatree({
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
                $('#requestor').val(newTemp);
                
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