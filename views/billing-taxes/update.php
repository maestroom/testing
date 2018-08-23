<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\TaxCode */

$this->title = 'Update Tax Code: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Tax Codes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="tax-code-update">

    <h1><a href="javascript:void(0);" title="<?= Html::encode($this->title) ?>" class="tag-header-black"><?= Html::encode($this->title) ?></a></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
