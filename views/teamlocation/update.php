<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\TeamlocationMaster */

$this->title = 'Edit Team Location: '.$model->team_location_name;
$this->params['breadcrumbs'][] = ['label' => 'Teamlocation Masters', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
	<div class="sub-heading"><a href="javascript:void(0);" title="<?= Html::encode($this->title) ?>" class="tag-header-black" ><?= Html::encode($this->title) ?></a></div>
    <div id="form_div"><?= $this->render('_form', [
        'model' => $model,
        'tlm_length'=>$tlm_length
    ]) ?></div>
