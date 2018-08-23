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
                <button id="controlbtn" aria-label="Expand or Collapse" title="Expand/Collapse" class="slide-control-btn" onclick="WorkflowToggle();"><span>&nbsp;</span></button>
                <input type="hidden" value="" id="fieldtype_id">
                <ul>
                    <li>
                        <a title="Field Calculations" href="javascript:addReportFieldRightNew('create-field-calculation');" class="admin-main-title">
							<em title="Field Calculations" class="fa fa-folder-open text-danger"></em>Field Calculations
                        </a>                          
                        <div class="select-items-dropdown">
                        <?php 
                            echo Select2::widget([
								'model' => $model,
								'attribute' => 'id',
								'data' => $fieldcalculationselectList,
								'options' => ['placeholder' => 'Select Field Calculation', 'data-name' => 'outer-select','title' => 'Select Field Calculation', 'id' => 'field-calculation_list_dropdown','nolabel'=>true,'aria-label'=>'Select Field Calculation'],
								'pluginOptions' => [
								   'allowClear' => true                                            
								],
                            ]);
                        ?>                            
                        </div>
                        <div class="left-dropdown-list">
                                <div class="admin-left-module-list">
                                        <ul class="sub-links">
                                                <?php if(!empty($fieldcalculationList)){ foreach ($fieldcalculationList as $single_field){?>
                                                        <li id="report_field-calculation_<?php echo $single_field->id; ?>"><a href="javascript:updateSelect2Dropdown(<?php echo $single_field->id; ?>,'Select Field Calculation','field-calculation');" title="<?=Html::encode($single_field->calculation_name); ?>"><em title="<?=Html::encode($single_field->calculation_name); ?>" class="fa fa-building text-danger"></em> <?=Html::encode($single_field->calculation_name); ?></a></li>
                                                <?php }}?>
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
    addReportFieldRight('create-field-calculation');
    $('#field-calculation_list_dropdown').on("change", function () { 
	    if(this.value != ''){
            updateSelect2Dropdown(this.value,'Select Field Calculation','field-calculation');
        }
    });
</script>
<noscript></noscript>
