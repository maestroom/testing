<?php
use yii\helpers\Html;
$this->registerJsFile(Yii::$app->request->baseUrl.'/js/jquery.form.min.js');
$this->registerJsFile(Yii::$app->request->baseUrl.'/js/jquery.MultiFile.js');

$js = <<<JS
// get the form id and set the event
$(function() {
 

$('#T7').MultiFile({ 
  list: '#T7-list',
        STRING: {
            remove: '<em class="fa fa-close text-danger" title="Remove"></em>'
         },
         maxsize:102400	
 });	
});

JS;
$this->registerJs($js);

/* @var $this yii\web\View */
/* @var $model app\models\EvidenceProduction */

$this->title = 'Edit Productions #'. $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Case Productions', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="right-main-container" id="production_form">
<div class="sub-heading" class="two-cols-fieldset-report"><a href="javascript:void(0);" class="tag-header-black" title="<?= Html::encode($this->title) ?>"><?= Html::encode($this->title) ?></a></div>
<div id="form_div"  class="two-cols-fieldset-report">
    <?= $this->render('_form', [
    	'case_id' =>$case_id,
        'model' => $model,
    	'media_list'=>$media_list,
    	'case_data'=>$case_data,
    	'data_exising_media'=>$data_exising_media,
        'production_docs'=>$production_docs,
    	'staff_assigned_arr'=>$staff_assigned_arr,
    	'prod_party_arr'=>$prod_party_arr,
    	'evidences_production' => $evidences_production
    ]) ?>
</div>
</div>
<div class="right-main-container" id="evidence_form" style="display: none;"></div>
