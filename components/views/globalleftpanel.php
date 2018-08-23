<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\models\Role;
use kartik\widgets\Select2;
use kartik\daterange\DateRangePicker;
use app\assets\ManageUserAccessAsset;
ManageUserAccessAsset::register($this);

$controller = Yii::$app->controller->id;
$action = Yii::$app->controller->action->id;
$accordianIndex = 'dynamic_filter_acc';
$isFilter = false;

/* RoleId */
$role_info = $_SESSION['role'];
$role_type = explode(',',$role_info->role_type);
//$role_type = explode(',', Role::findOne(Yii::$app->user->identity->role_id)->role_type);

if (isset($resuest['filter_id']) && $controller == 'global-projects') {
    $isFilter = true;
}
$save = Yii::$app->request->get('saved');
if ($controller == 'global-projects' && $action == 'save-filter-grid') {
    $accordianIndex = 'get_saved_filter';
}
if ($controller == 'global-projects' && $action == 'index') {
    $accordianIndex = 'dynamic_filter_acc';
}
if ($controller == 'global-projects' && $action == 'filter-option') {
    $accordianIndex = 'filetr_options';
}
$list_status = array(0 => 'Not Started', 1 => 'Started', 3 => 'On Hold', 4 => 'Completed', 6 => 'Past Due', 7 => 'Due Today', 8 => 'Closed', '9' => 'Canceled');
$list_todostatus = array(7 => 'ToDo Assigned', 8 => 'ToDo Transitioned', 9 => 'ToDo Completed', 11 => 'ToDo Transferred', 13 => 'ToDo Started', 14 => 'ToDo Not Started');
$filter_id = Yii::$app->request->get('filter_id', 0);
?>
<div class="acordian-main">
    <div id="accordion-container">
        <h3 data-index = '0' class="get_index" title="Dynamic Filter" id="dynamic_filter_acc">Dynamic Filter</h3>
        <div>
            <div class="acordian-div">
                <form id="dynamicfilter_gird" autocomplete="off">
                    <div  class="btn-zoom-filter">
                        <?php
                        $active = "active";
                        if ($action == 'index') {
                            ?>
                            <?= Html::a('Display Global Projects', '@web/index.php?r=global-projects/index', ['title' => 'Display Global Projects', 'class' => 'display-global-label ' . $active, 'id' => 'display_global_project_id']) ?>	
                        <?php } else { ?>
                            <?= Html::a('Display Global Projects', '@web/index.php?r=global-projects/index', ['title' => 'Display Global Projects', 'class' => 'display-global-label', 'id' => 'display_global_project_id']) ?>	
<?php } ?>			
<?= Html::a("<em class='fa fa-search-plus fa-2x text-primary global-dynamic-search' title='Filter Projects'><span style='display:none'>Dynamic Search</span></em>", null, ["href" => 'javascript:filterGrid()', 'title' => 'Filter Projects', 'class' => 'pull-right', 'id' => 'dynamic_search_filter_a']); ?>
                        <input type="hidden" value="" id="filterAttributes" >
                    </div>
                    <div class="global-second-section" id="dynamic_filter_hide_show">
                        <fieldset>
                        <legend class="sr-only">Dynamic Filter</legend>
                        <ul class="sidebar-acordian">
                            <!--<li class="btn-zoom-filter"><?= Html::a("<em class='fa fa-search-plus fa-2x text-primary'></em>", null, ["href" => 'javascript:filterGrid()', 'title' => 'Filter', 'class' => 'pull-right']); ?>
                            <input type="hidden" value="" id="filterAttributes">
                            </li>-->
                            <?php
                            if ($action == 'index') {
                                $active = "active";
                            }
                            ?>

                            <!-- IRT 75 Role Type CaseManager shows -->
                            <?php if ($role_type[0] == 1) { ?>			
                                <!-- client -->
                                <li  id="client_filter" class="custom-full-width"> <input type="checkbox" name='by_cleint' id="by_cleint" onclick='if (this.checked) {
                                            $(".by_clients").show();
                                        } else {
                                            $(".by_clients").hide();
                                        }' <?php if ($by_cleint == 'on') echo 'checked'; ?> class="by_client"> <label for="by_cleint" class="by_client" aria-label="By Client">By Client</label> 
                                                <fieldset><legend class="sr-only">By Client</legend>
                                    <ul id="clients_filter" class='by_clients filter_hide_all' style='display:<?php if ($by_cleint == 'on') echo '';
                            else echo 'none' ?>;'>
                                        <li>
                                            <em class="fa fa-spinner fa-pulse fa-2x"></em>
                                            <span class="sr-only">Loading...</span>
                                        </li>
                                    </ul></fieldset>         
                                </li>
<?php } ?>

                            <!-- IRT 75 Role Type TeamManager shows -->
<?php if ($role_type[0] == 2 || $role_type[1] == 2) { ?>	
                                <!-- Team -->
                                <li id="team_filter" class="custom-full-width"> <input type="checkbox" name='by_team' id='by_team' onclick='if (this.checked)
                                            $(".by_teams").show();
                                        else
                                            $(".by_teams").hide();' <?php if ($by_team == 'on') echo 'checked'; ?> class="by_teams_dt"><label for="by_team" class="by_teams_dt" aria-label="By Team">By Team</label> 
                                    <fieldset><legend class="sr-only">By Team</legend>
                                    <ul id="teams_filter" style='display:<?php if ($by_team == 'on') echo '';
    else echo 'none' ?>;' class='by_teams filter_hide_all'>
                                    <li><em class="fa fa-spinner fa-pulse fa-2x"></em>
                                        <span class="sr-only">Loading...</span></li>
                                    </ul></fieldset>
                                </li>
<?php } ?>

                            <!--  Team Member -->
                            <li id="teamember_filter" class="custom-full-width"> <input type="checkbox" name='by_teammanager' id='by_teammanager' onclick='if (this.checked)
                                        $(".by_teammanagers").show();
                                    else
                                        $(".by_teammanagers").hide();' <?php if ($by_teammanager == 'on') echo 'checked'; ?> class="by_teammanager"><label for="by_teammanager" class="by_teammanager" aria-label="By Team Member">By Team Member</label> 
                                <fieldset><legend class="sr-only">By Team Member</legend>
                                <ul id="teamembers_filter" style='display:<?php if ($by_teammanager == 'on') echo '';
else echo 'none' ?>;' class='by_teammanagers filter_hide_all'>
                                    <li><em class="fa fa-spinner fa-pulse fa-2x"></em>
                                        <span class="sr-only">Loading...</span></li>
                                    </ul></fieldset>
                            </li>

                            <!--  Case Created User -->
                            <li id="casecreated_filter" class="custom-full-width"> <input type="checkbox" name='by_casecreatedmanager' id='by_casecreatedmanager' onclick='if (this.checked)
                                        $(".by_casecreatedmanagers").show();
                                    else
                                        $(".by_casecreatedmanagers").hide();' <?php if ($by_casecreatedmanager == 'on') echo 'checked'; ?> class="by_casecreatemanager"><label class="by_casecreatemanager" for="by_casecreatedmanager" aria-label="By Case Created">By Case Created</label>
                                    <fieldset><legend class="sr-only">By Case Created</legend>
                                <ul id="casecreateds_filter" style='display:<?php if ($by_casecreatedmanager == 'on') echo '';
else echo 'none' ?>;' class='by_casecreatedmanagers filter_hide_all'>
                                    <li><em class="fa fa-spinner fa-pulse fa-2x"></em>
                                        <span class="sr-only">Loading...</span></li>
                                </ul></fieldset>
                            </li>

                            <!--  Case Manager -->
                            <li id="projectsubmitted_filter" class="custom-full-width"> <input type="checkbox" name='by_casemanager' id='by_casemanager' onclick='if (this.checked)
                                        $(".by_casemanagers").show();else
                                        $(".by_casemanagers").hide();' <?php if ($by_casemanager == 'on') echo 'checked'; ?> class="by_casemanager_submitted"  aria-label="By Project Submitted"><label for="by_casemanager" class="by_casemanager_submitted">By Project Submitted</label> 
                                        <fieldset><legend class="sr-only">By Project Submitted</legend>
                                <ul id="projectsubmitteds_filter" style='display:<?php if ($by_casemanager == 'on') echo '';
else echo 'none' ?>;' class='by_casemanagers filter_hide_all'>
                                    <li><em class="fa fa-spinner fa-pulse fa-2x"></em>
                                        <span class="sr-only">Loading...</span></li>
                                </ul></fieldset>						
                            </li>

                            <!--  Project Requester -->
                            <li id="projectrequested_filter" class="custom-full-width"> <input type="checkbox" name='by_project_requested' id='by_project_requested' onclick='if (this.checked)
                                        $("#projectrequesteds_filter").show();
                                    else
                                        $("#projectrequesteds_filter").hide();' <?php if ($by_project_requested == 'on') echo 'checked'; ?> class="by_project_requested"  aria-label="By Project Requester"><label for="by_project_requested" class="by_project_requested">By Project Requester</label> 
                                       <fieldset> <legend class="sr-only">By Project Requester</legend>
                                <ul id="projectrequesteds_filter" style='display:<?php if ($by_project_requested == 'on') echo '';
else echo 'none' ?>;' class='by_project_requesteds filter_hide_all'>
                                    <li><em class="fa fa-spinner fa-pulse fa-2x"></em>
                                        <span class="sr-only">Loading...</span></li>
                                </ul></fieldset>						
                            </li>

                            <!--  Project Priority -->
                            <li id="project_priority" class="custom-full-width"> <input type="checkbox" name='by_taskpriority' id='by_taskpriority'  onclick='if (this.checked)
                                        $(".by_taskpriority").show();else
                                        $(".by_taskpriority").hide();' <?php if ($by_taskpriority == 'on') echo 'checked'; ?> class="by_taskpriority_dt"  aria-label="By Project Priority"><label for="by_taskpriority" class="by_taskpriority_dt">By Project Priority</label>  
                                    <fieldset><legend class="sr-only">By Project Priority</legend>
                                <ul id="projects_priority" style='display:<?php if ($by_taskpriority == 'on') echo '';
                                else echo 'none' ?>;' class='by_taskpriority filter_hide_all'>
                                    <li><em class="fa fa-spinner fa-pulse fa-2x"></em>
                                        <span class="sr-only">Loading...</span></li>
                                </ul></fieldset>
                            </li>		

                            <!--  Task Status -->
                            <li class="custom-full-width"> <input type="checkbox" name='by_taskstatus' id='by_taskstatus' onclick='if (this.checked)
                                        $(".by_taskstatuss").show();
                                    else
                                        $(".by_taskstatuss").hide();' <?php if ($by_taskstatus == 'on') echo 'checked'; ?> class="by_taskstatus"><label for="by_taskstatus" class="by_taskstatus" aria-label="By Project Status">By Project Status</label>  
                                    <fieldset><legend class="sr-only">By Project Status</legend>
                                <ul style='display:<?php if ($by_taskstatus == 'on') echo '';
                                        else echo 'none' ?>;' class='by_taskstatuss filter_hide_all'>
                                        <div>
                                            <div id="tree10" class="tree-class"></div>
                                            <textarea name="taskStat" id="taskstatuss" style="visibility:hidden;height: 0px;margin: 0px;padding: 0px;" ></textarea>
                                        </div>
                                        <?php /*foreach ($list_status as $stus_id => $ls) { ?>
                                        <li>
                                            <input type="checkbox"  name='taskstatuss[]' id="taskstatus_<?= $stus_id; ?>" value="<?= $stus_id; ?>" class="taskstatus" aria-label="<?= $ls ?>">
                                            <label for="taskstatus_<?= $stus_id; ?>" class="taskstatus"><?= $ls ?></label></li>
<?php }*/ ?>
                                </ul></fieldset>
                            </li>

                            <li class="custom-full-width"> 
                                <input type="checkbox" name='by_todotatus' id='by_todotatus' onclick='if (this.checked)
                                            $(".by_todotatuss").show();else
                                            $(".by_todotatuss").hide();' <?php if ($by_todotatus == 'on') echo 'checked'; ?> class="by_taskstatus"  aria-label="By ToDo Status"><label for="by_todotatus" class="by_todostatus">By ToDo Status</label>  
                                <fieldset><legend class="sr-only">By ToDo Status</legend>
                                <ul style='display:<?php if ($by_todotatus == 'on') echo '';
else echo 'none' ?>;' class='by_todotatuss filter_hide_all'>
                                        <?php
                                        asort($list_todostatus);
                                        /*foreach ($list_todostatus as $stus_id => $ls) {
                                            ?>
                                        <li><input type="checkbox"  name='todotatus[]' id="todotatus_<?= $stus_id; ?>" value="<?= $stus_id; ?>" class="todotatus" aria-label="<?= $ls ?>">
                                            <label for="todotatus_<?= $stus_id; ?>" class="todotatus"><?= $ls ?></label></li>
                                        <?php } */?>
                                        <div>
                                            <div id="tree11" class="tree-class"></div>
                                            <textarea name="todoStat" id="todotatus" style="visibility:hidden;height: 0px;margin: 0px;padding: 0px;" ></textarea>
                                        </div>
                                </ul></fieldset>
                            </li>

                            <!--  Project Submitted Dates -->					
                            <li class="custom-full-width">
                                <input type="checkbox"  name='by_submitted_date' id="project_submitted_date" onclick='if (this.checked)
                                            $(".project_submitted_date").show();else
                                            $(".project_submitted_date").hide();' <?php if ($by_submitted_date == 'on') echo 'checked'; ?>  aria-label="By Project Submitted Date"><label for="project_submitted_date">By Project Submitted Date</label> 
                                <div class="project_submitted_date filter_hide_all" style="display:<?php if ($by_submitted_date == 'on') echo '';
                                        else echo 'none' ?>;">

                                    
<?php
echo DateRangePicker::widget([
    'name'=>'previous_submitted_date',
    'options' => ['placeholder' => 'Click to add date','class'=>'form-control'],
    'pluginOptions'=>[
		'showDropdowns'=> true,
	    'locale'=>[ 'format' => 'MM/DD/YYYY' ],
        'drops'=>'up'
	],
    'presetDropdown' => true,
    
]);
/*<p class='dynamicFilterDropdown'>echo Select2::widget([
    'name' => 'previous_submitted_date',
    'attribute' => 'previous_submitted_date',
    'data' => array('T' => 'Today', 'Y' => 'Yesterday', 'W' => 'Last Week', 'M' => 'Last Month'),
    'options' => ['prompt' => 'Select Project Submitted Date', 'class' => 'form-control', 'id' => 'previous_submitted_date'],
    'pluginOptions' => [
        'allowClear' => true
    ]
]);</p>*/
?>
                                    
                                    <?php /*?><div class="input-group calender-group">
                                        <input type="textbox"  name='submitted_start_date' value="<?php if (isset($filtter_attributes['submitted_date']['start'])) {
                                            echo date('m/d/Y', strtotime($filtter_attributes['submitted_date']['start']));
                                        } else {
                                            echo "";
                                        } ?>" id="start_submitted_date" class="project_date form-control"  placeholder="Start Date" readonly="readonly"><label for="start_submitted_date" style="display:none;">Start Date</label>
                                    </div>
                                    <div class="input-group calender-group">
                                        <input type="textbox"  name='submitted_end_date' value="<?php if (isset($filtter_attributes['submitted_date']['end'])) {
                                            echo date('m/d/Y', strtotime($filtter_attributes['submitted_date']['end']));
                                        } else {
                                            echo "";
                                        } ?>" id="end_submitted_date" class="project_date form-control"   placeholder="End Date" readonly="readonly"><label for="end_submitted_date" style="display:none;">End Date</label>
                                    </div><?php */?>
                                </div>
                            </li>
                            <!--  Project Due Dates -->	
                            <li class="custom-full-width">
                                <input type="checkbox"  name='by_due_date' id="project_due_date" onclick='if (this.checked)
                                            $(".project_due_date").show();else
                                            $(".project_due_date").hide();' <?php if ($by_due_date == 'on') echo 'checked'; ?>  aria-label="By Project Due Date"><label for="project_due_date">By Project Due Date</label> 
                                <div class="project_due_date filter_hide_all" style="display:<?php if ($by_due_date == 'on') echo '';
                                        else echo 'none' ?>;">
                                        <?php echo DateRangePicker::widget([
    'name'=>'previous_due_date',
    'options' => ['placeholder' => 'Click to add date','class'=>'form-control'],
    'pluginOptions'=>[
		'showDropdowns'=> true,
	    'locale'=>[ 'format' => 'MM/DD/YYYY' ],
        'drops'=>'up'
	],
    'presetDropdown' => true,
]);?>
                                   <?php /*?> <p class='dynamicFilterDropdown'>
<?php
echo Select2::widget([
    'name' => 'previous_due_date',
    'attribute' => 'previous_due_date',
    'data' => array('T' => 'Today', 'Y' => 'Yesterday', 'W' => 'Last Week', 'M' => 'Last Month'),
    'options' => ['prompt' => 'Select Project Due Date', 'class' => 'form-control', 'id' => 'previous_due_date'],
    'pluginOptions' => [
        'allowClear' => true
    ]
]);
?>
                                    </p>
                                    <div class="input-group calender-group">
                                        <input type="textbox" name='due_start_date' value="<?php if (isset($filtter_attributes['due_date']['start'])) {
    echo date('m/d/Y', strtotime($filtter_attributes['due_date']['start']));
} else {
    echo "";
} ?>" id="start_due_date" class="project_date form-control" size="15"  placeholder="Start Date"  readonly="readonly"><label for="start_due_date" style="display:none;">Start Due Date</label>
                                    </div>
                                    <div class="input-group calender-group">
                                        <input type="textbox" name='due_end_date' value="<?php if (isset($filtter_attributes['due_date']['end'])) {
    echo date('m/d/Y', strtotime($filtter_attributes['due_date']['end']));
} else {
    echo "";
} ?>" id="end_due_date" class="project_date form-control" size="15"  placeholder="End Date" readonly="readonly"><label for="end_due_date" style="display:none;">End Due Date</label>
                                    </div>
                                    <?php */?>
                                </div>
                            </li>
                            <!--  Project Completed Dates -->
                            <li class="custom-full-width">
                                <input type="checkbox"  name='by_completed_date' id="project_completed_date" onclick='if (this.checked)
                                            $(".project_completed_date").show();
                                        else
                                            $(".project_completed_date").hide();' <?php if ($by_completed_date == 'on') echo 'checked'; ?> aria-label="By Project Completed Date"><label for="project_completed_date">By Project Completed Date</label> 
                                <div class="project_completed_date filter_hide_all" style="display:<?php if ($by_completed_date == 'on') echo '';
else echo 'none' ?>;">
<?php echo DateRangePicker::widget([
    'name'=>'previous_completed_date',
    'options' => ['placeholder' => 'Click to add date','class'=>'form-control'],
    'pluginOptions'=>[
		'showDropdowns'=> true,
	    'locale'=>[ 'format' => 'MM/DD/YYYY' ],
        'drops'=>'up'
	],
    'presetDropdown' => true,
]);?>
                                    <?php /*?><p class='dynamicFilterDropdown'>
<?php
echo Select2::widget([
    'name' => 'previous_completed_date',
    'attribute' => 'previous_completed_date',
    'data' => array('T' => 'Today', 'Y' => 'Yesterday', 'W' => 'Last Week', 'M' => 'Last Month'),
    'options' => ['prompt' => 'Select Project Completed Date', 'class' => 'form-control', 'id' => 'previous_completed_date'],
    'pluginOptions' => [
        'allowClear' => true
    ]
]);
?>
                                    </p>
                                    <div class="input-group calender-group">
                                        <input type="textbox"  name='completed_start_date' value="<?php if (isset($filtter_attributes['completed_date']['start'])) {
    echo date('m/d/Y', strtotime($filtter_attributes['completed_date']['start']));
} else {
    echo "";
} ?>" id="start_completed_date" class="project_date form-control"  placeholder="Start Date" readonly="readonly"><label for="start_completed_date" style="display:none;">Completd Start Date</label>
                                    </div>
                                    <div class="input-group calender-group">
                                        <input type="textbox"  name='completed_end_date' value="<?php if (isset($filtter_attributes['completed_date']['end'])) {
    echo date('m/d/Y', strtotime($filtter_attributes['completed_date']['end']));
} else {
    echo "";
} ?>" id="end_completed_date" class="project_date form-control"   placeholder="End Date"  readonly="readonly"><label for="end_completed_date" style="display:none;">Completed End Date</label>
                                    </div>
                                    <?php */?>
                                </div>
                            </li>	
                        </ul>
                        </fieldset>
                    </div>
                </form>
            </div>
        </div>
        <h3 data-index = '2' aria-label="Saved Filter" title="Saved Filter" class="get_index" onclick="get_saved_filter();" onkeyup="get_saved_filter_keyup(event);" id="get_saved_filter">Saved Filter</h3>
        <div>
            <div class="acordian-div">
                <legend class="sr-only">Saved Filter</legend>
                <ul class="sidebar-acordian" id="saved_reports_ul">
                    <li><em class="fa fa-spinner fa-pulse fa-2x"></em>
                        <span class="sr-only">Loading...</span></li>		
                </ul>
            </div>
        </div> 
<?php if ($controller == 'global-projects' && $action == 'filter-option') { ?>
            <h3 data-index = '3' title="Filter Options" class="get_index" id="filetr_options" >Filter Options</h3>
            <div id="filetr_options">
                <div class="acordian-div">
                    <fieldset><legend class="sr-only">Filter Options</legend>
                    <ul class="sidebar-acordian">
                        <li><a href="javascript:BulkCloseSF(<?= $filter_id ?>);"  title="Bulk Close Projects"    class="">Bulk Close Projects</a></li>
                        <li><a href="javascript:BulkReopenSF(<?= $filter_id ?>);" title ="Bulk ReOpen Projects" class="">Bulk ReOpen Projects</a></li>		
                    </ul></fieldset>
                </div>
            </div> 
<?php } ?>
    </div>

</div>
<?php 
$statusList = [];
foreach($list_status as $s_id => $status) {
        $stats = [];
        $stats['title'] = Html::encode($status);
        $stats['isFolder'] = true;
        $stats['key'] = $s_id;
        $statusList[] = $stats;
}
$todostatusList = [];
foreach($list_todostatus as $todos_id => $status) {
        $todostats = [];
        $todostats['title'] = Html::encode($status);
        $todostats['isFolder'] = true;
        $todostats['key'] = $todos_id;
        $todostatusList[] = $todostats;
}
?>
<script type="text/javascript">
    var treeData = <?= json_encode($statusList); ?>;
    var treeData2 = <?= json_encode($todostatusList); ?>;
    $(function(){
        $('input').customInput();
        $("#tree10").dynatree({
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
                $('#taskstatuss').val(newTemp);
                
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
        $("#tree11").dynatree({
			checkbox: true,
			selectMode: 3,
			children: treeData2,
			onSelect: function(select, node) {
                var clientcaseAr = [];
				var selKeys = $.map(node.tree.getSelectedNodes(), function(node){
                    if(node.childList===null)
					    return node.data.key.toString();
                });
                //console.log(selKeys);
                mystring = JSON.stringify(selKeys);
                newTemp = mystring.replace(/"/g, "'");
                $('#todotatus').val(newTemp);
                
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
    

// by client
    $('.by_client').click(function () {
        if (!$('.by_client').is(':checked')) {
            if($("#tree3").length){
            $("#tree3").dynatree("getRoot").visit(function(node){
                node.select(false);
            });
            $('#clientCasesToInput').val(null);
            }
            $('.client_case').prop('checked', false);
            $('.by_client_case').prop('checked', false);
            $('.client_case').removeClass('checked');
            $('.by_client_case').removeClass('checked');
            $('.filter_hide_all').css('display', 'none');
        }
    });

// by teams
    $('.by_teams_dt').click(function () {
        if (!$('.by_teams_dt').is(':checked')) {
            if($("#tree4").length){
             $("#tree4").dynatree("getRoot").visit(function(node){
                node.select(false);
            });
            $('#teamLocsToInput').val(null);
            }
            $('.teams').prop('checked', false);
            $('.teams').removeClass('checked');
            /* by_teamloc */
            $('.by_teamloc').prop('checked', false);
            $('.by_teamloc').removeClass('checked');
        }
    });

// by casecreatemanager
    $('.by_casecreatemanager').click(function () {
        if (!$('.by_casecreatemanager').is(':checked')) {
            if($("#tree6").length){
                $("#tree6").dynatree("getRoot").visit(function(node){
                    node.select(false);
                });
                $('#casecreatedmanagers').val(null);
            }
            $('.casecreatemanager').prop('checked', false);
            $('.casecreatemanager').removeClass('checked');
        }
    });

// by casemanager
    $('#by_casemanager').click(function () {
        if (!$('.by_casemanager').is(':checked')) {
            if($("#tree7").length){
                $("#tree7").dynatree("getRoot").visit(function(node){
                    node.select(false);
                });
                $('#casemanagers').val(null);
            }
            $('.casemanager_projectsubmitted').prop('checked', false);
            $('.casemanager_projectsubmitted').removeClass('checked');
        }
    });

// by teammanager
    $('.by_teammanager').click(function () {
        if (!$('.by_teammanager').is(':checked')) {
            if($("#tree5").length){
            $("#tree5").dynatree("getRoot").visit(function(node){
                node.select(false);
            });
            $('#teammanagers').val(null);
            }
            $('.teammanager').prop('checked', false);
            $('.teammanager').removeClass('checked');
        }
    });
//by_project_requested
$('#by_project_requested').click(function () {
        if (!$('#by_project_requested').is(':checked')) {
            if($("#tree8").length){
                $("#tree8").dynatree("getRoot").visit(function(node){
                    node.select(false);
                });
                $('#requestor').val(null);
            }
            
        }
    });
//by taskstatus
    $('.by_taskstatus').click(function () {
        if (!$('.by_taskstatus').is(':checked')) {
            if($("#tree10").length){
                $("#tree10").dynatree("getRoot").visit(function(node){
                    node.select(false);
                });
                $('#taskstatuss').val(null);
            }
            $('.taskstatus').prop('checked', false);
            $('.taskstatus').removeClass('checked');
        }
    });
//by todostatus
    $('.by_todostatus').click(function () {
        if (!$('.by_todostatus').is(':checked')) {
            if($("#tree11").length){
                $("#tree11").dynatree("getRoot").visit(function(node){
                    node.select(false);
                });
                $('#todotatus').val(null);
            }
            $('.todotatus').prop('checked', false);
            $('.todotatus').removeClass('checked');
        }
    });

// by taskpriority
    $('.by_taskpriority_dt').click(function () {
        if (!$('.by_taskpriority_dt').is(':checked')) {
            if($("#tree9").length){
                $("#tree9").dynatree("getRoot").visit(function(node){
                    node.select(false);
                });
                $('#taskpriority').val(null);
            }
            
            $('.taskpriority').prop('checked', false);
            $('.taskpriority').removeClass('checked');
        }
    });

// by Project Status
    /*$('.by_taskstatus').click(function(){
     if(!$('.by_taskstatus').is(':checked')){
     $('.taskpriority').prop('checked',false);
     $('.taskpriority').removeClass('checked');
     }
     });*/

// Project Submitted Date
    $('#project_submitted_date').click(function () {
        if (!$('#project_submitted_date').is(':checked')) {
            $("input[name='previous_submitted_date']").val(null);
           // $('#start_submitted_date').val('');
           // $('#end_submitted_date').val('');
        }
    });

// Project Due Date
    $('#project_due_date').click(function () {
        if (!$('#project_due_date').is(':checked')) {
            $("input[name='previous_due_date']").val(null);
            //$("#previous_due_date").select2("val", "");
            //$('#start_due_date').val('');
            //$('#end_due_date').val('');
        }
    });

// Project completed date
    $('#project_completed_date').click(function () {
        if (!$('#project_completed_date').is(':checked')) {
            $("input[name='previous_completed_date']").val(null);
            //$("#previous_completed_date").select2("val", "");
            //$('#start_completed_date').val('');
           // $('#end_completed_date').val('');
        }
    });


    var filter_id = <?php echo $filter_id; ?>;
    var current_action = '<?php echo $action; ?>';
//$('#dynamic_filter_hide_show').css('display','none');	
    var accordionOptions = {
        heightStyle: 'fill', clearStyle: true, autoHeight: false, active: $('#accordion-container h3').index($('#<?= $accordianIndex ?>'))
    };
    var accordionOptions = {
	 heightStyle: 'fill',clearStyle: true,autoHeight: false, icons: { "header": "fa fa-caret-right pull-right", "activeHeader": "fa fa-caret-down pull-right" }
,create: function( event, ui ) {

//$("#accordion-container h3 span").removeClass('ui-accordion-header-icon');
$("#accordion-container h3 span").removeClass('ui-icon');

}, active:$('#accordion-container h3').index($('#<?=$accordianIndex?>'))};

    /*$(window).load(function(){
     hideLoader();
     $('#dynamic_filter_hide_show').delay(1000);
     });*/
    $('body').on('click', '#rdo_selectedreopen', function () {
        $('#all_label').removeAttr('class');
    });
    $('body').on('click', '#rdo_bulkreopen', function () {
        $('#selected_label').removeAttr('class');
    });
    if (current_action == 'index') {
        $('#dynamic_filter_hide_show').css('display', 'block');
        $('#dynamic_search_filter_a').css('display', 'block');
    } else {
        $('#dynamic_search_filter_a').css('display', 'none');
        $('#dynamic_filter_hide_show').css('display', 'none');
    }
//jQuery(document).ready(function($) { 
    var datepicker_id = 'start_due_date';
    var formElements = {};
    formElements[datepicker_id] = "%m/%d/%Y";
    datePickerController.createDatePicker({formElements: formElements});
    var datepicker_id = 'end_due_date';
    var formElements = {};
    formElements[datepicker_id] = "%m/%d/%Y";
    datePickerController.createDatePicker({formElements: formElements});
    var datepicker_id = 'start_completed_date';
    var formElements = {};
    formElements[datepicker_id] = "%m/%d/%Y";
    datePickerController.createDatePicker({formElements: formElements});
    var datepicker_id = 'end_completed_date';
    var formElements = {};
    formElements[datepicker_id] = "%m/%d/%Y";
    datePickerController.createDatePicker({formElements: formElements});
    var datepicker_id = 'start_submitted_date';
    var formElements = {};
    formElements[datepicker_id] = "%m/%d/%Y";
    datePickerController.createDatePicker({formElements: formElements});
    var datepicker_id = 'end_submitted_date';
    var formElements = {};
    formElements[datepicker_id] = "%m/%d/%Y";
    datePickerController.createDatePicker({formElements: formElements});

    $("#accordion-container").accordion(accordionOptions);
    /*$("#accordion-container h3").bind("click", function() {
     
     var data_index = $(this).attr('data-index');
     if(data_index == 0){
     $('#page-title span').text('Dynamic Filters');
     $('#display_global_project_id').on('click',function(){
     if(filter_id != '' && filter_id != 0 && current_action == 'filter-option'){
     location.href = baseUrl+'global-projects/index';
     }
     });
     }else if(data_index == 2){
     $('#page-title span').text('Saved Filters');
     if(filter_id != '' && filter_id != 0 && current_action == 'filter-option'){
     location.href = baseUrl+'global-projects/save-filter-grid&filter_id='+filter_id;
     }
     }else if(data_index == 3){
     $('#page-title span').text('Filter Options');
     }else{
     $('#page-title span').text('Global Project');
     }
     }); */
    $(window).resize(function () {
        // update accordion height
        $('#accordion-container').accordion("refresh");
        $( "#accordion-container" ).accordion( "destroy" );
		$("#accordion-container" ).accordion(accordionOptions);
    });
    $('body').on('change', '#client_filter', function () {
        if ($("#clients_filter li").length == 1) {
            $.ajax({
                type: "GET",
                url: baseUrl + "global-projects/get-client-case",
                dataType: 'html',
                cache: false,
                success: function (data) {
                    if (data != "") {
                        $("#clients_filter").html(data);
                    }
                }
            });
        }
    });
    $('body').on('click', '#rdo_bulkreopen', function () {
        var keys = $('#globalproject-saved-grid').yiiGridView('getSelectedRows');
        if (keys == '') {
            $('#selected_label').addClass('disabled');
        }

    });
    $('body').on('change', '#team_filter', function () {
        getTeamMember();
        if ($("#teams_filter li input").length == 0) {
            $.ajax({
                type: "GET",
                url: baseUrl + "global-projects/get-teams",
                dataType: 'html',
                cache: false,
                success: function (data) {
                    if (data != "") {
                        $("#teams_filter").html(data);
                    }
                }, complete: function () {
                    $('input').customInput();
                }
            });
        }
    });
    $('body').on('change', '#teamember_filter', function () {
        var team_loc_arr = [];
        $('#team_filter input[name="teams[]"]:checked').each(function () {
            var team_id = this.value;
            if ($('#team_loc_' + this.value).find('input[name="teamloc[]"]:checked').length > 0) {
                $('#team_loc_' + this.value).find('input[name="teamloc[]"]:checked').each(function () {
                    team_loc = {'team': team_id, 'loc': this.value};
                    team_loc_arr.push(team_loc);
                });
            } else {
                team_loc = {'team': team_id};
                team_loc_arr.push(team_loc);
            }
        });
        if ($("#teamembers_filter li input").length == 0) {
            $.ajax({
                type: "POST",
                url: baseUrl + "global-projects/get-teamembers",
                data: {team_loc_arr: team_loc_arr},
                dataType: 'html',
                cache: false,
                success: function (data) {
                    if (data != "") {
                        $("#teamembers_filter").html(data);
                    }
                }, complete: function () {
                    $('input').customInput();
                }
            });
        }
    });
    function getTeamMember() {
        var team_loc_arr = [];
        $('#team_filter input[name="teams[]"]:checked').each(function () {
            var team_id = this.value;
            if ($('#team_loc_' + this.value).find('input[name="teamloc[]"]:checked').length > 0) {
                $('#team_loc_' + this.value).find('input[name="teamloc[]"]:checked').each(function () {
                    team_loc = {'team': team_id, 'loc': this.value};
                    team_loc_arr.push(team_loc);
                });
            } else {
                team_loc = {'team': team_id};
                team_loc_arr.push(team_loc);
            }
        });
        $.ajax({
            type: "POST",
            url: baseUrl + "global-projects/get-teamembers",
            beforeSend: function () {
                $("#teamembers_filter").html('<li><em class="fa fa-spinner fa-pulse fa-2x"></em><span class="sr-only">Loading...</span></li>');
            },
            data: {team_loc_arr: team_loc_arr},
            dataType: 'html',
            cache: false,
            success: function (data) {
                if (data != "") {
                    $("#teamembers_filter").html(data);
                }
            }, complete: function () {
                $('input').customInput();
            }
        });
    }
    $('body').on('change', '#casecreated_filter', function () {
        if ($("#casecreateds_filter li input").length == 0) {
            $.ajax({
                type: "GET",
                url: baseUrl + "global-projects/get-case-created",
                dataType: 'html',
                cache: false,
                success: function (data) {
                    if (data != "") {
                        $("#casecreateds_filter").html(data);
                    }
                }, complete: function () {
                    $('input').customInput();
                }
            });
        }
    });
    $('body').on('change', '#projectsubmitted_filter', function () {
        if ($("#projectsubmitteds_filter li input").length == 0) {
            $.ajax({
                type: "GET",
                url: baseUrl + "global-projects/projectsubmitted",
                dataType: 'html',
                cache: false,
                success: function (data) {
                    if (data != "") {
                        $("#projectsubmitteds_filter").html(data);
                    }
                }, complete: function () {
                    $('input').customInput();
                }
            });
        }
    });
    $('body').on('change', '#by_project_requested', function () {
        if ($("#projectrequesteds_filter li input").length == 0) {
            $.ajax({
                type: "GET",
                url: baseUrl + "global-projects/projectrequested",
                dataType: 'html',
                cache: false,
                success: function (data) {
                    if (data != "") {
                        $("#projectrequesteds_filter").html(data);
                    }
                }, complete: function () {
                    $('input').customInput();
                }
            });
        }
    });

    $('body').on('change', '#project_priority', function () {
        if ($("#projects_priority li input").length == 0) {
            $.ajax({
                type: "GET",
                url: baseUrl + "global-projects/projectpriority",
                dataType: 'html',
                cache: false,
                success: function (data) {
                    if (data != "") {
                        $("#projects_priority").html(data);
                    }
                }, complete: function () {
                    $('input').customInput();
                }
            });
        }
    });

    filterGrid = function (allglobaltask) {
        var token = $('#YII_CSRF_TOKEN').val();
        $("#filterAttributes").val('');
        $.ajax({
            url: baseUrl + "global-projects/index",
            type: 'POST',
            data: $("#dynamicfilter_gird").serialize(),
            beforeSend: function () {
                showLoader();
            },
            success: function (data) {
                hideLoader();
                $('#admin_main_container').html(data);
                $('.all_filter').show();
                $("#filterAttributes").val($("#dynamicfilter_gird").serialize());
                $('#dynamic_filter').val(1);
            }
        });
    }
    function submit_savedynamicfiltter()
    {
        var filter_name = document.getElementById('save_filter').value;
        var filterAttributes = document.getElementById('filterAttributes').value;
        if (filterAttributes == "") {
            alert('Please apply 1+ Dynamic Filter before saving the results.');
            return false;
        }
        if (filter_name != '')
        {
            if (filter_name != 'Enter Filter Name')
            {
                var chkUrl = baseUrl + 'global-projects/checkfilter-exist';
                $.ajax({
                    url: chkUrl,
                    cache: false,
                    type: 'POST',
                    data: {filter_name: filter_name},
                    dataType: 'html',
                    beforeSend: function () {
                        showLoader();
                    },
                    success: function (data) {
                        hideLoader();
                        if (data == 0) {
                            $.ajax({
                                url: baseUrl + 'global-projects/savefilter',
                                cache: false,
                                type: 'POST',
                                data: {post_data: $("#dynamicfilter_gird").serializeArray(), filter_name: filter_name},
                                dataType: 'html',
                                beforeSend: function () {
                                    showLoader();
                                },
                                success: function (data) {
                                    hideLoader();
                                    if (data != 'Opps Something goes Wrong...') {
                                        $('#savec_filters').append(data);
                                        document.getElementById('save_filter').value = '';
                                        alert("The filter, " + filter_name + " has been saved.");
                                    } else {
                                        alert(data);
                                    }
                                }
                            });
                        } else {
                            alert(filter_name + ' Saved Filter already exists.   Please enter a unique value to perform this action.');
                            {
//                                 $.ajax({
//                                     url: baseUrl+'global-projects/savefilter',
//                                     cache: false,
//                                     type: 'POST',
//                                     data: 'filter_name=' + filter_name + '&' + $("#dynamicfilter_gird").serialize(),
//                                     dataType: 'html',
//                                     beforeSend:function(){
//                               			showLoader();
//                               	     },
//                                     success: function (data) {
//                                    	 hideLoader();
//                                         if (data != 'Opps Something goes Wrong...')
//                                         {
//                                             $('#savec_filters').append(data);
//                                             document.getElementById('save_filter').value='';
//                                             alert("The filter, "+filter_name+" has been saved.");
//                                         }
//                                         else
//                                         {
//                                             alert(data);
//                                         }
//                                     }
//                                 });
                            }
                        }
                    }
                });
            } else
            {
                alert('Please enter a Filter Name to perform this action.');
                document.getElementById('save_filter').focus();
                return false;

            }
        } else
        {
            alert('Please enter a Filter Name to perform this action.');
            document.getElementById('save_filter').focus();
            return false;

        }
    }
    /* Fetch the Saved Report on Left Panel of Report. */
    function get_saved_filter() {
        $('#saved_reports_ul').html('<li><em class="fa fa-spinner fa-pulse fa-2x"></em><span class="sr-only">Loading...</span></li>');
        $.ajax({
            type: "get",
            url: baseUrl + "global-projects/getsavedfilters",
            dataType: 'html',
            cache: false,
            success: function (data) {
                $('#saved_reports_ul').html(data);
                $('#savefilter_<?= Yii::$app->request->get("filter_id") ?>').addClass('active');
            }
        });

    }

    function get_saved_filter_keyup(event){
        var x = event.which || event.keyCode;
        if(x==13){
        $('#saved_reports_ul').html('<li><em class="fa fa-spinner fa-pulse fa-2x"></em><span class="sr-only">Loading...</span></li>');
        $.ajax({
            type: "get",
            url: baseUrl + "global-projects/getsavedfilters",
            dataType: 'html',
            cache: false,
            success: function (data) {
                $('#saved_reports_ul').html(data);
                $('#savefilter_<?= Yii::$app->request->get("filter_id") ?>').addClass('active');
            }
        });
        } else {
            return false;
        }
    }
    /*show saved filter gird*/
    function showSavefilter(filter_id) {
        location.href = baseUrl + "global-projects/save-filter-grid&filter_id=" + filter_id;
        /*$.ajax({
         url: baseUrl+"global-projects/save-filter-grid",
         type: 'POST',
         data: {filter_id:filter_id},
         beforeSend:function(){
         $('.allsavedfilter').removeClass('active');
         showLoader();
         },
         success: function (data) {
         $('#savefilter_'+filter_id).addClass('active');
         hideLoader();
         $('#admin_main_container').html(data);
         $('#page-title').html('<em class="fa fa-search-plus text-danger"></em> <span>Saved Filters</span>');
         },complete:function(){
         $('input').customInput();
         }
         });*/
    }
    /*show saved filter gird Option*/
    function FilterOptions(filter_id) {
        $.ajax({
                url: baseUrl + "global-projects/check-savedfilter",
                type: 'POST',
                data: {filter_id: filter_id},
                beforeSend: function () {
                    showLoader();
                },
                success: function (data) {
                    hideLoader();
                    if($.trim(data)=='OK'){
                        location.href = baseUrl + "global-projects/filter-option&filter_id=" + filter_id;
                    }else{
                        alert('Please select a Saved Filter');
                        return false;
                    }
                }
        });
    }
    function  deletesavefilter(filter_id) {
        var filter_name = $('#savefilter_' + filter_id).text();
        if (confirm('Are you sure you want to Delete ' + filter_name + '?')) {
            $.ajax({
                url: baseUrl + "global-projects/deletesave-filter",
                type: 'POST',
                data: {filter_id: filter_id},
                beforeSend: function () {
                    showLoader();
                },
                success: function (data) {
                    $('#savefilter_' + filter_id).parent().parent().remove();
                    hideLoader();
                }
            });
        }
    }
    function BackToSaveFilter(filter_id) {
        location.href = baseUrl + "global-projects/save-filter-grid&filter_id=" + filter_id;
    }
    /* Start : Bulk close Completed projects */
    function BulkCloseSF(filter_id) {
        //	var keys = $('#globalproject-grid').yiiGridView('getSelectedRows');
        var keys = $('#globalproject-saved-grid').yiiGridView('getSelectedRows');
        var count = keys.length;
        var task_ids = "";
        if (count >= 1) {
            $('#globalproject-saved-grid input:checked').each(function () {
                id = $(this).closest('tr').data('key');
                if (task_ids == "") {
                    task_ids = id;
                } else {
                    task_ids = task_ids + ', ' + id;
                }
            });
        }

        if (!$("#bulkreopendialog").length) {
            $('body').append("<div id='bulkreopendialog'></div>");
        }
        bulkreopendialog = $("#bulkreopendialog");
        bulkreopendialog.html('<fieldset><legend class="sr-only">Bulk Close Projects</legend>' +
                '<div class="custom-inline-block-width">' +
                '<input aria-setsize="2" aria-posinset="1" type="radio" name="bulkreopen" class="bulkreopen" value="selectedtask" id="rdo_selectedreopen" aria-label="Selected Projects in Grid" /><label for="rdo_selectedreopen" id="selected_label">Selected <span id="selectedtask">0</span> Projects in Grid</label>' +
                '<input aria-setsize="2" aria-posinset="2" type="radio" name="bulkreopen" class="bulkreopen" value="alltask" checked="checked" id="rdo_bulkreopen"  aria-label="All Projects in Grid" /><label for="rdo_bulkreopen" id="all_label">All Filtered <span id="alltask">0</span> Projects in Grid</label>' +
                '</div>' +
                '</fieldset>');
        // open the dialog
        bulkreopendialog.dialog({
            title: 'Bulk Close Projects',
            autoOpen: true,
            resizable: false,
            width: "50em",
            height: 302,
            modal: true,
            buttons: [
                {
                    text: "Cancel",
                    "title": "Cancel",
                    "class": 'btn btn-primary',
                    click: function () {
                        bulkreopendialog.dialog("close");
                    }
                },
                {
                    text: "Update",
                    "title": "Update",
                    "class": 'btn btn-primary',
                    click: function () {
                        var taskoperation = $('#bulkreopendialog input[type="radio"]:checked').val();
                        if (taskoperation == 'selectedtask') {
                            if (confirm("Are you sure you want to Close the selected " + count + " record(s)?"))
                            {
                                jQuery.ajax({
                                    url: baseUrl + 'global-projects/close-projects&filter_id=' + filter_id,
                                    data: {task_list: keys, flag: 'selected'},
                                    type: 'post',
                                    beforeSend: function (data) {
                                        showLoader();
                                    },
                                    success: function (data) {
                                        if (data == 'OK') {
                                            bulkreopendialog.dialog("close");
                                            location.reload();
                                            //	hideLoader();
                                            //	$.pjax.reload('#globalproject-saved-pajax', $.pjax.defaults);
                                        } else {
                                            hideLoader();
                                            alert('This action applies to a Project in Complete Status. Please select a Completed Project to perform this action.');
                                        }
                                    },
                                    complete: function (data) {
                                        hideLoader();
                                    }
                                });
                            }
                        } else {                            
                            var postData = '&flag=all&'+$('#global-filtered-form').serialize();
                            if (confirm("Are you sure you want to Close All record(s)?"))
                            {
                                jQuery.ajax({
                                    url: baseUrl + 'global-projects/close-projects&filter_id=' + filter_id,
                                    data: postData,
                                    type: 'post',
                                    beforeSend: function (data) {
                                        showLoader();
                                    },
                                    success: function (data) {
                                        if (data == 'OK') {
                                            bulkreopendialog.dialog("close");
                                            location.reload();
                                            //	hideLoader();
                                            //	$.pjax.reload('#globalproject-saved-pajax', $.pjax.defaults);
                                        } else {
                                            hideLoader();
                                            alert('This action applies to a Project in complete status. Please select a completed Project to perform this action.');
                                        }
                                    },
                                    complete: function (data) {
                                        hideLoader();
                                    }
                                });
                            }
                        }
                    }
                }
            ],
            open: function () {
                bulkreopendialog.find('#selectedtask').html(count);
                bulkreopendialog.find('#alltask').html($('#globalproject-saved-grid .summary b#totalItemCount').text());
                if (count == 0) {
                    bulkreopendialog.find('#rdo_selectedreopen').prop('checked', false);
                    bulkreopendialog.find('label[for="rdo_selectedreopen"]').removeClass('checked');
                    bulkreopendialog.find('#rdo_selectedreopen').prop('disabled', true);
                    bulkreopendialog.find('label[for="rdo_selectedreopen"]').addClass('disabled');
                    bulkreopendialog.find('#rdo_bulkreopen').prop('checked', true);
                    bulkreopendialog.find('label[for="rdo_bulkreopen"]').addClass('checked');

                    bulkreopendialog.find('#rdo_bulkreopen').focus();
                    bulkreopendialog.find('label[for="rdo_selectedreopen"]').removeClass("focus")
                    bulkreopendialog.find('label[for="rdo_bulkreopen"]').addClass("focus");
                } else {
                    bulkreopendialog.find('#rdo_bulkreopen').prop('checked', false);
                    bulkreopendialog.find('label[for="rdo_bulkreopen"]').removeClass('checked');
                    bulkreopendialog.find('#rdo_selectedreopen').prop('disabled', false);
                    bulkreopendialog.find('label[for="rdo_selectedreopen"]').removeClass('disabled');
                    bulkreopendialog.find('#rdo_selectedreopen').prop('checked', true);
                    bulkreopendialog.find('label[for="rdo_selectedreopen"]').addClass('checked');

                    bulkreopendialog.find('label[for="rdo_bulkreopen"]').removeClass("focus");
                    bulkreopendialog.find('#rdo_selectedreopen').focus();
                    bulkreopendialog.find('label[for="rdo_selectedreopen"]').addClass("focus");
                }

                $('input').customInput();
            },
            close: function () {
                $(this).dialog('destroy').remove();
                // Close code here (incidentally, same as Cancel code)
            }
        });
    }

    /* Start : Bulk ReOpen closed projects */
    function BulkReopenSF(filter_id)
    {
        //var keys = $('#globalproject-grid').yiiGridView('getSelectedRows');
        var keys = $('#globalproject-saved-grid').yiiGridView('getSelectedRows');
        var count = keys.length;
        var task_ids = "";
        if (count >= 1) {
            $('#globalproject-saved-grid input:checked').each(function () {
                id = $(this).closest('tr').data('key');
                if (task_ids == "") {
                    task_ids = id;
                } else {
                    task_ids = task_ids + ', ' + id;
                }
            });
        }

        if (!$("#bulkreopendialog").length) {
            $('body').append("<div id='bulkreopendialog'></div>");
        }
        bulkreopendialog = $("#bulkreopendialog");
        bulkreopendialog.html('<fieldset><legend class="sr-only">Bulk ReOpen Projects</legend>' +
                '<div class="custom-inline-block-width">' +
                '<input aria-setsize="2" aria-posinset="1" type="radio" name="bulkreopen" class="bulkreopen" value="selectedtask" id="rdo_selectedreopen"><label for="rdo_selectedreopen" id="selected_label">Selected <span id="selectedtask">0</span> Projects in Grid</label>' +
                '<input aria-setsize="2" aria-posinset="2" type="radio" name="bulkreopen" class="bulkreopen" value="alltask" checked="checked" id="rdo_bulkreopen"/><label for="rdo_bulkreopen" id="all_label">All Filtered <span id="alltask">0</span> Projects in Grid</label>' +
                '</div>' +
                '</fieldset>');

        // open the dialog
        bulkreopendialog.dialog({
            title: 'Bulk ReOpen Projects',
            autoOpen: true,
            resizable: false,
            width: "50em",
            height: 302,
            modal: true,
            buttons: [
                {
                    text: "Cancel",
                    "title": "Cancel",
                    "class": 'btn btn-primary',
                    click: function () {
                        bulkreopendialog.dialog("close");
                    }
                },
                {
                    text: "Update",
                    "title": "Update",
                    "class": 'btn btn-primary',
                    click: function () {
                        var taskoperation = $('#bulkreopendialog input[type="radio"]:checked').val();
                        //console.log(taskoperation);return false;
                        if (taskoperation == 'selectedtask') {
                            if (confirm("Are you sure you want to ReOpen the selected " + count + " record(s)?"))
                            {
                                jQuery.ajax({
                                    url: baseUrl + 'global-projects/reopen-projects&filter_id=' + filter_id,
                                    data: {task_list: keys, flag: 'selected'},
                                    type: 'post',
                                    beforeSend: function (data) {
                                        showLoader();
                                    },
                                    success: function (data) {
                                        if (data == 'OK') {
                                            bulkreopendialog.dialog("close");
                                            location.reload();
                                            //hideLoader();
                                            //$.pjax.reload('#globalproject-saved-pajax', $.pjax.defaults);
                                        } else {
                                            hideLoader();
                                            //alert('This action applies to a closed Project.Please select a closed Project to perform this action.');
                                            alert('This action applies to Closed Projects. Please select a Closed Project to perform this action.');
                                        }
                                    },
                                    complete: function (data) {
                                        hideLoader();
                                    }
                                });
                            }
                        } else {                            
                            if (confirm("Are you sure you want to ReOpen All record(s)?"))
                            {
                                var postData = '&flag=all&'+$('#global-filtered-form').serialize();
                                jQuery.ajax({
                                    url: baseUrl + 'global-projects/reopen-projects&filter_id=' + filter_id,
                                    data: postData,
                                    type: 'post',
                                    beforeSend: function (data) {
                                        showLoader();
                                    },
                                    success: function (data) {
                                        if (data == 'OK') {
                                            bulkreopendialog.dialog("close");
                                            //hideLoader();
                                            //$.pjax.reload('#globalproject-saved-pajax', $.pjax.defaults);
                                            location.reload();
                                        } else {
                                            hideLoader();
                                            //alert('This action applies to a closed Project.Please select a closed Project to perform this action.');
                                            alert('This action applies to Closed Projects. Please select a Closed Project to perform this action.');
                                        }
                                    },
                                    complete: function (data) {
                                        hideLoader();
                                    }
                                });
                            }
                        }
                    }
                }
            ],
            open: function () {
                bulkreopendialog.find('#selectedtask').html(count);
                bulkreopendialog.find('#alltask').html($('#globalproject-saved-grid .summary b#totalItemCount').text());
                if (count == 0) {
                    bulkreopendialog.find('#rdo_selectedreopen').prop('checked', false);
                    bulkreopendialog.find('label[for="rdo_selectedreopen"]').removeClass('checked');
                    bulkreopendialog.find('#rdo_selectedreopen').prop('disabled', true);
                    bulkreopendialog.find('label[for="rdo_selectedreopen"]').addClass('disabled');
                    bulkreopendialog.find('#rdo_bulkreopen').prop('checked', true);
                    bulkreopendialog.find('label[for="rdo_bulkreopen"]').addClass('checked');

                    bulkreopendialog.find('#rdo_bulkreopen').focus();
                    bulkreopendialog.find('label[for="rdo_selectedreopen"]').removeClass("focus")
                    bulkreopendialog.find('label[for="rdo_bulkreopen"]').addClass("focus");
                } else {
                    bulkreopendialog.find('#rdo_bulkreopen').prop('checked', false);
                    bulkreopendialog.find('label[for="rdo_bulkreopen"]').removeClass('checked');
                    bulkreopendialog.find('#rdo_selectedreopen').prop('disabled', false);
                    bulkreopendialog.find('label[for="rdo_selectedreopen"]').removeClass('disabled');
                    bulkreopendialog.find('#rdo_selectedreopen').prop('checked', true);
                    bulkreopendialog.find('label[for="rdo_selectedreopen"]').addClass('checked');

                    bulkreopendialog.find('label[for="rdo_bulkreopen"]').removeClass("focus");
                    bulkreopendialog.find('#rdo_selectedreopen').focus();
                    bulkreopendialog.find('label[for="rdo_selectedreopen"]').addClass("focus");
                }
                $('input').customInput();
            },
            close: function () {
                $(this).dialog('destroy').remove();
                // Close code here (incidentally, same as Cancel code)
            }
        });
    }
    //});
<?php if ($controller == 'global-projects' && $action == 'save-filter-grid') { ?>
        get_saved_filter();
<?php } ?>
</script>
<noscript></noscript>
