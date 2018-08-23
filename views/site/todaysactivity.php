<?php
use yii\helpers\Html;	
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\web\Session;
use app\models\User;
$this->title = "Today's Activity";
?>
<div class="row">
  <div class="col-md-12">
      <h1 id="page-title" role="heading" class="page-header"> <em class="fa fa-line-chart" title="Today's Activity"></em><a href="javascript:void(0);" title="Today's Activity" class="tag-header-red"> Today's Activity </a></h1>
  </div>
</div>
<div class="row">
<div class="col-xs-12 col-sm-12 col-md-12 single-cols-container">
		<input type="hidden" id="activity_offset" value="0">
					<div class="tab-inner-fix">

						<div id="kv-grid-demo" class="grid-view">
							<div id="kv-grid-demo-container" class="kv-grid-container"
								style="overflow: auto;">
								<input type="hidden" value="0" id="noactivities" />
								<div  id="activity-log-dynamic"></div>
							</div>
						</div>
					</div>
</div>
</div>
<script>
	function activityalltask2(taskid, caseId, service_task_id, todo_filteredtodayact, opt, type, teamId, team_loc){
                        if(type == "team"){
                        	var path = baseUrl + "track/index&taskid="+taskid+"&case_id="+caseId+"&servicetask_id="+service_task_id+"&todo_filteredtodayact=todo_filteredtodayact";
                        } else {
                        	var path = baseUrl + "track/index&taskid="+taskid+"&case_id="+caseId+"&servicetask_id="+service_task_id+"&todo_filteredtodayact=todo_filteredtodayact";
                        }
                    	method = "post";
						var form = document.createElement("form");
                        form.setAttribute("method", method);
                        form.setAttribute("action", path);
                        var hiddenField = document.createElement("input");
                        hiddenField.setAttribute("type", "hidden");
                        hiddenField.setAttribute("name", 'taskid');
                        hiddenField.setAttribute("value", taskid);
                        form.appendChild(hiddenField);
                        var hiddenField = document.createElement("input");
                        hiddenField.setAttribute("type", "hidden");
                        hiddenField.setAttribute("name", 'caseId');
                        hiddenField.setAttribute("value", caseId);
                        form.appendChild(hiddenField);
						if(type == 'team'){
							var hiddenField = document.createElement("input");
                            hiddenField.setAttribute("type", "hidden");
                            hiddenField.setAttribute("name", 'teamId');
                            hiddenField.setAttribute("value", teamId);
                            form.appendChild(hiddenField);
                            var hiddenField = document.createElement("input");
                            hiddenField.setAttribute("type", "hidden");
                            hiddenField.setAttribute("name", 'team_loc');
                            hiddenField.setAttribute("value", team_loc);
                            form.appendChild(hiddenField);
						}
						if (opt == 'passtodo'){
                    		var hiddenField = document.createElement("input");
                            hiddenField.setAttribute("type", "hidden");
                            hiddenField.setAttribute("name", 'service_task_id');
                            hiddenField.setAttribute("value", service_task_id);
                            form.appendChild(hiddenField);
                            var hiddenField = document.createElement("input");
                            hiddenField.setAttribute("type", "hidden");
                            hiddenField.setAttribute("name", 'todo_filteredtodayact');
                            hiddenField.setAttribute("value", todo_filteredtodayact);
                            form.appendChild(hiddenField);
                    	}
                    	var hiddenField = document.createElement("input");
						hiddenField.setAttribute("type", "hidden");
                        hiddenField.setAttribute("name",  yii.getCsrfParam());
                        hiddenField.setAttribute("value", yii.getCsrfToken());
                        form.appendChild(hiddenField);
                        document.body.appendChild(form);
                        form.submit();
                    }
                   function activityalltasknotes(taskid, caseId, service_task_id, instructionnotes, unit_id){
                    method = "post";
                            var path = baseUrl + "track/index&taskid="+taskid+"&case_id="+caseId+"&servicetask_id="+service_task_id+"&instructionnotes=instructionnotes&unit_id="+unit_id;
                            var form = document.createElement("form");
                            form.setAttribute("method", method);
                            form.setAttribute("action", path);
                            var hiddenField = document.createElement("input");
                            hiddenField.setAttribute("type", "hidden");
                            hiddenField.setAttribute("name", 'taskid');
                            hiddenField.setAttribute("value", taskid);
                            form.appendChild(hiddenField);
                            var hiddenField = document.createElement("input");
                            hiddenField.setAttribute("type", "hidden");
                            hiddenField.setAttribute("name", 'caseId');
                            hiddenField.setAttribute("value", caseId);
                            form.appendChild(hiddenField);
                            var hiddenField = document.createElement("input");
                            hiddenField.setAttribute("type", "hidden");
                            hiddenField.setAttribute("name", 'service_task_id');
                            hiddenField.setAttribute("value", service_task_id);
                            form.appendChild(hiddenField);
                            var hiddenField = document.createElement("input");
                            hiddenField.setAttribute("type", "hidden");
                            hiddenField.setAttribute("name", 'instructionnotes');
                            hiddenField.setAttribute("value", instructionnotes);
                            form.appendChild(hiddenField);
                            var hiddenField = document.createElement("input");
                            hiddenField.setAttribute("type", "hidden");
                            hiddenField.setAttribute("name", 'unit_id');
                            hiddenField.setAttribute("value", unit_id);
                            form.appendChild(hiddenField);
                            var hiddenField = document.createElement("input");
                            hiddenField.setAttribute("type", "hidden");
                            hiddenField.setAttribute("name",  yii.getCsrfParam());
							hiddenField.setAttribute("value", yii.getCsrfToken());
                            form.appendChild(hiddenField);
                            document.body.appendChild(form);
                            form.submit();
                    }
                    function activityalltask1(taskid, caseId, service_task_id, instructionnotes, unit_id){
                    method = "post";
                            var path = httpPath + "track/index&taskid="+taskid+"&case_id="+caseId+"&servicetask_id="+service_task_id+"&instructionnotes=instructionnotes&unit_id="+unit_id;
                            var form = document.createElement("form");
                            form.setAttribute("method", method);
                            form.setAttribute("action", path);
                            var hiddenField = document.createElement("input");
                            hiddenField.setAttribute("type", "hidden");
                            hiddenField.setAttribute("name", 'taskid');
                            hiddenField.setAttribute("value", taskid);
                            form.appendChild(hiddenField);
                            var hiddenField = document.createElement("input");
                            hiddenField.setAttribute("type", "hidden");
                            hiddenField.setAttribute("name", 'caseId');
                            hiddenField.setAttribute("value", caseId);
                            form.appendChild(hiddenField);
                            var hiddenField = document.createElement("input");
                            hiddenField.setAttribute("type", "hidden");
                            hiddenField.setAttribute("name", 'service_task_id');
                            hiddenField.setAttribute("value", service_task_id);
                            form.appendChild(hiddenField);
                            var hiddenField = document.createElement("input");
                            hiddenField.setAttribute("type", "hidden");
                            hiddenField.setAttribute("name", 'instructionnotes');
                            hiddenField.setAttribute("value", instructionnotes);
                            form.appendChild(hiddenField);
                            var hiddenField = document.createElement("input");
                            hiddenField.setAttribute("type", "hidden");
                            hiddenField.setAttribute("name", 'unit_id');
                            hiddenField.setAttribute("value", unit_id);
                            form.appendChild(hiddenField);
                            var hiddenField = document.createElement("input");
                            hiddenField.setAttribute("type", "hidden");
                            hiddenField.setAttribute("name",  yii.getCsrfParam());
							hiddenField.setAttribute("value", yii.getCsrfToken());
                            form.appendChild(hiddenField);
                            document.body.appendChild(form);
                            form.submit();
                    }
                    function activityalltask(taskid, caseId, taskunit, servicetask_id){
                   			method = "post";
                            var path = httpPath + "track/index&taskid="+taskid+"&case_id="+caseId+"&servicetask_id="+servicetask_id;
                            var form = document.createElement("form");
                            form.setAttribute("method", method);
                            form.setAttribute("action", path);
                            var hiddenField = document.createElement("input");
                            hiddenField.setAttribute("type", "hidden");
                            hiddenField.setAttribute("name", 'taskid');
                            hiddenField.setAttribute("value", taskid);
                            form.appendChild(hiddenField);
                            var hiddenField = document.createElement("input");
                            hiddenField.setAttribute("type", "hidden");
                            hiddenField.setAttribute("name", 'caseId');
                            hiddenField.setAttribute("value", caseId);
                            form.appendChild(hiddenField);
                            var hiddenField = document.createElement("input");
                            hiddenField.setAttribute("type", "hidden");
                            hiddenField.setAttribute("name", 'taskunit');
                            hiddenField.setAttribute("value", taskunit);
                            form.appendChild(hiddenField);
                            var hiddenField = document.createElement("input");
                            hiddenField.setAttribute("type", "hidden");
                            hiddenField.setAttribute("name", 'servicetask_id');
                            hiddenField.setAttribute("value", servicetask_id);
                            form.appendChild(hiddenField);
                            var hiddenField = document.createElement("input");
                            hiddenField.setAttribute("type", "hidden");
                            hiddenField.setAttribute("name",  yii.getCsrfParam());
							hiddenField.setAttribute("value", yii.getCsrfToken());
                            form.appendChild(hiddenField);
                            document.body.appendChild(form);
                            form.submit();
                    } 
$(document).ready(function() {
	


					changeTodaysActivity();
});
</script>