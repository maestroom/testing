<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\TodoCats */

$this->title = 'Edit ToDo Category: '.$model->todo_cat;
$this->params['breadcrumbs'][] = ['label' => 'Todo Cats', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>

	<div class="sub-heading"><a href="javascript:void(0);" title="<?= Html::encode($this->title) ?>" class="tag-header-black"><?= Html::encode($this->title) ?></a></div>
    <div id="form_div"><?= $this->render('_form', [
        'model' => $model,
         'tdc_length' => $tdc_length
    ]) ?></div>
