<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\UnitPrice */

$this->title = 'Add Unit Price';
$this->params['breadcrumbs'][] = ['label' => 'Unit Prices', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

	<div class="sub-heading"><a href="javascript:void(0);" title="<?= Html::encode($this->title) ?>" class="tag-header-black" ><?= Html::encode($this->title) ?></a></div>
    <div id="form_div"><?= $this->render('_form', [
        'model' => $model,
         'up_length' => $up_length
    ]) ?></div>

