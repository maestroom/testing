<?php
// User Form
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\models\Options;
use app\models\User;
use app\components\IsataskFormFlag;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use app\assets\ManageUserAccessAsset;
ManageUserAccessAsset::register($this);

	//echo "<pre>",print_r($model); die;
?>
<div class="row">
    <div class="panel-custom-radios">
        <div class="custom-full-width">
            <div class="row">
                <fieldset>
                    <legend class="sr-only">Auto-Inherit Section</legend>
                    <div class="col-sm-4 form-group">
                        <input type="radio" <?php if ($model->usr_inherent_teams == 0) echo 'checked'; ?> name="usr_inherent_teams" id="usr_inherent_no_teams1" aria-setsize="3" aria-posinset="1" value="0" />
                        <label for="usr_inherent_no_teams1">Auto-Inherit No Teams</label></div>
                    <div class="col-sm-4 form-group">
                        <input type="radio" <?php if ($model->usr_inherent_teams == 1) echo 'checked'; ?> name="usr_inherent_teams" id="usr_inherent_no_teams2" aria-setsize="3" aria-posinset="2" value="1" />
                        <label for="usr_inherent_no_teams2">Auto-Inherit Locations in Selected Teams</label>
                    </div>
                    <div class="col-sm-4 form-group">
                        <input type="radio" <?php if ($model->usr_inherent_teams == 2) echo 'checked'; ?> name="usr_inherent_teams" id="usr_inherent_no_teams3" aria-setsize="3" aria-posinset="3" value="2" />
                        <label for="usr_inherent_no_teams3">Auto-Inherit All New Teams</label>
                    </div>
                </fieldset>    
            </div> 
        </div>
    </div>
</div>

<div class="user-access-myteam">
    <a href="#" id="btnSelectAllTeam">Select all</a> - <a href="#" id="btnDeselectAllTeam">Deselect all</a> 
    <div id="tree4" class="tree-class"></div>
    <textarea name="teamLocs" id="teamLocsToInput" style="visibility: hidden; height: 0px; margin: 0px; padding: 0px;"><?= json_encode($selectedteamloc); ?></textarea>
</div>


<?php /*?>
<div class="col-xs-12 input-field form-group">
    <div class="col-sm-2 form-group" style="padding-top:8px;"><p>Select Team(s)</p></div>
    <div class="col-sm-9 form-group">
        <?php
        echo Select2::widget([
            'name' => 'useraccessTeams',            
            'data' => $teamNames,
            'showToggleAll' => false,
            'options' => ['placeholder' => 'Select Team(s)',
                'title' => 'Select Team(s)',
                'class' => 'form-control',
                'id' => 'useraccessTeams',
                'multiple' => true,
            ],
            'pluginOptions' => [
                'allowClear' => true,
            ],
            'pluginEvents' => [
                "select2:select" => "function() { teamChangeEvent(); }",
                //"select2:selecting" => "function() { teamChangeEvent(); }",
                "select2:unselect" => "function() { teamChangeEvent(); }",
            ]
        ]);
        ?>
        <input type="hidden" name="selected_locations" class="selected_teams_locations" value="">
    </div>
</div>
<div class="col-sm-12 bulk-team-locs user-permissions-box">
    <div class="col-sm-6">        
        <div class=""><label>Multi-select Team Locs to Apply Permissions</label></div>      
        <div class="my-label">
            <span>Select All/None</span>
            <div class="pull-right"><input type="checkbox" id="chk-team-apply" name="chk_team_apply" value="1" title="select Team Locations apply permision" />
                <label for="chk-team-apply"><span class="sr-only">select Team Locations apply permission</span></label>
            </div>   
        </div>
        <ul class="user-manage-selector" style="top:50px;">
            <li>
                <div class="header-filter clear custom-full-width" id="header">
                   <input type="text" id="filterFromTeamLocs" title="Filter Team Location" placeholder="Filter List"/>
                   <span title="Clear" class="clear_text" data-idname="filterFromTeamLocs">&times;</span>
                </div>
                <div class="filter-content">
                    <ul class="pull-left fromTeams"></ul>
                </div>
            </li>
        </ul>

    </div>
    <div class="col-sm-1">
        <div class="section-arrow-fixed text-center"><a href="javascript:void(0);" class="link-arrow-main" id="get_selected_teams"><i class="glyphicon glyphicon-chevron-right text-primary fa-2x"></i><span class="sr-only">Move Selected Teams</span></a></div>
        <div class="section-arrow-fixed text-center"><a href="javascript:void(0);" class="link-arrow-main" id="remove_selected_teams"><i class="glyphicon glyphicon-chevron-left text-primary fa-2x"></i><span class="sr-only">Remove Selected Teams</span></a></div>
    </div>
    <div class="col-sm-6">
        <div class=""><label>Applied Permissions</label></div>
         <div class="my-label"><span>Select All/None</span>
         <div class="pull-right"><input type="checkbox" id="chk-team-applied" name="chk_team" value="1" title="select Team Locations applied permision" />
             <label for="chk-team-applied"><span class="sr-only">select Team Locations applied permission</span></label>
         </div></div>
         <ul class="user-manage-selector" style="top:50px;">
            <li>
                <div class="header-filter clear custom-full-width" id="header">
                   <input type="text" id="filterFromPostTeamLocs" title="Filter Team Location" placeholder="Filter List"/>
                   <span title="Clear" class="clear_text" data-idname="filterFromPostTeamLocs">&times;</span>
                </div>
                <div class="filter-content">
            <ul class="pull-left teamDataToPost">
                <?php
                    if (!empty($projectsecurity)) {
                        $dbTeamData = [];
                        foreach ($projectsecurity as $single_security) {
                            if ($single_security['client_case_id'] == 0 && $single_security['client_id'] == 0) {
                             $dbKey = $single_security['team_id'] . $single_security['team_loc'];
                            ?>
                            <li class="clear teams_li_to custom-full-width T_<?= $single_security['team_id'] ?>_L_<?= $single_security['team_loc'] ?>" data-team_id="<?= $single_security['team_id'] ?>" data-team_loc="<?= $single_security['team_loc'] ?>"><a href="javascript:void(0)"><?= $single_security['TeamName'] ?> - <?= $single_security['TeamLocationName'] ?></a>
                            <input name="teamLocations[]" type="hidden" value="<?= $single_security['team_id'] ?>,<?= $single_security['team_loc'] ?>"></li>
                            <input type="hidden" name="db_data[<?= $dbKey ?>]" value="<?= $single_security['id'] ?>">
                        <?php
                        }
                    }
                    if (!empty($dbTeamData)) {
                ?>
                    <input type="hidden" name="db_data" value="<?= json_encode($dbTeamData) ?>">
                <?php
                }
}
?>
                    </ul>
                </div>
            </li>
        </ul>       
    </div>
</div>
<?php */?>
<script>
    $('#user-tabs input').customInput();
   /* Apply permission */
   $('#chk-team-apply').click(function(){
	   if($('#chk-team-apply').is(":checked"))
		   $('.teams_li_from').addClass('active');
	   else 
		   $('.teams_li_from').removeClass('active');
   });      
                        
   /* Appliend Permission */                     
   $('#chk-team-applied').click(function(){
	   if($('#chk-team-applied').is(":checked"))
		   $('.teams_li_to').addClass('active');
	   else 
		   $('.teams_li_to').removeClass('active');
   }); 


   var treeData = <?= json_encode($teamList); ?>;
    
    $(function(){
        $("#tree4").dynatree({
			checkbox: true,
			selectMode: 3,
			children: treeData,
			onSelect: function(select, node) {
                var clientcaseAr = [];
				// Get a list of all selected nodes, and convert to a key array:
				var selKeys = $.map(node.tree.getSelectedNodes(), function(node){
                    if(node.childList===null)
					    return node.data.key.toString();
                });
                //clientcaseAr.push(selKeys);
                //console.log(JSON.stringify(selKeys));

                $('#teamLocsToInput').val(JSON.stringify(selKeys));
				/*$("#echoSelection3").text(JSON.stringify(selKeys));

				// Get a list of all selected TOP nodes
				var selRootNodes = node.tree.getSelectedNodes(true);
				// ... and convert to a key array:
				var selRootKeys = $.map(selRootNodes, function(node){
					return node.data.key;
				});
				$("#echoSelectionRootKeys3").text(selRootKeys.join(", "));
				$("#echoSelectionRoots3").text(selRootNodes.join(", "));*/
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
			// The following options are only required, if we have more than one tree on one page:
            // initId: "treeData",
			// cookieId: "dynatree-Cb3",
			// idPrefix: "dynatree-Cb3-"
		});
    });    

    $("#btnToggleSelect").click(function(){
        $("#tree4").dynatree("getRoot").visit(function(node){
            node.toggleSelect();
        });
        return false;
    });

    $("#btnDeselectAllTeam").click(function(){
        $("#tree4").dynatree("getRoot").visit(function(node){
            node.select(false);
        });
        return false;
    });

    $("#btnSelectAllTeam").click(function(){
        $("#tree4").dynatree("getRoot").visit(function(node){
            node.select(true);
        });
        return false;
    });                      
</script>