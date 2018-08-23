<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Tasks */

$this->title = 'Add Project';
$this->params['breadcrumbs'][] = ['label' => 'Case Projects', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->registerJsFile(Yii::$app->request->baseUrl.'/js/jquery.form.min.js');
?>
<div id="project_container" class="right-main-container">
    <div class="sub-heading" class="two-cols-fieldset"><?= Html::encode($this->title) ?></div>
    <div id="form_div"  class="two-cols-fieldset">
        <?= $this->render('_form', [
    'model' => $model,
    'instruct_model' => $instruct_model,
    'priorityList' => $priorityList,
    'case_id' => $case_id
]) ?>
    </div>
</div>
