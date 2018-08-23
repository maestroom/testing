<script type="text/javascript">
	$(document).ready(function(){
		$('#submit_for_pdf').hide();		
	});
	
	function runmyreport(){
		var task_id = $('#task_id').val();
		if (task_id == "") {
            alert('Please Enter a Project #');
            return false;
        }else {
        	 $.ajax({
 	            url: baseUrl + "activity-report/taskexist/",
 	            type : "post",
 	            data : {
				'task_id' : task_id,	
				},
 	            success: function (data) {
 	 	            	if(data=='OK'){
 	 	            		$.ajax({
 	 	       	            	url: baseUrl + "activity-report/runprojecttransactiviyreport/",
 	 	       	            	type : "post",
 	 	       	            	data : {
								 'task_id' : task_id,
								},
 	 	       	            	success: function (data) {
 	 	       	                	/*$('#myreport_result').show();
 	 	       	                	$('#myreport_pdf').show();*/
 	 	       	                	$('#activity_report').html(data);
 	 	       	                	$('#task_id_error').empty();
 	 	       	                	$('#submit_for_pdf').show();
 	 	       	                	/*$(".project_status").removeClass('hide');*/
 	 	       	            	},
 	 	       	            	beforeSend: function(data){
 	 	       	            		$(".project_status").addClass('hide');
 	 	       		        	}
 	 	       	        	});
 	 	 	            }
 	 	 	            else {
							//alert(data);
							$('#task_id_error').html('Please Adjust Your Process:-The Project # does not exist in the application.  Please enter an existing Project # to generate the report.');
                                                        $('#task_id_error').focus();
							$('#activity_report').empty();
							$('#project_status_id').empty();
							$('#submit_for_pdf').hide();
 	 	 	 	        }
 	            },
 	           	beforeSend: function(data){
 	           			$('#myreport_result').hide();	
	                	$('#myreport_pdf').hide();
	            		$(".project_status").addClass('hide');
 	           	}
        	}); 
	       
        }
	}
</script>
<noscript></noscript>

<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\grid\GridView;

use kartik\grid\datetimepicker;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\EvidenceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'ActivityReport';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="right-main-container" id="media_container">
	<div class="">
    <fieldset class="two-cols-fieldset workflow-management">
        <div class="create-form">
			<div class="form-group field-team-team_name required">
				<div class="row input-field">
					<div class="col-md-2">
						<label for="task_id" class="form_label">Enter Project #</label>
						<span class="require-asterisk">*</span>
					</div>
					<div class="col-md-2">
						<input type="text" name="task_id" class="form-control" id="task_id"  aria-required="true">
					</div>
					<div class="col-sm-3">
						<div class="">
							<button onclick="runmyreport();" title="Run" class="btn btn-primary" id="runmyreport" type="button">Run</button>  
						</div>
					</div>
					<div class="col-sm-3 col-sm-offset-2">
						<div class="" id = "project_status_id">
							
						</div>
					</div>
					
        </div> <!-- End -->
        <div class="has-error">
            <div class="help-block col-sm-offset-2" id="task_id_error" tabindex="0">
			</div>
        </div>
        
        <div class="clearfix">&nbsp;</div>
        <div class="clearfix">&nbsp;</div>
        <div class="clearfix">&nbsp;</div>
        <div class="row input-field">
        <div id="activity_report">
					
		</div>
		</div>
      </div>  
</fieldset>
<div class=" button-set text-right">
		<button onclick="my_reportpdf();" title="PDF Export" class="btn btn-primary" id="submit_for_pdf" type="button">PDF</button>
  </div>
	 <!-- <div class="button-set text-right" id="last_part">
           
      </div>-->
</div>

