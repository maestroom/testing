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
                        <a title="Field Types" href="javascript:addReportFieldRightNew('create-field-type');" class="admin-main-title">
                          <em title="Field Types" class="fa fa-folder-open text-danger"></em>Field Types
                        </a>                          
                        <div class="select-items-dropdown">
                            <?php 
							   echo Select2::widget([
									'name'=>'Select dropdown',
									'attribute' => 'field_theme_list',
									'data' => $themeList,
									'options' => ['prompt' => 'Filter by Field Theme', 'title' => 'Filter by Field Theme', 'id' => 'report-type-theme_id', 'class' => 'form-control','nolabel'=>'true'],
									'pluginOptions' => [
										'allowClear' => true
									]
								]); 
							?>                        
                        </div>
                        <div class="left-dropdown-list sub-links-field-types">
                            <div class="admin-left-module-list">
									<ul class="sub-links">
									  <?php if(!empty($fieldstypeList)){ foreach ($fieldstypeList as $fieldtype) { ?>
											<li id="report_field-type_<?php echo $fieldtype->id; ?>"><a href="javascript:updateReportfieldtype(<?php echo $fieldtype->id; ?>,'Select Field Type','field-type','<?=$fieldtype->field_type_theme_id?>');" title="<?=Html::encode($fieldtype->field_type); ?>"><em title="<?=Html::encode($fieldtype->field_type); ?>" class="fa fa-building text-danger"></em> <?=Html::encode($fieldtype->field_type); ?></a></li>
									  <?php }
										} ?>
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
    /**
 * Get Field Theme wise field types in report management
 */
$('#report-type-theme_id').on("change", function (event) { 
		var chk_status = checkformstatus("event"); // check form edit status 
		if(chk_status == true) {
			var theme_id = $(this).val();
			jQuery.ajax({
			   url: baseUrl +'/report-management/ajax-field-theme&id='+theme_id,
			   type: 'get',
			   beforeSend:function (data) {showLoader();},
			   success: function (data) {
				   hideLoader();
				   jQuery('.sub-links-field-types').html(data);
			   }
			});
			//console.log($(this).val()); 
		}
    });
    addReportFieldRight('create-field-type');	
</script>
<noscript></noscript>
