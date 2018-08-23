<?php
use yii\helpers\Html;
use app\models\User;
$controller=Yii::$app->controller->id;
$action=Yii::$app->controller->action->id;
$resuest = Yii::$app->request->get();

$params = Yii::$app->request->get();
$isTeam=false;
if(isset($resuest['team_id']) && isset($resuest['team_loc']) && $controller=='track'){
	$isTeam=true;
}
$accordianIndex = 'team_overview_acc';
if($controller == 'team-projects' || $isTeam)
	$accordianIndex = 'team_project_acc';	
else if($controller == 'team-overview')
	$accordianIndex = 'team_overview_acc';
else if($controller == 'team-documents')
	$accordianIndex = 'team_documents_acc';
else if($controller == 'team-tasks')
	$accordianIndex = 'team_tasks_acc';

?>
 <div class="acordian-main">
			 <div id="accordion-container">
			  <h3 id="team_overview_acc" title="Team Overview">Team Overview</h3>
			  <div>
			   <div class="acordian-div">
			   <ul class="sidebar-acordian">
				<li><a href="javascript:void(0);" class="myCaseModules <?= ($action=='taskassignments')?'active':''; ?>" onclick="task_assignments();" data-module="task_assignment"  title="Task Assignments - Active">Task Assignments - Active</a></li>
                <li><a href="javascript:void(0);" class="myCaseModules <?= ($action=='taskassigncompleted')?'active':''; ?>" onclick="task_assignments_completed();" data-module="task_assignment"  title="Task Assignments - Complete">Task Assignments - Complete</a></li>
				<li><a href="javascript:void(0);" class="myCaseModules <?= ($action=='assignbyprojectsize')?'active':''; ?>" onclick="assignby_projectsize();" data-module="assignby_projectsize"  title="Assignments by Project Size">Assignments by Project Size</a></li>
				<li><a href="javascript:void(0);" class="myCaseModules <?= ($action=='taskdistribute')?'active':'';  ?>" onclick="team_distribute();" data-module="task_distribution"  title="Task Distribution">Task Distribution</a></li>
				<li><a href="javascript:void(0);" class="myCaseModules <?= ($action=='followupdistribute')?'active':''; ?>" onclick="followup_distribute();" data-module="followup_distribution"  title="Follow-up Distribution">Follow-up Distribution</a></li>
				<?php if ((new User)->checkAccess(5.073)) { ?><li><a href="javascript:void(0);" class="myTeamModules <?= ($controller=='summary-comment' && $action=='index' && isset($resuest['team_id']))?'active':''; ?>" data-module="team_summary_comment" title="Post Summary Comments">Post Summary Comments</a></li><?php }?>
				
			   </ul>
			   </div> 
			  </div>
			  <?php if ((new User)->checkAccess(5.01)){ ?>
			  <h3 id="team_project_acc" title="Team Projects">Team Projects</h3>
			  <div>
			   <div class="acordian-div activate">
			    <ul class="sidebar-acordian">
			    			<li><a href="javascript:void(0);" class="myTeamModules <?= ($controller == 'team-projects' && $action=='index')?'active':''; ?>" data-module="list_projects" title="Display Projects">Display Projects</a></li>
			    		<?php if($controller == 'team-projects' && ($action=='index' ||  $action=='instrution')){ ?>
			    			<li><a href="javascript:void(0);" class="myTeamModules <?= ($controller == 'team-projects' && $action=='instrution')?'active':''; ?>" data-module="project_instructions" title="Project Instructions">Project Instructions</a></li>
						<?php } ?>
						<?php
							if ((new User)->checkAccess(5.02)) {
							 if(($controller == 'team-projects' || $controller == 'track') && ($action=='index')) { ?>
							<li><a href="javascript:void(0);" class="myTeamModules <?= ($controller == 'track' && $action=='index')?'active':''; ?>" data-module="track_project" title="Track Project">Track Project</a></li>
							<?php } } ?>
							<?php if ((new User)->checkAccess(5.07)) { if($controller == 'team-projects' && ($action=='index' ||  $action=='post-comment')){ ?>
								<li><a href="javascript:void(0);" class="myTeamModules <?php if($controller == 'team-projects' && $action == 'post-comment') { ?> active <?php }?>" data-module="post_project_comment" title="Post Project Comment">Post Project Comment</a></li>
							<?php } }?>
						<?php 
							if ((new User)->checkAccess(5.012)) {
								if($controller == 'team-projects' && ($action=='index')){ ?>
									<li><a href="javascript:void(0);" class="myTeamModules" data-module="apply_teampriority" title="Apply Team Priority">Apply Team Priority</a></li>
						<?php } }?>
				</ul>
			   </div>
			  </div>
			  <?php }  if ((new User)->checkAccess(5.014)){ ?>
			  
			  <h3 id="team_tasks_acc" title="Team Tasks">Team Tasks</h3>
			  <div>
			   <div class="acordian-div">
			    <ul class="sidebar-acordian">
				<li><a href="javascript:void(0);" class="myTeamModules <?= ($controller == 'team-tasks' && $action=='index')?'active':''; ?>" data-module="team_tasks" title="Display Team Tasks">Display Team Tasks</a></li>
				<?php if ((new User)->checkAccess(5.0142)){
					if($controller == 'team-tasks' && ($action=='index')){ ?>
				<li class="assignedonly_content"><a href="javascript:void(0);" class="myTeamModules" data-module="transition_tasks" title="Bulk Transition Tasks">Bulk Transition Tasks</a></li>
				<?php } } if ((new User)->checkAccess(5.041)){
					if($controller == 'team-tasks' && ($action=='index')){ ?>
						<li class="assignedonly_content" id="bulk_transfer_task_location" style="display:none;"><a href="javascript:void(0);" class="myTeamModules" data-module="transfer_location_tasks" title="Bulk Transfer Task Location">Bulk Transfer Task Location</a></li>
				<?php } }if ((new User)->checkAccess(5.0143)){
					if($controller == 'team-tasks' && ($action=='index')){  ?>
				<li class="assignedonly_content"><a href="javascript:void(0);" class="myTeamModules" data-module="unassign_tasks" title="Bulk UnAssign Tasks">Bulk UnAssign Tasks</a></li>
				<?php } } if ((new User)->checkAccess(5.0141)){ ?>
					<li class="unassignedonly_content"><a href="javascript:void(0);" class="myTeamModules" data-module="assign_tasks" title="Bulk Assign Tasks">Bulk Assign Tasks</a></li>
				<?php } if ((new User)->checkAccess(5.0144)){ ?>
				<?php if($controller == 'team-tasks' && ($action=='index')){ ?>
				<li><a href="javascript:void(0);" class="myTeamModules" data-module="bulk_complete_tasks" id="teamtaskbulkcomplete" title="Bulk Complete Tasks">Bulk Complete Tasks</a></li>
				<?php } } ?>
				</ul>
			   </div>
			  </div>
			  <?php }   if ((new User)->checkAccess(5.08)){?>
			  <h3 id="team_documents_acc" title="Team Documents">Team Documents</h3>
			  <div>
			   <div class="acordian-div">
			    <ul class="sidebar-acordian">
                <li><a href="javascript:void(0);" class="myCaseModules <?= ($controller == 'team-documents' && $action=='index')?'active':''; ?>" data-module="list_document" onclick="list_teamdocument();" title="Display Documents">Display Documents</a></li>
                <?php if ((new User)->checkAccess(5.09)){ ?>                
				<li><a href="javascript:void(0);" id="create_folder" class="myCaseModules" data-module="create_folder"  title="Create Folder">Create Folder</a></li>
				<?php } if ((new User)->checkAccess(5.10)){ ?>
				<li><a href="javascript:void(0);" id="upload_file" class="myCaseModules" data-module="upload_file"  title="Upload File">Upload File</a></li>
				<?php } ?>
				<li><a href="javascript:void(0);" id="permission_folder" class="myCaseModules" data-module="edit_permissions"  title="Edit Permissions">Edit Permissions</a></li>
				<li><a href="javascript:void(0);" id="rename_folder" class="myCaseModules" data-module="rename"  title="Rename">Rename</a></li>
				<li><a href="javascript:void(0);" id="delete_folder" class="myCaseModules" data-module="delete"  title="Delete">Delete</a></li>
				<li><a href="javascript:void(0);" id="copy_folder" class="myCaseModules" data-module="copy"  title="Copy">Copy</a></li>
				<li><a href="javascript:void(0);" id="cut_folder"  class="myCaseModules" data-module="cut"  title="Cut">Cut</a></li>
				<li><a href="javascript:void(0);" id="paste_folder" class="myCaseModules" data-module="paste"  title="Paste">Paste</a></li>
			   </ul>
			   </div>
			  </div>
			<?php } ?>
			</div>
			</div>
<script type="text/javascript">

var accordionOptions = {
	heightStyle: 'fill',clearStyle: true,autoHeight: false, active:$('#accordion-container h3').index($('#<?=$accordianIndex?>'))
};
var accordionOptions = {
	 heightStyle: 'fill',clearStyle: true,autoHeight: false, icons: { "header": "fa fa-caret-right pull-right", "activeHeader": "fa fa-caret-down pull-right" }
,create: function( event, ui ) {

//$("#accordion-container h3 span").removeClass('ui-accordion-header-icon');
$("#accordion-container h3 span").removeClass('ui-icon');

}, active:$('#accordion-container h3').index($('#<?=$accordianIndex?>'))};
//jQuery(document).ready(function($) { 
	$("#accordion-container" ).accordion(accordionOptions);
 	$(window).resize(function(){
 	 	// update accordion height
 		$('#accordion-container').accordion("refresh");
		 $( "#accordion-container" ).accordion( "destroy" );
		$("#accordion-container" ).accordion(accordionOptions);
 	});
       $("#accordion-container h3").bind("click", function() {
	   var str = $('#page-title span').text();
           
	   if($(this).text() == 'System Management'){
           }
       });  
// });
</script>
<noscript></noscript>
