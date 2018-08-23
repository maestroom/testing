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
                        <a title="Field Lookup" href="javascript:addReportFieldRight('create-field-lookup');" class="admin-main-title">
                          <em class="fa fa-folder-open text-danger"></em>Field Lookup
                        </a>                          
                        <div class="select-items-dropdown">
                            
			<?php 
                            echo Select2::widget([
                                    'model' => $model,
                                    'attribute' => 'id',
                                    'data' => $fieldlookupList,
                                    'options' => ['placeholder' => 'Select Field Lookup', 'id' => 'field-lookup_list_dropdown','title'=>'Select Field Lookup'],
                                    'pluginOptions' => [
                                            'allowClear' => true                                            
                                    ],
                                    'pluginEvents' => [
										"change" => "function() { updateSelect2Dropdown(this.value,'Select Field Lookup','field-lookup'); }",
									]
                            ]);
                            ?>	                                             
                        </div>
                        <div class="left-dropdown-list">
                                <div class="admin-left-module-list">
                                        <ul class="sub-links">
                                               <?php if(!empty($fieldlookupList)){ foreach ($fieldlookupList as $id=>$lookup){?>
                                                        <li id="report_field-lookup_<?php echo $id; ?>"><a href="javascript:updateSelect2Dropdown(<?php echo $id; ?>,'Select Field Lookup','field-lookup');"><em class="fa fa-building text-danger"></em> <?=Html::encode($lookup); ?></a></li>
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
    addReportFieldRight('create-field-lookup');
    $('#field-operator_list_dropdown').on("change", function () { 
        if(this.value != ''){
            updateSelect2Dropdown(this.value,'Select Field Lookup','field-lookup');
        }
    });
</script>
<noscript></noscript>
