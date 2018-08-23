<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\bootstrap\Modal;


/* @var $this yii\web\View */
/* @var $model app\models\search\EvidenceSearch */
/* @var $form yii\widgets\ActiveForm */
?>
<?php
Modal::begin([
    'header' => '<h3 class="modal-title"><i class="glyphicon glyphicon-search"></i> Filter Media Gird</h3>',
    'size' => Modal::SIZE_LARGE,
    'options' => ['id' => 'modalMediaFilter']
]);
?>
<div class="evidence-search">
    <?php $form = ActiveForm::begin([
        'id' => $model->formName(),
        'enableClientValidation'=>false,
        'action' => ['index'],
        'method' => 'post',
    ]); ?>
    <?php foreach($columns as $col){
    if(!in_array($col['class'],array('\kartik\grid\ExpandRowColumn','\kartik\grid\CheckboxColumn','kartik\grid\ActionColumn'))) {
       // echo "<pre>",print_r($col);
        
        if(isset($col['filterType']))
            echo $form->field($model, $col['attribute'])->widget($col['filterType']::classname(), $col['filterWidgetOptions']);
         else   
            echo $form->field($model, $col['attribute']);
    }
    }?>
  <div class="form-group">
        <input type='hidden' name='pjax-search' value='Y'>
        <?= Html::button('Search', ['class' => 'btn btn-primary','onclick'=>'searchMedia("'.$model->formName().'");']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default','onclick'=>'resetSearchMedia("'.$model->formName().'");']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
<script>
function searchMedia(form_id){
	var form = $('form#'+form_id);
    $('#modalMediaFilter').modal('hide');    
    $.pjax.defaults.url =form.attr('action')+'&'+form.serialize();
    $.pjax.defaults.push= false;
    if($('#dynagrid-media-pjax'))
        $.pjax.reload('#dynagrid-media-pjax', $.pjax.defaults);
    
}
function resetSearchMedia(form_id){
    var form = $('form#'+form_id);
    form[0].reset();
    form.find(':input').val('');
    form.find('option').attr('selected', false).change();
    $('#modalMediaFilter').modal('hide');    
    $.pjax.defaults.url =form.attr('action');
    $.pjax.defaults.push= false;
    if($('#dynagrid-media-pjax'))
        $.pjax.reload('#dynagrid-media-pjax', $.pjax.defaults);
}
</script>
<noscript></noscript>
<?php Modal::end(); ?>
