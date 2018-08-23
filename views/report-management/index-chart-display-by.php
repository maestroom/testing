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
                <input type="hidden" value="" id="chart_display_by_id">
                <ul>
                    <li>
                        <a title="Chart Display By" href="javascript:addReportFieldRightNew('create-chart-display-by');" class="admin-main-title">
                          <em title="Chart Display By" class="fa fa-folder-open text-danger"></em>Chart Display By
                        </a>                          
                        <div class="select-items-dropdown">
                            <?php 
                            echo Select2::widget([
                                    'model' => $model,
                                    'attribute' => 'id',
                                    'data' => $chartdisplayselectList,
                                    'options' => ['placeholder' => 'Select Chart Display By', 'title' => 'Select Chart Display By', 'id' => 'chart-display-by_list_dropdown'],
                                    /*'pluginOptions' => [
                                            'allowClear' => true                                            
                                    ],*/
                            ]);
                            ?>	                           
                        </div>
                        <div class="left-dropdown-list">
                                <div class="admin-left-module-list">
                                        <ul class="sub-links">
                                               <?php if(!empty($chartdisplayList)){ foreach ($chartdisplayList as $single){?>
                                                        <li id="report_chart-display-by_<?php echo $single->id; ?>">
                                                            <a href="javascript:updateSelect2Dropdown(<?php echo $single->id; ?>,'Select Chart Display By','chart-display-by');" title="<?=Html::encode($single->chart_display_by); ?>">
                                                                <em title="<?=Html::encode($single->chart_display_by); ?>" class="fa fa-building text-danger"></em> 
                                                                <?=Html::encode($single->chart_display_by); ?>
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
    addReportFieldRight('create-chart-display-by');		
      $('#chart-display-by_list_dropdown').on("change", function () { 
        if(this.value != ''){
            updateSelect2Dropdown(this.value,'Select Chart Display By','chart-display-by');
        }
    });
</script>
<noscript></noscript>
