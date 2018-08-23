<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\CaseCloseType */

$this->title = 'Create Case Custodian Type';
$this->params['breadcrumbs'][] = ['label' => 'Case Custodian', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div id="form_div">
    <?= $this->render('_form', [
        'model' => $model,
        'evidences_cust_len'=>$evidences_cust_len
    ]) ?>
</div>
