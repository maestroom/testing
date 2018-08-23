<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Unit */

$this->title = 'Add Unit';
$this->params['breadcrumbs'][] = ['label' => 'Units', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

	<div class="sub-heading"><a href="javascript:void(0);" title="<?= Html::encode($this->title) ?>" class="tag-header-black" ><?= Html::encode($this->title) ?></a></div>
    <div id="form_div"><?= $this->render('_form', [
        'model' => $model,
		'unit_length'=>$unit_length
    ]) ?></div>
