<?php 
use app\assets\ManageUserAccessAsset;
ManageUserAccessAsset::register($this);
if(!empty($clientList)){
?>
<div>
    <div id="tree-media_client_case" class="tree-class"></div>
    <textarea name="clientCases" id="clientCasesToInput" style="visibility:hidden;height: 0px;margin: 0px;padding: 0px;" ></textarea>
    
</div>
<script>
var treeData = <?= json_encode($clientList); ?>;
    
    $(function(){
        $("#tree-media_client_case").dynatree({
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
                $('#clientCasesToInput').val(newTemp);
                
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
<?php }else{ ?>No Record Found...<?php } ?>