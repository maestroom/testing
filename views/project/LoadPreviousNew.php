<?php 
use kartik\widgets\Select2;
use yii\web\JsExpression;
use yii\helpers\Url;
$resultsJs = <<< JS
function (data, params) {
	params.page = params.page || 1;
    return {
        results: data.items,
        pagination: {
            more: (params.page * 50) < data.total_count
        }
    };
}
JS;
?>
<div class="row">
	<div class="col-sm-1"></div>
	<div class="col-sm-10 form-group"><br /></div>
	<div class="col-sm-1"></div>
</div>
<div class="row form-group">
	<div class="col-sm-1"></div>
	<div class="col-sm-10">
		<div class="form-group field-taskinstruct-task_timedue has-success">
			<div class="row input-field">
				<div class="col-md-2">
					<label class="form_label" for="loadprevious_case_id">Case</label>
				</div>
				<div class="col-md-9" id="loadprevious_parent">
						<?php
						echo Select2::widget([
									'id'=>'case_id',
									'name' => 'case_id',
									'initValueText' => $client_case_name,
									'value'=>$case_id,
									'options' => [
										'prompt' => 'Select Case',
										'class' => 'form-control',
										'id'=>'loadprevious_case_id',
										'nolabel'=>true,
									],
									'pluginOptions' => [
										'allowClear' => false,
										'dropdownParent'=> new JsExpression('$("#loadprevious_parent")'),
										'ajax' => [
											'url' => Url::to(['mycase/clientcasejsonlist']),
											'dataType' => 'json',
											'delay' => 250,
											'data' => new JsExpression('function(params) { return {q:params.term, page: params.page}; }'),
											'processResults' => new JsExpression($resultsJs),
											'cache' => true
											
										],
									],
									'pluginEvents' => [
									'change' => "function() {
										loadProjectDropdown(this.value);
									}",
									]
								]);
								
						/*foreach ($clientCaseList as $id=>$name){
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
						]);*/
						?>
				</div>
			</div>
		</div>
	</div>
	<div class="col-sm-1"></div>
</div>
<div class="row">
	<div class="col-sm-1"></div>
	<div id="load_project_grid" class="col-sm-10">
		<em class="fa fa-spinner fa-pulse fa-2x"></em>
		<span class="sr-only">Loading...</span>
	</div>
	<div class="col-sm-1"></div>
</div>
<script>
	
$(document).ready(function(){
	//console.log('demo'+<?=$case_id?>);
	loadProjectDropdown(<?=$case_id?>);
	//loadProjectGird(<?=$case_id?>);	
});
function loadProjectDropdown(case_id){
	$.ajax({
		url:baseUrl + "project/load-project-dropdown",
		data:{case_id:case_id},
	    type:"get",
	    beforeSend:function(){
			$('#load_project_grid').html('<em class="fa fa-spinner fa-pulse fa-2x"></em><span class="sr-only">Loading...</span>');
	    },
	    success:function(mydata){
	    	$('#load_project_grid').html(mydata);
		}
	});
}
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
