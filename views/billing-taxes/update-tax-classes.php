<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\TaxClass */

$this->title = 'Edit Tax Class: ' . $model->class_name;
$this->params['breadcrumbs'][] = ['label' => 'Tax Classes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="right-main-container">	
	<div class="sub-heading"><a href="javascript:void(0);" title="<?= Html::encode($this->title) ?>" class="tag-header-black"><?= Html::encode($this->title) ?></a></div>
		<div class="taxes_div">
		    <?= $this->render('_form-tax-classes', [
		        'model' => $model,
		    	'pricePoint_data' => $pricePoint_data,
		    	'taxclassesprice' => $taxclassesprice,
		    	'tax_class_length' =>$tax_class_length
		    ]) ?>
		</div>
</div>
