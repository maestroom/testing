<?php
use yii\helpers\Html;
use app\components\IsataskFormFlag;
use yii\widgets\ActiveForm;
\app\assets\SystemAsset::register($this);
?>
<style>
.btn-minwidth {
	min-width: 100px;
}
</style>
			  	
		<?php $form = ActiveForm::begin(['id' => 'formIDcfrom','enableAjaxValidation' => false,'enableClientValidation' => true,'options' => ['enctype'=>'multipart/form-data','class'=>'frm_form_builder']]); ?>
		<?= IsataskFormFlag::widget(); // change flag ?>
		<div id="first">
			<div class="sub-heading"><a href="javascript:void(0);" class="tag-header-black" title="Edit Custodian Interview Form">Edit Custodian Interview Form</a></div>
		
			<fieldset class="one-cols-fieldset">
			 <div class="custodian-form">
			   <?= $form->field($model, 'Form_name',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-9'>{input}\n{hint}\n{error}</div></div>"])->textInput(['maxlength'=>$ecf_length['Form_name']])->label($model->getAttributeLabel('Form_name'), ['class'=>'form_label']) ?>
			   <?= $form->field($model, 'Form_desc',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-9'>{input}\n{hint}\n{error}</div></div>"])->textArea(['rows'=>5,'maxlength'=>$ecf_length['Form_desc']])->label($model->getAttributeLabel('Form_desc'), ['class'=>'form_label']) ?>
			 </div>
			  
			</fieldset>
		
			<div class="button-set text-right">
				<?= Html::button('Cancel', ['title'=>"Cancel",'class' => 'btn btn-primary','onclick'=>'clearForm($("#formIDcfrom"));']) ?>
				<?= Html::button('Next', ['title'=>"Next",'class' => 'btn btn-primary','onclick'=>'NextCF();']) ?>
			</div>
		</div>
		<div id="second">
		<input type="hidden" id="formtype" value="custodianform">
		<div class="form-builder-title">
		 <h2 class="pull-left" id="secondTitle">Custodian Form-1</h2>
			 <ul id="form_builder_toolbox1" class="form-builder-nav-panel pull-right">
				<li id='copy' class='toolbox1' title="Add Existing Field"><?= Html::a( Html::img('@web/images/plus-sm-icon.png',['title'=>"Add Existing Field",'alt'=>'Move to Add Existing Field']), null,['href'=>'javascript:void(0);','alt' => 'Move to Add Existing Field']);?></li> 
				<li id='text' class='toolbox1' title="Text Heading"><?= Html::a( Html::img('@web/images/heading-sm-icon.png',['title'=>"Text Heading",'alt'=>'Move to Heading']), null,['href'=>'javascript:void(0);','title' => 'Move to Heading','aria-label'=>'Move to Heading']);?></li>
				<li id='textarea' class='toolbox1' title="Text Multi"><?= Html::a(Html::img('@web/images/textarea-sm-icon.png',['title'=>"Text Multi",'alt'=>'Move to Textarea']),  null,['href'=>'javascript:void(0);','alt' => 'Move to Textarea','aria-label'=>'Move to Textarea']);?></li>
				<li id='textbox' class='toolbox1' title="Text Single"><?= Html::a(Html::img('@web/images/input-sm-icon.png',['title'=>"Text Single",'alt'=>'Move to Input Textbox']), null,['href'=>'javascript:void(0);','alt' => 'Move to Input Textbox','aria-label'=>'Move to Input Textbox']);?></li>
				<li id='number' class='toolbox1' title="Number"><?= Html::a(Html::img('@web/images/hashtag-sm-icon.png',['title'=>"Number",'alt'=>'Move to Input Number']), null,['href'=>'javascript:void(0);','alt' => 'Move to Input Number']);?></li>
				<li id='dropdown' class='toolbox1' title="Dropdown"><?= Html::a(Html::img('@web/images/select-sm-icon.png',['title'=>"Dropdown",'alt'=>'Move to Select Box']),  null,['href'=>'javascript:void(0);','alt' => 'Move to Select Box','aria-label'=>'Move to Select Box']);?></li>
				<li id='checkbox' class='toolbox1' title="Checkbox"><?= Html::a(Html::img('@web/images/checkbox-sm-icon.png',['title'=>"Checkbox",'alt'=>'Move to Checkbox']),  null,['href'=>'javascript:void(0);','alt' => 'Move to Checkbox','aria-label'=>'Move to Checkbox']);?></li>
				<li id='radio' class='toolbox1' title="Radio Button"><?= Html::a(Html::img('@web/images/radio-sm-icon.png',['title'=>"Radio Button",'alt'=>'Move to Radio Button']),  null,['href'=>'javascript:void(0);','alt' => 'Move to Radio Button','aria-label'=>'Move to Radio Button']);?></li>
				<li id='datetime' class='toolbox1' title="Date Picker"><?= Html::a(Html::img('@web/images/calender-sm-icon.png',['title'=>"Date Picker",'alt'=>'Move to Calender Textbox']),  null,['href'=>'javascript:void(0);','alt' => 'Move to Calender Textbox','aria-label'=>'Move to Calender Textbox']);?></li>
			</ul>
		</div>
		<fieldset class="one-cols-fieldset">
			<div class="create-form" id="form_builder_panel">
					<ol class="form-builder-ol"></ol>
			</div>
		<input type="hidden" value="0" name="EvidenceCustodiansForms[Publish]" id="saved"/>
		<input type="hidden" value="" id="sort_order" name="sort_order" />
		<ul id="form_builder_properties" style="display:none; position:relative;">
			<li>Select an element to display its options</li>
		</ul>
		</fieldset>
		<div class="button-set text-right">
		 <?= Html::button('Back', ['title'=>"Back",'class' => 'btn btn-primary  pull-left btn-minwidth','onclick'=>'$("#first").show();$("#second").hide();']) ?>
		 <?= Html::button('Cancel', ['title'=>"Cancel",'class' => 'btn btn-primary btn-minwidth','onclick'=>"cancelcustodian();"]) ?>
		 <?= Html::button('Preview', ['title'=>"Preview",'class' => 'btn btn-primary btn-minwidth','onclick'=>'Admin.formbuilder.preview();','id'=>'preview']) ?>
		 <?= Html::button('Remove', ['title'=>"Remove",'class' => 'btn btn-primary btn-minwidth','id'=>'deleteCustForm']) ?>
		 <?php if(!$model->Publish){ echo Html::button('Save', ['title'=>"Save",'class' => 'btn btn-primary btn-minwidth','id'=>'submitTaskFormD']); } ?>
		 <?= Html::button('Publish', ['title'=>"Publish",'class' => 'btn btn-primary btn-minwidth','onclick'=>'document.getElementById("saved").value=1;','id'=>'publishFormCustD']) ?>
		</div>
		</div>
		<?php ActiveForm::end(); ?>
		<?php //echo "<pre>",print_r($formbuilder_data),"</pre>";die; ?>
		<div style="displa:none">
		<?php if(!empty($formbuilder_data)){?>
		<form method="POST" id="custodian-edit" autocomplete="off">
			<?php 
				foreach ($formbuilder_data as $ele_id=>$fdata) {
				if($fdata['remove'] == 0){
			?>
			<input type="hidden" name="<?=$ele_id?>[id]" value="<?=$ele_id?>">
			<input type="hidden" name="<?=$ele_id?>[type]" value="<?=$fdata['type'] ?>">
			<input type="hidden" name="<?=$ele_id?>[label]" value="<?=Html::encode($fdata['label'])?>">
			<input type="hidden" name="<?=$ele_id?>[value]" value="<?=Html::encode($fdata['value'])?>">
			<input type="hidden" name="<?=$ele_id?>[values]" value="<?=Html::encode($fdata['values'])?>">
			<!--<input type="hidden" name="<?php //$ele_id?>[values_option_ids]" value="<?php //Html::encode($fdata['values_option_ids'])?>">-->
			<input type="hidden" name="<?=$ele_id?>[description]" value="<?=Html::encode($fdata['description'])?>">
			<input type="hidden" name="<?=$ele_id?>[required]" value="<?=$fdata['required'] ?>">
			<input type="hidden" name="<?=$ele_id?>[order]" value="<?=$fdata['order'] ?>">
			<input type="hidden" name="<?=$ele_id?>[text_val]" value="<?=Html::encode($fdata['text_val'])?>">
			<input type="hidden" name="<?=$ele_id?>[sync_prod]" value="<?=$fdata['sync_prod'] ?>">
			<!--<input type="hidden" name="<?php //$ele_id ?>[field_type]" value="<?php //$fdata['field_type'] ?>">-->
			<input type="hidden" name="<?=$ele_id?>[optionchk]" value="<?=$fdata['optionchk'] ?>">
			<input type="hidden" name="<?=$ele_id?>[qareportuse]" value="<?=$fdata['qareportuse'] ?>">
			<input type="hidden" name="<?=$ele_id?>[default_answer]" value="<?=$fdata['default_answer'] ?>">
			<input type="hidden" name="<?=$ele_id?>[default_unit]" value="<?=$fdata['default_unit'] ?>">
			<input type="hidden" name="<?=$ele_id?>[edit]" value="1">
			<?php }}?>
			
		</form>
		<?php }?>
		</div>
	 </div>
<div class="dialog" id="form_builder_preview" title="Preview"></div>
<script type="text/javascript">
$('input').bind('input', function(){
	$("#formIDcfrom #is_change_form").val('1'); // change flag to 1
	$("#formIDcfrom #is_change_form_main").val('1'); // change flag to 1
});	
$('textarea').bind("input",function(){
	$("#formIDcfrom #is_change_form").val('1'); // change flag to 1
	$("#formIDcfrom #is_change_form_main").val('1'); // change flag to 1
});
$('#form_builder_toolbox1').click(function(){
	$("#formIDcfrom #is_change_form").val('1'); // change flag to 1
	$("#formIDcfrom #is_change_form_main").val('1'); // change flag to 1
});
$('document').ready(function(){
	$('#active_form_name').val('formIDcfrom');
});

/*CancelCustodian*/
function cancelcustodian(){
	var chk_status = checkformstatus('event');
	if(chk_status == true)
		commonAjax(baseUrl +'/system/custodianforms','admin_main_container');
}

jQuery(document).ready(function($) {
   $("#second").hide();
   /* $( "#tabs" ).tabs({
      beforeLoad: function( event, ui ) {
        ui.jqXHR.error(function() {
          ui.panel.html("Error loading current tab." );
        });
      }
    }); */
   Admin.formbuilder.init();
   $("#submitTaskFormD").click(function(){
	   submitForm();
   });
   $("#publishFormCustD").click(function(){
		if(form_validation()){
			submitForm();
		}
   });
   $("#deleteCustForm").click(function(){
	   var form_name = $('#evidencecustodiansforms-form_name').val();
	   arg = new Array();
	   arg[0]='<?php echo $id?>';
	   if(confirm('Are you sure you want to Remove '+form_name+'?'))
	   	   deleteCustForm('<?php echo $id?>');
	   //dailogConfirmed('Confirm','Are you sure you want to delete this record?','deleteCustForm',arg)
   });
   submitForm = function (){
	var form = $('#formIDcfrom');
	//console.log('form.serialize=>',form.serialize());
	$.ajax({
        url    : form.attr('action'),
        type   : 'post',
        data   : form.serialize(),
        beforeSend : function()    {
        	$('.btn').attr('disabled','disabled');
        },
        success: function (response){
        	if(response == 'OK'){
        		commonAjax(baseUrl +'/system/custodianforms','admin_main_container');
        	}else{
            	$('.btn').removeAttr("disabled");
        	}
        },
        error  : function (){
            console.log('internal server error');
        }
    });
  }
    var Url = Admin.formbuilder.BASEURL+'?action=element_bulk';
	var into = $("#form_builder_panel ol");
	$.ajax({
		url: Url,
		type:"post",
		data:$('#custodian-edit').serialize(),
		cache: false,
		dataType:'json',
		success:function(result){
			//$(into).html('');
			$.each(result,function(key,val){
				//alert(into);
				$(into).prepend(val);
				var $newrow = $(into).find('li:first');
				Admin.formbuilder.properties($newrow);
				Admin.formbuilder.layout($newrow);
				Admin.formbuilder.attr.update($newrow);
				//show
				var id=$('#form_builder_panel ol li label:first').attr('for');
				var id_a=$('#form_builder_panel ol li label:first').find('a').attr('href');
				$newrow.hide().slideDown('slow');
				$(into).sortable("refresh");
			});
			delete result;
			//$('#custodian-edit').remove();
		},
		complete:function(){
			//$('input').customInput();
			$('.datepickers').each(function(e){
				var datepicker_id = $(this).attr('id');
				var formElements={};
				formElements[datepicker_id] = "%m/%d/%Y";
				datePickerController.createDatePicker({formElements: formElements });	
			});
		}
	});
});

setTimeout(function(){autosize(document.querySelector('textarea'));},100);
function NextCF(){
	if($('#evidencecustodiansforms-form_name').val()==''){
		$("#evidencecustodiansforms-form_name").trigger('blur');
	}else{
		$("#first").hide();
		$("#second").show();
		$("#secondTitle").text($("#evidencecustodiansforms-form_name").val());
	}
}
$('.datepickers').each(function(e){
	var datepicker_id = $(this).attr('id');
	var formElements={};
	formElements[datepicker_id] = "%m/%d/%Y";
	datePickerController.createDatePicker({formElements: formElements });	
});
</script>
<noscript></noscript>
