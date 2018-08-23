<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use app\components\IsataskFormFlag;
?>
 <div class="instruction_box sml dialog-box">
	 <?php $form = ActiveForm::begin([
						'id' => 'loadpriority',
						'options' => ['class' => 'form1','novalidate'=>'novalidate'],
						'action' => '@web/index.php?r=team-projects/updateteampriority',
						'fieldConfig' => [
							'template' => "<div class=\"col-sm-12\">{label}\n{input}</div>\n{error}",
							'labelOptions' => ['class' => 'form_label'],
						],
					]); 
			    ?>
	<div class="col-sm-12">
		<div class="loadteampriority_main_half" >
			<div class="" id="unique_parent"><!--loadteampriority_first_half -->
			    <?= IsataskFormFlag::widget(); // change flag ?>
			    <!--<input type="hidden" name="active_form_name" id="active-form-name" value="" />-->
			    <?php 
			    $teampriority_data=[];
			    if(!empty($dropdown_data)) {
				foreach($dropdown_data as $data => $value){
						$teampriority_data[$value['id']] = $value['priority_name'];
				}}
			    echo Select2::widget([
					'name' => 'team_prioriy',
                    'attribute' => 'team_prioriy',
                    'data' => $teampriority_data,
                    'options' => ['prompt' => 'Select Team Priority', 'class' => 'form-control','id'=>'loadteampriority', 'onChange' => 'team_priority_desc();'],
                    'pluginOptions' => [
							//'allowClear' => true,                                                        
							'dropdownParent' => new JsExpression('$("#loadpriority")')
						]
                    ]);
			   ?>
			</div>	
			<!-- get team priority -->
			<input type="hidden" name="task_id" value="<?=$task_id?>">
			<input type="hidden" name="team_id" value="<?=$team_id?>">
			<input type="hidden" name="team_loc" value="<?=$team_loc?>">
			<span id="get_team_priority" style="float:left;padding:10px 10px 10px 5px;"></div>
			<div class="loadteampriority_second_half custom-full-width">
				<input type="checkbox" name='remove_team_priority' id="remove_team_priority" value="click">
				<label for="remove_team_priority">Remove the current Team Priority.</label>
			</div>
		</div>
	</div>
	<?php ActiveForm::end(); ?>
</div>

 <script>
	/* change select */ 
	$('select').on("change",function(){
		$("#loadpriority #is_change_form").val('1');
		$("#loadpriority #is_change_form_main").val('1');
	});
	$(':checkbox').change(function(){
		/* change */
		$("#loadpriority #is_change_form").val('1');
		$("#loadpriority #is_change_form_main").val('1');
	});
	$('document').ready(function(){ 
		$("#active_form_name").val('loadpriority'); 
	}); // form name
	
	$('.instruction_box input').customInput();
	// team priority description
	function team_priority_desc()
	{
		var priority = $('#loadteampriority').val();
		$.ajax({
			type:'get',
			url: httpPath + "team-tasks/get-desc-team-priority&priority="+priority,
			cache: false,
			dataType: 'html',
			success: function (data) {
				$('#get_team_priority').html(data);
			}
		});
	}
 </script>
<noscript></noscript>
<style>
#select2-loadteampriority-results{
    height: 90px;
}
</style>
