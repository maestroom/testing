<?php 
use app\assets\ManageUserAccessAsset;
ManageUserAccessAsset::register($this);
if(!empty($locations)){?>
<fieldset>
<div id="location-tree" class="tree-class"></div>
<textarea name="filterloc" id="filterloc" style="visibility: hidden; height: 0px; margin: 0px; padding: 0px;"></textarea>
    <?php /*?><legend class="sr-only">Filter Templates / Tasks by Team Location</legend>
    <ul class="custom-inline-block-width">
            <?php foreach ($locations as $id=>$name){?>
            <li>
                <label for="filterloc_<?=$id?>"><span class="sr-only"><?=$name?></span></label>
                <input name="filterloc_<?=$id?>" type="checkbox" id="filterloc_<?=$id?>" value="<?=$id?>" <?php if(isset($filter_ids) && $filter_ids!="" && in_array($id,explode(",",$filter_ids))){?>checked="checked"<?php }?> class="filter_locs"  rel="filterloc_name_<?=$id?>" aria-label="<?=$name?>">
                <em class="fa fa-file-o" aria-hidden="true"></em>
                <label class="form_label"><?=$name?></label>
            </li>	
            <?php }?>
    </ul><?php */?>
</fieldset>
<script>
var treeData = <?= json_encode($locList); ?>;
$(function(){
	$("#location-tree").dynatree({
		checkbox: true,
		selectMode: 3,
		children: treeData,
		onSelect: function(select, node) {
			var clientcaseAr = [];
			var selKeys = $.map(node.tree.getSelectedNodes(), function(node){
				if(node.childList===null)
					return node.data.key.toString();
			});
			$('#filterloc').val(JSON.stringify(selKeys));
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
<?php }?>
