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

//	echo "<pre>",print_r($mycases); die;
?>
<div class="row">
    <div class="panel-custom-radios">
        <div class="custom-full-width ">           
            <div class="row">
                <fieldset>
                    <legend class="sr-only">Auto-Inherit Section</legend>
                <div class="col-sm-4 form-group">
                    <input type="radio" checked="true" name="bulk_inherent_cases" id="bulk_no_client_cases" value="0"  aria-posinset="1" aria-setsize="3" />
                    <label for="bulk_no_client_cases">Auto-Inherit No Client Cases</label>
                </div>
                <div class="col-sm-4 form-group">
                    <input type="radio" name="bulk_inherent_cases" id="bulk_inherent_new_cases" value="1"  aria-posinset="2" aria-setsize="3" />
                    <label for="bulk_inherent_new_cases">Auto-Inherit All New Cases within Client(s)</label>
                </div>
                <div class="col-sm-4 form-group">
                    <input type="radio" name="bulk_inherent_cases" id="bulk_inherent_client_cases" value="2"  aria-posinset="3" aria-setsize="3" />
                    <label for="bulk_inherent_client_cases">Auto-Inherit All Client Cases</label>
                </div>
            </fieldset>
            </div> 
        </div>
    </div>
</div>
<div class="user-access-mycasebulk" >
    <a href="#" id="btnSelectAllbulk">Select all</a> - <a href="#" id="btnDeselectAllbulk">Deselect all</a> 
    <div id="tree33" class="tree-class"></div>
    <textarea name="clientCases" id="clientCasesToInputbulk" style="visibility:hidden;height: 0px;margin: 0px;padding: 0px;" ></textarea>
</div>

<?php /*?>
<div class="col-xs-12 input-field form-group">
    <div class="col-sm-2 form-group" style="padding-top:8px;"><p>Select Client(s)</p></div>
    <div class="col-sm-9 form-group">
        <?php
        echo Select2::widget([
            'name' => 'useraccessclients',
            'data' => $clientList,
            'showToggleAll' => false,
            'options' => [
                'placeholder' => 'Select Client(s)',
                'title' => 'Select Client(s)',
                'class' => 'form-control',
                'id' => 'bulkuseraccessclients',
                'multiple' => true,
            ],
            'pluginOptions' => [
                'allowClear' => true,
            ],
            'pluginEvents' => [
                "select2:select" => "function() { BulkclientChangeEvent(); }",
                "select2:unselect" => "function() { BulkclientChangeEvent(); }",
            ]
        ]);
        ?>
        <input type="hidden" name="selected_clientcases" class="selected_teams_locations" value="">
    </div>
</div>
<div class="col-sm-12 bulk-client-cases user-permissions-box">
    <div class="col-sm-6">
        <div><label>My Cases: Multi-select Client Cases to Apply Permissions</label></div>
        <span>Select All/None</span>
        <div class="pull-right"><input type="checkbox" id="chk-case-client-apply" name="chk_case_client_apply" value="1" title="select all client case apply permision" />
             <label for="chk-case-client-apply"><span class="sr-only">Select All or None</span></label>
        </div>
        <ul class="user-manage-selector" style="top: 45px;">
            <li>
                <div class="header-filter clear custom-full-width" id="header">
                    <input type="text" class="" id="bulkFilterClientCases" title="Filter Client Case" placeholder="Filter List"/>
                    <span title="Clear" class="clear_text" data-idname="bulkFilterClientCases">&times;</span>
                </div>
                <div class="filter-content">
                    <ul class="pull-left fromClientCases clients_case_left_container"></ul>
                </div>
            </li>
        </ul>        
    </div>
    <div class="col-sm-1">
        <div class="section-arrow-fixed text-center"><a href="javascript:void(0);" class="link-arrow-main" id="move_selected_client_cases"><i class="glyphicon glyphicon-chevron-right text-primary fa-2x"></i><span class="sr-only">move selected client cases</span></a></div>
        <div class="section-arrow-fixed text-center"><a href="javascript:void(0);" class="link-arrow-main" id="remove_selected_client_cases"><i class="glyphicon glyphicon-chevron-left text-primary fa-2x"></i><span class="sr-only">Remove selected client cases</span></a></div>
    </div>
    <div class="col-sm-6">
        <div class=""><label>My Cases: Applied Permissions</label></div>
        <div class="my-label"><span>Select All/None</span>
        <div class="pull-right"><input type="checkbox" id="chk-case-client-applied" name="chk_case_client" value="1" title="select all client case applied permision" />
            <label for="chk-case-client-applied"><span class="sr-only">Select All or None</span></label>
        </div></div>
        <ul id="table_field_container" class="pull-left custom-inline-block-width user-manage-selector" style="top: 45px;">
            <li>
                <div class="clear custom-full-width header-filter" id="header">
                    <input type="text" class="" id="filterclientCaseDataToPost" title="Filter Client Case" placeholder="Filter List"/>
                    <span title="Clear" class="clear_text" data-idname="filterclientCaseDataToPost">&times;</span>
                </div>
                <div class="filter-content">
                    <ul class="pull-left clientCaseDataToPost clients_case_left_container">                        
                    </ul>
                </div>
            </li>
        </ul>        
    </div>
</div>
<?php */?>
<script>
	/* Apply permission */
	$('#chk-case-client-apply').click(function(){
		   if($('#chk-case-client-apply').is(":checked")) {
			   $('.client_case_li_from').addClass('active');
		   } else {
			   $('.client_case_li_from').removeClass('active');
		   }
	});      
	                     
	/* Appliend Permission */                     
	$('#chk-case-client-applied').click(function(){
		   if($('#chk-case-client-applied').is(":checked")) {
			   $('.client_case_li_to').addClass('active');
		   } else {
			   $('.client_case_li_to').removeClass('active');
		   }
	});                

	/* Bulk client change event */
    function BulkclientChangeEvent() {
        var selValues = $("#bulkuseraccessclients").val();
        var allFlag = '0';
        if (selValues.indexOf('All') != '-1') {
            allFlag = 'All';
        }
        $.ajax({
            url: baseUrl + 'user/get-cleint-case-list',
            cache: false,
            type: 'post',
            data: $('form#User_access').serialize(),
            beforeSend: function () {
                showLoader();
            },
            success: function (response) {
                hideLoader();
                $('#User_access #user_access_first .fromClientCases').html(response);
                if (allFlag == 'All') {
                    $("#bulkuseraccessclients").select2("val", "");
                }
            },
            error: function () {
                console.log('internal server error');
            }
        });
    }
    $(document).ready(function () {
//   $(document).on('click','#move_selected_client_cases',function(){
//      alert('nelson'); 
//   });
        $(document).on('click', '#User_access #user_access_first #move_selected_client_cases', function () {
            var activeteamlocs = $('#User_access .fromClientCases').find('li.active');
            activeteamlocs.each(function () {
                var client_case_id = $(this).data('client_case_id');
                var client_id = $(this).data('client_id');
                var input_cal = '<input name="clientCasesWithCleint[]" type="hidden" value="' + client_id + ',' + client_case_id + '">';

                if (!$('ul.clientCaseDataToPost').children('li').hasClass('C_' + client_id + '_CC_' + client_case_id)) {
                    $('ul.clientCaseDataToPost').append('<li class="clear clietcases_li custom-full-width C_' + client_id + '_CC_' + client_case_id + '" data-client_id="' + client_id + '" data-client_case_id="' + client_case_id + '">' + $(this).html() + input_cal + '</li>');
                }
            });
            $('ul.fromClientCases li.active').remove();
        });
        $(document).on('click', '#User_access #user_access_first #remove_selected_client_cases', function () {
            var activeteamlocs = $('#User_access .clientCaseDataToPost').find('li.active');
            activeteamlocs.each(function () {
                var client_case_id = $(this).data('client_case_id');
                var client_id = $(this).data('client_id');
                $('#User_access ul.fromClientCases').append('<li class="clear clietcases_li custom-full-width" data-client_id="' + client_id + '" data-client_case_id="' + client_case_id + '"><a href="javascript:void(0)">' + $(this).children('a').html() + '</a></li>');
            });
            $('#User_access ul.clientCaseDataToPost li.active').remove();
        });
        $(document).on('keyup', '#User_access #filterClientCases', function () {
            var filter = $(this).val();
            $("#User_access ul.fromClientCases li").each(function () {
                if ($(this).attr('id') != 'header') {
                    if ($(this).find('a').html().search(new RegExp(filter, "i")) < 0) {
                        $(this).hide();
                    } else {
                        $(this).show();
                    }
                }
            });
        });
        $(document).on('keyup', '#User_access #filterclientCaseDataToPost', function () {
            var filter = $(this).val();
            $("#User_access ul.clientCaseDataToPost li").each(function () {
                if ($(this).attr('id') != 'header') {
                    if ($(this).find('a').html().search(new RegExp(filter, "i")) < 0) {
                        $(this).hide();
                    } else {
                        $(this).show();
                    }
                }
            });
        });
    });


    var treeData = <?= json_encode($clientList); ?>;
    
    $(function(){
        $("#tree33").dynatree({
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

                $('#clientCasesToInputbulk').val(JSON.stringify(selKeys));
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
        $("#tree33").dynatree("getRoot").visit(function(node){
            node.toggleSelect();
        });
        return false;
    });

    $("#btnDeselectAllbulk").click(function(){
        $("#tree33").dynatree("getRoot").visit(function(node){
            node.select(false);
        });
        return false;
    });

    $("#btnSelectAllbulk").click(function(){
        $("#tree33").dynatree("getRoot").visit(function(node){
            node.select(true);
        });
        return false;
    }); 
</script>
