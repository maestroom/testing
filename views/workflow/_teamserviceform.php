<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\components\IsataskFormFlag;

/* @var $this yii\web\View */
/* @var $model app\models\UnitPrice */
/* @var $form yii\widgets\ActiveForm */
$url=Url::to(['workflow/checksecrviceteamloc']);
$js = <<<JS
        
// get the form id and set the event
$('form#{$model->formName()}').on('beforeSubmit', function(e) {   
   var form = $(this);
		$.ajax({
            url    : form.attr('action'),
            type   : 'post',
            data   : form.serialize(),
            beforeSend : function()    {
            	$('.submitTeamService').attr('disabled','disabled');
            },
            success: function (response){
            	if(response == 'OK')
             		TeamServide($model->teamid);
            	else{
                	$('.submitTeamService').removeAttr("disabled");
             		$('#teamserviceform').html(response);		
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


function updateTeamservice(){
  var old_locs= $('#old_teamloc').val().split(',');   
  var curr_locs = $('.teamservice_locs:checkbox:checked');
  var cure_loc_arr = new Array();
  $('.teamservice_locs:checkbox:checked').each(function(){
  	cure_loc_arr.push($(this).val());
  });
  if(curr_locs.length==old_locs.length){
         $('form#{$model->formName()}').submit();    				
  }else{
       $.ajax({
         	url    : baseUrl+'workflow/checkteamsecrviceteamloc',
            type   : 'post',
            data   : {old_locs:old_locs,curr_locs:cure_loc_arr,teamservice_id:$('#teamservice-id').val()},
         	success: function (response){
         		if(response=='OK'){
					$('form#{$model->formName()}').submit();
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

$startlogic = Yii::$app->params['startlogic'];
$endarlogic = Yii::$app->params['endlogic'];
$duration = Yii::$app->params['duration'];
?>
<?php $form = ActiveForm::begin(['id' => $model->formName(),'enableAjaxValidation' => false,'enableClientValidation' => true]); ?>
<?= IsataskFormFlag::widget(); // change flag ?>
<fieldset class="one-cols-fieldset">
    <div class="create-form">
    	<?= $form->field($model, 'service_name',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-9'>{input}\n{hint}\n{error}</div></div>"])->textInput(['maxlength'=>$model_field_length['service_name']])->label($model->getAttributeLabel('service_name'), ['class'=>'form_label']) ?>
    	<?php if($model->teamid!=1) {
    			if(!$model->isNewRecord) { 
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
                    <?= $form->field($model, 'team_location',['template' => "<div class='row input-field'><fieldset><legend class='sr-only'>Team Location</legend><div class='col-md-3'>{label}<span class='text-danger'>*</span></div><div class='col-md-9'>{input}\n{hint}\n{error}</div></fieldset></div>",'labelOptions'=>['class'=>'form_label']])->checkboxList($teamLocation,
	    		['item' => function($index, $label, $name, $checked, $value) {
                            $return = '<div class="col-sm-12">';
                            if($label != 'First add Rate(s), to then see and select Service Task(s).'){
                                if($checked)
                                    $return .= '<input title="This field is required" aria-required = "true" id="'.$name.'-'.$value.'" checked="'.$checked.'"  type="checkbox" name="' . $name . '" value="' . $value . '" class="teamservice_locs chk_'.$value.'" aria-label="'.ucwords($label).'"><label for="'.$name.'-'.$value.'" class="form_label">'.ucwords($label).'</label>';
                                else
                                    $return .= '<input title="This field is required" aria-required = "true" id="'.$name.'-'.$value.'"  type="checkbox" name="' . $name . '" value="' . $value . '" class="teamservice_locs" aria-label="'.ucwords($label).'"><label for="'.$name.'-'.$value.'" class="form_label">'.ucwords($label).'</label>';
                            } else {
                                $return .= '<label class="form_label text-muted">'.$label.'</label>';
                            }
                            $return .= '</div>';
                return $return;
            },'class'=>'custom-full-width']); ?>
    	<?php if(!$model->isNewRecord && $model->teamid!=1){ ?>
    	<div class="form-group field-teamservice-service_description">
            <div class="row input-field">
                <div class="col-md-3"><label for="teamservice-sla-logic" class="form_label">SLA Logic</label></div>
                <div class="col-md-9">
                   <input type="button" value="Add Logic" name="addlogic" class="btn btn-primary" id="AddLogicTeamservice">
                </div>
            </div>
        </div>
        <div class="form-group field-teamservice-service_description">
            <div class="row input-field">
                <div class="col-md-3"></div>
                <div class="col-md-9">
                    <input type="hidden" name="editLogicId" id="editLogicId" value="" />
	                <input type="hidden" name="deletedLogicId" id="deletedLogicId" value="" />
					<div class="table-responsive">
						<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table table-striped table-style-width" id="slaLogic">
							<thead>
								<tr>
									<th class="media-size-width"><a href="javascript:void(0);" title="Media Size" class="tag-header-black">Media Size</a></th>
									<th class="time-width"><a href="javascript:void(0);" title="Time" class="tag-header-black">Time</a></th>
									<th class="priority-width"><a href="javascript:void(0);" title="Priority" class="tag-header-black">Priority</a></th>
									<th class="third-th inner-action"><a href="javascript:void(0);" title="Action" class="tag-header-black">Action</a></th>
								</tr>
							</thead>
							<tbody id="logic_sla_list">
					   		<?php if (!empty($modelteamsla)) {
				                    foreach ($modelteamsla as $sla) { ?>	
										<tr id="sla_logic_content_<?= $sla->id; ?>">
					   					<td>
										<?php
											$start_unit = $listUnit[$sla->size_start_unit_id];
											$end_unit = $listUnit[$sla->size_end_unit_id];
											$endlogic = "";
											$endlogic = " AND " . $endarlogic[$sla->end_logic] . " " . $sla->end_qty . " " . $end_unit;
											echo $logic_name = $teamLocation[$sla->team_loc_id] . " - " . $startlogic[$sla->start_logic] . " " . $sla->start_qty . " " . $start_unit . $endlogic;
										?>
					   					</td>
					   					<td><?= $sla->del_qty . " " . $duration[$sla->del_time_unit]; ?></td>
					   					<td><?= $projectPriority[$sla->project_priority_id]; ?></td>
					   					<td class="third-td">
					   						<a href='javascript:slalogiccontentaction("edit",<?= $sla->id ?>);' class="icon-fa icon-set" title="Edit SLA logic content" aria-label="Edit SLA logic content"><em class="fa fa-pencil text-primary" title="Edit SLA logic content"></em></a> 
					   						<a href='javascript:slalogiccontentaction("delete",<?= $sla->id ?>,"<?=$logic_name?>");' class="icon-fa icon-set" title="Delete SLA logic content" aria-label="Delete SLA logic content"><em title="Delete SLA logic content" class="fa fa-close text-primary"></em></a>
					   					</td>
					   					<?php
			                                echo "<input type='hidden' name='TeamserviceSla[{$sla->id}][id]' value='{$sla->id}'/>";
			                                echo "<input type='hidden' name='TeamserviceSla[{$sla->id}][teamservice_id]' value='{$sla->teamservice_id}'/>";
			                                echo "<input type='hidden' name='TeamserviceSla[{$sla->id}][team_loc_id]' value='{$sla->team_loc_id}'/>";
			                                echo "<input type='hidden' name='TeamserviceSla[{$sla->id}][start_logic]' value='{$sla->start_logic}'/>";
			                                echo "<input type='hidden' name='TeamserviceSla[{$sla->id}][start_qty]' value='{$sla->start_qty}'/>";
			                                echo "<input type='hidden' name='TeamserviceSla[{$sla->id}][size_start_unit_id]' value='{$sla->size_start_unit_id}'/>";
			                                echo "<input type='hidden' name='TeamserviceSla[{$sla->id}][end_logic]' value='{$sla->end_logic}'/>";
			                                echo "<input type='hidden' name='TeamserviceSla[{$sla->id}][end_qty]' value='{$sla->end_qty}'/>";
			                                echo "<input type='hidden' name='TeamserviceSla[{$sla->id}][size_end_unit_id]' value='{$sla->size_end_unit_id}'/>";
			                                echo "<input type='hidden' name='TeamserviceSla[{$sla->id}][del_qty]' value='{$sla->del_qty}'/>";
			                                echo "<input type='hidden' name='TeamserviceSla[{$sla->id}][del_time_unit]' value='{$sla->del_time_unit}'/>";
			                                echo "<input type='hidden' name='TeamserviceSla[{$sla->id}][project_priority_id]' value='{$sla->project_priority_id}'/>";
		                                ?>
                                            </tr>
                                            <?php
                        }
                    }
                    ?>					   		
                            </tbody>
						</table>
					</div>
	            </div>  
				</div>
			</div>
        <?php } }?>
    	<?= $form->field($model, 'service_description',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-9'>{input}\n{hint}\n{error}</div></div>"])->textArea(['rows'=>3,'maxlength'=>$model_field_length['service_description']])->label($model->getAttributeLabel('service_description'), ['class'=>'form_label']) ?>
    	<?= $form->field($model, 'teamid')->hiddenInput()->label(false);?>
    	<?php if(!$model->isNewRecord){?>
    	<?= $form->field($model, 'id')->hiddenInput()->label(false);?>
    	<?php }?>
    </div>
    <div id="addLogicBox"></div>	
</fieldset>
<div class=" button-set text-right">
  <?= Html::button('Cancel', ['title'=>'Cancel','class' => 'btn btn-primary','onclick'=>'CancelTeamService('.$model->teamid.');']) ?>
  <?php if(!$model->isNewRecord && $model->teamid!=1){ ?>
  	<?= Html::button('Update', ['title' => 'Update','class' =>  'btn btn-primary submitTeamService','onclick'=>'updateTeamservice();']) ?>
  <?php }else{ ?>
  	<?= Html::submitButton($model->isNewRecord ? 'Add' : 'Update', ['title'=>$model->isNewRecord ? 'Add' : 'Update','class' =>  'btn btn-primary submitTeamService']) ?>
  <?php } ?>
</div>
<?php ActiveForm::end(); ?>
<script>
	/** Input Keyup **/
	$('input').bind('input', function(){
		$('#Teamservice #is_change_form').val('1'); 
		$('#Teamservice #is_change_form_main').val('1');
	}); 
	$(':checkbox').change(function(){
		$('#Teamservice #is_change_form').val('1'); 
		$('#Teamservice #is_change_form_main').val('1');
	}); 
	$('textarea').bind('input', function(){ 
		$('#Teamservice #is_change_form').val('1'); 
		$('#Teamservice #is_change_form_main').val('1'); 
	});
	$('document').ready(function(){ $('#active_form_name').val("Teamservice"); });
	$(function() {
	  
	  $('input').customInput();
          var team_id = <?php echo  $model->isNewRecord ? '0' : $model->id ?>;
          $('#teamservice-team_location .teamservice_locs').on('click', function() { 
              if($(this).is(":unchecked")) { 
                var location_id = $(this).val();                                  
                if(team_id != 0){
                  jQuery.ajax({
					url: baseUrl +'/workflow/check-location-service-task-exist&id='+team_id+'&location_id='+location_id,
					type: 'get',
		//			beforeSend:function (data) {showLoader();},
					success: function (data) {
							hideLoader();
							if(data == 'exist'){                                                                                       
								jQuery(".chk_"+location_id).prop('checked',true); 
								jQuery(".chk_"+location_id).next().addClass('checked');
							}
						}
					});  
				}                
		   }
         });
	});
</script>
<noscript></noscript>	
