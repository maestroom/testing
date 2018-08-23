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
                        <a title="Report Types" href="javascript:addReportFieldRightNew('create-report-type');" class="admin-main-title">
                          <em title="Report Types" class="fa fa-folder-open text-danger"></em>Report Types
                        </a>                          
                        <div class="select-items-dropdown">
                            <?php 
                            echo Select2::widget([
                                    'model' => $model,
                                    'attribute' => 'id',
                                    'data' => $reporttypeselectList,
                                    'options' => ['placeholder' => 'Select Report type', 'id' => 'report-type_list_dropdown','title'=>'Select Report Type'],
                                    'pluginOptions' => [
                                            'allowClear' => true                                            
                                    ],
                            ]);
                            ?>                            
                        </div>
                        <div class="left-dropdown-list">
                                <div class="admin-left-module-list">
                                        <ul class="sub-links">
                                                <?php if(!empty($reporttypeList)){ foreach ($reporttypeList as $single_field){?>
                                                        <li id="report_report-type_<?php echo $single_field->id; ?>">
                                                            <a href="javascript:updateReportDropdown(<?php echo $single_field->id; ?>,'Select Report Type','report-type');" title="<?=Html::encode($single_field->report_type); ?>">
                                                                <em title="<?=Html::encode($single_field->report_type); ?>" class="fa fa-building text-danger"></em> 
                                                                <?=Html::encode($single_field->report_type); ?>
                                                            </a>
                                                        </li>
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
    addReportFieldRightreportType('create-report-type');		
      $('#report-type_list_dropdown').on("change", function () { 
        if(this.value != ''){
            updateSelect2Dropdown(this.value,'Select Report Type','report-type');
        }
    });
</script>
<noscript></noscript>
