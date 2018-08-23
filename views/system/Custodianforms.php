<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\components\IsataskFormFlag;
\app\assets\SystemAsset::register($this);
?>
  <div class="right-main-container slide-open" id="maincontainer">
			<fieldset class="two-cols-fieldset">
			<div class="administration-main-cols">
			 <div class="administration-lt-cols pull-left">
			 	<button title="Expand/Collapse" id="controlbtn" class="slide-control-btn" aria-label="Expand or Collapse" onclick="WorkflowToggle();">
                                    <span class="screenreader">Expand or Collapse</span>
				</button>
			  <ul>
			   <li><a href="javascript:void(0);" title="Custodian Form" class="admin-main-title" onclick="changemainform();"><em class="fa fa-folder-open text-danger" title="Custodian Interview Form"></em> Custodian Interview Form</a>
			    <div class="manage-admin-left-module-list">
				<ul class="sub-links">
				 <?php if(!empty($dataCustodianForms)) {
						foreach ($dataCustodianForms as $cform){
					?>
				 	<li class="cfrom" id="cfrom_<?= $cform['Id']?>"><a href="javascript:void(0);" title="<?= $cform['Form_name'] ?>" onclick="updateCustodianFormNew(<?= $cform['Id']?>);"><em class="fa fa-file-o  <?php if(!$cform['Publish']) {?> text-gray <?php } else {?> text-danger <?php }?>" title="<?= $cform['Form_name'] ?>"></em> <?= $cform['Form_name'] ?></a></li>		
				 <?php }} ?>
				</ul>
				</div>
			   </li>
			  </ul>
			 </div>
			 
			 <div class="administration-rt-cols pull-right" id="admin_right">
			  	<?php $form = ActiveForm::begin(['id' => 'formIDcfrom','enableAjaxValidation' => false,'enableClientValidation' => true,'options' => ['enctype'=>'multipart/form-data']]); ?>
			  	<?= IsataskFormFlag::widget(); // change flag ?>
				<div id="first">
					<div class="sub-heading"><a href="javascript:void(0);" title="Add Custodian Interview Form" class="tag-header-black">Add Custodian Interview Form</a></div>
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
				<div id="second" style="display: none">
				<input type="hidden" id="formtype" value="custodianform">
				<div class="form-builder-title">
				 <h2 class="pull-left" id="secondTitle">
					 <a href="javascript:void(0);" title="Custodian Form-1" class="tag-header-black">Custodian Form-1</a></h2>
					 <ul id="form_builder_toolbox1" class="form-builder-nav-panel pull-right">
						<li id='copy' class='toolbox1' title="Add Existing Field"><?= Html::a( Html::img('@web/images/plus-sm-icon.png',['title'=>"Add Existing Field",'alt'=>'Move to Add Existing Field']), null,['href'=>'javascript:void(0);','alt' => 'Move to Add Existing Field']);?></li>
						<li id='text' class='toolbox1' title="Text Heading"><?= Html::a( Html::img('@web/images/heading-sm-icon.png',['title'=>"Text Heading",'alt'=>'Move to Heading']), null,['href'=>'javascript:void(0);','alt' => 'Move to Heading']);?></li>
						<li id='textarea' class='toolbox1' title="Text Multi"><?= Html::a(Html::img('@web/images/textarea-sm-icon.png',['title'=>"Text Multi",'alt'=>'Move to Textarea']),  null,['href'=>'javascript:void(0);','alt' => 'Move to Textarea']);?></li>
						<li id='textbox' class='toolbox1' title="Text Single"><?= Html::a(Html::img('@web/images/input-sm-icon.png',['title'=>"Text Single",'alt'=>'Move to Input Textbox']), null,['href'=>'javascript:void(0);','alt' => 'Move to Input Textbox']);?></li>
						<li id='number' class='toolbox1' title="Number"><?= Html::a(Html::img('@web/images/hashtag-sm-icon.png',['title'=>"Number",'alt'=>'Move to Input Number']), null,['href'=>'javascript:void(0);','alt' => 'Move to Input Number']);?></li>
						<li id='dropdown' class='toolbox1' title="Dropdown"><?= Html::a(Html::img('@web/images/select-sm-icon.png',['title'=>"Dropdown",'alt'=>'Move to Select Box']),  null,['href'=>'javascript:void(0);','alt' => 'Move to Select Box']);?></li>
						<li id='checkbox' class='toolbox1' title="Checkbox"><?= Html::a(Html::img('@web/images/checkbox-sm-icon.png',['title'=>"Checkbox",'alt'=>'Move to Checkbox']),  null,['href'=>'javascript:void(0);','alt' => 'Move to Checkbox']);?></li>
						<li id='radio' class='toolbox1' title="Radio Button"><?= Html::a(Html::img('@web/images/radio-sm-icon.png',['title'=>"Radio Button",'alt'=>'Move to Radio Button']),  null,['href'=>'javascript:void(0);','alt' => 'Move to Radio Button']);?></li>
						<li id='datetime' class='toolbox1' title="Date Picker"><?= Html::a(Html::img('@web/images/calender-sm-icon.png',['title'=>"Date Picker",'alt'=>'Move to Calender Textbox']),  null,['href'=>'javascript:void(0);','alt' => 'Move to Calender Textbox']);?></li>
					 </ul>
				</div>
				<fieldset class="one-cols-fieldset">				
					<div class="create-form" id="form_builder_panel">
				 		<ol class="form-builder-ol ui-sortable"></ol>
					</div>
				<input type="hidden" value="0" name="EvidenceCustodiansForms[Publish]" id="saved"/>
				<input type="hidden" value="" id="sort_order" name="sort_order" />
				
				<ul id="form_builder_properties" style="display:none; position:relative;">
					<li>Select an element to display its options</li>
				</ul>
				</fieldset>
				<div class="button-set text-right">
					 <?= Html::button('Back', ['title'=>"Back",'class' => 'btn btn-primary  pull-left btn-minwidth','onclick'=>'$("#first").show();$("#second").hide();']) ?>
					 <?= Html::button('Cancel', ['title'=>"Cancel",'class' => 'btn btn-primary btn-minwidth','onclick'=>"cancelcustodianform();"]) ?>
					 <?= Html::button('Preview', ['title'=>"Preview",'class' => 'btn btn-primary btn-minwidth','onclick'=>'Admin.formbuilder.preview();','id'=>'preview']) ?>
					 <?= Html::button('Save', ['title'=>"Save",'class' => 'btn btn-primary btn-minwidth','id'=>'submitTaskFormD']) ?>
					 <?= Html::button('Publish', ['title'=>"Publish",'class' => 'btn btn-primary btn-minwidth','onclick'=>'document.getElementById("saved").value=1;','id'=>'publishFormCustD']) ?>
				</div>
				</div>
				<?php ActiveForm::end(); ?>
			 </div>
			</div>
			</fieldset>
			
		   </div>
<div class="dialog" id="form_builder_preview" title="Preview"></div>
<style>.btn-minwidth {
    min-width: 100px;
}</style>
<script type="text/javascript">
/* checkFlagStatus */
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
$('document').ready(function(){ $('#active_form_name').val('formIDcfrom'); }); // add form name
function changemainform(){
	var chk_status = checkformstatus("event","");
	if(chk_status==true)
			commonAjax(baseUrl +'/system/custodianforms','admin_main_container');
}
function cancelcustodianform(){
	var chk = checkformstatus("event","");
	if(chk == true)
		commonAjax(baseUrl +'/system/custodianforms','admin_main_container');
}
$('input').customInput();
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
	   submitForm();
   });
   submitForm = function (){

	var form = $('#formIDcfrom');
	console.log('form.serialize=>',form.serialize());
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
});
setTimeout(function(){autosize(document.querySelector('textarea'));},100);
function NextCF(){
	if($('#evidencecustodiansforms-form_name').val()==''){
		$("#evidencecustodiansforms-form_name").trigger('blur');
	}
	else{
		$("#first").hide();
		$("#second").show();
		$("#secondTitle").text($("#evidencecustodiansforms-form_name").val());
	}
}

</script>
<noscript></noscript>
