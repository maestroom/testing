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
                <ul>
                    <li>
                        <a title="Report Formats" href="javascript:addReportFieldRightNew('create-report-format');" class="admin-main-title">
                          <em title="Report Formats" class="fa fa-folder-open text-danger"></em>Report Formats
                        </a>                          
                        <div class="select-items-dropdown">
                              <?php 
                            echo Select2::widget([
                                    'model' => $model,
                                    'attribute' => 'id',
                                    'data' => $reportformatselectList,
                                    'options' => ['placeholder' => 'Select Report Format', 'id' => 'report-format_list_dropdown','title'=>'Select Report Format'],
                                    'pluginOptions' => [
                                            'allowClear' => true                                            
                                    ],
                            ]);
                            ?>	                            
                        </div>
                        <div class="left-dropdown-list">
                                <div class="admin-left-module-list">
                                        <ul class="sub-links">
                                                <?php if(!empty($reportformatList)){ foreach ($reportformatList as $fieldtype){?>
                                                        <li id="report_report-format_<?php echo $fieldtype->id; ?>"><a href="javascript:updateSelect2Dropdown(<?php echo $fieldtype->id; ?>,'Select Report Format','report-format');" title="<?=Html::encode($fieldtype->report_format); ?>"><em title="<?=Html::encode($fieldtype->report_format); ?>" class="fa fa-building text-danger"></em> <?=Html::encode($fieldtype->report_format); ?></a></li>
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
    addReportFieldRight('create-report-format');		
      $('#report-format_list_dropdown').on("change", function () { 
        if(this.value != ''){
            updateSelect2Dropdown(this.value,'Select Report Format','report-format');
        }
    });
</script>
<noscript></noscript>
