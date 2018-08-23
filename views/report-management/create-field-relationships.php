<?php
use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = 'Add Table Fields';
$this->params['breadcrumbs'][] = ['label' => 'Report', 'url' => ['index-field-relationship']];
$this->params['breadcrumbs'][] = $this->title;
//echo "<prE>",print_r($current_table),"</prE>";
?>
<div class="sub-heading"><a href="javascript:void(0);" class="tag-header-black" title="<?= Html::encode($this->title) ?>"><?= Html::encode($this->title) ?></a></div>
<div id='reportform_div'>
    <?= $this->render('_form-field-relationships', [
		'modelReportTables' => $modelReportTables,
		'modelReportFields' => $modelReportFields,
		'modelReportFieldsRelationships' => $modelReportFieldsRelationships,
		'tableList' => $tableList,
                'model_field_length'=>$model_field_length   
    ]) ?>
</div>
