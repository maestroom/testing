<?php
use yii\helpers\Html;
use app\models\User;

$controller=Yii::$app->controller->id;
?>
 <div class="acordian-main">
  <div id="accordion-container">
      <?php if ((new User)->checkAccess(8.01)) { ?>
	<h3 title="System Management">System Management</h3>
		<div>
		<div class="acordian-div">
		  <ul class="sidebar-acordian">
		    <?php if ((new User)->checkAccess(8.023)) {?><li><a href="javascript:void(0);" class="sysModules" data-module="project_sort" class="<?=$active ?>" title="Project Sort Display">Project Sort Display</a></li><?php }?>
		    <?php if ((new User)->checkAccess(8.024)) {?><li><a href="javascript:void(0);" class="sysModules" data-module="managedd" class="<?=$active ?>" title="Manage Dropdown">Manage Dropdown</a></li><?php }?>
		    <li><a href="javascript:void(0);" class="sysModules" data-module="unitconversion" class="<?=$active ?>" title="Unit Conversions">Unit Conversions</a></li>
		    <?php if ((new User)->checkAccess(8.025)) {?><li><a href="javascript:void(0);" class="sysModules" data-module="rebrandsystem" class="<?=$active ?>" title="Rebrand System">Rebrand System</a></li><?php }?>
		    <?php if ((new User)->checkAccess(8.021)) {?><li><a href="javascript:void(0);" class="sysModules" data-module="customwording" class="<?=$active ?>" title="Custom Wording">Custom Wording</a></li><?php }?>
		    <?php if ((new User)->checkAccess(8.02)) {?><li><a href="javascript:void(0);" class="sysModules" data-module="sysupdate" class="<?=$active ?>" title="System Updates">System Updates</a></li><?php }?>
		    <?php if ((new User)->checkAccess(8.027)) {?><li><a href="javascript:void(0);" class="sysModules" data-module="emailsetting" class="<?=$active ?>" title="Email Alerts Configuration">Email Alerts Configuration</a></li><?php }?>
		    <?php if ((new User)->checkAccess(8.029)) {?><li><a href="javascript:void(0);" class="sysModules" data-module="ldapconfig" class="<?=$active ?>"  title="LDAP Configuration">LDAP Configuration</a></li><?php }?>
		    <?php if ((new User)->checkAccess(8.026)) {?><li><a href="javascript:void(0);" class="sysModules" data-module="emailtempconfig" class="<?=$active ?>" title="Email Template Configuration">Email Template Configuration</a></li><?php }?>
		    <?php if ((new User)->checkAccess(8.0292)) {?><li><a href="javascript:void(0);" class="sysModules" data-module="SlaBusinessHrs" class="<?=$active ?>" title="Business Hours">Business Hours</a></li><?php }?>
		    <?php if ((new User)->checkAccess(8.0291)) {?><li><a href="javascript:void(0);" class="sysModules" data-module="SessionTimeoutSetting" class="<?=$active ?>" title="Session Timeout">Session Timeout</a></li><?php }?>
			<?php if ((new User)->checkAccess(8.0293)) {?><li><a href="javascript:void(0);" class="sysModules" data-module="SystemMaintenance" class="<?=$active ?>" title="System Maintenance">System Maintenance</a></li><?php } ?>
		  </ul>
		  </div>
		</div>
	<?php } if (Yii::$app->user->identity->role_id == 0) { ?>
        <h3 title="Report Management">Report Management</h3>
        <div>
            <div class="acordian-div">
                <ul class="sidebar-acordian">
                    <li>
                        <a href="javascript:void(0);" class="reportModules" data-module="field-type" title="Field Types">Field Types</a>
                    </li>
                    <li>
                         <a href="javascript:void(0);" class="reportModules" data-module="field-operator" title="Field Operators">Field Operators</a>
                    </li>
                    <li>
                        <a href="javascript:void(0);" class="reportModules" data-module="field-relationship" title="Field Relationships">Field Relationships</a>
                    </li>
                    <li>
                        <a href="javascript:void(0);" class="reportModules" data-module="calculation-function" title="Field Functions">Field Functions</a>
                    </li>
                    <li>
                        <a href="javascript:void(0);" class="reportModules" data-module="field-calculation" title="Field Calculations">Field Calculations</a>
                    </li>
                    <li>
                        <a href="javascript:void(0);" class="reportModules" data-module="report-format" title="Report Formats">Report Formats</a>
                    </li>
                    <li>
                        <a href="javascript:void(0);" class="reportModules" data-module="chart-format" title="Report Chart Formats">Report Chart Formats</a>
                    </li>
                    <li>
                        <a href="javascript:void(0);" class="reportModules" data-module="chart-display-by" title="Report Chart Display By">Report Chart Display By</a>                            
                    </li>
                    <li>
                        <a href="javascript:void(0);" class="reportModules" data-module="report-type" title="Report Types">Report Types</a>                            
                    </li>
                    <!--<li>
                        <a href="javascript:void(0);" class="reportModules" data-module="calculation-sp" title="Calculation Stored Procedure">Calculation Stored Procedure</a>
                    </li>-->
                    <!--<li>
                        <a href="javascript:void(0);" class="reportModules" data-module="field-lookup">Field Lookup</a>                            
                    </li>-->                    
                </ul>
            </div>
        </div>
	<?php }?>
	<?php if ((new User)->checkAccess(8.035)) {?>
	<h3 title="Form Field Management">Form Field Management</h3>
	<div>
         <div class="acordian-div">
             <ul class="sidebar-acordian">
                 <?php if ((new User)->checkAccess(8.0351)) {?>
					<li>
						<a href="javascript:void(0);" class="FormfieldModules" data-module="system-form" title="System Form">System Forms</a>
					</li>
                 <?php }?>
				 <?php if ((new User)->checkAccess(8.0352)) {?>
				 	<li>
					 	<a href="javascript:void(0);" class="sysModules" data-module="custodianforms" class="<?=$active ?>" title="Custodian Interview Forms">Custodian Interview Forms</a>
					</li>
				 <?php }?>
              </ul>
         </div>
    </div>   
	<?php } if ((new User)->checkAccess(8.03)) {?>
	<h3 title="Workflow Management">Workflow Management</h3>
		<div>
			<div class="acordian-div">
				 <ul class="sidebar-acordian">
				 	<?php if (in_array(1, $roleTypes) || $roleId=='0') { ?><li><a href="javascript:void(0);" class="workflowModules" data-module="CaseManagerTeam" class="<?=$active ?>" title="Case Manager Team">Case Manager Team</a></li><?php }?>
		  			<?php if (in_array(2, $roleTypes) || $roleId=='0') { ?><li><a href="javascript:void(0);" class="workflowModules" data-module="OperationTeams" class="<?=$active ?>" title="Operation Teams">Operation Teams</a></li><?php }?>
					<?php if ((new User)->checkAccess(8.031)) {?><li><a href="javascript:void(0);" class="workflowModules" data-module="WorkflowTemplates" class="<?=$active ?>" title="Workflow Templates">Workflow Templates</a></li><?php }?>
				 </ul>
			</div>
		</div>
	<?php } ?>
	<?php if ((new User)->checkAccess(8.04)) {?>
	<h3 title="Client Management">Client Management</h3>
		<div>
			<div class="acordian-div">
				<ul class="sidebar-acordian">
				 	<li><a href="javascript:void(0);" class="clientModules" data-module="ClientManagement" class="<?=$active ?>" title="Display Client Management">Display Client Management</a></li>
		  		</ul>
			</div>
		</div>
	<?php } ?>
	<?php if ((new User)->checkAccess(8.05) && ($roleId == '0' || in_array(1,$roleTypes))) { ?>
	<h3 title="Case Management">Case Management</h3>
		<div>
			<div class="acordian-div">
				<ul class="sidebar-acordian">
				 	<li><a href="javascript:void(0);" class="caseModules" data-module="CaseManagement" class="<?=$active ?>" title="Display Case Management">Display Case Management</a></li>
		  		</ul>
			</div>	
		</div>
	<?php } ?>
	<?php if ((new User)->checkAccess(8.06)) {?>
	<h3 title="User Management">User Management</h3>
		<div>
			<div class="acordian-div">
				<ul class="sidebar-acordian">
				 	<?php if ((new User)->checkAccess(8.061)) {?><li><a href="javascript:void(0);" class="userModules" data-module="ManageRoles" class="<?=$active ?>" title="Manage Roles">Manage Roles</a></li><?php } ?>
		  			<?php if ((new User)->checkAccess(8.062)) {?><li><a href="javascript:void(0);" class="userModules" data-module="ManageUsers" class="<?=$active ?>" title="Manage Users">Manage Users</a></li><?php } ?>
		  			<?php if ((new User)->checkAccess(8.062)) {?><li><a href="javascript:void(0);" class="userModules" data-module="ManageUserAccess" class="<?=$active ?>" title="Manage User Access">Manage User Access</a></li><?php } ?>
		    	 </ul>
			</div>
		</div>
	<?php } ?>
  </div>
</div>
<script type="text/javascript"> 
var accordionOptions = {
	 heightStyle: 'fill',clearStyle: true,autoHeight: false, icons: { "header": "fa fa-caret-right pull-right", "activeHeader": "fa fa-caret-down pull-right" }
,create: function( event, ui ) {

//$("#accordion-container h3 span").removeClass('ui-accordion-header-icon');
$("#accordion-container h3 span").removeClass('ui-icon');

}};
	$("#accordion-container").accordion(accordionOptions);

	

	$("#accordion-container h3").bind("click", function() {
	   var str = $('#page-title span').text();
	   if($(this).text() == 'System Management'){
		  	$(".sysModules").removeClass('active');
	   }	
	   if($(this).text() == 'Workflow Management'){
			$(".workflowModules").removeClass('active');
	   }
	   if($(this).text() == 'Client Management'){
			$(".clientModules").removeClass('active');
	   }
	   if($(this).text() == 'Case Management'){
			$(".caseModules").removeClass('active');
	   }
	   if($(this).text() == 'User Management'){
			$(".userModules").removeClass('active');
	   }
           if($(this).text() == 'Report Management'){
			$(".reportModules").removeClass('active');
	   }
	});
 	$(window).resize(function(){
	 	// update accordion height
 		$('#accordion-container').accordion("refresh");
		 $( "#accordion-container" ).accordion( "destroy" );
		$("#accordion-container" ).accordion(accordionOptions);
 	});
</script>
<noscript></noscript>
