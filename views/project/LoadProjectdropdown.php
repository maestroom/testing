<?php
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\widgets\Select2;
use yii\web\JsExpression;
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
	<div class="col-sm-12">
        <div class="row input-field">
                <div class="col-md-2">
                    <label class="form_label" for="previous_workflow_project_id">Previous Workflow</label>
                </div>
                <div class="col-md-9">
                    <?php
					echo Select2::widget([
							 	'id'=>'previous_workflow_project_id',
								'name' => 'previous_workflow_project_id',
								'options' => [
									'prompt' => 'Select Previous Workflow',
									'class' => 'form-control',
									'id'=>'previous_workflow_project_id',
									'nolabel'=>true,
								],
								'pluginOptions' => [
									'allowClear' => false,
                                    'dropdownParent'=> new JsExpression('$("#load_project_grid")'),
									'ajax' => [
										'url' => Url::to(['project/getprojectjsonlist','case_id'=>$case_id]),
										'dataType' => 'json',
										'delay' => 250,
										'data' => new JsExpression('function(params) { return {q:params.term, page: params.page}; }'),
										'processResults' => new JsExpression($resultsJs),
										'cache' => true
										
									],
								],
								'pluginEvents' => [
								'change' => "function() {
								}",
								]
							]);?>
                </div>
        </div>
    </div>
</div>    
