<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\components\IsataskFormFlag;
\app\assets\SystemAsset::register($this);
$sync_prod = Yii::$app->params['sync_prod'];
?>

<?php $form = ActiveForm::begin(['id' => 'formIDServicefrom','enableAjaxValidation' => false,'enableClientValidation' => true,'options' => ['enctype'=>'multipart/form-data']]); ?>
<?= IsataskFormFlag::widget(); // change flag ?>
<div class="form-builder-title">
    <input type="hidden" id="formtype" value="<?= ($get_data['form'] == 'instruction')?'instructionform':'dataform'; ?>">
    <h2 class="pull-left" id="secondTitle"><?=$servicetaskModel->service_task; ?> <?php if($get_data['form'] == 'instruction'){ echo ' - (Instruction)'; } else { echo ' - Task Outcome Form'; }?></h2>
    <ul id="form_builder_toolbox1" class="form-builder-nav-panel pull-right">
        <li id='copy' class='toolbox1' title="Add Existing Field"><?= Html::a( Html::img('@web/images/plus-sm-icon.png',['title'=>"Add Existing Field",'alt'=>'Move to Add Existing Field']), null,['href'=>'javascript:void(0);','alt' => 'Move to Add Existing Field']);?></li>
        <li id='text' class='toolbox1' title="Text Heading"><?= Html::a( Html::img('@web/images/heading-sm-icon.png',['title'=>"Text Heading",'alt'=>'Move to Heading']), null,['href'=>'javascript:void(0);','alt' => 'Move to Heading']);?></li>
        <li id='textarea' class='toolbox1' title="Text Multi"><?= Html::a(Html::img('@web/images/textarea-sm-icon.png',['title'=>"Text Multi",'alt'=>'Move to Textarea']),  null,['href'=>'javascript:void(0);','alt' => 'Move to Textarea']);?></li>
        <li id='textbox' class='toolbox1' title="Text single"><?= Html::a(Html::img('@web/images/input-sm-icon.png',['title'=>"Text single",'alt'=>'Move to Input Textbox']), null,['href'=>'javascript:void(0);','alt' => 'Move to Input Textbox']);?></li>
        <li id='number' class='toolbox1' title="Number"><?= Html::a(Html::img('@web/images/hashtag-sm-icon.png',['title'=>"Number",'alt'=>'Move to Input Number']), null,['href'=>'javascript:void(0);','alt' => 'Move to Input Number']);?></li>
        <li id='dropdown' class='toolbox1' title="Dropdown"><?= Html::a(Html::img('@web/images/select-sm-icon.png',['title'=>"Dropdown",'alt'=>'Move to Select Box']),  null,['href'=>'javascript:void(0);','alt' => 'Move to Select Box']);?></li>
        <li id='checkbox' class='toolbox1' title="Checkbox"><?= Html::a(Html::img('@web/images/checkbox-sm-icon.png',['title'=>"Checkbox",'alt'=>'Move to Checkbox']),  null,['href'=>'javascript:void(0);','alt' => 'Move to Checkbox']);?></li>
        <li id='radio' class='toolbox1' title="Radio Button"><?= Html::a(Html::img('@web/images/radio-sm-icon.png',['title'=>"Radio Button",'alt'=>'Move to Radio Button']),  null,['href'=>'javascript:void(0);','alt' => 'Move to Radio Button']);?></li>
        <li id='datetime' class='toolbox1' title="Date Picker"><?= Html::a(Html::img('@web/images/calender-sm-icon.png',['title'=>"Date Picker",'alt'=>'Move to Calender Textbox']),  null,['href'=>'javascript:void(0);','alt' => 'Move to Calender Textbox']);?></li>
    </ul>
</div>
<fieldset class="one-cols-fieldset">
	<div class="create-form" id="form_builder_panel">
		<ol class="form-builder-ol"></ol>
	</div>
	<input type="hidden" value="0" name="Servicetask[publish]" id="saved" />
	<input type="hidden" value="" id="sort_order" name="sort_order" />
	<input type="hidden" value="<?=$get_data['form']?>" id="form" name="form" />
	<ul id="form_builder_properties" style="display: none; position: relative;">
            <li>Select an element to display its options</li>
	</ul>
</fieldset>
<div class="button-set text-right">
    <?= Html::button('Cancel', ['title'=>"Cancel",'class' => 'btn btn-primary','onclick'=>'CancelServiceTaskForm('.$servicetaskModel->teamId.','.$servicetaskModel->teamservice_id.');'])?>
    <?= Html::button('Preview', ['title'=>"Preview",'class' => 'btn btn-primary','onclick'=>'Admin.formbuilder.preview();','id'=>'preview'])?>
    <?php if(($servicetaskModel->hasform==1 && $servicetaskModel->publish==1 && $get_data['form'] == 'instruction') || ($servicetaskModel->data_publish==1 && $servicetaskModel->data_hasform==1 && $get_data['form'] == 'data')){
        }else{ ?>
        <?= Html::button('Save', ['title'=>"Save",'class' => 'btn btn-primary','id'=>'submitServiceTaskForm']) ?>
        <?php } ?>
    <?php if($get_data['mod'] == 'edit'){?>
            <?= Html::button('Remove', ['title'=>"Remove",'class' => 'btn btn-primary text-left','id'=>'deleteServiceTaskForm'])?>
    <?php }?>
    <?= Html::button('Publish', ['title'=>"Publish",'class' => 'btn btn-primary','onclick'=>'document.getElementById("saved").value=1;','id'=>'publishServiceTaskForm'])?>
    <?= $form->field($servicetaskModel, 'teamservice_id')->hiddenInput()->label(false); ?>
    <?= $form->field($servicetaskModel, 'teamId')->hiddenInput()->label(false); ?>
</div>
<?php ActiveForm::end(); ?>
<div class="dialog" id="form_builder_preview" title="Preview"></div>
<?php if(!empty($formbuilder_data)){?>
<form method="POST" id="formbuilder-edit" autocomplete="off">
	<?php foreach ($formbuilder_data as $ele_id=>$fdata) {
		if($fdata['remove'] == 0){ 
        //echo "<pre>",print_r($fdata),"</pre>";
        ?>

		<input type="hidden" name="<?=$ele_id?>[id]" value="<?=$ele_id?>">
                <input type="hidden" name="<?=$ele_id?>[form_type]" value="<?=$fdata['form_type']?>"> 
                <input type="hidden" id="hide_<?=$ele_id?>" name="<?=$ele_id?>[element_view]" value="<?=$fdata['element_view']?>"> 
		<input type="hidden" name="<?=$ele_id?>[type]" value="<?=$fdata['type'] ?>"> 
		<input type="hidden" name="<?=$ele_id?>[label]" value="<?=($fdata['label'])?>">
		<input type="hidden" name="<?=$ele_id?>[value]"	value="<?=($fdata['value'])?>"> 
		<input type="hidden" name="<?=$ele_id?>[values]" value="<?= $fdata['values']?>"> 
		<input type="hidden" name="<?=$ele_id?>[description]" value="<?=($fdata['description'])?>"> 
		<input type="hidden" name="<?=$ele_id?>[required]" value="<?=$fdata['required'] ?>">
		<input type="hidden" name="<?=$ele_id?>[no_load_prev]" value="<?=$fdata['no_load_prev'] ?>"> 
		<input type="hidden" name="<?=$ele_id?>[order]" value="<?=$fdata['order'] ?>">
		<input type="hidden" name="<?=$ele_id?>[text_val]" value="<?=($fdata['text_val'])?>"> 
		<input type="hidden" name="<?=$ele_id?>[sync_prod]" value="<?= $sync_prod[$fdata['sync_prod']]; ?>"> 
		<!-- <input type="hidden" name="<?php //$ele_id?>[field_type]" value="<?php //$fdata['field_type'] ?>"> -->
		<input type="hidden" name="<?=$ele_id?>[optionchk]" value="<?=$fdata['optionchk'] ?>"> 
		<input type="hidden" name="<?=$ele_id?>[qareportuse]" value="<?=$fdata['qareportuse'] ?>"> 
		<input type="hidden" name="<?=$ele_id?>[default_answer]" value="<?= ($fdata['default_answer']); ?>">
		<input type="hidden" name="<?=$ele_id?>[default_unit]" value="<?=$fdata['default_unit'] ?>">
		<input type="hidden" name="<?=$ele_id?>[edit]" value="1">
	<?php }} //die('here');?>
</form>
<?php }?>
<script type="text/javascript">
/* change flag form */
$('#form_builder_toolbox1').click(function(){
	$("#formIDServicefrom #is_change_form").val("1"); // change flag to 1
	$("#is_change_form_main").val("1"); // change flag to 1
});
jQuery(document).ready(function($) {
   $("#active_form_name").val('formIDServicefrom'); // Form Name actuve
   Admin.formbuilder.init();
   $("#submitServiceTaskForm").click(function(){
	   submitForm(0);
   });
   $("#publishServiceTaskForm").click(function(){
	if(form_validation()){
            submitForm(1);
	}
   });
   $("#deleteServiceTaskForm").click(function(){	
        var secondtitle = $('#secondTitle').html();
        if(confirm("Are you sure you want to Remove "+secondtitle+"?")){
             jQuery.ajax({
                 url: baseUrl +'/workflow/deleteselectedform',
                 data:{id:<?=$servicetaskModel->id?>,form:'<?=$get_data['form']?>'},
                 type: 'post',
                 success: function (data) {
                     hideLoader();
                     if(data == 'OK'){
                             CancelServiceTask(<?=$servicetaskModel->teamId?>,<?=$servicetaskModel->teamservice_id?>);
                     }else{
                          //  alert('Error');
                     }
                 }
            });
        }
   });
   submitForm = function (publish)
   {
	var form = $('#formIDServicefrom');
	$.ajax({
        url    : form.attr('action'),
        type   : 'post',
        data   : form.serialize()+'&publish='+publish,
        beforeSend : function()    {
            showLoader();
        	$('.btn').attr('disabled','disabled');
        },
        success: function (response){
        	if(response == 'OK'){
        		CancelServiceTask(<?=$servicetaskModel->teamId?>,<?=$servicetaskModel->teamservice_id?>);
        	}else{
            	$('.btn').removeAttr("disabled");
        	}
        },
        error  : function (){
            console.log('internal server error');
        }
    });
  }
});
</script>
<noscript></noscript>
<?php if(!empty($formbuilder_data)){?>
<script type="text/javascript">
var Url = Admin.formbuilder.BASEURL+'?action=element_bulk';
var into = $("#form_builder_panel ol");
$.ajax({
	url: Url,
	type:"post",
	data:$('#formbuilder-edit').serialize(),
	cache: false,
	dataType:'json',
	success:function(result){
		//$(into).html('');
		$.each(result,function(key,val){
                    $(into).prepend(val);
                    var $newrow = $(into).find('li:first');
                    Admin.formbuilder.properties($newrow);
                    Admin.formbuilder.layout($newrow);
                    Admin.formbuilder.attr.update($newrow);
                    //show
                    var id=$('#form_builder_panel ol li label:first').attr('for');
                    var id_a=$('#form_builder_panel ol li label:first').find('a').attr('href');
                    $newrow.hide().slideDown('slow');
		});
		delete result;
		//$('#custodian-edit').remove();
	},
	complete:function(){
            $('.datepickers').each(function(e){
                var datepicker_id = $(this).attr('id');
                var formElements={};
                formElements[datepicker_id] = "%m/%d/%Y";
                datePickerController.createDatePicker({formElements: formElements });	
            });
	}
});



</script>
<noscript></noscript>
<?php }?>
