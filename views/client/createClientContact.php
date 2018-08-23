<?php

use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $model app\models\ClientContacts */

$this->title = 'Add Client Contact';
$this->params['breadcrumbs'][] = ['label' => 'Client Contact', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<!-- <div class="sub-heading"><?php //echo Html::encode($this->title) ?></div>  -->
<div id="clientcontactform">
<?= $this->render('_formClientContact', [
    'model' => $model,
    'client_id' => $client_id,
    'contactTypeList' => $contactTypeList,
    'countryList' => $countryList,
	'caseDataProvider' => $caseDataProvider,
	'model_field_length' => $model_field_length
]); ?>
</div>
