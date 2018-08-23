<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\EvidenceEncryptType */

$this->title = 'Add Media Encryption Type';
$this->params['breadcrumbs'][] = ['label' => 'Evidence Encrypt Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
	<div class="sub-heading"><a href="javascript:void(0);" title="<?= Html::encode($this->title) ?>" class="tag-header-black" ><?= Html::encode($this->title) ?></a></div>
    <div id="form_div"><?= $this->render('_form', [
        'model' => $model,
        'eet_length' =>$eet_length
    ]) ?></div>

