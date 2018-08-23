<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use app\components\IsataskFormFlag;
use app\models\FormBuilderSystem;

$cust_form = ArrayHelper::map(FormBuilderSystem::find()->select(['sys_field_name','sort_order'])->where(['sys_form'=>'custodian_form','grid_only'=>0])->orderBy('sort_order')->all(),'sys_field_name','sort_order');

?>
<?php $form = ActiveForm::begin(['id' => $model->formName(),'enableAjaxValidation' => false,'enableClientValidation' => true]); ?>
<?= IsataskFormFlag::widget(); // change flag ?>
<fieldset class="one-cols-fieldset">
    <div class="create-form" id="list">
		<div class="listing-item" data-order=<?=$cust_form['cust_fname']?>>
    <?= $form->field($model, 'cust_fname',['template' => "<div class='row input-field'><div class='col-md-3'>{label}<span class='text-danger'>*</span></div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['maxlength'=>$evidences_cust_len["cust_fname"]]); ?>        
    </div>
    <div class="listing-item" data-order=<?=$cust_form['cust_lname']?>>
    <?= $form->field($model, 'cust_lname',['template' => "<div class='row input-field'><div class='col-md-3'>{label}<span class='text-danger'>*</span></div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['maxlength'=>$evidences_cust_len["cust_lname"]]); ?>        
    </div>
    <div class="listing-item" data-order=<?=$cust_form['cust_email']?>>
    <?= $form->field($model, 'cust_email',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['aria-required'=>'true']); ?>
    </div>
    <div class="listing-item" data-order=<?=$cust_form['cust_mi']?>>
    <?= $form->field($model, 'cust_mi',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['maxlength'=>$evidences_cust_len["cust_mi"]]); ?>        
    </div>
    <div class="listing-item" data-order=<?=$cust_form['title']?>>
    <?= $form->field($model, 'title',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['maxlength'=>$evidences_cust_len["title"]]); ?>            
    </div>
    <div class="listing-item" data-order=<?=$cust_form['dept']?>>
    <?= $form->field($model, 'dept',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['maxlength'=>$evidences_cust_len["dept"]]); ?>            
    </div> 
    
</fieldset>
<?php ActiveForm::end(); ?>
<script>
/*rearrange divs based on system from Start*/
var $people = $('#list'),
$peopleli = $people.children('.listing-item');

$peopleli.sort(function(a,b){
	var an = parseInt(a.getAttribute('data-order')),
		bn = parseInt(b.getAttribute('data-order'));

	if(an > bn) {
		return 1;
	}
	if(an < bn) {
		return -1;
	}
	return 0;
});

$peopleli.detach().appendTo($people);
/*rearrange divs based on system from END*/
/** is change form **/
$('input').bind('input', function(){
	$('#EvidenceCustodians #is_change_form').val('1'); $('#EvidenceCustodians #is_change_form_main').val('1'); // change event
});
$('document').ready(function(){ $('#active_form_name').val('EvidenceCustodians'); });
</script>
