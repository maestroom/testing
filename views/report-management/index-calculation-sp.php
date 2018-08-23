<?php
/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\Select2;
?>
<div class="right-main-container slide-open" id="maincontainer">
    <fieldset class="two-cols-fieldset report-management">
        <div class="administration-main-cols">
            <div class="administration-lt-cols pull-left">
                <button id="controlbtn" aria-label="Expand or Collapse" title="Expand/Collapse" class="slide-control-btn" onclick="WorkflowToggle();" aria-label="Expand or Collapse"><span>&nbsp;</span></button>
                <input type="hidden" value="" id="fieldtype_id">
                <ul>
                    <li>
                        <a title="Calculation Stored Procedure" href="javascript:addReportFieldRight('create-calculation-sp');" class="admin-main-title">
                          <em class="fa fa-folder-open text-danger"></em>Calculation Stored Procedure
                        </a>                          
                        <div class="select-items-dropdown">
                            <?php 
								echo Select2::widget([
									'model' => $model,
									'attribute' => 'id',
									'data' => $calculationSpselectList,
									'options' => ['placeholder' => 'Select Calculation Stored Procedure','title' => 'Select Calculation Stored Procedure', 'id' => 'sp-calculation_list_dropdown'],
									'pluginOptions' => [
										'allowClear' => true                                            
									],
								]);
							?>                            
                        </div>
                        <div class="left-dropdown-list">
							<div class="admin-left-module-list">
								<ul class="sub-links">
									<?php if(!empty($calculationSpList)){ foreach ($calculationSpList as $single_field){?>
											<li id="report_function-calculation_<?php echo $single_field->id; ?>"><a href="javascript:updateSelect2Dropdown(<?php echo $single_field->id; ?>,'Select Calculation Stored Procedure','calculation-sp');" title="<?=Html::encode($single_field->sp_name); ?>"><em class="fa fa-building text-danger"></em> <?=Html::encode($single_field->sp_name); ?></a></li>
									<?php }} ?>
								</ul>
							</div>
						</div>
                    </li>
                </ul>
            </div>  
            <div class="administration-rt-cols pull-right" id="admin_right"></div>
        </div>
    </fieldset>
</div>
<script>
    addReportFieldRight('create-calculation-sp');
    $('#sp-calculation_list_dropdown').on("change", function () { 
        if(this.value != ''){
            updateSelect2Dropdown(this.value,'Select Calculation Stored Procedure','calculation-sp');
        }
    });
</script>
<noscript></noscript>
