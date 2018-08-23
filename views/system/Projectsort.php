<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\components\IsataskFormFlag;
\app\assets\CustomInputAsset::register($this);
$js = <<<JS
// get the form id and set the event
$('form#{$model->formName()}').on('beforeSubmit', function(e) {
   var form = $(this);
		$.ajax({
            url    : form.attr('action'),
            type   : 'post',
            data   : form.serialize(),
            beforeSend : function()    {
            	$('#submitMediaDataType').attr('disabled','disabled');
            },
            success: function (response){
            	if(response == 'OK')
             		commonAjax(baseUrl +'/system/projectsort','admin_main_container');
            	else{
                	$('#submitMediaDataType').removeAttr("disabled");
            	}
            },
            error  : function (){
                console.log('internal server error');
            }
        });
		return false;
   // do whatever here, see the parameter \$form? is a jQuery Element to your form
}).on('submit', function(e){
    e.preventDefault();
});
JS;

$this->registerJs($js);
?>
<style>
/* webroot/css/main.css */
DIV#settings-fieldvalue {
    padding-left: 1em;
}
 
DIV#settings-fieldvalue LABEL{
display: block;
}
DIV#settings-fieldvalue INPUT {
    display: inline;
}
</style>
<div class="right-main-container" role="radiogroup" aria-labelledby="project_sort_display">			
    <div class="sub-heading" id="project_sort_display"><a href="javascript:void(0);" title="Project Sort Display" class="tag-header-black">Project Sort Display</a></div>
	 <?php $form = ActiveForm::begin(['id' => $model->formName(),'enableAjaxValidation' => false,'enableClientValidation' => true]); ?>
	 <?= IsataskFormFlag::widget(); // change flag ?>
	 <!--<fieldset class="one-cols-fieldset">-->
         <div class="one-cols-fieldset">
	 <legend class="sr-only">Project Sort Display</legend>
	 <div class="administration-form">
	 <?php 
            $model->field= 'project_sort';
            echo $form->field($model, 'field')->hiddenInput()->label(false);
            if($model->fieldvalue === null) $model->fieldvalue = 0;
            $list = [3 => 'By Team Priority then by Project Priority then by Project Due Date in descending order', 0 => 'By Project Priority then by Project Due Date in descending order (Default)', 1 => 'By Due Date in descending order', 2=>'By Project # in descending order'];
                /* echo $form->field($model, 'fieldvalue',['template' => "{label}{input}"])->radioList($list,['item' => function($index, $label, $name, $checked, $value) {
                        $return = '<label for="'.$name.'-'.$value.'">';
                        if($checked)
                                $return .= '<input id="'.$name.'-'.$value.'" checked="'.$checked.'"  type="radio" name="' . $name . '" value="' . $value . '" tabindex="3">';
                        else
                                $return .= '<input id="'.$name.'-'.$value.'"  type="radio" name="' . $name . '" value="' . $value . '" tabindex="3">';

                        $return .= ucwords($label);
                        $return .= '</label>';

                        return $return;
                }])->label('&nbsp;'); */ ?>		
                <div class="custom-inline-block-width">
                    <?php $i=1; $count = count($list); foreach ($list as $id=>$label){ ?>
                        <input title="Project Sort Display <?= $label ?>" type="radio" aria-setsize="<?= $count ?>" aria-posinset="<?= $i ?>" name="Settings[fieldvalue]" id="fieldvalue-<?=$id?>" value="<?=$id?>" <?php if($model->fieldvalue==$id){?>checked="checked"<?php }?>/>
                        <label for="fieldvalue-<?=$id?>" ><?= $label ?></label>
                    <?php $i++; } ?>
                </div>
	 </div>
<!--	</fieldset> -->
         </div>
	<div class="button-set text-right">
	<?= Html::submitButton('Update', ['title'=>"Update",'class' => 'btn btn-primary','id'=>'submitMediaDataType']) ?>
	</div>
	<?php ActiveForm::end(); ?>
</div>
<script>
$(function() {
  $('input').customInput();
  $('#active_form_name').val('Settings');
});
$(':radio').change(function(){
	$('#Settings #is_change_form').val('1');
	$('#Settings #is_change_form_main').val('1');  // settings flag change
});
</script>
<noscript></noscript>