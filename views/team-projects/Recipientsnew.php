<?php 
use app\assets\ManageUserAccessAsset;
ManageUserAccessAsset::register($this);
use app\models\User; 
?>
<div id="wftabs">
     <fieldset>
            <legend class="sr-only">Add Case Manager Role or Team</legend>
			<ul>
				<li><a href="#tabs-casemanager">Case Manager Roles</a></li>
				<li><a href="#tabs-team">Teams</a></li>
			</ul>
			<fieldset>
				<legend class="sr-only">Case Manager Role</legend>
				<div id="tabs-casemanager">
					<a href="#" id="btnSelectAllCase">Select all</a> - <a href="#" id="btnDeselectAllCase">Deselect all</a> 
					<div class='pull-right'><a href="#" id="btnSelectedCaseEmail">Email Selected</a> - <a href="#" id="btnAllCaseEmail">Email all</a></div>
					<div>&nbsp;&nbsp;</div>
					<div id="casemanager-tree" class="tree-class"></div>
					<textarea name="temp_casemanager" id="temp_casemanager" style="visibility: hidden; height: 0px; margin: 0px; padding: 0px; display:none;"></textarea>
					<input type='hidden' id="temp_email_casemanager_envolpe" style="visibility: hidden; height: 0px; margin: 0px; padding: 0px; display:none;"/>
					<input type='hidden' id="temp_email_casemanager_envolpeo" style="visibility: hidden; height: 0px; margin: 0px; padding: 0px; display:none;"/>
				</div>
			</fieldset>
			<fieldset>
            	<legend class="sr-only">Team</legend>
				<div id="tabs-team">
					<a href="#" id="btnSelectAllTeam">Select all</a> - <a href="#" id="btnDeselectAllTeam">Deselect all</a> 
					<div class='pull-right'><a href="#" id="btnSelecteTeamEmail">Email Selected</a> - <a href="#" id="btnAllTeamEmail">Email all</a></div>
					<div>&nbsp;&nbsp;</div>
            		<div id="team-tree" class="tree-class"></div>
            		<textarea name="temp_team" id="temp_team" style="visibility: hidden; height: 0px; margin: 0px; padding: 0px;display:none;"></textarea>
					<input type='hidden' id="temp_email_team_envolpe" style="visibility: hidden; height: 0px; margin: 0px; padding: 0px; display:none;"/>
					<input type='hidden' id="temp_email_team_envolpeo" style="visibility: hidden; height: 0px; margin: 0px; padding: 0px; display:none;"/>
				</div>
			</fieldset>
	</fieldset>
</div>	

<script>
var treeData = <?= json_encode($caseUserList); ?>;
var treeteamData = <?= json_encode($teamUserList); ?>;
$(function(){
	$("#casemanager-tree").dynatree({
		checkbox: true,
		selectMode: 3,
		children: treeData,
		noLink:true,
		onSelect: function(select, node) {
			if(node.childList === null){
				var span_id='caserole_'+node.data.role_id+'_'+node.data.id;
				if(node.isSelected()){
					$('#'+span_id).removeClass('fa fa-envelope');
					$('#'+span_id).addClass("fa fa-envelope-o");
				}else{
					$('#'+span_id).removeClass("fa fa-envelope-o");
					$('#'+span_id).removeClass('fa fa-envelope');
				}
			}else{
				node.expand(true);
				var i;
				for(i=0; i < node.childList.length; i++){
					var span_id='caserole_'+node.childList[i].data.role_id+'_'+node.childList[i].data.id;
					if(node.childList[i].isSelected()){
						$('#'+span_id).removeClass('fa fa-envelope');
						$('#'+span_id).addClass("fa fa-envelope-o");
					}else{
						$('#'+span_id).removeClass("fa fa-envelope-o");
						$('#'+span_id).removeClass('fa fa-envelope');
					}
				}
			}
			var clientcaseAr = [];
			var selKeys = $.map(node.tree.getSelectedNodes(), function(node){
				if(node.childList===null)
					return node.data.key.toString();
			});
			$('#temp_casemanager').val(JSON.stringify(selKeys));
		},
		onQueryExpand: function(select, node) {
			var case_roles_users_env="";
			var case_roles_users_envo="";
			$("#casemanager-tree").dynatree("getRoot").visit(function(node) {
				if(node.isSelected() && node.data.isFolder==false) {
					var span_id='caserole_'+node.data.role_id+'_'+node.data.id;
					if($('#'+span_id).hasClass('fa-envelope')){
						if(case_roles_users_env=="")
							case_roles_users_env=span_id;
						else
							case_roles_users_env+=","+span_id;
					}
					if($('#'+span_id).hasClass('fa-envelope-o')){
						if(case_roles_users_envo=="")
							case_roles_users_envo=span_id;
						else
							case_roles_users_envo+=","+span_id;
					}
				}
			});
			$('#temp_email_casemanager_envolpe').val(case_roles_users_env);
			$('#temp_email_casemanager_envolpeo').val(case_roles_users_envo);
		},
		onExpand: function(flag, node) {
			var list_env=$('#temp_email_casemanager_envolpe').val();
			var list_envo=$('#temp_email_casemanager_envolpeo').val();
			var separator =  ",";
			var values = list_env.split(separator);
			var valueso = list_envo.split(separator);
			$("#casemanager-tree").dynatree("getRoot").visit(function(node) {
				if(node.isSelected() && node.data.isFolder==false) {
					var span_id='caserole_'+node.data.role_id+'_'+node.data.id;
					for(var i = 0 ; i < values.length ; i++) {
						if(values[i] == span_id) {
							$('#'+span_id).removeClass("fa fa-envelope-o");
							$('#'+span_id).removeClass("fa fa-envelope");
							$('#'+span_id).addClass('fa fa-envelope');
						}
					}
					for(var i = 0 ; i < valueso.length ; i++) {
						if(valueso[i] == span_id) {
							$('#'+span_id).removeClass("fa fa-envelope-o");
							$('#'+span_id).removeClass("fa fa-envelope");
							$('#'+span_id).addClass('fa fa-envelope-o');
						}
					}
				}else{
					if(!node.isSelected() && node.data.isFolder==false){
						var span_id='caserole_'+node.data.role_id+'_'+node.data.id;
						$('#'+span_id).removeClass("fa fa-envelope-o");
						$('#'+span_id).removeClass("fa fa-envelope");
					}
				}
			});
		},
		onDblClick: function(node, event) {
			//node.toggleSelect();
		},
		onKeydown: function(node, event) {
			if( event.which == 32 ) {
				node.toggleSelect();
				return false;
			}
		},
	});
	$("#team-tree").dynatree({
		checkbox: true,
		selectMode: 3,
		children: treeteamData,
		noLink:true,
		onSelect: function(select, node) {
			if(node.childList === null){
				var span_id='teammanager_'+node.data.team_id+'_'+node.data.id;
				if(node.isSelected()){
					$('#'+span_id).removeClass('fa fa-envelope');
					$('#'+span_id).addClass("fa fa-envelope-o");
				}else{
					$('#'+span_id).removeClass('fa fa-envelope');
					$('#'+span_id).removeClass("fa fa-envelope-o");	
				}
			}else{
				node.expand();
				var i;
				for(i=0; i < node.childList.length; i++){
					var span_id='teammanager_'+node.childList[i].data.team_id+'_'+node.childList[i].data.id;
					if(node.childList[i].isSelected()){
						$('#'+span_id).removeClass('fa fa-envelope');
						$('#'+span_id).addClass("fa fa-envelope-o");
					}else{
						$('#'+span_id).removeClass('fa fa-envelope');
						$('#'+span_id).removeClass("fa fa-envelope-o");
					}
				}
			}
			var clientcaseAr = [];
			var selKeys = $.map(node.tree.getSelectedNodes(), function(node){
				if(node.childList===null)
					return node.data.key.toString();
			});
			$('#temp_team').val(JSON.stringify(selKeys));
		},
		onQueryExpand: function(select, node) {
			var case_roles_users_env="";
			var case_roles_users_envo="";
			$("#team-tree").dynatree("getRoot").visit(function(node) {
				if(node.isSelected() && node.data.isFolder==false) {
					var span_id='teammanager_'+node.data.team_id+'_'+node.data.id;
					if($('#'+span_id).hasClass('fa-envelope')){
						if(case_roles_users_env=="")
							case_roles_users_env=span_id;
						else
							case_roles_users_env+=","+span_id;
					}
					if($('#'+span_id).hasClass('fa-envelope-o')){
						if(case_roles_users_envo=="")
							case_roles_users_envo=span_id;
						else
							case_roles_users_envo+=","+span_id;
					}
				}
			});
			$('#temp_email_team_envolpe').val(case_roles_users_env);
			$('#temp_email_team_envolpeo').val(case_roles_users_envo);
		},
		onExpand: function(flag, node) {
			var list_env=$('#temp_email_team_envolpe').val();
			var list_envo=$('#temp_email_team_envolpeo').val();
			var separator =  ",";
			var values = list_env.split(separator);
			var valueso = list_envo.split(separator);
			$("#team-tree").dynatree("getRoot").visit(function(node) {
				if(node.isSelected() && node.data.isFolder==false) {
					var span_id='teammanager_'+node.data.team_id+'_'+node.data.id;
					for(var i = 0 ; i < values.length ; i++) {
						if(values[i] == span_id) {
							$('#'+span_id).removeClass("fa fa-envelope-o");
							$('#'+span_id).removeClass("fa fa-envelope");
							$('#'+span_id).addClass('fa fa-envelope');
						}
					}
					for(var i = 0 ; i < valueso.length ; i++) {
						if(valueso[i] == span_id) {
							$('#'+span_id).removeClass("fa fa-envelope-o");
							$('#'+span_id).removeClass("fa fa-envelope");
							$('#'+span_id).addClass('fa fa-envelope-o');
						}
					}
				}else{
					if(!node.isSelected() && node.data.isFolder==false){
						var span_id='teammanager_'+node.data.team_id+'_'+node.data.id;
						$('#'+span_id).removeClass("fa fa-envelope-o");
						$('#'+span_id).removeClass("fa fa-envelope");
					}
				}
			});
		},
		onDblClick: function(node, event) {
			//node.toggleSelect();
		},
		onKeydown: function(node, event) {
			if( event.which == 32 ) {
				node.toggleSelect();
				return false;
			}
		},
	});
	$("#btnDeselectAllTeam").click(function(){
		$('#temp_email_team_envolpe').val(null);
		$('#temp_email_team_envolpeo').val(null);
        $("#team-tree").dynatree("getRoot").visit(function(node){
			var span_id='teammanager_'+node.data.team_id+'_'+node.data.id;
			$('#'+span_id).removeClass("fa fa-envelope-o");
			$('#'+span_id).removeClass('fa fa-envelope');
            node.select(false);
			node.expand(false);
        });
        return false;
    });
	$("#btnDeselectAllCase").click(function(){
		$('#temp_email_casemanager_envolpe').val(null);
		$('#temp_email_casemanager_envolpeo').val(null);
        $("#casemanager-tree").dynatree("getRoot").visit(function(node){
            var span_id='caserole_'+node.data.role_id+'_'+node.data.id;
			$('#'+span_id).removeClass("fa fa-envelope-o");
			$('#'+span_id).removeClass('fa fa-envelope');
			node.select(false);
			node.expand(false);
        });
		return false;
    });
    $("#btnSelectAllTeam").click(function(){
        $("#team-tree").dynatree("getRoot").visit(function(node){
			var span_id='teammanager_'+node.data.team_id+'_'+node.data.id;
			if(!$('#'+span_id).hasClass('fa-envelope')){
			$('#'+span_id).removeClass("fa fa-envelope-o");
			$('#'+span_id).removeClass('fa fa-envelope');
			$('#'+span_id).addClass("fa fa-envelope-o");
			}
            node.select(true);
		});
        return false;
    }); 
	$("#btnSelectAllCase").click(function(){
        $("#casemanager-tree").dynatree("getRoot").visit(function(node){
			var span_id='caserole_'+node.data.role_id+'_'+node.data.id;
			if(!$('#'+span_id).hasClass('fa-envelope')){
			$('#'+span_id).removeClass("fa fa-envelope-o");
			$('#'+span_id).removeClass('fa fa-envelope');
			$('#'+span_id).addClass("fa fa-envelope-o");
			}
            node.select(true);
        });
        return false;
    }); 
	$("#btnSelecteTeamEmail").click(function(){
			var case_roles_users="";
			$("#team-tree").dynatree("getRoot").visit(function(node) {	
					if(node.isSelected() && node.data.isFolder==true) {node.expand();}
					if(node.isSelected() && node.data.isFolder==false) {
						var span_id='teammanager_'+node.data.team_id+'_'+node.data.id;
						$('#'+span_id).removeClass("fa fa-envelope-o");
						$('#'+span_id).removeClass('fa fa-envelope');
						$('#'+span_id).addClass('fa fa-envelope');
						if(case_roles_users=="")
	  						case_roles_users=span_id;
	  					else
	  						case_roles_users+=","+span_id;
					}
			});
			if(case_roles_users == ''){
				alert("Please select a user to view the comment.");
			}
	});
	$("#btnSelectedCaseEmail").click(function(){
			var case_roles_users="";
			$("#casemanager-tree").dynatree("getRoot").visit(function(node) {
					if(node.isSelected() && node.data.isFolder==true) {node.expand();}
					if(node.isSelected() && node.data.isFolder==false) {
						var span_id='caserole_'+node.data.role_id+'_'+node.data.id;
						$('#'+span_id).removeClass("fa fa-envelope-o");
						$('#'+span_id).removeClass('fa fa-envelope');
						$('#'+span_id).addClass('fa fa-envelope');
						if(case_roles_users=="")
	  						case_roles_users=span_id;
	  					else
	  						case_roles_users+=","+span_id;
					}
			});
			if(case_roles_users == ''){
				alert("Please select a user to view the comment.");
			}
	});
	$("#btnAllTeamEmail").click(function(){
		var case_roles_users="";
		$("#team-tree").dynatree("getRoot").visit(function(node){
		node.select(true);
		});
		$("#team-tree").dynatree("getRoot").visit(function(node) {
			if(node.isSelected() && node.data.isFolder==true) {node.expand();}
			if(node.isSelected() && node.data.isFolder==false) {
				var span_id='teammanager_'+node.data.team_id+'_'+node.data.id;
				$('#'+span_id).removeClass("fa fa-envelope-o");
				$('#'+span_id).removeClass('fa fa-envelope');
				$('#'+span_id).addClass('fa fa-envelope');
				if(case_roles_users=="")
					case_roles_users=span_id;
				else
					case_roles_users+=","+span_id;
			}
		});
	});
	$("#btnAllCaseEmail").click(function(){
		var case_roles_users="";
		$("#casemanager-tree").dynatree("getRoot").visit(function(node){
		node.select(true);
		});
		$("#casemanager-tree").dynatree("getRoot").visit(function(node) {
			if(node.isSelected() && node.data.isFolder==true) {node.expand();}
			if(node.isSelected() && node.data.isFolder==false) {
				var span_id='caserole_'+node.data.role_id+'_'+node.data.id;
				$('#'+span_id).removeClass("fa fa-envelope-o");
				$('#'+span_id).removeClass('fa fa-envelope');
				$('#'+span_id).addClass('fa fa-envelope');
				if(case_roles_users=="")
					case_roles_users=span_id;
				else
					case_roles_users+=","+span_id;
			}
		});
	});
});
$( "#wftabs" ).tabs({
    beforeLoad: function( event, ui ) {
      ui.jqXHR.error(function() {
        ui.panel.html(
          "Error loading current tab." );
      });
    }
});
function caseemailids(obj){
	if($(obj).hasClass('fa-envelope-o')){
		$(obj).removeClass('fa fa-envelope-o');
		$(obj).addClass('fa fa-envelope');
	}else if($(obj).hasClass('fa-envelope')){
			$(obj).removeClass('fa fa-envelope');
			$(obj).addClass('fa fa-envelope-o');
	}
}
function teamemailids(obj){
	if($(obj).hasClass('fa-envelope-o')){
		$(obj).removeClass('fa fa-envelope-o');
		$(obj).addClass('fa fa-envelope');
	}else if($(obj).hasClass('fa-envelope')){
		$(obj).removeClass('fa fa-envelope');
		$(obj).addClass('fa fa-envelope-o');
	}
}
</script>