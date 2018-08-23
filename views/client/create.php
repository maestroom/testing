<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Client */

$this->title = 'Add Client';
$this->params['breadcrumbs'][] = ['label' => 'Client', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sub-heading"><a href="javascript:void(0);" class="tag-header-black" title="<?= Html::encode($this->title) ?>"><?= Html::encode($this->title) ?></a></div>
<div id='clientform_div'>
    <?= $this->render('_form', [
    'model' => $model,
    'industryList' => $industryList,
    'countryList' => $countryList,
    'model_field_length' => $model_field_length
	]) ?>
</div>
<script>
	$(document).ready(function(){
		$("#client-country_id").val("224").trigger("change");
	});
</script>
