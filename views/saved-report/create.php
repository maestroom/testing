<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\ReportsUserSaved */

$this->title = 'Create Reports User Saved';
$this->params['breadcrumbs'][] = ['label' => 'Reports User Saveds', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="reports-user-saved-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
