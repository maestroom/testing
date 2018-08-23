<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\EvidenceProduction */
$this->registerJsFile(Yii::$app->request->baseUrl.'/js/jquery.form.min.js');
$this->registerJsFile(Yii::$app->request->baseUrl.'/js/jquery.MultiFile.js');
$current_action = Yii::$app->controller->action->id;
//$this->registerJsFile(Yii::$app->request->baseUrl.'/js/bootstrap-filestyle.js');
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
if($current_action != 'create'){
	$this->title = 'Create Evidence Production';
}else{
	$this->title = 'Add Evidence Production';
}
$this->params['breadcrumbs'][] = ['label' => 'Evidence Productions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="right-main-container" id="production_form">
<div class="sub-heading" class="two-cols-fieldset-report"><a href="javascript:void(0);" class="tag-header-black" title="<?= Html::encode($this->title) ?>"><?= Html::encode($this->title) ?></a></div>
    <div id="form_div"  class="two-cols-fieldset-report">
        <?= $this->render('_form', [
        	'case_id' =>$case_id,
            'model' => $model,
            'media_list'=>$media_list,
            'evidences_production'=>$evidences_production,
            'case_data'=>$case_data,'staff_assigned_arr'=>$staff_assigned_arr,'prod_party_arr'=>$prod_party_arr
        ]) ?>

    </div>
</div>
<div class="right-main-container" id="evidence_form" style="display: none;"></div>
<script>
    $(function() {
        $('input').customInput();
    });    
</script>
<noscript></noscript>
