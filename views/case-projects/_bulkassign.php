<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\widgets\Select2;
use app\components\IsataskFormFlag;
$this->title = 'Bulk Assign Case Manager Tasks';
$this->params['breadcrumbs'][] = ['label' => 'Case Projects', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div id="project_container" class="right-main-container">
    <div class="sub-heading"><a href="javascript:void(0);" class="tag-header-black" title="<?= Html::encode($this->title) ?>"><?= Html::encode($this->title) ?></a></div>
<!-- <fieldset class="one-cols-fieldset"> -->
			<?php ActiveForm::begin(['id' => 'add-bulkassignproject-form', 'action' => '@web/index.php?r=case-projects/assign-bulk-user']); ?>
				<?= IsataskFormFlag::widget(); // change flag ?>
				<div class='form-group' id="bulk_assign">
		    		<div class="col-md-12">
		    	    		<div class='col-md-3'>
		    	    			<label class="form_label">Display Results by:</label>
		    	    		</div>
			    	    	<div class='col-md-7'>
								<?php 
								$dropdownArr = array("1" => "Tasks", "2" => "Client/Tasks", "3" => "Case/Tasks", "4" => "Random Sampling/Tasks");
									 echo Select2::widget([
										'name' => 'displayResult',
										'attribute' => 'displayResult',
										'data' => $dropdownArr,
										'options' => ['prompt' => 'Select Task Type', 'class' => 'form-control billing-dropdown-filterlist','id'=>'displayResult'],
										/*'pluginOptions' => [
										  'allowClear' => true
										]*/
									]);
								?>
			    	    	</div>
			    	    <div class='col-md-2'>
			    	    	<input type="hidden" name="caseId" id="caseId" value="<?php echo $caseId; ?>" />
			    	    	<input type="button" class="btn btn-primary" name="go" title="Search" value="Search" id="go_display_result" onClick="godisplayresult();" />
			    	    </div>
			    	</div>	    
		     	 </div>
			     <div class='form-group'>
			     	<div class="bulktableDiv" id="bulktableDiv">
			     		<!-- set ajax based table -->
			     	</div>
			     </div>
		     
	         <div class="button-set text-right">
	           	<input type="button" value="Back" title="Back" name="yt1" class="btn btn-primary" id="bulkbackbtn">
	           	<input type="button" value="Update" title="Update" name="yt0"  class="btn btn-primary" id="updateBulkUserByCaseBtn" style="display:none;" onclick="bulkupdateassignuser();">
	         </div>
        	<?php ActiveForm::end(); ?>
<!-- </fieldset> -->
</div>
<script>
$("#bulkbackbtn").click(function(event){
	window.location = baseUrl +'/case-projects/index&case_id='+<?php echo $caseId; ?>;
});
$('document').ready(function(){
	$('#active_form_name').val('add-bulkassignproject-form'); // check
});
</script>
<noscript></noscript>

