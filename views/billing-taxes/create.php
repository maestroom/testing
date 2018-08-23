<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\TaxCode */

$this->title = 'Create Tax Code';
$this->params['breadcrumbs'][] = ['label' => 'Tax Codes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tax-code-create">

    <h1><a href="javascript:void(0);" title="<?= Html::encode($this->title) ?>" class="tag-header-black"><?= Html::encode($this->title) ?></a></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
