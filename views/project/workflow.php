<?php 
use app\assets\ManageUserAccessAsset;
ManageUserAccessAsset::register($this);
use app\models\User; 
?>
<div id="wftabs">
     <fieldset>
            <legend class="sr-only">Add Template / Tasks to Workflow</legend>
                    <ul>
                        <li><a href="#tabs-wftemplates">Workflow Templates</a></li>
                        <?php if((new User)->checkAccess(4.021)){ ?>
                        <li><a href="#tabs-wftasks">Workflow Tasks</a></li>
                        <?php } ?>
                        <?php if(isset($flag) && $flag=='Saved') {}else if(isset($flag) && $flag=='Edit'){} else {?>
                        <li><a href="#tabs-loadprev">Previous Workflow</a></li>
                        <?php }?>
                        <li><a href="#tabs-filtertaskloc">Filter Task Locations</a></li>
                    </ul>
                    <fieldset>
                        <legend class="sr-only">Workflow Templates</legend>
                        <div id="tabs-wftemplates">
                        <div id="wftemplates-tree" class="tree-class"></div>
                        <textarea name="temp_service_task" id="temp_service_task" style="visibility: hidden; height: 0px; margin: 0px; padding: 0px; display:none;"></textarea>
                        <?php 
                            /*if(!empty($serviceTaskTemplate_data)){ $i=0; 
                                foreach($serviceTaskTemplate_data as $val) { ?>
                            <?php if(isset($serviceTaskTemplate_servicedata[$val['id']])){ ?>
                                <div id="main_workflow_temp_<?php echo $val['id']?>" class="add-workflow-div pt-1 pb-1 pl-1">
                                        <div class="custom-checkbox">
                                            <input type="checkbox" value="<?php echo $val['id']; ?>" name="temp_service_task[]" id="wftemplate_<?php echo $val['id'];?>" onclick="$('.workflowtempchild_<?php echo $val['id']; ?>').attr('checked',this.checked);"/> 
                                            <label for="wftemplate_<?php echo $val['id'];?>" class="chkbox-global-design"><?php echo $val['temp_name'];?></label>
                                    </div>
                                    <div style="display: none;" id="template_<?php echo $val['id']?>">
                                        <?php if(isset($serviceTaskTemplate_servicedata[$val['id']])){ ?>
                                            <ul>
                                                <?php foreach ($serviceTaskTemplate_servicedata[$val['id']] as $servicelist){ ?>
                                                    <li id="<?= $servicelist['servicetask_id'].'_'.$servicelist['team_loc']?>">
                                                        <span id="servicename_<?= $servicelist['servicetask_id'].'_'.$servicelist['team_loc']?>"><?php echo $servicelist['service_task']; ?></span>
                                                        <div class="pull-right ">
                                                            <div class="custom-checkbox">
                                                                <label for="temp_<?=$i?>"><span class="sr-only"><?php echo $val['temp_name'];?></span></label>
                                                                <input type="checkbox" id="temp_<?=$i?>" style=" margin: 0px!important;" value="<?php echo $servicelist['servicetask_id'] ?>" rel="<?php echo $servicelist['servicetask_id']?>" data-service="<?php echo $servicelist['service_task']; ?>" data-teamservice="<?php echo $servicelist['service_name']; if(isset($servicelist['team_loc']) && $servicelist['team_loc'] > 0) {echo ' - ' . $servicelist['team_location_name'];}?>" data-loc=<?php echo $servicelist['team_loc']?> name="Template_service_tasks[]" class=" workflowtempchild_<?php echo $val['id']?> workflowchild_<?php echo $servicelist['teamservice_id'] . $servicelist['team_loc']; ?> mychild service_checkbox">
                                                                <input type="hidden" name="ServiceteamLoc[<?php echo $servicelist['id'] ?>][]" class="sloc_<?php echo $servicelist['servicetask_id'] ?>" id="stl_<?php echo $servicelist['servicetask_id'] ?>" value="<?php echo $servicelist['team_loc']; ?>"/>
                                                            </div>
                                                        </div>
                                                    </li>
                                                <?php $i++; } ?>
                                            </ul>
                                        <?php }?>
                                    </div>
                            </div>
                        <?php }
                        }} */?>	
                    </div>
                    </fieldset>
		<?php if((new User)->checkAccess(4.021)){ ?>
            <fieldset>
                <legend class="sr-only">Workflow Tasks</legend>
		<div id="tabs-wftasks">
            <div id="wftasks-tree" class="tree-class"></div>
            <textarea name="wftasks_service_task" id="wftasks_service_task" style="visibility: hidden; height: 0px; margin: 0px; padding: 0px;display:none;"></textarea>
            	<?php /*if(!empty($teamservice_locations)){
                        foreach ($teamservice_locations as $key => $value) {
                           foreach ($teamservice_locations[$key] as $tlkey => $data) { 
                        ?>
                        <div class="myheader">
                            <a href="javascript:void(0);" id="team_service_<?=$key?>">
                                <?= $teamserviceName[$key] ?>
                                <?php if(isset($teamLocation[$tlkey])) {echo ' - ' . $teamLocation[$tlkey];} ?>
                            </a>
                            <div class="pull-right header-checkbox">
                                <input type="hidden" name="checkboxparentid" class= "checkparenthid" value="<?php echo $key; ?>">
                                <input type="checkbox" onclick="checkChildWorkflow('<?php echo $key .'_'. $tlkey; ?>');" value="<?php echo $key . $tlkey; ?>" id="Service_tasks_<?=$key .'_'. $tlkey?>" name="Service_tasks[]" class="workflowparent_<?php echo $key .'_'. $tlkey ?>" aria-label="" />
                                <label for="Service_tasks_<?=$key .'_'. $tlkey?>"><span class="sr-only">Select all Service tasks of Teamservice <?=$teamserviceName[$key].' and location '.$teamLocation[$tlkey]?></span></label>
                            </div>
                         </div>
                         <?php if (isset($teamservice_locations[$key][$tlkey]) && !empty($teamservice_locations[$key][$tlkey])) {?>
                            <div class="content" style="padding: 0px;">
                                <ul>
                                 <?php foreach ($teamservice_locations[$key][$tlkey] as $service_list) { ?>
                                        <li id="<?= $service_list['id'].'_'.$tlkey ?>">                                            
                                            <div class="pt-1 pb-1 pl-1">
                                                <div class="custom-checkbox w-100">
                                                <label for="Service_tasks_<?=$service_list['id'].'_'.$key .'_'. $tlkey?>" class="chkbox-global-design"><?php echo $service_list['service_task']; ?></label>
                                                <input type="checkbox" style=" margin: 0px!important;" onclick="checkParentWorkflow('<?php echo $key .'_'. $tlkey; ?>');" value="<?php echo $service_list['id'] ?>" id="Service_tasks_<?=$service_list['id'].'_'.$key .'_'. $tlkey?>" rel="<?php echo $service_list['id']?>" data-service="<?php echo $service_list['service_task']; ?>" data-teamservice="<?php echo $teamserviceName[$key]; if(isset($teamLocation[$tlkey])) {echo ' - ' . $teamLocation[$tlkey];}?>" data-loc=<?php echo $tlkey?> name="Service_tasks[]" data-teamservice_id="<?php echo $key ?>" class="workflowchildservicetask_<?php echo $key .'_'. $tlkey ?> mychild service_checkbox" aria-label="Select Service task <?php echo $service_list['service_task']; ?> of Teamservice <?=$teamserviceName[$key].' and location '.$teamLocation[$tlkey]?>" />
                                                </div>
                                                <input type="hidden" name="ServiceteamLoc[<?php echo $service_list['id'] ?>][]" class="sloc_<?php echo $service_list['id'] ?>" id="stl_<?php echo $service_list['id'] ?>" value="<?php echo $tlkey; ?>"/>
                                            </div>
                                        </li>
					<?php }?>
                      </ul>                                                      
					</div>
					<?php }?>
			 <?php }}} */?>
		</div>
    </fieldset>
		<?php } ?>
        
        <fieldset>
            <legend class="sr-only">Previous Workflow</legend>
            <div id="tabs-loadprev" style="overflow:inherit">

            </div>
        </fieldset>
        <fieldset>
            <legend class="sr-only">Filter Task Locations</legend>
            <div id="tabs-filtertaskloc" style="overflow:inherit">
                <div id="location-tree" class="tree-class" style="overflow: auto;height: 100%;"><div>
                <textarea name="filterloc" id="filterloc" style="visibility: hidden; height: 0px; margin: 0px; padding: 0px;"><?php if(isset($optionModel->set_loc) && $optionModel->set_loc!="") { echo $optionModel->set_loc;} ?></textarea>
            </div>
        </fieldset>

     </fieldset>
</div>
<script>
var treeData = <?= json_encode($sttemplateList); ?>;
var treeDataTask = <?= json_encode($stasklateList); ?>;
var treeDataTaskLocation = <?= json_encode($locList); ?>;
$(function(){
	$("#wftemplates-tree").dynatree({
		checkbox: true,
		selectMode: 3,
		children: treeData,
		onSelect: function(select, node) {
			var clientcaseAr = [];
			var selKeys = $.map(node.tree.getSelectedNodes(), function(node){
				if(node.childList===null)
					return node.data.key.toString();
			});
			$('#temp_service_task').val(JSON.stringify(selKeys));
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
    $("#wftasks-tree").dynatree({
		checkbox: true,
		selectMode: 3,
		children: treeDataTask,
		onSelect: function(select, node) {
			var clientcaseAr = [];
			var selKeys = $.map(node.tree.getSelectedNodes(), function(node){
				if(node.childList===null)
					return node.data.key.toString();
			});
			$('#wftasks_service_task').val(JSON.stringify(selKeys));
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
    $("#location-tree").dynatree({
		checkbox: true,
		selectMode: 3,
		children: treeDataTaskLocation,
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

    <?php if(isset($optionModel->set_loc) && $optionModel->set_loc!="") { if($filtersavedlocnames!="" && !empty($filtersavedlocnames)) {?>
       setTimeout(function() {
            if($('#add-project-workflow').parent().find('.ui-dialog-buttonpane').find('#filter-task-loc')) {
                $('#add-project-workflow').parent().find('.ui-dialog-buttonpane').find('#filter-task-loc').remove();
            }
            $('#add-project-workflow').parent().find('.ui-dialog-buttonpane').prepend("<div id='filter-task-loc' class='pull-left text-danger' style='margin-top: 10px;vertical-align: middle;'>Filter Task Locations: <?php echo implode(", ",$filtersavedlocnames);?></div>"); 
        },100);
    <?php } }?>
});
function checkParentWorkflow(parent_id){
	if($(".workflowchildservicetask_"+parent_id).is(':checked')){
		$(".workflowparent_"+parent_id).prop('checked',true);
		$(".workflowparent_"+parent_id).next('label').addClass('checked');
	}else{
		if($('.workflowchildservicetask_'+parent_id+':checked').length == 0){  
	 		 $('.workflowparent_'+parent_id).prop('checked',false);
	 		 $('.workflowparent_'+parent_id).next('label').removeClass('checked');
	 	}
	}
}
function checkChildWorkflow(parent_id){
	if($(".workflowparent_"+parent_id).is(':checked')){
		$('.workflowchildservicetask_'+parent_id).each(function(){
			$(this).prop('checked',true);
			var label = $('label[for="'+$(this).attr('id')+'"]');
			setTimeout(function(){label.addClass('checked')},100);
		});
	}else{
		$('.workflowchildservicetask_'+parent_id).each(function(){
			$(this).prop('checked',false);
			var label = $('label[for="'+$(this).attr('id')+'"]');
			setTimeout(function(){label.removeClass('checked')},100);
		});
	}
}
$("#wftabs .myheader a").click(function () {
    $header = $(this).parent();
    $content = $header.next();
    $content.slideToggle(500, function () {
	$header.text(function () {
	  //  change text based on condition
	  //return $content.is(":visible") ? "Collapse" : "Expand";
	});
    });	
});
/**
 * Header span
 */
$('#wftabs .myheader').on('click',function(){
	if($(this).hasClass('myheader-selected-tab')){
		$(this).removeClass('myheader-selected-tab');
	}else{
		$(this).addClass('myheader-selected-tab');
	}	
});
$( "#wftabs" ).tabs({
    beforeActivate: function (event, ui) {
	    if(ui.newPanel.selector=='#tabs-wftasks') {
            $("#wftemplates-tree").dynatree("getRoot").visit(function(node){
                node.select(false);
            });
            $('#temp_service_task').val(null);
            $("#tabs-loadprev").html(null);
        } else if(ui.newPanel.selector=='#tabs-loadprev') { 
            $("#wftemplates-tree").dynatree("getRoot").visit(function(node){
                node.select(false);
            });
            $('#temp_service_task').val(null);
             $("#wftasks-tree").dynatree("getRoot").visit(function(node){
                node.select(false);
            });
            $('#wftasks_service_task').val(null);
            var case_id = jQuery('#case_id').val();
            LoadPreviousNew(case_id);
        } else if(ui.newPanel.selector=='#tabs-filtertaskloc') { 
            $("#wftemplates-tree").dynatree("getRoot").visit(function(node){
                node.select(false);
            });
            $('#temp_service_task').val(null);
             $("#wftasks-tree").dynatree("getRoot").visit(function(node){
                node.select(false);
            });
            $('#wftasks_service_task').val(null);
            $("#tabs-loadprev").html(null);
        } else {
            $("#wftasks-tree").dynatree("getRoot").visit(function(node){
                node.select(false);
            });
            $('#wftasks_service_task').val(null);
            $("#tabs-loadprev").html(null);
        }
    },
    beforeLoad: function( event, ui ) {
      ui.jqXHR.error(function() {
        ui.panel.html(
          "Error loading current tab." );
      });
    }
  });
</script>
<noscript></noscript>
