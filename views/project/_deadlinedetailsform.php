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
<fieldset class="one-cols-fieldset">
    <div class="col-sm-12">
    <?= $form->field($modelInstruct, 'task_priority', ['template' => "<div class='row input-field'><div class='col-md-2'>{label}<span class='require-asterisk'>*</span></div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->widget(Select2::classname(), [
            'data' => $priorityList,
            'options' => ['prompt' => 'Select Project Priority', 'id' => 'taskinstruct-task_priority', 'onchange' => 'checktotalhours()','nolabel'=>true ],

            ])->label('Project Priority');//->textInput(); ?>
            <?= $form->field($modelInstruct, 'project_name',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->widget(Typeahead::classname(), [
			'options' => ['placeholder' => 'Filter List','maxlength'=>$tasks_instruct_length['project_name'],'aria-label'=>'Project Name'],
			'pluginOptions' => ['highlight'=>true],
			'dataset' => [
                            [
                                'remote' => [
                                    'url' => Url::toRoute(['project/bring-projectname','case_id'=>$case_id]) . '&term=%QUERY',
                                    'wildcard' => '%QUERY'	
                                ]
                            ]
                        ]
		]); ?>
   
   <?= $form->field($modelInstruct, 'requestor',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->widget(Typeahead::classname(), [
        'options' => ['placeholder' => 'Filter List','aria-label'=>'Project Requester','maxlength'=>$tasks_instruct_length['requestor']],
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
    'options' => ['prompt' => 'Select Project Request Type', 'id' => 'projectReqType', 'title' => 'Project Request type'],
    /*'pluginOptions' => [
        'allowClear' => true
    ],*/
])->label('Project Request Type');//->textInput(); ?>
   </div>
   <input type="hidden" id="task_instruct_id" value="<?= $modelInstruct->id ?>" >
</fieldset>
<div class=" button-set text-right">
	<?php if(isset($flag) && $flag=='Saved') {?>
	<?= Html::button('Cancel', ['title'=>'Cancel','class' => 'btn btn-primary','onclick'=>'cancelProject('.$case_id.',"saved","");']) ?>
	<?php }else if(isset($flag) && $flag=='Edit'){?>
	<?= Html::button('Cancel', ['title'=>'Cancel','class' => 'btn btn-primary','onclick'=>'cancelProject('.$case_id.',"change",'.$task_id.');']) ?>
	<?php }else{?>
	<?= Html::button('Cancel', ['title'=>'Cancel','class' => 'btn btn-primary','onclick'=>'cancelProject('.$case_id.',"","");']) ?>
	<?php }?>
 <?= Html::button('Next', ['title'=>'Next','class' =>  'btn btn-primary','id'=>'nextstep1','onclick'=>'validateSteps(1);']) ?>
</div>
