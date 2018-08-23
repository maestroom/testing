<?php
// Role Form
use yii\helpers\Html;	
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\components\IsataskFormFlag;
// End
$first_tab = 'Add';
//echo "<prE>",print_r($security_features),"</pre>";die;
?>
<fieldset class="two-cols-fieldset">
	<div id="role-tabs">
		<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all" role="tablist">
			<?php if(!$model->isNewRecord){ $first_tab = 'Edit'; } ?>
			<li><a href="#first" title="<?= $first_tab ?> Role"><?= $first_tab ?> Role</a></li>
			<li><a href="#second" title="Role Security">Role Security</a></li>
		</ul>
		<div id="first">
		<?php $form = ActiveForm::begin(['action'=> (($model->isNewRecord==true)?Url::to(['user/addrole']):Url::to(['user/editrole'])),'id' => $model->formName(), 'enableAjaxValidation' => false, 'enableClientValidation' => true]); ?>
			<?= IsataskFormFlag::widget(); // change flag ?>
			<div class="tab-inner-fix">
<!-- 				<fieldset class="one-cols-fieldset"> -->
					<div class="create-form">
                                            <?= $form->field($model, 'id')->hiddenInput()->label(false); ?>
                                              <?php $model->role_type = explode(",", $model->role_type); ?>
                                                <?= $form->field($model, 'role_type',['template' => '<div class="row input-field custom-full-width"><div class="col-md-2">{label}</div><div class="col-md-8"><div class="row"><legend class="sr-only">Role Type</legend>{input}<div class="col-sm-12">{error}</div>{hint}</div></div></div>','labelOptions'=>['class'=>'form_label']])->checkboxList(['1' => ' Case Manager', '2' => ' Team Member'],
                                                        ['item' => function($index, $label, $name, $checked, $value) {
                                                            $return = '<div class="col-sm-5">';
                                                            if ($checked)
                                                                $return .= '<input aria-labelledby="role_type_lbl_chk_'.$index.'" id="role_type-' . $value . '" checked="' . $checked . '" title="This field is required"  type="checkbox" name="' . $name . '" value="' . $value . '">';
                                                            else
                                                                $return .= '<input aria-labelledby="role_type_lbl_chk_'.$index.'" id="role_type-' . $value . '" title="This field is required" type="checkbox" name="' . $name . '" value="' . $value . '">';
                                                            $return .= '<label id="role_type_lbl_chk_'.$index.'" for="role_type-' . $value . '" class="form_label">';
                                                            $return .= ucwords($label);
                                                            $return .= '</label></div>';
                                                            return $return;
                                                        }]
                                                    ); ?>
                                                <?= $form->field($model, 'role_name',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['maxlength'=>$model_field_length['role_name']]); ?>
                                                <?= $form->field($model, 'role_description',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textArea(['rows'=>5,'maxlength'=>$model_field_length['role_description']]); ?>
                                            </div>
<!-- 					</fieldset> -->
				</div>
				<div class="button-set text-right">
						<?= Html::button($model->isNewRecord ? 'Cancel' : 'Delete', ['title' => $model->isNewRecord ? 'Cancel' : 'Delete','class' =>  'btn btn-primary','id'=>'roleDetails','onclick'=>'RoleAction("'.$model->id.'");']) ?>
			  	   		<?= Html::button('Next', ['title' => 'Next','class' => 'btn btn-primary','onclick'=>'NextRoleSecurity("'.$model->formName().'",this);']) ?>
				</div>
		  	<?php ActiveForm::end(); ?>
		</div>
		
		<div id="second">
			<div id="form_div">
				<?= $this->render('_rolesecuritygrid', [
					'model' => $model,
					'security_features' => $security_features
				]); ?>
			</div>
		</div>
	</div>
</fieldset>

<script type = "text/javascript">
$(function() {
  $('input').customInput();
});
/* Start : Role form */
$('input').bind('input', function(){
	$('#is_change_form').val('1');
	$('#Role #is_change_form_main').val('1');
});
$('textarea').bind('input', function(){ 
	$('#Role #is_change_form').val('1'); 
	$('#Role #is_change_form_main').val('1'); 
});
$(':checkbox').change(function() {
	$('#Role #is_change_form').val('1');
	$('#Role #is_change_form_main').val('1'); 
});
$('document').ready(function(){ $('#active_form_name').val('Role'); });
/* End */

$(function() {
    $( "#role-tabs" ).tabs({
      beforeActivate: function (e, ui) {
    	  /*if(ui.newPanel.attr('id') == 'second'){
            e.preventDefault();	  
     	  }
      	  if(ui.newPanel.attr('id') == 'first'){
            e.preventDefault();	  
    	  }*/
      },
      beforeLoad: function( event, ui ) {
        ui.jqXHR.error(function() {
          ui.panel.html(
            "Error loading current tab." );
        });
      }
    });
    $("#role-tabs").tabs({disabled: 1});
 });
 			
/**
 * Page Load Admin Right second div hide by default
 * jQuery Ready Event
 */
jQuery(document).ready(function(){
    $("#role-tabs").tabs({disabled: [1]});
});

/**
 * Role Security Page shows after Role Edit or Add
 * Action NextRoleSecurity
 */
function NextRoleSecurity(formid,btn){
    
	var form = $('form#'+formid).serialize();
	var role_id = $('#role-id').val();
	if($('#role_type-1').is(':checked') == false && $('#role_type-2').is(':checked') == false){
		$("#role_type-1").trigger('blur'); $("#role-role_name").trigger('blur');
	} else if($('#role-role_name').val()==''){
		$("#role-role_name").trigger('blur');
	} else {
		//$("#role-tabs").tabs("enable", 1);
		if(role_id != ''){
			$.ajax({
		      	url    : baseUrl +'/user/role-security-update',
		        cache: false,
		        type   : 'post',
		        data   : 'role_id='+role_id+'&data='+form,
		        beforeSend:function (response) {showLoader();},
		        success: function (response){
		        	hideLoader();
                                $("#role-tabs").tabs({disabled: [0]});
                                $("#role-tabs").tabs({enabled: [1]});
                                $('#role-tabs').tabs({active:1});
                                $("#second").html(response);
                                /*jQuery("#second").show();			        
		    	  	jQuery("#first").hide();
		    	  	jQuery('li[aria-controls="second"]').addClass('ui-tabs-active ui-state-active');
				jQuery('li[aria-controls="first"]').removeClass('ui-tabs-active ui-state-active');*/
		        },
		        error  : function (){
		            console.log('internal server error');
		        }
		    });
		}else{
			$.ajax({
		      	url    : baseUrl +'/user/role-security-add',
		        cache: false,
		        type   : 'post',
		        data   : form,
		        beforeSend:function (response) {showLoader();},
		        success: function (response){
		        	hideLoader();
		 		/*jQuery('li[aria-controls="second"]').addClass('ui-tabs-active ui-state-active');
		 		jQuery('li[aria-controls="first"]').removeClass('ui-tabs-active ui-state-active');
		     		jQuery('#role_details').val(form);		     		
		 		jQuery("#second").show(); jQuery("#first").hide();*/
                                $("#second").html(response);
                                $("#role-tabs").tabs({disabled: [0]});
                                $("#role-tabs").tabs({enabled: [1]});
                                $('#role-tabs').tabs({active:1});
		        
		        },
		        error  : function (){
		            console.log('internal server error');
		        }
		    });
		}
	}
}

$('.myheader').on('click',function(){
	if($(this).hasClass('myheader-selected-tab')){
		$(this).removeClass('myheader-selected-tab');
	}else{
		$(this).addClass('myheader-selected-tab');
	}	
});
</script>
<noscript></noscript>
