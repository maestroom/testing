<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use kartik\widgets\Select2;
use kartik\widgets\Typeahead;


/* @var $this yii\web\View */
/* @var $model app\models\Tasks */
/* @var $form yii\widgets\ActiveForm */
?>
<?php $form = ActiveForm::begin(['id' => $model->formName(),'enableAjaxValidation' => false,'enableClientValidation' => true]); ?>
<fieldset class="" style="height: 318px !important;">
   <?= $form->field($modelInstruct, 'project_name',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(); ?>
   <?= $form->field($modelInstruct, 'task_priority',['template' => "<div class='row input-field'><div class='col-md-2'>{label}<span class='require-asterisk'>*</span></div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->widget(Select2::classname(), [
    'data' => $priorityList,
    'options' => ['prompt' => false, 'id' => 'priority'],
   /* 
		'pluginOptions' => [
			'allowClear' => true
		],
   */
])->label('Project Priority'); //->textInput(); ?>
   <?= $form->field($modelInstruct, 'requestor',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->widget(Typeahead::classname(), [
					'options' => ['placeholder' => 'Filter as you type ...'],
					'pluginOptions' => ['highlight'=>true],
					'dataset' => [
					    [
							'remote' => [
								'url' => Url::toRoute(['project/bring-requestor','case_id'=>$case_id]) . '&term=%QUERY',
								'wildcard' => '%QUERY'	
							]
					    ]
					]
				]);
   ?>
   <?= $form->field($modelInstruct, 'task_projectreqtype',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->widget(Select2::classname(), [
    'data' => $projectReqType_data,
    'options' => ['prompt' => false, 'id' => 'projectReqType'],
    /*'pluginOptions' => [
        'allowClear' => true
    ],*/
])->label('Project Request Type');//->textInput(); ?>
</fieldset>
<div class=" button-set text-right">
 <?= Html::button('Cancel', ['title'=>'Cancel','class' => 'btn btn-primary','onclick'=>'CaseCloseType();']) ?>
 <?= Html::button('Next', ['title'=>'Next','class' =>  'btn btn-primary','id'=>'nextstep1','onclick'=>'validateSteps("nextstep1");']) ?>
</div>
<?php ActiveForm::end(); ?>
