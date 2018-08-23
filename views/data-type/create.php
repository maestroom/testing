<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\DataType */

$this->title = 'Add Data Type';
$this->params['breadcrumbs'][] = ['label' => 'Data Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

	<div class="sub-heading"><a href="javascript:void(0);" title="<?= Html::encode($this->title) ?>" class="tag-header-black" ><?= Html::encode($this->title) ?></a></div>
    <div id="form_div"><?= $this->render('_form', [
        'model' => $model,
        'data_type_length' =>$data_type_length
    ]) ?></div>

