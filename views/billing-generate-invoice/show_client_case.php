<?php
	use yii\helpers\Html;
	use yii\helpers\ArrayHelper;
	use yii\bootstrap\ActiveForm;
	use app\assets\ManageUserAccessAsset;
	ManageUserAccessAsset::register($this);
?>
<?php /*if(!empty($client_data_case)){
$i=1; foreach($client_data_case as $key => $clientcase) { ?>
<li>
	<?php 
			$checked=""; 
	?>
	<input type="checkbox" <?php echo $checked; ?> id="clientcases_<?php echo $key; ?>" name="clientcases[]" class="clientcases" value="<?php echo $key; ?>" aria-label="<?php echo $clientcase; ?>" />
	<label for="clientcases_<?php echo $key; ?>" class="clientcases"><?php echo $clientcase; ?></label>
</li>
<?php }*/ 

if(!empty($clientList)){?>
<div>
   <div id="bgitree" class="tree-class"></div>
   <textarea name="clientcases" id="clientCasesToInput" style="visibility:hidden;height: 0px;margin: 0px;padding: 0px;" ><?php if(!empty($selectedCases)){echo json_encode($selectedCases);}?></textarea>
</div>
<script>
var treeData = <?= json_encode($clientList); ?>;
    $(function(){
        $("#bgitree").dynatree({
			checkbox: true,
			selectMode: 3,
			children: treeData,
			onSelect: function(select, node) {
                var clientcaseAr = [];
				var selKeys = $.map(node.tree.getSelectedNodes(), function(node){
                    if(node.childList===null)
					    return node.data.key.toString();
                });
                $('#clientCasesToInput').val(JSON.stringify(selKeys));
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
<?php } else { ?>
NO
<?php }?>
							