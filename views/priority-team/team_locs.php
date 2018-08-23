<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

\app\assets\CustomInputAsset::register($this);
/* @var $this yii\web\View */
/* @var $searchModel app\models\EvidenceTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = 'Team Location';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mycontainer" id="user_access_second">
	<div class="create-form">
		<div class="form-group field-team-team-name">
			<div class="row input-field required">	
				<div class="col-md-3">
					Select Team Priority <em style='color:red;'>*</em>
					<span class='sr-only'>Required</span>
				</div>	
				<!-- Get all client/cases -->
				<div class="col-md-6">
					<div style="min-height:114px; height:auto!important; max-height: 114px !important;overflow: auto;margin-top:5px;border:1px solid #DBDBDB;width:300px;display:block;" id="displaystatusteampriority">
						<span>
                                                    <fieldset>
                                                        <legend class="sr-only">Select Team Priority</legend>
							<ul class='by_clientcases custom-full-width' id="by_clientcases" style='width: 100%!important;list-style:none;'>
								<li>
									<input id="teamlocationall" class="form-control" name="Report[statusall]" type="checkbox"  class="statusall" aria-label="Select All or None">
									<label class="form_label" for="teamlocationall">Select All/None</label>
								</li>
								<?php $i=0; foreach($myteams as $key => $teams) { ?>
									<li>
										<input type="checkbox" id="teamlocation_<?php echo $key; ?>" name="teamlocation[]" class="teamlocation" data-priority-type="<?= $teams['tasks_priority_name']; ?>" data-prioritydesc="<?= $teams['priority_desc']!=""?$teams['priority_desc']:""; ?>" data-id=<?= $teams['id'] ?> value="<?= $teams['tasks_priority_name']; ?>" />
										<label for="teamlocation_<?php echo $key; ?>" class="teamlocation"><?php echo $teams['tasks_priority_name']; ?></label>
									</li>
								<?php $i++; } ?>
							</ul>
                                                    </fieldset>    
						</span>
					</div>
					<div class="help-block" id="teamlocation_error"></div>
				</div>
				<!-- End -->
				<div class="col-md-2">
					<?= Html::button('Add New', ['title'=> 'Add New', 'class' => 'btn btn-primary', 'onclick'=>'AddPriorityTeam();']) ?>
				</div>
			</div>
		</div>
	</div>
</div>	
<script>
$(function() {
	$('input').customInput();
});
/** TeamLocation All **/
$("#teamlocationall").change(function(){
	if($("#teamlocationall").is(':checked')){
		$(".teamlocation").prop('checked',true);
		$(".teamlocation").addClass('checked');
	} else {
		$(".teamlocation").prop('checked',false);
		$(".teamlocation").removeClass('checked');
	}
});
</script>
<noscript></noscript>
