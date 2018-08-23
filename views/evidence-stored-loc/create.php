<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\EvidenceStoredLoc */

$this->title = 'Add Media Stored Location';
$this->params['breadcrumbs'][] = ['label' => 'Evidence Stored Locs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

	<div class="sub-heading"><a href="javascript:void(0);" title="<?= Html::encode($this->title) ?>" class="tag-header-black" ><?= Html::encode($this->title) ?></a></div>
    <div id="form_div"><?= $this->render('_form', [
        'model' => $model,
         'esl_length'=>$esl_length
    ]) ?></div>

