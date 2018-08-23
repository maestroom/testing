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
                        <a title="Chart Formats" href="javascript:addReportFieldRightNew('create-chart-format');" class="admin-main-title">
                          <em title="Chart Formats" class="fa fa-folder-open text-danger"></em>Chart Formats
                        </a>                          
                        <div class="select-items-dropdown">
                            <?php 
                            echo Select2::widget([
                                    'model' => $model,
                                    'attribute' => 'id',
                                    'data' => $chartformatselectList,
                                    'options' => ['placeholder' => 'Select Chart Format','title' => 'Select Chart Format', 'id' => 'chart-format_list_dropdown'],
                                    'pluginOptions' => [
                                            'allowClear' => true                                            
                                    ],
                            ]);
                            ?>	                             
                        </div>
                        <div class="left-dropdown-list">
                                <div class="admin-left-module-list">
                                        <ul class="sub-links">
                                                <?php if(!empty($chartformatList)){ foreach ($chartformatList as $single){?>
                                                        <li id="report_chart-format_<?php echo $single->id; ?>"><a href="javascript:updateSelect2Dropdown(<?php echo $single->id; ?>,'Select Chart Format','chart-format');" title="<?=Html::encode($single->chart_format); ?>"><em title="<?=Html::encode($single->chart_format); ?>" class="fa fa-building text-danger"></em> <?=Html::encode($single->chart_format); ?></a></li>
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
    addReportFieldRight('create-chart-format');	
    $('#chart-format_list_dropdown').on("change", function () { 
        if(this.value != ''){
            updateSelect2Dropdown(this.value,'Select Chart Format','chart-format');
        }
    });
</script>
<noscript></noscript>
    
