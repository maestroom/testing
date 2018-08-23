<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\PriorityTeam */

$this->title = 'Edit Team Location Priority';
$this->params['breadcrumbs'][] = ['label' => 'Priority Teams', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>

	<div class="sub-heading"><a href="javascript:void(0);" title="<?= Html::encode($this->title) ?>" class="tag-header-black" ><?= Html::encode($this->title) ?></a></div>
    <div id="form_div"><?= $this->render('_form', [
      //  'model' => $model,
		'pt_length'=>$pt_length,
		'priority_details' => $priority_details,
    ]) ?></div>
