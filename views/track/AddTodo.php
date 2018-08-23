<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Case */

$this->title = 'Add ToDo';
$this->params['breadcrumbs'][] = ['label' => 'Case', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div id="form_div">
<?= $this->render('_todo-form', [
	    'model'=>$model,
    	'servicetask_id'=>$servicetask_id,
    	'task_id'=>$task_id,
    	'team_loc'=>$team_loc,
    	'taskunit_id'=>$taskunit_id,
    	'todo_cat_list'=>$todo_cat_list,
    	'tasks_units_todos_length'=>$tasks_units_todos_length
	]) ?>
</div>
