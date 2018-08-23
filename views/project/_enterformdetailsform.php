<?php 
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
?>
<fieldset class="one-cols-fieldset">
    <div id="formbuilder_data"></div>
</fieldset>
<div class=" button-set text-right">
<input type="hidden" id="remove_attachment" name="remove_attachment" value="">

	<?php if(isset($flag) && $flag=='Saved') {?>
	<?= Html::button('Cancel', ['title'=>'Cancel','class' => 'btn btn-primary','onclick'=>'cancelProject('.$case_id.',"saved","");']) ?>
	<?php }else if(isset($flag) && $flag=='Edit'){?>
	<?= Html::button('Cancel', ['title'=>'Cancel','class' => 'btn btn-primary','onclick'=>'cancelProject('.$case_id.',"change",'.$task_id.');']) ?>
	<?php }else{?>
	<?= Html::button('Cancel', ['title'=>'Cancel','class' => 'btn btn-primary','onclick'=>'cancelProject('.$case_id.',"","");']) ?>
	<?php }?>
<?= Html::button('Previous', ['title'=>'Previous','class' => 'btn btn-primary','onclick'=>'gotostep(2);']) ?>
<?php if(isset($flag) && ($flag=='Edit')) {?>
     <?= Html::button('ReSubmit', ['title'=>'ReSubmit','class' =>  'btn btn-primary','id'=>'btnsubmit','onclick'=>'ResubmitProject();']) ?>
<?php }else{ ?>
    <?= Html::button('Save', ['title'=>'Save','class' =>  'btn btn-primary','id'=>'btnsave','onclick'=>'SaveProject();']) ?>
    <?= Html::button('Submit', ['title'=>'Submit','class' =>  'btn btn-primary','id'=>'btnsubmit','onclick'=>'SubmitProject();']) ?>
<?php } ?>
</div>
<script>
showLoader();
/* IRT 59 Warning Message Popup */
$('#formbuilder_data').change(function(){
	// change flag to main form
	$('#is_change_form_main').val('1');
});
</script>
<noscript></noscript>
