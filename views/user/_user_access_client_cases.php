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

//	echo "<pre>",print_r($mycases); die;
?>
<div class="row">
    <div class="panel-custom-radios">
        <div class="custom-full-width ">           
            <div class="row">
                <fieldset>
                    <legend class="sr-only">Auto-Inherit Section</legend>
                        <div class="col-sm-4 form-group">
                            <input type="radio" <?php if ($model->usr_inherent_cases == 0) echo 'checked'; ?> name="usr_inherent_cases" id="usr_inherent_no_client_cases" aria-setsize="3" aria-posinset="1" value="0" />
                            <label for="usr_inherent_no_client_cases">Auto-Inherit No Client Cases</label>
                        </div>
                        <div class="col-sm-4 form-group">
                            <input type="radio" <?php if ($model->usr_inherent_cases == 1) echo 'checked'; ?> name="usr_inherent_cases" id="usr_inherent_new_cases" value="1" aria-setsize="3" aria-posinset="2" />
                            <label for="usr_inherent_new_cases">Auto-Inherit Cases in Selected Clients</label>
                        </div>
                        <div class="col-sm-4 form-group">
                            <input type="radio" <?php if ($model->usr_inherent_cases == 2) echo 'checked'; ?> name="usr_inherent_cases" id="usr_inherent_client_cases" value="2" aria-setsize="3" aria-posinset="3" />
                            <label for="usr_inherent_client_cases">Auto-Inherit All Client Cases</label>
                        </div>
                </fieldset>
            </div> 
        </div>
    </div>
</div>

<div class="user-access-mycase">
    <a href="#" id="btnSelectAll">Select all</a> - <a href="#" id="btnDeselectAll">Deselect all</a> 
    <div id="tree3" class="tree-class"></div>
    <textarea name="clientCases" id="clientCasesToInput" style="visibility:hidden;height: 0px;margin: 0px;padding: 0px;" ><?= json_encode($selectedCases); ?></textarea>
</div>

<script>
    $('#user-tabs input').customInput();
   /* Apply permission */
   /*$('#chk-case-client-apply').click(function(){
	   if($('#chk-case-client-apply').is(":checked")) {
		   $('.client_case_li_from').addClass('active');
	   } else {
		   $('.client_case_li_from').removeClass('active');
	   }
   });*/      
                        
   /* Appliend Permission */                     
   /*$('#chk-case-client-applied').click(function(){
	   if($('#chk-case-client-applied').is(":checked")) {
		   $('.client_case_li_to').addClass('active');
	   } else {
		   $('.client_case_li_to').removeClass('active');
	   }
   });*/

   /*var treeData = [
		{title: "Folder", isFolder: true, key: "id3",
			children: [
				{title: "Sub-item 3.1",
					children: [
						{title: "Sub-item 3.1.1", key: "id3.1.1" },
						{title: "Sub-item 3.1.2", key: "id3.1.2" }
					]
				},
				{title: "Sub-item 3.2",
					children: [
						{title: "Sub-item 3.2.1", key: "id3.2.1" },
						{title: "Sub-item 3.2.2", key: "id3.2.2" }
					]
				}
			]
		},
	];*/

    /*var treeData = [
        {title:"APPLE & APP","isFolder":true,key:13,
            children: [
                {title:"Apple & app case",key:"35"},
                {title:"TEST auto inherit cases in client",key:"36"}
            ]
        },
        {title:"APPLE & STR","isFolder":true,key:12,
            children:[
                {title:"teststests",key:"30"}
            ]
        },
        {title:"APPLErewt & GRAPESuir board","isFolder":true,key:11,
            children:[
                {title:"AP-112254 & Testing",key:"28"}
            ]
        },
        {title:"cadmin","isFolder":true,key:10,
            children:[
                {title:"AP-11223",key:"27"},
                {title:"isatask",key:"32"},
                {title:"new cadmin case",key:"37"},
                {title:"NewCase",key:"29"},
                {title:"Perception",key:"33"},
                {title:"test test",key:"34"}
            ]
        },
        {title:"New Client","isFolder":true,key:15,
            children:[
                {title:"New Case",key:"38"}
            ]
        },
        {title:"settings","isFolder":true,key:14}
    ];*/

    var treeData = <?= json_encode($clientList); ?>;
    
    $(function(){
        $("#tree3").dynatree({
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

                $('#clientCasesToInput').val(JSON.stringify(selKeys));
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
        $("#tree3").dynatree("getRoot").visit(function(node){
            node.toggleSelect();
        });
        return false;
    });

    $("#btnDeselectAll").click(function(){
        $("#tree3").dynatree("getRoot").visit(function(node){
            node.select(false);
        });
        return false;
    });

    $("#btnSelectAll").click(function(){
        $("#tree3").dynatree("getRoot").visit(function(node){
            node.select(true);
        });
        return false;
    });                 
</script>