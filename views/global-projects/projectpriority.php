<?php 

if(!empty($priorityList)) {
	/*foreach ($priority_data as $stus_id=>$ls) {?>
		<li>
			<input type="checkbox"  name='taskpriority[]' id="taskpriority_<?=$stus_id;?>" value="<?=$ls;?>" class="taskpriority" aria-label="<?=$ls?>">
			<label for="taskpriority_<?=$stus_id;?>" class="taskpriority"> <?php  echo $ls ?></label>
		</li><?php 
	}*/
?>
<div>
    <div id="tree9" class="tree-class"></div>
    <textarea name="taskpriority" id="taskpriority" style="visibility:hidden;height: 0px;margin: 0px;padding: 0px;" ></textarea>
</div>
<script>
var treeData = <?= json_encode($priorityList); ?>;
    
    $(function(){
        $("#tree9").dynatree({
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
                $('#taskpriority').val(newTemp);
                
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
