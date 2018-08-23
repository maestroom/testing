<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\EvidenceType */

$this->title = 'Add Media Type';
$this->params['breadcrumbs'][] = ['label' => 'Evidence Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

	<div class="sub-heading"><a href="javascript:void(0);" title="<?= Html::encode($this->title) ?>" class="tag-header-black" ><?= Html::encode($this->title) ?></a></div>
    <div id="form_div"><?= $this->render('_form', [
        'model' => $model,
    	'units'=>$units,
    	'evidence_type_length' =>$evidence_type_length
    ]) ?></div>

