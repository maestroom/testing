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
                        <a title="Field Relationships" href="javascript:addReportTableRelationships('create-field-relationships');" class="admin-main-title">
                          <em title="Field Relationships" class="fa fa-folder-open text-danger"></em>Field Relationships
                        </a>                          
                        <div class="select-items-dropdown">
						<?php 
                            echo Select2::widget([
                                    'model' => $modelReportTables,
                                    'attribute' => 'id',
                                    'data' => $tableList,
                                    'options' => ['placeholder' => 'Select Table Name', 'id' => 'table_name_list_dropdown','title'=>'Select Table Name','nolabel'=>true],
                                    /*'pluginOptions' => [
                                            'allowClear' => true                                            
                                    ],*/
                                    'pluginEvents' => [
										"change" => "function() { updateSelect2Dropdown(this.value,'Select Table Name','field-relationships'); }",
									]
                            ]);
                        ?>	                                             
                        </div>
                        <div class="left-dropdown-list">
                                <div class="admin-left-module-list">
                                        <ul class="sub-links">
                                               <?php if(!empty($tableList)){ foreach ($tableList as $id=>$tableName){?>
                                                        <li id="report_field-relationships_<?php echo $id; ?>"><a href="javascript:updateSelect2Dropdown(<?php echo $id; ?>,'Select Table Name','field-relationships');" title="<?=Html::encode($tableName); ?>"><em title="<?=Html::encode($tableName); ?>" class="fa fa-building text-danger"></em> <?=Html::encode($tableName); ?></a></li>
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
    addReportTableRelationships('create-field-relationships');
    $('#table_name_list_dropdown').on("change", function () { 
        if(this.value != ''){
            updateSelect2Dropdown(this.value,'Select Table Name','field-relationships');
        }
    });
</script>
<noscript></noscript>
