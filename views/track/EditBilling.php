<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Case */

$this->title = 'Edit Billing Item';
$this->params['breadcrumbs'][] = ['label' => 'Billing', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div id="form_div">
<?= $this->render('_billing-form', [
	    'model'=>$model,
    ]) ?>
</div>