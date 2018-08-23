<?php
/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Update Template: '.$model->temp_name ;
?>
<div class="sub-heading"><a href="javascript:void(0);" title="<?= Html::encode($this->title) ?>" class="tag-header-black"><?= Html::encode($this->title) ?></a></div>
<div id="form_div">
    <?= $this->render('_teamplateform', [
	        'model' => $model,
    		'services'=>$services,
    		'model_field_length' => $model_field_length,
    		'all_request_types' => $all_request_types
    ]) ?>
</div>
<script>
$(".template_lis").removeClass('active');
$("#template_<?=$model->id?>").addClass('active');
</script>
<noscript></noscript>
