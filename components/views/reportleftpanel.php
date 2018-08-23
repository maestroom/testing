<?php
use yii\helpers\Html;
use app\models\User;
$controller=Yii::$app->controller->id;
$action=Yii::$app->controller->action->id;
$accordianIndex = 0;
if($controller == 'saved-report'){
	$accordianIndex = 0;		
}else if($controller == 'custom-report'){
	$accordianIndex = 1;	
}
else if($controller == 'activity-report'){
	$accordianIndex = 2;	
}
?>
 <div class="acordian-main">
    <div id="accordion-container">
	<?php if((new User)->checkAccess(11.2)){ ?>    
        <h3 data-index = '0' class="get_index" id="get_report" title="Saved Reports">Saved Reports</h3>
            <div>
				<div class="acordian-div">
					<ul class="sidebar-acordian" id="saved_reports_ul">
						<li><?= Html::a('Display Saved Reports','@web/index.php?r=saved-report/index',['title'=>'Display Reports','class'=>(($controller=='saved-report' && $action=="index")?"active":'')]) ?></li>		
						<?php if($controller == 'saved-report'){ ?>
							<li><a href="javascript:void(0);" class="RunReport <?php if($action=="run-savereport"){?>active<?php }?>" data-module="saved-reports" onClick = "return RunSavedReport();" title='Run Saved Report'>Run Saved Report</a></li>
							<li><?php // Html::a('Edit Report Criteria','@web/index.php?r=saved-report/run-report',['title'=>'Display Reports','class'=>(($controller=='saved-report' && $action=="")?"active":'')]) ?></li>						
							<?php if((new User)->checkAccess(11.3)){ ?><li><a href="javascript:void(0);" class="EditReport <?php if($action=="edit-savereport"){?>active<?php }?>" data-module="saved-reports" onClick = "return edit_report_user_criteria();" title='Edit Saved Report'>Edit Saved Report</a></li><?php }?>
							<?php if((new User)->checkAccess(11.4)){ ?><li><a href="javascript:void(0);" class="EditReport" data-module="saved-reports" onClick = "return edit_report_user_saved();" title='Edit Saved Report Access'>Edit Saved Report Access</a></li><?php }?>
							<?php if((new User)->checkAccess(11.5)){ ?><li><a href="javascript:void(0);" class="DeleteReport" data-module="saved-reports" onClick = "return DeleteSavedReport();" title='Delete Saved Report(s)'>Delete Saved Report(s)</a></li><?php }?>
						<?php } ?>
					</ul>
				</div>
            </div> 
    <?php } if((new User)->checkAccess(11.1)){ ?>    
        <h3 data-index = '1' class="get_index" title="Create Reports">Create Reports</h3>
            <div>
				<div class="acordian-div">
					<ul class="sidebar-acordian" id="custom_reports_ul">
						<?php $active="";
							if($controller=='custom-report' && $action=="index" && $_GET["flag"]!='edit' && $_GET["flag"]!='run' ){
								$active="active";
							}
						?>
						<li><?= Html::a('Create Report','@web/index.php?r=custom-report/index',['title'=>'Add Custom Report','class'=>$active]) ?></li>
						<?php if(isset($_GET['flag']) && ($_GET['flag']=='edit' || $_GET['flag']=='run')) {?>
							<li><?= Html::a('Edit Report','javascript:void(0);',['title'=>'Edit Custom Report','class'=>(($controller=='custom-report' && $action=="index")?"active":'')]) ?></li>
						<?php }?>
						
					</ul>
				</div>
            </div> 
    <?php } if((new User)->checkAccess(11.6)){ ?>	
		<h3 data-index = '2' class="get_index" title="Activity Reports">Activity Reports</h3>
        <div>
			<div class="acordian-div">
		  		<ul class="sidebar-acordian">
            		<li><?= Html::a('Project Transaction Activity','@web/index.php?r=activity-report/',['title'=>'Project Transaction Activity','class'=>(($controller=='activity-report' && $action=="index")?"active":'')]) ?></li>
		  		</ul>
			</div>
        </div>
      <?php } /* if((new User)->checkAccess(75.3)){ ?>
		<!--<h3 data-index = '1' class="get_index">Status Reports</h3>-->
            <!--<div>
				<div class="acordian-div">
				  <ul class="sidebar-acordian">
					<li>
					<?= Html::a('Projects by Client/Cases','@web/index.php?r=status-report/requestbyclientcase',['title'=>'Projects by Client/Cases','class'=>(($controller=='status-report' && $action=="requestbyclientcase")?"active":'')]) ?></li>
					<li>
					<?= Html::a('Projects by Team Services','@web/index.php?r=status-report/projectbyteamservice',['title'=>'Projects by Team Services','class'=>(($controller=='status-report' && $action=="projectbyteamservice")?"active":'')]) ?></li>
					<li>
					<?= Html::a('ToDo Follow-up items by Service','@web/index.php?r=status-report/todofollowitembyteam',['title'=>'ToDo Follow-up items by Service','class'=>(($controller=='status-report' && $action=="todofollowitembyteam")?"active":'')]) ?></li>
					<li>
					<?= Html::a('ToDo Follow-up items by Duration','@web/index.php?r=status-report/todofollowitembyduration',['title'=>'ToDo Follow-up items by Duration','class'=>(($controller=='status-report' && $action=="todofollowitembyduration")?"active":'')]) ?></li>
				  </ul>
				</div>
            </div>--> 
        <?php } if((new User)->checkAccess(75.4)){ ?>
        <!--<h3 data-index = '3' class="get_index">Processing Reports</h3>-->
            <!--<div>
				<div class="acordian-div">
					<ul class="sidebar-acordian">
						<li <?php if($controller=='ProcessingReport' && $action=="data-processing"){ $active="active"; } ?> >
	                	<?= Html::a('Data Processed by Client/Case','@web/index.php?r=processing-report/data-processing',['title'=>'Data Processed by Client/Case','class'=> $active]) ?></li>
	                	<li <?php if($controller=='ProcessingReport' && $action=="data-service"){ $active="active"; } ?> >
	                	<?= Html::a('Data Processed by Service','@web/index.php?r=processing-report/data-service',['title'=>'Data Processed by Service','class'=> $active]) ?></li>
					</ul>
				</div>
            </div>-->  
         <?php } if((new User)->checkAccess(75.5)){ ?>
          <h3 data-index = '3' class="get_index">Accuracy Reports</h3>
            <div>
				<div class="acordian-div">
					<ul class="sidebar-acordian">
						<li <?php if($controller=='AccuracyReport' && $action=="sla-turntime"){ $active="active"; } ?> >
	                	<?= Html::a('SLA Turn-Time by Client/Cases','@web/index.php?r=accuracy-report/sla-turntime',['title'=>'SLA Turn-Time by Client/Cases','class'=> $active]) ?></li>
	                	<li <?php if($controller=='AccuracyReport' && $action=="sla-turntime-service"){ $active="active"; } ?> >
	                	<?= Html::a('SLA Turn-Time by Service','@web/index.php?r=accuracy-report/sla-turntime-service',['title'=>'SLA Turn-Time by Service','class'=> $active]) ?></li>
	                	<li <?php if($controller=='AccuracyReport' && $action=="sla-turntime-project-service"){ $active="active"; } ?> >
	                	<?= Html::a('SLA Late Projects by Service','@web/index.php?r=accuracy-report/sla-turntime-project-service',['title'=>'SLA Late Projects by Service','class'=> $active]) ?></li>
	                	<li <?php if($controller=='AccuracyReport' && $action=="sla-accuracy-service"){ $active="active"; } ?> >
	                	<?= Html::a('SLA Accuracy by Service','@web/index.php?r=accuracy-report/sla-accuracy-service',['title'=>'SLA Accuracy by Service','class'=> $active]) ?></li>
	                	<li <?php if($controller=='AccuracyReport' && $action=="sla-turntime-service"){ $active="active"; } ?> >
	                	<?= Html::a('QA Percentage Accuracy','@web/index.php?r=accuracy-report/data-service',['title'=>'QA Percentage Accuracy','class'=> $active]) ?></li>
					</ul>
				</div>
            </div> 
         <?php } */?>       
     </div> 
 </div>
<script type="text/javascript">
	var accordionOptions = {
		heightStyle: 'fill',clearStyle: true,autoHeight: false,active:<?= $accordianIndex; ?>
	};
	var accordionOptions = {
	 heightStyle: 'fill',clearStyle: true,autoHeight: false, icons: { "header": "fa fa-caret-right pull-right", "activeHeader": "fa fa-caret-down pull-right" }
,create: function( event, ui ) {

//$("#accordion-container h3 span").removeClass('ui-accordion-header-icon');
$("#accordion-container h3 span").removeClass('ui-icon');

}, active:<?= $accordianIndex; ?>};
	$("#accordion-container").accordion(accordionOptions);
 	$(window).resize(function(){
 	 	// update accordion height
 		$('#accordion-container').accordion("refresh");
		 $( "#accordion-container" ).accordion( "destroy" );
		$("#accordion-container" ).accordion(accordionOptions);
 	});	
 	
</script>
<noscript></noscript>
