<?php /*if(!empty($list_clients)){
		foreach ($list_clients as $key=>$lc) {?>							
	<li><input type="checkbox"   name='cleints[]' id="client_<?=$key;?>" value="<?=$key;?>" onclick='if(this.checked)$("<?php echo "#case_".$key?>").show();else $("<?php echo "#case_".$key?>").hide();' class="client_case"> <label for="client_<?=$key;?>" class="client_case"><?php  echo $lc?></label> 
            <?php if(!empty($list_cases[$key])){?>
                <fieldset><legend class="sr-only">By client, <?= $lc ?></legend><ul class="filter_hide_all"  style="display: none;" id='<?php echo'case_'.$key?>'>
                                    <?php foreach ($list_cases[$key] as $k=>$lcc){?>
                                                    <li class='by_clients' ><input type="checkbox" name='cleintscase[]' id="client_case_<?= $k;?>" value="<?= $k;?>" class="by_client_case" aria-label="<?=$lcc?>"> <label for="client_case_<?= $k;?>" class="by_client_case"><?=$lcc?></label></li>
                                    <?php } ?>
                </ul></fieldset>
            <?php }?>
	</li>
<?php }}else{ ?>No Record Found...<?php } ?>
<script>$('input').customInput();</script>
<noscript></noscript>
<?php */
use app\assets\ManageUserAccessAsset;
ManageUserAccessAsset::register($this);
if(!empty($clientList)){
?>
<div>
    <div id="tree3" class="tree-class"></div>
    <textarea name="clientCases" id="clientCasesToInput" style="visibility:hidden;height: 0px;margin: 0px;padding: 0px;" ></textarea>
    
</div>
<script>
var treeData = <?= json_encode($clientList); ?>;
    
    $(function(){
        $("#tree3").dynatree({
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