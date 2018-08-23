<?php
use yii\helpers\Html;
use app\models\User;
$controller=Yii::$app->controller->id;
$action=Yii::$app->controller->action->id;
$resuest = Yii::$app->request->get();
$isCase=false;
if(isset($resuest['case_id']) && $controller=='track'){
	$isCase=true;
}
$accordianIndex = 'case_overview_acc';
if(($controller == 'case-projects' || $controller == 'project') || $isCase)
	$accordianIndex = 'case_project_acc';	
else if($controller == 'case-overview')
	$accordianIndex = 'case_overview_acc';
else if(($controller == 'case' && $action == 'case-summary') || ($controller == 'summary-comment' && $action == 'index'))
	$accordianIndex = 'case_summary_acc';
else if($controller == 'case-custodians')
	$accordianIndex = 'case_custodians_acc';
else if($controller == 'case-production')
	$accordianIndex = 'case_productions_acc';
else if($controller == 'case-budget')
	$accordianIndex = 'case_budget_acc';	
else if($controller == 'case-documents')
	$accordianIndex = 'case_documents_acc';

$info ='';
$OS = array("Windows"=>"/Windows/i","Linux"=>"/Linux/i","Unix"=>"/Unix/i","Mac"=>"/Mac/i");
$agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : NULL;
foreach($OS as $key => $value){
        if(preg_match($value, $agent)){
                $info = $key;
                break;
        }
}
?>
<div class="acordian-main">
			 <div id="accordion-container">
			 

			  <?php if ((new User)->checkAccess(4.13)) {?>
			  <h3 id="case_overview_acc" title="Case Overview">Case Overview</h3>
			  <div>
			   <div class="acordian-div">
			   <ul class="sidebar-acordian">
				<li><a href="javascript:void(0);" class="myCaseModules <?= ($action=='total-projects')?'active':''; ?>" data-module="total_projects" title="Total Projects">Total Projects</a></li>
				<li><a href="javascript:void(0);" class="myCaseModules <?= ($action=='total-media-projects')?'active':''; ?>" data-module="total_media" title="Total Media">Total Media</a></li>
				<li><a href="javascript:void(0);" class="myCaseModules <?= ($action=='total-media-unit-size')?'active':''; ?>" data-module="mediatype_by_size" title="Media Type By Size">Media Type By Size</a></li>
				<li><a href="javascript:void(0);" class="myCaseModules <?= ($action=='media-by-custodian')?'active':''; ?>" data-module="media_by_custodian" title="Media By Custodian">Media By Custodian</a></li>
				<li><a href="javascript:void(0);" class="myCaseModules <?= ($action=='production-by-type')?'active':''; ?>" data-module="total_productions" title="Total Productions">Total Productions</a></li>
				<li><a href="javascript:void(0);" class="myCaseModules <?= ($action=='production-producing-parties')?'active':''; ?>" data-module="producing_parties" title="Productions Producing Parties">Productions Producing Parties</a></li>
			   </ul>
			   </div> 
			  </div>
			  <?php }?>
			  <?php if ((new User)->checkAccess(4.121)) {?>
			  <h3 id="case_summary_acc" title="Case Summary">Case Summary</h3>
			  <div>
			   <div class="acordian-div">
			   <ul class="sidebar-acordian">
			   	<li><a href="javascript:void(0);" class="myCaseModules <?= ($action=='case-summary')?'active':''; ?>" data-module="case_summary" title="Case Summary">Display Case Summary</a></li>
			    <?php if ((new User)->checkAccess(4.0803)) {?><li><a href="javascript:void(0);" class="myCaseModules <?= ($controller=='summary-comment' && $action=='index' && isset($resuest['case_id']))?'active':''; ?>" data-module="case_summary_comment" title="Post Summary Comments">Post Summary Comments</a></li><?php }?>
			   </ul>
			   </div> 
			  </div>
			  <?php }?>
			  <?php if ((new User)->checkAccess(4.001)){ ?>
			  <h3 id="case_custodians_acc" title="Case Custodians">Case Custodians</h3>
			  <div>
			   <div class="acordian-div">
			    <ul class="sidebar-acordian">
				<li><a href="javascript:void(0);" class="myCaseModules <?= ($controller == 'case-custodians' && $action=='index')?'active':''; ?>" data-module="list_custodian" id="list_custodian" title="Display Custodians">Display Custodians</a></li>
				<!--  
				<li><a href="javascript:void(0);" class="myCaseModules" data-module="edit_custodian">Edit Custodian</a></li>
				<li><a href="javascript:void(0);" class="myCaseModules" data-module="delete_custodian">Delete Custodian</a></li>
				-->
				<?php if($controller == 'case-custodians' && $action == 'index') { ?>
				<li><a href="javascript:void(0);" class="myCaseModules" data-module="interview_form" title="Custodians Interview Form">Interview Form</a></li>
				<?php  if((new User)->checkAccess(4.005)){ ?>
				<li><a href="javascript:void(0);" class="myCaseModules" data-module="pdf_interview_form" title="Download PDF Interview Form">PDF Interview Form(s)</a></li>
				<?php } } ?>
			   </ul>
			   </div>
			  </div>
			  <?php }?>
			  <?php if ((new User)->checkAccess(4.006)) {?>
			  <h3 id="case_productions_acc" title="Case Productions">Case Productions</h3>
			  <div>
			   <div class="acordian-div">
			    <ul class="sidebar-acordian">
				<li><a href="javascript:void(0);" class="myCaseModules <?= ($controller == 'case-production' && $action=='index')?'active':''; ?>" data-module="list_production" onclick="list_caseproduction();" title="Display Productions">Display Productions</a></li>	
					
				<?php if($controller == 'case-production' && $action == 'index') { ?>
					
					<?php if ((new User)->checkAccess(4.0074)) {?>
				<li><a href="javascript:void(0);" class="myCaseModules" onclick="AddProductionAttorneyNotes();" title="Add Attorney Notes">Add Attorney Notes</a></li>
				<?php } ?>
				
				  <?php if($info=='Windows') { ?> <li><a href="javascript:void(0);" class="myCaseModules" onclick="ProductionShortcut();" title="Save Production Shortcut">Save Production Shortcut</a></li> <?php } ?>
					
					<?php if ((new User)->checkAccess(4.0073)) {?>
						<li><a href="javascript:void(0);" class="myCaseModules" onclick="editCaseMediaProductionDetail();" title="Media - Update Production Bates">Media - Update Production Bates</a></li>
					<?php } ?>
				<?php if ((new User)->checkAccess(4.00741)) {?>
				<li><a href="javascript:void(0);" class="myCaseModules" onclick="CaseProductionMediaHold();" title="Media - Hold Media">Media - Hold Media</a></li>
				<?php }?>
				
				
				
                
				<?php }?>
			   </ul>
			   </div>
			  </div>
			  <?php }?>
			  <?php if ((new User)->checkAccess(4.01)) {?>
			  <h3 id="case_project_acc" title="Case Projects">Case Projects</h3>
			  <div>
			   <div class="acordian-div activate">
			    <ul class="sidebar-acordian">
			    	<?php if($controller == 'case-projects' && $action == 'load-canceled-projects' && (new User)->checkAccess(4.0811)) { ?>
						<li><a href="javascript:loadCanceledProjects();" class="myCaseModules <?= ($controller == 'case-projects' && $action=='load-canceled-projects')?'active':''; ?>" data-module="list_projects" title="Display Canceled  Projects">Display Canceled  Projects</a></li>
						<li><a href="javascript:void(0);" class="myCaseModules" data-module="uncancel_projects" title="UnCancel Projects">UnCancel Projects</a></li>
					<?php }else if($controller == 'case-projects' && $action == 'change-project') { ?>
						<li><a href="javascript:void(0);" class="myCaseModules <?= ($controller == 'case-projects' && $action=='index')?'active':''; ?>" data-module="list_projects" title="Display Projects">Display Projects</a></li>
					<?php }else if($controller == 'case-projects' && $action == 'load-saved-projects') { ?>
						<li><a href="javascript:void(0);" class="myCaseModules <?= ($controller == 'case-projects' && $action=='index')?'active':''; ?>" data-module="list_projects" title="Display Projects">Display Projects</a></li>
						<li><a href="javascript:loadSavedProjects();" class="myCaseModules <?= ($controller == 'case-projects' && $action=='load-saved-projects')?'active':''; ?>" data-module="list_projects" title="Display Saved Projects">Display Saved Projects</a></li>
					<?php } else if($controller == 'case-projects' && $action == 'load-closed-projects' && (new User)->checkAccess(4.081)) { ?>
						<li><a href="javascript:loadClosedProjects();" class="myCaseModules <?= ($controller == 'case-projects' && $action=='load-closed-projects')?'active':''; ?>" data-module="list_projects" title="Display Closed Projects">Display Closed Projects</a></li>
						<li><a href="javascript:void(0);" class="myCaseModules" data-module="reopen_projects" title="ReOpen Projects">ReOpen Projects</a></li>
			    	<?php } else { ?>
			    		<li><a href="javascript:void(0);" class="myCaseModules <?= ($controller == 'case-projects' && $action=='index')?'active':''; ?>" data-module="list_projects" title="Display Projects">Display Projects</a></li>
						<?php if((new User)->checkAccess(4.01)) {?>
						<li><a href="javascript:loadSavedProjects();" class="myCaseModules <?= ($controller == 'case-projects' && $action=='load-saved-projects')?'active':''; ?>" data-module="list_projects" title="Display Saved Projects">Display Saved Projects</a></li>
						<li><a href="javascript:loadCanceledProjects();" class="myCaseModules <?= ($controller == 'case-projects' && $action=='load-canceled-projects')?'active':''; ?>" data-module="list_projects" title="Display Canceled  Projects">Display Canceled  Projects</a></li>
						<li><a href="javascript:loadClosedProjects();" class="myCaseModules <?= ($controller == 'case-projects' && $action=='load-closed-projects')?'active':''; ?>" data-module="list_projects" title="Display Closed Projects">Display Closed Projects</a></li>
						<?php }?>
			    		<?php if(($controller == 'case-projects' && $action == 'index') || ($controller == 'case-projects' && $action == 'edit')) { ?>
						<?php } ?>
						<?php if(($controller == 'case-projects' && $action == 'index') || ($controller == 'track' && $action == 'index')) { ?>
						<?php if ((new User)->checkAccess(4.03)){?>	
						<li>
						<a href="javascript:void(0);" class="myCaseModules" data-module="track_project" title="Track Project">Track Project</a>
						</li>
						<?php }  }?>
						
						<?php 
						if ((new User)->checkAccess(4.08)){
						if(($controller == 'case-projects' && $action == 'index') || ($controller == 'case-projects' && $action == 'post-comment')) { ?>
							<li><a href="javascript:void(0);" class="myCaseModules <?php if($controller == 'case-projects' && $action == 'post-comment') { ?> active <?php }?>" data-module="post_project_comment" title="Post Project Comment">Post Project Comment</a></li>
						<?php } } 
						if(($controller == 'case-projects' && $action == 'index')) {
						if ((new User)->checkAccess(4.0811)){ ?>
							<li><a href="javascript:void(0);" class="myCaseModules" data-module="cancel_project" title="Cancel Project">Cancel Project</a></li>
						<!-- <li><a href="javascript:void(0);" class="myCaseModules" data-module="remove_project">Remove Project</a></li> -->
						<?php } }
						if(($controller == 'case-projects' && $action == 'index')) {
						if ((new User)->checkAccess(4.081)){ ?>
							<li id="close_project_process" style="display:none;"><a href="javascript:void(0);" class="myCaseModules" data-module="close_projects" title="Close Projects">Close Projects</a></li>
						<?php } } ?>
				 	<?php } ?>
			    </ul>
			   </div>
			  </div>
			  <?php }?>
			  <?php if ((new User)->checkAccess(4.09)) {?>
			  <h3 id="case_budget_acc" title="Case Budget">Case Budget</h3>
			  <div>
			   <div class="acordian-div">
			    <ul class="sidebar-acordian">
				<li><a href="javascript:void(0);" class="myCaseModules <?= ($controller == 'case-budget' && $action=='index')?'active':''; ?>" data-module="casebudget_chart" title="Display Case Budget">Display Case Budget</a></li>
				<!--<li><a href="javascript:void(0);" class="myCaseModules" data-module="casebudget_pdf">PDF</a></li>-->
			   </ul>
			   </div>
			  </div>
			  <?php }?>
			  <?php if ((new User)->checkAccess(4.10)) {?>
			  <h3 id="case_documents_acc" title="Case Documents">Case Documents</h3>
			  <div>
			   <div class="acordian-div">
			    <ul class="sidebar-acordian">
                                <?php if($action !='projectdoc'){ ?>
                                    <li><a href="javascript:void(0);" class="myCaseModules <?= ($controller == 'case-documents' && $action=='index')?'active':''; ?>" data-module="list_document" onclick="list_casedocument();" title="Display Documents">Display Documents</a></li>
                                <?php } ?>
				<?php if($controller == 'case-documents' && $action == 'index') { ?>
						<?php if((new User)->checkAccess(4.11)){ ?>
                                <li><a href="javascript:void(0);" id="create_folder" class="myCaseModules" data-module="create_folder" title="Create Folder">Create Folder</a></li>
						<?php }  if((new User)->checkAccess(4.12)){ ?>
				<li><a href="javascript:void(0);" id="upload_file" class="myCaseModules" data-module="upload_file" title="Upload File">Upload File</a></li>
				<?php } ?>
				<li><a href="javascript:void(0);" id="permission_folder" class="myCaseModules" data-module="edit_permissions"  title="Edit Permissions">Edit Permissions</a></li>
				<li><a href="javascript:void(0);" id="rename_folder"  class="myCaseModules" data-module="rename" title="Rename">Rename</a></li>
				<li><a href="javascript:void(0);" id="delete_folder" class="myCaseModules" data-module="delete" title="Delete">Delete</a></li>
				<li><a href="javascript:void(0);" id="copy_folder" class="myCaseModules" data-module="copy" title="Copy">Copy</a></li>
				<li><a href="javascript:void(0);" id="cut_folder" class="myCaseModules" data-module="cut" title="Cut">Cut</a></li>
                <li><a href="javascript:void(0);" id="paste_folder" class="myCaseModules" data-module="paste" title="Paste">Paste</a></li>
				<?php }
                 else if($controller == 'case-documents'){ ?>
                                <li><a href="javascript:void(0);" class="myCaseModules <?= ($controller == 'case-documents' && $action=='projectdoc' && ($resuest['type']== '' || $resuest['type']=='I'))?'active':''; ?>" data-module="list_document" onclick="list_projectdocument('I');" title="Instruction Documents">Instruction Documents</a></li>
                                <li><a href="javascript:void(0);" class="myCaseModules <?= ($controller == 'case-documents' && $action=='projectdoc' && $resuest['type']== 'IN')?'active':''; ?>" data-module="list_document" onclick="list_projectdocument('IN');" title="Instruction Notes Documents">Instruction Notes Documents</a></li>
                                <li><a href="javascript:void(0);" class="myCaseModules <?= ($controller == 'case-documents' && $action=='projectdoc' && $resuest['type']== 'T')?'active':''; ?>" data-module="list_document" onclick="list_projectdocument('T');" title="ToDo Documents">ToDo Documents</a></li>
                                <li><a href="javascript:void(0);" class="myCaseModules <?= ($controller == 'case-documents' && $action=='projectdoc' && $resuest['type']== 'TD')?'active':''; ?>" data-module="list_document" onclick="list_projectdocument('TD');" title="Task Details Documents">Task Details Documents</a></li>
                                <li><a href="javascript:void(0);" class="myCaseModules <?= ($controller == 'case-documents' && $action=='projectdoc' && $resuest['type']== 'C')?'active':''; ?>" data-module="list_document" onclick="list_projectdocument('C');" title="Comment Documents">Comment Documents</a></li>
                                <?php } ?>
			   </ul>
			   </div>
			  </div>
			  <?php }?>
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

	$("#accordion-container").accordion(accordionOptions);
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
 //});
</script>
<noscript></noscript>
