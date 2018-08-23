<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\components\IsataskFormFlag;
/* @var $this yii\web\View */
/* @var $model app\models\UnitPrice */
/* @var $form yii\widgets\ActiveForm */
$js = <<<JS
function SaveTeam(form_id,btn){
		
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
				commonAjax(baseUrl +'/workflow/operationalteam&team_id=0','admin_main_container');
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

function DeleteTeam(team_id){
	var team_name = $('#team-team_name').val();
	if (confirm('Are you sure you want to Delete '+team_name+'?')){
		jQuery.ajax({
			url: baseUrl +'/workflow/removeteam&id='+team_id,
			type: 'get',
			beforeSend:function (data) {showLoader();},
			success: function (data) {
					hideLoader();
					if(data == 'true'){
						commonAjax(baseUrl +'/workflow/operationalteam&team_id=0','admin_main_container');
					}else{
						alert(team_name+" cannot be Deleted.");
					}
				}
			
		});
	}

}

function updateTeamservice(form_id,btn){
  var old_locs= $('#old_teamloc').val().split(',');   
  var curr_locs = $('.teamservice_locs:checkbox:checked');
  var cure_loc_arr = new Array();
  $('.teamservice_locs:checkbox:checked').each(function(){
  	cure_loc_arr.push($(this).val());
  });
  if(curr_locs.length==old_locs.length){
         SaveTeam(form_id,btn);    				
  }else{
       $.ajax({
         	url    : baseUrl+'workflow/checkteamloc',
            type   : 'post',
            data   : {old_locs:old_locs,curr_locs:cure_loc_arr,team_id:$('#team_id').val()},
         	success: function (response){
         		if(response=='OK'){
					SaveTeam(form_id,btn);
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

$form = ActiveForm::begin(['action'=>$model->isNewRecord ?Url::to(['workflow/addteam']):Url::to(['workflow/editeam']),'id' => $model->formName(),'enableAjaxValidation' => false,'enableClientValidation' => true]); ?>
<?= IsataskFormFlag::widget(); // change flag ?>
<div class="tab-inner-fix">
<fieldset class="one-cols-fieldset">
    <div class="create-form">
    <?php $internal = array(1=>'Internal',2=>'External');
            if($model->isNewRecord){ $model->team_type=1; } ?>
                <?= $form->field($model, 'team_type',['template' => '<div class="row input-field custom-full-width"><fieldset><legend class="sr-only">Team Type</legend><div class="col-md-2">{label}</div><div class="col-md-8"><div class="row">{input}{error}{hint}</div></div></fieldset></div>','labelOptions'=>['class'=>'form_label']])->radioList([1=>'Internal',2=>'External'],
                        ['item' => function($index, $label, $name, $checked, $value) use($internal) {
                            $return = '<div class="col-sm-12">';
                            if($checked)
                                $return .= '<input aria-required="true" title="This field is required" aria-labelledby="lbl_rdo_team_type_'.$index.'" aria-setsize="2" aria-posinset="'.($index+1).'" id="Team-team_type-'.$value.'"  checked="'.$checked.'"  type="radio" name="' . $name . '" value="' . $value . '">';
                            else
                                $return .= '<input aria-required="true" title="This field is required" aria-labelledby="lbl_rdo_team_type_'.$index.'" aria-setsize="2" aria-posinset="'.($index+1).'" id="Team-team_type-'.$value.'"  type="radio" name="' . $name . '" value="' . $value . '">';

                            $return .= '<label for="Team-team_type-'.$value.'" class="form_label" id="lbl_rdo_team_type_'.$index.'">'.ucwords($label).'</label></div>';
                            return $return;
                        }]
                    ) 
                ?>
		<?= $form->field($model, 'team_name',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-10'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['maxlength'=>$model_field_length['team_name']]) ?>
		<?php if(!$model->isNewRecord) {
                    if(!empty($model->team_location)) {
                        foreach ($model->team_location as $savedtl) {
                            if(!in_array($savedtl,array_keys($teamLocation))) {
                                unset($model->team_location[$savedtl]);
                            }
                        }
                    } 
                ?>
		<input type="hidden" value="<?php echo implode(',',$model->team_location); ?>" name="old_teamloc" id="old_teamloc" />
		<?php } ?>
                <?php echo $form->field($model, 'team_location',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-10'><fieldset> <legend class='sr-only'>Select Team Location</legend>{input}\n{hint}\n{error}</fieldset></div></div>",'labelOptions'=>['class'=>'form_label']])->checkboxList($teamLocation,
                    ['item' => function($index, $label, $name, $checked, $value) {
                        $return = '<div class="col-sm-12">';
                        if($label != 'First add Rate(s), to then see and select Service Task(s).') {
                            if($checked)
                                $return .= '<input title="This field is required" aria-required="true" id="Team-team_location-'.$value.'" checked="'.$checked.'"  type="checkbox" name="' . $name . '" value="' . $value . '" class="teamservice_locs chk_'.$value.'" >';
                            else
                                $return .= '<input title="This field is required" aria-required="true" id="Team-team_location-'.$value.'"  type="checkbox" name="' . $name . '" value="' . $value . '" class="teamservice_locs" >';

                            $return .= '<label for="Team-team_location-'.$value.'" class="form_label">'.$label.'</label>';
                        } else {
                            $return .= '<label class="form_label text-muted">'.$label.'</label>';
                        }
                    $return .= '</div>';
                    return $return;
                },'class'=>'custom-full-width']); ?>
    	
    	<?= $form->field($model, 'team_description',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-10'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textArea(['rows'=>3,'maxlength'=>$model_field_length['team_description']]) ?>
    </div>	
    <?php if(!$model->isNewRecord) { ?>
    	<input type="hidden" name='team_id' id="team_id" value="<?= $model->id ?>" />
    <?php } ?>
</fieldset>
</div>
<div class=" button-set text-right">
    <?= Html::button('Cancel', ['title' => 'Cancel', 'class' => 'btn btn-primary','onclick'=>'reset_form();']) ?>
    <?php if(!$model->isNewRecord){ ?>
        <?= Html::button('Delete', ['title' =>'Delete', 'class' =>  'btn btn-primary','id'=>'','onclick'=>'DeleteTeam("'.$model->id.'");']) ?>
        <?= Html::button($model->isNewRecord ? 'Add' : 'Update', ['title' => $model->isNewRecord ?'Add':'Update', 'class' =>  'btn btn-primary submitTeamService','onclick'=>'updateTeamservice("'.$model->formName().'",this);']) ?>
        <?php } else{ ?>
    <?= Html::button($model->isNewRecord ? 'Add' : 'Update', ['title' => $model->isNewRecord ?'Add':'Update', 'class' =>  'btn btn-primary submitTeamService','onclick'=>'SaveTeam("'.$model->formName().'",this);']) ?>
    <?php }?>
</div>
<?php ActiveForm::end(); ?>
<script>
    var team_id = <?php echo $model->isNewRecord ? '0' : $model->id?>;    
    
    /** change flag **/
    $('input').bind('input', function(){
        $('#Team #is_change_form').val('1'); 
        $('#Team #is_change_form_main').val('1');
    }); 
    $(':radio').change(function(){ 
        $('#Team #is_change_form').val('1'); 
        $('#Team #is_change_form_main').val('1');
    }); 
    $(':checkbox').change(function(){ 
        $('#Team #is_change_form').val('1'); 
        $('#Team #is_change_form_main').val('1');
    }); 
    $('textarea').bind('input', function(){ 
        $('#Team #is_change_form').val('1'); 
        $('#Team #is_change_form_main').val('1'); 
    });
    $('document').ready(function(){
        $('#active_form_name').val("Team"); // form name
    });
	
    $(function() {
        $('input').customInput();
        $('#team-team_location .teamservice_locs').on('click', function(){ 
            if($(this).is(":unchecked")) { 
               var location_id = $(this).val();                 
              if(team_id != 0){
                jQuery.ajax({
                    url: baseUrl +'/workflow/check-location-team-exist&id='+team_id+'&location_id='+location_id,
                    type: 'get',
                    beforeSend:function (data) {showLoader();},
                    success: function (data) {
                        hideLoader();
                        if(data == 'exist') {                                                                                       
                            jQuery(".chk_"+location_id).prop('checked',true); 
                            jQuery(".chk_"+location_id).next().addClass('checked');
                        }
                    }
                });  
              }                
            }
        });
    });
    function reset_form(){
            $('#team-team_name').val('');
            $('#team-team_description').val('');
            $('#team-team_location').val('');
    }	
</script>
<noscript></noscript>
