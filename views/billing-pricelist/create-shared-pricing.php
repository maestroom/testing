<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Pricing */

$this->title = 'Add Price Point';
$this->params['breadcrumbs'][] = ['label' => 'Pricings', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sub-heading"><a href="javascript:void(0);" class="tag-header-black" title="<?= Html::encode($this->title) ?>"><?= Html::encode($this->title) ?></a></div>
<div id='sharedpricing_div'>
    <?= $this->render('_form-team-pricing', [
        'model' => $model,
    	'listunitType' => $listunitType,
		'pricingUtmbsCodes' => $pricingUtmbsCodes,
    	'teamList'=>$teamList,
    	'sourceDiv'=>'sharedpricing_div',
    	'pricing_length' => $pricing_length
    ]) ?>
</div>
