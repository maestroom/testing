<?php 
use kartik\widgets\Select2;
use yii\web\JsExpression;
use app\components\IsataskFormFlag;
foreach ($teamLocation as $teamloc=>$team_loc_name){
				$teamlocation_dropdown[$teamloc] = $team_loc_name; 
			}
?>
<div class="create-form">
	<form name="transfer_task_loc" id="transfer_task_loc" autocomplete="off">
	  <?= IsataskFormFlag::widget(); // change flag ?>
      <div class="row without-border">
        <div class="col-md-4">
          <label class="form_label required" for="location">Location</label>
        </div>
        <div class="col-md-8" id="transfertaskloc">
		  <?php 
			echo Select2::widget([
				'name' => 'location',
				'attribute' => 'location',
				'data' => $teamlocation_dropdown,
				'options' => ['prompt' => 'Select Location', 'class' => 'form-control', 'aria-required'=>'true', 'id'=>'location','onchange'=>'if(this.value != ""){ $(".help-block").html("");$(this).parent().parent().removeClass("has-error");} else {$(".help-block").html("Location cannot be blank.");$(this).parent().parent().addClass("has-error"); }'],
				'pluginOptions' => [
					// 'allowClear' => true,
					'dropdownParent' => new JsExpression('$("#transfertaskloc")')
				]
            ]);
		  ?>	
          <div class="help-block"></div>
        </div>
      </div></form>
</div>
<script>
	/* changeflag */
	$('select').on("change",function(){
		$("#transfer_task_loc #is_change_form").val("1");
		$("#is_change_form_main").val("1"); 
	});
</script>
