<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\components\IsataskFormFlag;
/* @var $this yii\web\View */
/* @var $model app\models\UnitPrice */
/* @var $form yii\widgets\ActiveForm */
$servicetask_id=0;
if(!$model->isNewRecord){
$servicetask_id=$model->id;
}
$js = <<<JS
// get the form id and set the event
function SaveServiceTask(form_id,btn){
	var form = $('form#'+form_id);
	$.ajax({
        url    : form.attr('action'),
        cache: false,
        type   : 'post',
        data   : form.serialize(),
        beforeSend : function()    {
        	$(btn).attr('disabled','disabled');
        },
        success: function (response){
        	if(response == 'OK'){
				CancelServiceTask($teamId,$model->teamservice_id);
			}else{
				$('#form_div').html(response);
        		$(btn).removeAttr("disabled");
        	}
        },
        error  : function (){
            console.log('internal server error');
        }
    });
}
$('input[name="Servicetask[billable_item]"]').on('click',function(){
		if(this.value == 1)				
			$('#servicetask-force_entry').prop('checked',true);
		else
			$('#servicetask-force_entry').prop('checked',false);								
});
function updateServicetask(obj){
  var old_locs= $('#old_teamloc').val().split(',');   
  var curr_locs = $('.teamservice_locs:checkbox:checked');
  var cure_loc_arr = new Array();
  $('.teamservice_locs:checkbox:checked').each(function(){
  	cure_loc_arr.push($(this).val());
  });
  if(curr_locs.length==old_locs.length){
         SaveServiceTask("{$model->formName()}",obj);
  }else{
       $.ajax({
         	url    : baseUrl+'workflow/checkserviceteamloc',
            type   : 'post',
            data   : {old_locs:old_locs,curr_locs:cure_loc_arr,servicetask_id:{$servicetask_id}},
         	success: function (response){
         		if(response=='OK'){
					SaveServiceTask("{$model->formName()}",obj);
				}else{
					$('.teamservice_locs').each(function(){
							if(old_locs.indexOf($(this).val()) != -1){
								$(this).prop("checked",true);
								$(this).next().addClass('checked');
							}
					 });		
         			alert(response);
					return false;		
				}
			}		
       });      				
  }           	
}
JS;
$this->registerJs($js);
?>
<?php 
$form = ActiveForm::begin(['id' => $model->formName(),'enableAjaxValidation' => false,'enableClientValidation' => true]); ?>
<?= IsataskFormFlag::widget(); // change flag ?>
<fieldset class="one-cols-fieldset">
    <div class="create-form">
    	<?= $form->field($model, 'service_task',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-9'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['maxlength'=>$model_field_length['service_task']]) ?>
    	<?php if($teamId!=1){
                if(!$model->isNewRecord){
                                if(!empty($model->team_location)){
                                        foreach ($model->team_location as $savedtl){
                                                if(!in_array($savedtl,array_keys($teamLocation))){
                                                        unset($model->team_location[$savedtl]);
                                                }
                                        }
                                }
                                ?>
                        <input type="hidden" value="<?php echo implode(',',$model->team_location); ?>" name="old_teamloc" id="old_teamloc" />
                <?php } ?>
    		<?= $form->field($model, 'team_location',['template' => "<div class='row input-field'><fieldset><legend class='sr-only'>Team Location</legend><div class='col-md-2'>{label}<span class='text-danger'>*</span></div><div class='col-md-9'>{input}\n{hint}\n{error}</div></fieldset></div>",'labelOptions'=>['class'=>'form_label']])->checkboxList($teamLocation,
                    ['item' => function($index, $label, $name, $checked, $value) {
                        $return = '<div class="col-sm-12">';
                        if($label != 'First add Rate(s), to then see and select Service Task(s).') {
                                if($checked)
                                    $return .= '<input class="teamservice_locs" id="'.$name.'-'.$value.'" checked="'.$checked.'"  type="checkbox" name="' . $name . '" value="' . $value . '"><label for="'.$name.'-'.$value.'" aria-label="'.ucwords($label).'" class="form_label">'.ucwords($label).'</label>';
                                else
                                    $return .= '<input class="teamservice_locs" id="'.$name.'-'.$value.'"  type="checkbox" name="' . $name . '" aria-label="'.ucwords($label).'" value="' . $value . '"><label for="'.$name.'-'.$value.'" class="form_label">'.ucwords($label).'</label>';
                        } else {
                            $return .= '<label class="form_label text-muted">'.$label.'</label>';
                        }
                        $return .= '</div>';
                        return $return;
                    },'class'=>'custom-full-width']); ?>
    	<?php }?>
    	<?= $form->field($model, 'description',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-9'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textArea(['rows'=>3,'maxlength'=>$model_field_length['description']])->label('Task Description') ?>
        
            <?php
            $billable_items = array(1 => 'Yes - Optional', 2 => 'Yes - Forced', 0 => 'No');
               if($model->isNewRecord){$model->billable_item=1;}?>
               <?= $form->field($model, 'billable_item',['template' => '<div class="row custom-full-width"><fieldset><legend class="sr-only">Billable Item</legend><div class="col-md-2">{label}</div><div class="col-md-9"><div class="row">{input}{error}{hint}</div></div></fieldset></div>'])->radioList([1 => 'Yes - Optional', 2 => 'Yes - Forced', 0 => 'No'],['item' => function($index, $label, $name, $checked, $value) use($billable_items) {
                    $return = '<div class="col-sm-12"><label for="'.$name.'-'.$value.'" class="form_label">';
                        if($checked)
                            $return .= '<input id="'.$name.'-'.$value.'" aria-setsize="3" aria-posinset="'.($index+1).'"  checked="'.$checked.'"  class="form_label_radio" type="radio" name="' . $name . '" value="' . $value . '" aria-label="'.$billable_items[$value].'">';
                        else
                            $return .= '<input id="'.$name.'-'.$value.'"  aria-setsize="3" aria-posinset="'.($index+1).'"  type="radio" aria-label="Billable Item '.$value.'" class="form_label_radio" name="' . $name . '" value="' . $value . '" aria-label="'.$billable_items[$value].'">';
                        $return .= ucwords($label);
                        $return .= '</label></div>';
                    return $return;
               }])->label($model->getAttributeLabel('billable_item'), ['class'=>'form_label']); ?>
        
    	<?= $form->field($model, 'sampling',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-9 percent-width'>{input}<span>%</span>\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['maxlength'=>$model_field_length['sampling']])->label('Random Sample'); ?>
                <?php
                    $yes_no = array(1 => 'Yes',0 => 'No');
                    if(!$model->isNewRecord){ 
                ?>
	    	<?= $form->field($model, 'task_hide',['template' => '<div class="row custom-full-width"><fieldset><legend class="sr-only">Hide Task</legend><div class="col-md-2">{label}</div><div class="col-md-9"><div class="row">{input}{error}{hint}</div></div></fieldset></div>'])->radioList([ 1 => 'Yes',0 => 'No'],['item' => function($index, $label, $name, $checked, $value) use($yes_no) {
                    $return = '<div class="col-sm-12"><label for="'.$name.'-'.$value.'" class="form_label">';
                        if($checked)
                            $return .= '<input id="'.$name.'-'.$value.'" checked="'.$checked.'"  class="form_label_radio" type="radio" name="' . $name . '" value="' . $value . '" aria-label="Hide Task '.$yes_no[$value].'">';
                        else
                            $return .= '<input id="'.$name.'-'.$value.'" type="radio" class="form_label_radio" name="' . $name . '" value="' . $value . '" aria-label="Hide Task '.$yes_no[$value].'">';
                        $return .= ucwords($label);
                        $return .= '</label></div>';
                    return $return;
	    	}])->label($model->getAttributeLabel('Hide Task'), ['class'=>'form_label']); ?>
    		<?php // $form->field($model, 'task_hide',['template' => "<div class='row input-field custom-full-width'><div class='col-md-2'>{label}</div><div class='col-md-9'>{input}<label for='servicetask-task_hide'>Hide Task</label>\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->checkbox(['label' => '','labelOptions'=>['class'=>'form_label']])->label(false);?>
    	<?php } ?>
    	<?= $form->field($model, 'teamservice_id')->hiddenInput()->label(false); ?>
    	<?= $form->field($model, 'teamId')->hiddenInput()->label(false); ?>
    </div>	
</fieldset>
<div class=" button-set text-right">
	<?= Html::button('Cancel', ['title' => 'Cancel','class' => 'btn btn-primary','onclick'=>'CancelServiceTaskMainForm('.$teamId.','.$model->teamservice_id.');']) ?>
  <?php if(!$model->isNewRecord && $teamId!=1){ ?>
  	<?= Html::button('Update', ['title'=> 'Update','class' =>  'btn btn-primary submitTeamService','onclick'=>'updateServicetask(this);']) ?>
  <?php }else{ ?>
	<?= Html::button($model->isNewRecord ? 'Add' : 'Update', ['title' => $model->isNewRecord ? 'Add' : 'Update','class' =>  'btn btn-primary submitTeamService','onclick'=>'SaveServiceTask("'.$model->formName().'",this);']) ?>
  <?php } ?>
</div>
<?php ActiveForm::end(); ?>
<script>
 	$('input').customInput();
 	/* ChangeFlag */
 	$('input').bind('input', function(){
		$('#Servicetask #is_change_form').val('1'); 
		$('#Servicetask #is_change_form_main').val('1');
	}); 
	$(':radio').change(function(){ 
		$('#Servicetask #is_change_form').val('1'); 
		$('#Servicetask #is_change_form_main').val('1');
	}); 
	$(':checkbox').change(function(){ 
		$('#Servicetask #is_change_form').val('1'); 
		$('#Servicetask #is_change_form_main').val('1');
	}); 
	$('textarea').bind('input', function(){ 
		$('#Servicetask #is_change_form').val('1'); 
		$('#Servicetask #is_change_form_main').val('1'); 
	});
	$('document').ready(function(e){
		/* form name */
		$('#active_form_name').val('Servicetask');
	});
</script>
<noscript></noscript>
