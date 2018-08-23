<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
/* @var $this yii\web\View */
/* @var $model app\models\Tasks */

$this->title = 'Add Project';
$this->params['breadcrumbs'][] = ['label' => 'Case Projects', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->registerJsFile(Yii::$app->request->baseUrl.'/js/jquery.form.min.js');
?>
<div id="project_container" class="right-main-container">
    <div id="tabs">
    	<ul>
			<li><a href="#tabs-deadlinedetails">Enter Deadline Details</a></li>
			<li><a href="#tabs-attachmedia">Attach Media</a></li>
			<li><a href="#tabs-selectworkflow">Select Workflow</a></li>
			<li><a href="#tabs-formdetails">Enter Form Details</a></li>
		</ul>
		<div id="tabs-deadlinedetails">
			<div id="form_div_deadlinedetails">
			<?=$this->render('_form',[
					'model' => $model,
					'modelInstruct'=>$modelInstruct,
					'case_id'=>$case_id,
					'priorityList'=>$priorityList,
					'projectReqType_data'=>$projectReqType_data,
					'listSalesRepo'=>$listSalesRepo,
					
			]);?>
			</div>
		</div>
		<div id="tabs-attachmedia">
			<div id="form_div_attachmedia">tab2</div>
		</div>
		<div id="tabs-selectworkflow">
			<div id="form_div_selectworkflow">
				tab3
			</div>
		</div>
		<div id="tabs-formdetails">
			<div id="form_div_formdetails">
				tab4
			</div>		
		</div>
	</div>	
</div>
<script>
$( "#tabs" ).tabs({
	  disabled: [ 1, 2, 3 ],
    beforeActivate: function (event, ui) {
	      
    },
    beforeLoad: function( event, ui ) {
      ui.jqXHR.error(function() {
        ui.panel.html(
          "Error loading current tab." );
      });
    }
  });
function validateSteps(step){
	$falg=false;
	$.ajax({
		url:baseUrl+'user/uservalidate',
		type:'post',
		data:form,
		success:function(response){
			for (var key in response) {
					$("#"+key).next().html(response[key]);
					$("#"+key).parent().parent().parent().addClass('has-error');
			}
		}
	});
	return $falg;
}
</script>
<noscript></noscript>