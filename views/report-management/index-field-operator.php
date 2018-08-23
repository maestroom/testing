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
                <input type="hidden" value="" id="field_operator_id">
                <ul>
                    <li>
                        <a title="Field Operators" href="javascript:addReportFieldRightNew('create-field-operator');" class="admin-main-title">
                          <em title="Field Operators" class="fa fa-folder-open text-danger"></em>Field Operators
                        </a>                          
                        <div class="select-items-dropdown">
                            
			<?php 
                            echo Select2::widget([
                                    'model' => $model,
                                    'attribute' => 'id',
                                    'data' => $fieldoperatorboxList,
                                    'options' => ['placeholder' => 'Select Field Operator', 'id' => 'field-operator_list_dropdown','title'=>'Select Field Operator','nolabel'=>true],
                                    'pluginOptions' => [
                                            'allowClear' => true                                            
                                    ],
                            ]);
                            ?>	                                             
                        </div>
                        <div class="left-dropdown-list">
                                <div class="admin-left-module-list">
                                        <ul class="sub-links">
                                               <?php if(!empty($fieldoperatorList)){ foreach ($fieldoperatorList as $fieldoperator){?>
                                                        <li id="report_field-operator_<?php echo $fieldoperator->id; ?>"><a href="javascript:updateSelect2Dropdown(<?php echo $fieldoperator->id; ?>,'Select Field Operator','field-operator');" title="<?=Html::encode($fieldoperator->field_operator); ?>"><em title="<?=Html::encode($fieldoperator->field_operator); ?>" class="fa fa-building text-danger"></em> <?=Html::encode($fieldoperator->field_operator); ?></a></li>
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
    addReportFieldRight('create-field-operator');
    $('#field-operator_list_dropdown').on("change", function () { 
        if(this.value != ''){
            updateSelect2Dropdown(this.value,'Select Field Operator','field-operator');
        }
    });
</script>
<noscript></noscript>
