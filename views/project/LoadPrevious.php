<?php use kartik\widgets\Select2;
use yii\web\JsExpression;
 ?>
<div class="row">
	<div class="col-sm-12"><div class="form-group field-taskinstruct-task_timedue has-success">
		<div class="row input-field">
			<div class="col-md-2">
				<label class="form_label">Case</label>
			</div>
			<div class="col-md-9" id="loadprevious_parent">
					<?php
					foreach ($clientCaseList as $id=>$name){
						$clientcase_dropdown[$id] = $name;
					}
					 echo Select2::widget([
					'name' => 'case_id',
                    'attribute' => 'case_id',
                    'data' => $clientcase_dropdown,
                    'options' => ['prompt' => 'Select Case','class' => 'form-control','onchange'=>'loadProjectGird(this.value);','id'=>'loadprevious_case_id'],
                    'pluginOptions' => [
                      //'allowClear' => true,
                      'dropdownParent' => new JsExpression('$("#loadprevious_parent")')
                    ]
                    ]);
					 ?>
			</div>
		</div>
	</div>
</div>
<div id="load_project_grid" class="col-sm-12" style="height:515px!important;">
<em class="fa fa-spinner fa-pulse fa-2x"></em>
<span class="sr-only">Loading...</span>
</div>
</div>
<script>
	
$(document).ready(function(){
	console.log('demo'+<?=$case_id?>);
	loadProjectGird(<?=$case_id?>);
	//loadProjectGird(<?=$case_id?>);	
});
function loadProjectGird(case_id){
	$.ajax({
		url:baseUrl + "project/load-project-gird",
		data:{case_id:case_id},
	    type:"get",
	    beforeSend:function(){
			$('#load_project_grid').html('<em class="fa fa-spinner fa-pulse fa-2x"></em><span class="sr-only">Loading...</span>');
	    },
	    success:function(mydata){
	    	$('#load_project_grid').html(mydata);
		},complete:function(){
			$('#load_project_grid input').customInput();
		}
	});
}
</script>
<noscript></noscript>
