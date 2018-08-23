<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use app\models\TasksUnitsData;
use app\components\IsataskFormFlag;
$this->title = 'Edit Data Field';
$this->params['breadcrumbs'][] = ['label' => 'Data Field', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$required_class="";
?>

<div id="form_div">
<form method="post" action="<?=Url::to(['track/editdatafield&id='.$model->id])?>" id="TasksUnitsData" autocomplete="off">
<?= IsataskFormFlag::widget(); // change flag ?>
<fieldset class="one-cols-fieldset">
    <div class="create-form">
    	<div class="form-group field-tasksunitsbilling-quantity">
			<div class='row input-field custom-full-width'>
                            <fieldset>
                                <legend class="sr-only"><?= $model->formBuilder->element_label; ?></legend>
                                <div class='col-md-3'>
                                    <label class="form_label" id="datafield_label"><?= $model->formBuilder->element_label; ?><?php if(!$model->formBuilder->element_required){$required_class = 'required-entry'; ?>
                                    <span class="data-required">* </span><?php } ?></label>
                                </div>
                                <div class='col-md-9'>
                                    <?php 
                                        if (in_array($model->formBuilder->element_type,array('checkbox','dropdown','radio'))) {
                                        $allValues=ArrayHelper::map(TasksUnitsData::find()->select('element_value')->joinWith('tasksUnits')->where('form_builder_id='.$model->form_builder_id.' AND tbl_tasks_units.task_instruct_servicetask_id ='.$model->tasksUnits->task_instruct_servicetask_id." AND evid_num_id=".$model->evid_num_id." AND tbl_tasks_units_data.modified = '{$model->modified}'")->all(),'element_value','element_value');
                                        $i=0; 
                                    ?>
                                    <input type="hidden" name="TasksUnitsData[modified]" value="<?= $model->modified ?>" />
                                    <?php
                                        foreach ($model->formBuilder->formElementOptions as $option){
                                            if($model->formBuilder->element_type=='checkbox') {
                                                if(in_array($option->id,$allValues)) {
                                                    echo  "<input aria-label=".$option->element_option." id='".$model->formBuilder->element_id."_".$i."' type='checkbox' name='".$model->formBuilder->element_id."[]' value='".$option->id."' checked class='form-control $required_class'>";
                                                    echo  "<label for='".$model->formBuilder->element_id."_".$i."'>".$option->element_option."</label>";
                                                } else {
                                                    echo  "<input aria-label=".$option->element_option." id='".$model->formBuilder->element_id."_".$i."' type='checkbox' name='".$model->formBuilder->element_id."[]' value='".$option->id."' class='form-control $required_class'>";
                                                    echo  "<label for='".$model->formBuilder->element_id."_".$i."'>".$option->element_option."</label>";
                                                }
                                            } else if($model->formBuilder->element_type=='dropdown') {
                                                if($i==0){echo "<select name='".$model->formBuilder->element_id."' class='form-control $required_class'>";}
                                                if($option->id == $model->element_value) {
                                                    echo  "<option value='".$option->id."' selected>".$option->element_option."</option>";
                                                } else {
                                                    echo  "<option value='".$option->id."' >".$option->element_option."</option>";
                                                }
                                                if(($i+1)==count($model->formBuilder->formElementOptions)){echo "</select>";}
                                            } else if($model->formBuilder->element_type=='radio') {
                                                if(in_array($option->id,$allValues)) {
                                                    echo  "<input aria-label=".$option->element_option." id='".$model->formBuilder->element_id."_".$i."' type='radio' name='".$model->formBuilder->element_id."[]' value='".$option->id."' checked class='form-control $required_class'>";
                                                    echo  "<label for='".$model->formBuilder->element_id."_".$i."'>".$option->element_option."</label>";
                                                } else {
                                                    echo  "<input aria-label=".$option->element_option." id='".$model->formBuilder->element_id."_".$i."' type='radio' name='".$model->formBuilder->element_id."[]' value='".$option->id."' class='form-control'>";
                                                    echo  "<label for='".$model->formBuilder->element_id."_".$i."'>".$option->element_option."</label>";
                                                }
                                            }
                                            $i++;
                                        }
                                    } else {
                                        if ($model->formBuilder->element_type=='textbox'){
                                                echo  "<input type='text' id='".$model->formBuilder->element_id."' name='".$model->formBuilder->element_id."' value='".$model->element_value."' class='form-control $required_class' >"; 
                                        }else if ($model->formBuilder->element_type=='number'){
                                                echo  "<input type='text' id='".$model->formBuilder->element_id."' name='".$model->formBuilder->element_id."' value='".$model->element_value."' class='form-control numeric-field-qu negative-key $required_class' >"; 
                                        }else if ($model->formBuilder->element_type=='datetime'){
                                                echo  "<div class='calender-group'><input type='text' id='".$model->formBuilder->element_id."' name='".$model->formBuilder->element_id."' value='".$model->element_value."' class='form-control $required_class' ></div>";
                                        }else if (in_array($model->formBuilder->element_type,array('textarea'))){
                                                echo "<textarea name='".$model->formBuilder->element_id."' row='3' class='form-control $required_class'>".Html::encode($model->element_value)."</textarea>";
                                        }else{
                                                echo Html::encode($model->element_value);
                                        }
                                    }
                                    ?>
                                </div>
                            </fieldset>
			</div>
    	</div>
    <?php if($model->formBuilder->element_type=='number'){?>
    <div class="form-group field-tasksunitsbilling-quantity">
		<div class='row input-field'>
			<div class='col-md-3'>
				<label class="form_label">Unit</label>
			</div>
			<div class='col-md-9'>
				<?php 
					$unit_dd = "<select name='unit_id' class='clsunit form-control'>";
					$list = Yii::$app->db->createCommand('select * from tbl_unit WHERE remove=0')->queryAll();
					foreach ($list as $unititem) {
						if($model->element_unit == $unititem['id']){
							$unit_dd.="<option value=" . $unititem['id'] . " selected>" . $unititem['unit_name'] . "</option>";
						}else{
							$unit_dd.="<option value=" . $unititem['id'] . ">" . $unititem['unit_name'] . "</option>";
						}
					}
					$unit_dd.="</select>";
					echo $unit_dd;
				?> 
				</div>
			</div>
    	</div>
    <?php }?>
    </div>  
</fieldset>
</form>
</div>
<script>
/* change event */
$('select').on('change', function(){
  $('#TasksUnitsData #is_change_form').val('1'); // change flag
  $('#is_change_form_main').val('1'); // change flag value
});
$('input').bind('input', function(){
  $('#TasksUnitsData #is_change_form').val('1'); // change flag
  $('#is_change_form_main').val('1'); // change flag value
});
$(':checkbox').change(function(){
  $('#TasksUnitsData #is_change_form').val('1'); // change flag
  $('#is_change_form_main').val('1'); // change flag value
});
$(':radio').change(function(){
  $('#TasksUnitsData #is_change_form').val('1'); // change flag
  $('#is_change_form_main').val('1'); // change flag value
});
$('textarea').bind("input",function(){
  $('#TasksUnitsData #is_change_form').val('1'); // change flag
  $('#is_change_form_main').val('1'); // change flag value
});
/* End */
$('input').customInput();
<?php if ($model->formBuilder->element_type=='datetime'){ ?>
datePickerController.createDatePicker({	                     
    formElements: { "<?php echo $model->formBuilder->element_id?>": "%m/%d/%Y" },
    callbackFunctions:{
		"datereturned":[changeflag],
	}
});
<?php }?>

</script>
<noscript></noscript>
