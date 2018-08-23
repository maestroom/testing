<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Case */

$this->title = 'Add Instruction Notes';
$this->params['breadcrumbs'][] = ['label' => 'Case', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div id="form_div">
<?= $this->render('_instrcution-notes-form', [
	    'model' 			=> $model,
    	'servicetask_id'	=>$servicetask_id,
    	'task_id'			=>$task_id,
    	'tasks_units_notes_length'=> $tasks_units_notes_length
	]) ?>
</div>
