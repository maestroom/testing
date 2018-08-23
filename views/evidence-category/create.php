<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\EvidenceCategory */

$this->title = 'Add Media Category';
$this->params['breadcrumbs'][] = ['label' => 'Evidence Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
	<div class="sub-heading"><a href="javascript:void(0);" title="<?= Html::encode($this->title) ?>" class="tag-header-black" ><?= Html::encode($this->title) ?></a></div>
	<div id="form_div">
    <?= $this->render('_form', [
        'model' 					=> $model,
        'evidence_category_length' 	=> $evidence_category_length
    ]) ?></div>
