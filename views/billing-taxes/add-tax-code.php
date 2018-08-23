<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\TaxClass */

$this->title = 'Add Tax Code';
$this->params['breadcrumbs'][] = ['label' => 'Tax Classes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="right-main-container">
<div class="sub-heading"><a href="javascript:void(0);" title="<?= Html::encode($this->title) ?>" class="tag-header-black"><?= Html::encode($this->title) ?></a></div>
<div class="taxes_div">
    <?= $this->render('_form-tax-codes', [
        'model' => $model,
        'tax_classes' => $tax_classes,
        'tax_code_length' =>$tax_code_length
    ]) ?>
</div></div>
