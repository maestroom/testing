<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Case */

$this->title = 'Add Case';
$this->params['breadcrumbs'][] = ['label' => 'Case', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sub-heading"><a href="javascript:void(0);" title="<?= Html::encode($this->title) ?>" class="tag-header-black"><?= Html::encode($this->title) ?></a></div>
<div id="Caseform_div">
    <?= $this->render('_form', [
	    'model' => $model,
	    'client_id' => $client_id,
	    'listCaseType' => $listCaseType,
		'listCaseCloseType' => $listCaseCloseType,
	    'listSalesRepo' => $listSalesRepo,
	    'model_field_length' => $model_field_length
	]) ?>
</div>

