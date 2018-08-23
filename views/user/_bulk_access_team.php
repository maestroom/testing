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

//echo "<pre>",print_r($model); die;
?>
<div class="row">
    <div class="panel-custom-radios">
        <div class="custom-full-width ">           
            <div class="row">
                <fieldset>
                    <legend class="sr-only">Auto-Inherit Section</legend>
                <div class="col-sm-4 form-group">
                    <input type="radio" checked="true" name="bulk_usr_inherent_teams" aria-posinset="1" aria-setsize="3" id="bulk_usr_inherent_teams1" value="0" />
                    <label for="bulk_usr_inherent_teams1">Auto-Inherit No Teams</label>
                </div>
                <div class="col-sm-4 form-group">
                    <input type="radio" name="bulk_usr_inherent_teams" id="bulk_usr_inherent_teams2" value="1" aria-posinset="2" aria-setsize="3" />
                    <label for="bulk_usr_inherent_teams2">Auto-Inherit All New Locations within Team(s)</label>
                </div>
                <div class="col-sm-4 form-group">
                    <input type="radio" name="bulk_usr_inherent_teams" id="bulk_usr_inherent_teams3" value="2" aria-posinset="3" aria-setsize="3" />
                    <label for="bulk_usr_inherent_teams3">Auto-Inherit All New Teams</label>
                </div>
            </fieldset>
            </div> 
        </div>
    </div>
</div>

<div class="user-access-myteambulk">
    <a href="#" id="btnSelectAllTeambulk">Select all</a> - <a href="#" id="btnDeselectAllTeambulk">Deselect all</a> 
    <div id="tree44" class="tree-class"></div>
    <textarea name="teamLocs" id="teamLocsToInputbulk" style="visibility: hidden; height: 0px; margin: 0px; padding: 0px;"><?= json_encode($selectedteamloc); ?></textarea>
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
                'id' => 'bulkuseraccessTeams',
                'multiple' => true,
            ],
            'pluginOptions' => [
                'allowClear' => true,
            ],
            'pluginEvents' => [
                "select2:select" => "function() { bulkTeamChangeEvent(); }",
                //"select2:selecting" => "function() { teamChangeEvent(); }",
                "select2:unselect" => "function() { bulkTeamChangeEvent(); }",
            ]
        ]);
        ?>
        <input type="hidden" name="selected_locations" class="selected_teams_locations" value="">
    </div>
</div>
<div class="col-sm-12 bulk-team-locs user-permissions-box">
    <div class="col-sm-6">        
        <div class=""><label>My Teams: Multi-select Team Locations to Apply Permissions</label></div>
        <span>Select All/None</span>
        <div class="pull-right"><input type="checkbox" id="chk-team-apply" name="chk_team_apply" value="1" title="select Team Locations apply permision" />
        	<label for="chk-team-apply"><span class="sr-only">Select All or None</span></label>
        </div>  
        <ul class="user-manage-selector" style="top: 45px;">
            <li>
                <div class="header-filter clear custom-full-width" id="header">
                    <input type="text" class="" id="filterFromTeamLocs" title="Filter Team Location" placeholder="Filter List"/>
                    <span title="Clear" class="clear_text" data-idname="filterFromTeamLocs">&times;</span>
                </div>
                <div class="filter-content">
                    <ul class="pull-left fromTeams"></ul>
                </div>
            </li>
        </ul>        
    </div>
    <div class="col-sm-1">
        <div class="section-arrow-fixed text-center"><a href="javascript:void(0);" class="link-arrow-main" id="get_selected_teams"><i class="glyphicon glyphicon-chevron-right text-primary fa-2x"></i><span class="sr-only">Move selected client cases</span></a></div>
        <div class="section-arrow-fixed text-center"><a href="javascript:void(0);" class="link-arrow-main" id="remove_selected_teams"><i class="glyphicon glyphicon-chevron-left text-primary fa-2x"></i><span class="sr-only">Remove selected client cases</span></a></div>
    </div>
    <div class="col-sm-6">
        <div class=""><label>My Teams: Applied Permissions</label></div>
        <div class="my-label"><span>Select All/None</span>
        <div class="pull-right"><input type="checkbox" id="chk-team-applied" name="chk_team" value="1" title="select Team Locations applied permision" />
        	<label for="chk-team-applied"><span class="sr-only">Select All or None</span></label>
        </div></div>
        <ul id="table_field_container" class="pull-left custom-inline-block-width user-manage-selector" style="top: 45px;">
            <li>
                <div class="clear custom-full-width header-filter " id="header">
                    <input type="text" class="" id="filterFromPostTeamLocs" title="Filter Team Location" placeholder="Filter List"/>
                    <span title="Clear" class="clear_text" data-idname="filterFromPostTeamLocs">&times;</span>
                </div>
                <div class="filter-content">
                    <ul class="pull-left teamDataToPost">                        
                    </ul>
                </div>
            </li>
        </ul>        
    </div>
</div>
<?php */?>
<script>
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

var $selector = $('#bulkuseraccessTeams');
$selector.select2().on('select2:open', function() {    
    //$selector.select2('positionDropdown', true);
//    $('#user_access_second .select2-container--krajee.select2-container--open').css('top','153px');
//    $('#user_access_second .select2-container--krajee.select2-container--open').css('left','171px');
});    

function bulkTeamChangeEvent(){   
    var selValues = $("#bulkuseraccessTeams").val();
    var allFlag = '0';        
    if(selValues.indexOf('All') != '-1'){
        allFlag = 'All';         
    }    
    $.ajax({
        url    : baseUrl +'user/get-team-location-list',
        cache: false,
        type   : 'post',
        data   : $('form#User_access').serialize(),
        beforeSend : function()    {
                showLoader();			
        },
        success: function (response) {
                hideLoader();
                $('#User_access .fromTeams').html(response);
                if(allFlag == 'All'){
                   $("#User_access #bulkuseraccessTeams").select2("val", ""); 
                } 
                $(document).on('keyup','#User_access #filterFromTeamLocs',function () {    
                         var filter = $(this).val();                        
                         $("#User_access ul.fromTeams li").each(function () {
                                 if ($(this).attr('id') != 'header') {
                                         if ($(this).find('a').html().search(new RegExp(filter, "i")) < 0) {
                                                 $(this).hide();
                                         } else {
                                                 $(this).show();
                                         }
                                 }
                         });
                 });
        },
        error  : function (){
                console.log('internal server error');
        }
    });
}
var treeData = <?= json_encode($teamNames); ?>;
    
    $(function(){
        $("#tree44").dynatree({
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

                $('#teamLocsToInputbulk').val(JSON.stringify(selKeys));
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
        $("#tree44").dynatree("getRoot").visit(function(node){
            node.toggleSelect();
        });
        return false;
    });

    $("#btnDeselectAllTeambulk").click(function(){
        $("#tree44").dynatree("getRoot").visit(function(node){
            node.select(false);
        });
        return false;
    });

    $("#btnSelectAllTeambulk").click(function(){
        $("#tree44").dynatree("getRoot").visit(function(node){
            node.select(true);
        });
        return false;
    }); 
</script>