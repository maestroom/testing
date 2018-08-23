<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\Select2;
/* @var $this yii\web\View */
/* @var $model app\models\UnitPrice */
/* @var $form yii\widgets\ActiveForm */
$startlogic = Yii::$app->params['startlogic'];
$endarlogic = Yii::$app->params['endlogic'];
$duration = Yii::$app->params['duration'];
if(!empty($data)){
    if(isset($data['team_loc_id']) && $data['team_loc_id']!="") $model->team_loc_id=$data['team_loc_id'];
    if(isset($data['start_logic']) && $data['start_logic']!="") $model->start_logic=$data['start_logic'];
    if(isset($data['start_qty']) && $data['start_qty']!="") $model->start_qty=$data['start_qty'];
    if(isset($data['size_start_unit_id']) && $data['size_start_unit_id']!="") $model->size_start_unit_id=$data['size_start_unit_id'];
    if(isset($data['end_logic']) && $data['end_logic']!="") $model->end_logic=$data['end_logic'];
    if(isset($data['end_qty']) && $data['end_qty']!="") $model->end_qty=$data['end_qty'];
    if(isset($data['size_end_unit_id']) && $data['size_end_unit_id']!="") $model->size_end_unit_id=$data['size_end_unit_id'];
    if(isset($data['del_qty']) && $data['del_qty']!="") $model->del_qty=$data['del_qty'];
    if(isset($data['del_time_unit']) && $data['del_time_unit']!="") $model->del_time_unit=$data['del_time_unit'];
    if(isset($data['project_priority_id']) && $data['project_priority_id']!="") $model->project_priority_id=$data['project_priority_id'];
    if(isset($data['id']) && $data['id']!="") $model->id=$data['id'];
}
?>
<?php $form = ActiveForm::begin(['id' => $model->formName(), 'enableAjaxValidation' => false, 'enableClientValidation' => true]); ?>
<fieldset class="one-cols-fieldset">
    <div class="create-form">    	
    		<?= 
                    $form->field($model, 'team_loc_id',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-8'><fieldset><legend class='sr-only'>{label}</legend>{input}\n{hint}\n{error}</fieldset></div></div>",'labelOptions'=>['class'=>'form_label','label'=>'Team Location']])->checkboxList($teamLocation,
                        ['item' => function($index, $label, $name, $checked, $value) {
                                $return = '<div class="col-sm-12">';
                                if($label != 'First add Rate(s), to then see and select Service Task(s).') {
                                    if($checked)
                                        $return .= '<input title="This field is required" aria-required = "true" id="TeamserviceSla-team_loc_id-'.$value.'" checked="'.$checked.'"  type="checkbox" name="' . $name . '" value="' . $value . '" onclick="checkboxclick(this);"><label for="TeamserviceSla-team_loc_id-'.$value.'" class="form_label" >'.ucwords($label).'</label>';
                                    else
                                        $return .= '<input title="This field is required" aria-required = "true" id="TeamserviceSla-team_loc_id-'.$value.'"  type="checkbox" name="' . $name . '" value="' . $value . '"><label for="TeamserviceSla-team_loc_id-'.$value.'" class="form_label" onclick="checkboxclick(this);">'.ucwords($label).'</label>';
                                } else {
                                    $return .= '<label class="form_label text-muted">'.$label.'</label>';
                                }
                                $return .= '</div>';
                            return $return;
                        },'class'=>'custom-full-width']); 
                ?>
                        <div class="row input-field">
                            <div class='col-md-3'>Start<span class="require-asterisk-again">*</span></div>
                                <div class='col-md-3'>
                                    <?= 
                                        $form->field($model, 'start_logic',['template' => "{input}\n{hint}\n{error}",'labelOptions'=>['class'=>'form_label']])->widget(Select2::classname(), [
                                            'data' => $startlogic,
                                            'pluginOptions' => [
                                                'allowClear' => false
                                        ],]); 
                                    ?>
                                </div>
			<div class='col-md-2'>
                            <?= $form->field($model, 'start_qty',['template' => "{input}\n{hint}\n{error}",'labelOptions'=>['class'=>'form_label']])->textInput(); ?>
			</div>
			<div class='col-md-3'>
			<?= $form->field($model, 'size_start_unit_id',['template' => "{input}\n{hint}\n{error}",'labelOptions'=>['class'=>'form_label']])->widget(Select2::classname(), [
					'data' => $listUnit,
					'options'=>['prompt'=>'','aria-label' => 'start','onchange'=>'changeUnitorType("size_end_unit_id",this.value)','title' => 'This field is required', 'aria-required' => 'true'],
					/*'pluginOptions' => [
						'allowClear' => true
					],*/]);?>
			</div>
    		</div>    		
    		<div class="row input-field">
			<div class='col-md-3'>End<span class="require-asterisk-again">*</span></div>
			
                <div class='col-md-3'>
                <?= $form->field($model, 'end_logic',['template' => "{input}\n{hint}\n{error}",'labelOptions'=>['class'=>'form_label']])->widget(Select2::classname(), [
                                'data' => $endarlogic,
                                //'options'=>['prompt'=>''],
                                'options' => ['title' => 'This field is required', 'aria-label' => 'end', 'aria-required' => 'true'],
                                'pluginOptions' => [
                                    'allowClear' => false
                                ],]);?>
                 </div>
			<div class='col-md-2'>
                            <?= $form->field($model, 'end_qty',['template' => "{input}\n{hint}\n{error}",'labelOptions'=>['class'=>'form_label']])->textInput(); ?>
			</div>
			<div class='col-md-3'>
                            <?= $form->field($model, 'size_end_unit_id',['template' => "{input}\n{hint}\n{error}",'labelOptions'=>['class'=>'form_label']])->widget(Select2::classname(), [
                                    'data' => $listUnit,
                                    'options'=>['prompt'=>'','aria-label' => 'Proposed Delivery Time','onchange'=>'changeUnitorType("size_start_unit_id",this.value)','title' => 'This field is required', 'aria-required' => 'true'],
                                    /*'pluginOptions' => [
                                        'allowClear' => true
                                    ],*/]); 
                            ?>
			</div>
    		</div>
    		
    		<div class="row input-field">
			<div class='col-md-3'>Proposed Delivery Time<span class="require-asterisk-again">*</span></div>
			<div class='col-md-3'>
    		<?= $form->field($model, 'del_qty',['template' => "{input}\n{hint}\n{error}",'labelOptions'=>['class'=>'form_label']])->textInput(); ?>
			</div>
			<div class='col-md-5'>
    		<?= $form->field($model, 'del_time_unit',['template' => "{input}\n{hint}\n{error}",'labelOptions'=>['class'=>'form_label']])->widget(Select2::classname(), [
                            'data' => $duration,
                            'options'=>['prompt'=>'','title' => 'This field is required', 'aria-required' => 'true'],
                            /*'pluginOptions' => [
                                    'allowClear' => true
                            ],*/]); ?>
			</div>
    		</div>
    		<?= $form->field($model, 'project_priority_id',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->widget(Select2::classname(), [
                    'data' => $projectPriority,
                    'options'=>['prompt'=>''],
                    'pluginOptions' => [
                            'allowClear' => true
                    ],]);?>
    		<?= $form->field($model,'teamservice_id',['template' => "{input}"])->hiddenInput(['value'=>$modelteamservice->id]); ?>
    		<?php if($action == 'Edit'){
                   	echo $form->field($model,'id',['template' => "{input}"])->hiddenInput(); 
    		}?>
    </div>
    <div id="addLogicBox"></div>	
</fieldset>
<?php ActiveForm::end(); ?>
<script>
    $('input').customInput();
    function checkboxclick(obj) {
		$(obj).prop('checked',true);
		$(obj).next('label').addClass('checked');
	}
</script>
<noscript></noscript>
