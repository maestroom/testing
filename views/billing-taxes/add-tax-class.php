<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\TaxClass */

$this->title = 'Add Tax Class';
$this->params['breadcrumbs'][] = ['label' => 'Tax Classes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="right-main-container">
<div class="sub-heading"><a href="javascript:void(0);" title="<?= Html::encode($this->title) ?>" class="tag-header-black"><?= Html::encode($this->title) ?></a></div>
<div class="taxes_div">
    <?= $this->render('_form-tax-classes', [
        'model' => $model,
        'tax_class_length' =>$tax_class_length
    ]) ?>
</div></div>
