<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\PriorityProject */

$this->title = 'Add Priority Project';
$this->params['breadcrumbs'][] = ['label' => 'Priority Projects', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

	<div class="sub-heading"><a href="javascript:void(0);" title="<?= Html::encode($this->title) ?>" class="tag-header-black" ><?= Html::encode($this->title) ?></a></div>
    <div id="form_div"><?= $this->render('_form', [
        'model' => $model,
        'pp_length' =>$pp_length,
        'maxPriorityProjectOrder' => $maxPriorityProjectOrder
    ]) ?></div>
