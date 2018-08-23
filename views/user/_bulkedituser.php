<?php
/* @var $this yii\web\View */
use yii\helpers\Html;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use app\components\IsataskFormFlag;
?>
<form id="bulk_user_select" autocomplete="off">
<div id="bulk_user">
	<div class="row">
		<div class="col-md-10" id="bulkedituser_parent">
				<?= IsataskFormFlag::widget(); // change flag ?>
				<?php if(!empty($role_details)){ foreach ($role_details as $role){
							$role_details_dropdown[$role->id] = $role->role_name;
						}
						}
					  echo Select2::widget([
									'name' => 'bulk_edit',
									'attribute' => 'bulk_edit',
									'data' => $role_details_dropdown,
									'options' => ['prompt' => 'Select Role','class' => 'form-control','id'=>'bulk_edit'],
									'pluginOptions' => [
        //'allowClear' => true,
        'dropdownParent' => new JsExpression('$("#bulkedituser_parent")')
        
    ],
								]); 
					 ?>
			</select>
		</div>
	</div>
</div>
</form>
<script>
	/* changeFlag */
	$('select').on('change', function() {
		$('#bulk_user_select #is_change_form').val('1');
		$('#bulk_user_select #is_change_form_main').val('1');
	});
</script>

