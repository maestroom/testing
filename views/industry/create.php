<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Industry */

$this->title = 'Add Client Industry';
$this->params['breadcrumbs'][] = ['label' => 'Industries', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sub-heading"><a href="javascript:void(0);" class="tag-header-black" title="<?= Html::encode($this->title) ?>"><?= Html::encode($this->title) ?></a></div>
<div id="form_div">
    <?= $this->render('_form', [
        'model' => $model,
        'industry_length' => $industry_length
    ]) ?>
</div>
