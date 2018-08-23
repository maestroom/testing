<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\ClientContacts */

$this->title = 'Case Summary';
$this->params['breadcrumbs'][] = ['label' => 'Case Contact', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<!-- <div class="sub-heading"><?php // Html::encode($this->title) ?></div>  -->
<div id="form_div">
	<?=  $this->render('_formCaseSummary', [
	    'model' => $model,
	    'model_field_length' => $model_field_length
	]);?>
</div>
