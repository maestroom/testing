<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\UnitPrice */

$this->title = 'Add Team Service';
$this->params['breadcrumbs'][] = ['label' => 'Unit Prices', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

	<div class="sub-heading"><a href="javascript:void(0);" title="<?= Html::encode($this->title) ?>" class="tag-header-black"><?= Html::encode($this->title) ?></a></div>
	<div id="teamserviceform">
    <?= $this->render('_teamserviceform', [
        'model' => $model,
    	'teamLocation'=>$teamLocation,
    	'model_field_length' => $model_field_length
    ]) ?>
	</div>