<?php 
use app\assets\ManageUserAccessAsset;
ManageUserAccessAsset::register($this);
?>
<div class="form-group field-user-ad_group">
<div class="row input-field">
<div class="col-md-3"><label for="user-ad_grp" class="form_label">Group Users</label></div>
<div class="col-md-7">
<?php if(!empty($userList)) {?>
<a href="#" id="btnSelectAll">Select all</a> - <a href="#" id="btnDeselectAll">Deselect all</a> 
<div id="grp_users-tree" class="tree-class grp"></div>
<textarea name="grp_users" id="grp_users" style="visibility: hidden; height: 0px; margin: 0px; padding: 0px;"></textarea>
                  <?php /*?><ul id="userList">
                  	<li id="" style="border-bottom: 1px solid rgb(221, 221, 221); padding-top: 5px; width: 200px ! important; padding-bottom: 5px;"> 
	                      <span>
	                      <input type="checkbox" class="grp_all" onclick="$('.grp').prop('checked',this.checked);">                    
	                      Select All
	                      </span> 
	                  </li>
	                <?php foreach ($ldap_result as $uid=>$val) { ?>
	                    <li id="adg_<?php echo $uid?>"> 
	                      <span>
	                       <input type="checkbox" class="grp" name="grp_users[]" value="<?php echo $uid?>">                    
	                      <?php echo $val?>
	                      </span> 
	                       
                      </li>
                      <?php } ?>
                  </ul>
				  <?php */?>
<script>
var treeData = <?= json_encode($userList); ?>;
$(function(){
	$("#grp_users-tree").dynatree({
		checkbox: true,
		selectMode: 3,
		children: treeData,
		onSelect: function(select, node) {
			var clientcaseAr = [];
			var selKeys = $.map(node.tree.getSelectedNodes(), function(node){
				if(node.childList===null)
					return node.data.key.toString();
			});
			$('#grp_users').val(JSON.stringify(selKeys));
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
$("#btnDeselectAll").click(function(){
	$("#grp_users-tree").dynatree("getRoot").visit(function(node){
		node.select(false);
	});
	return false;
});

$("#btnSelectAll").click(function(){
	$("#grp_users-tree").dynatree("getRoot").visit(function(node){
		node.select(true);
	});
	return false;
});  
</script>
<?php } else { ?>
<span class="padlef10">No users were retrieved for selected group or user has already been imported</span>
<?php } ?>
<div class="help-block"></div>
</div>    
</div>
</div>  
<script>
function removeAdgUser(uid)
{
	removed_users=$("#removed_users").val();
	if(removed_users=="")
		$("#removed_users").val(uid);
	else
		$("#removed_users").val(removed_users+","+uid);
	
	$("#adg_"+uid).remove();
}
</script>
<noscript></noscript>