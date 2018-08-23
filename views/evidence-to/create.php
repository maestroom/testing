<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\EvidenceTo */

$this->title = 'Add Media To';
$this->params['breadcrumbs'][] = ['label' => 'Evidence Tos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

	<div class="sub-heading"><a href="javascript:void(0);" title="<?= Html::encode($this->title) ?>" class="tag-header-black" ><?= Html::encode($this->title) ?></a></div>
    <div id="form_div"><?= $this->render('_form', [
        'model' => $model,
        'evidence_to_length' =>$evidence_to_length
    ]) ?></div>
