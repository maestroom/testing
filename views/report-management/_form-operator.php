<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\components\IsataskFormFlag;

/* @var $this yii\web\View */
/* @var $model app\models\ReportsFieldType */
/* @var $form yii\widgets\ActiveForm */
$form = ActiveForm::begin(['id' => $model->formName(), 'enableAjaxValidation' => false, 'enableClientValidation' => true]);
?>
<?= IsataskFormFlag::widget(); // change flag ?>
<fieldset class="one-cols-fieldset">
    <div class="create-form">
        <?= $form->field($model, 'field_operator', ['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-9'>{input}\n{hint}\n{error}</div></div>", 'labelOptions' => ['class' => 'form_label required']])->textInput(['maxlength'=>$model_field_length['field_operator']]); ?>    	
        <?= $form->field($model, 'field_operator_use', ['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-9'>{input}\n{hint}\n{error}</div></div>", 'labelOptions' => ['class' => 'form_label required']])->textArea(['maxlength'=>$model_field_length['field_operator_use']]); ?>    	
        <div class='row input-field'>
            <div class="form-group clearfix">
                    <div class='col-md-3'>
                            Field Types<span class='text-danger'>*</span>
                    </div>
                    <div class='col-md-7'>
                            <?php $operator_id = $model->isNewRecord?0:$model->id; ?>
                            <?= Html::Button('Add Field Types', ['title' => 'Associate Field Types to this Field Operator','class' => 'btn btn-primary', 'id' => 'pricepoint-tax-classes', 'onClick' => 'getallfieldtypes('.$operator_id.');']) ?>
                    </div>
            </div>
        </div>
        <div class="row input-field">
            <div class="form-group clearfix">
                    <div class="col-md-3"></div>
                    <div class="col-md-9">
                            <!-- table stripped -->
                                    <table class="table table-striped sm-table-report" id="form-fieldtype-report" width="100%" cellspacing="0" cellpadding="0" border="0">
                                            <thead>
                                                    <tr>
                                                            <th><a href="javascript:void(0);" class="tag-header-black" title="Associated Field Types"><strong>Associated Field Types</strong></a></th>
                                                            <th><a href="javascript:void(0);" class="tag-header-black"  title="Action"><strong>Action</strong></a></th>
                                                    </tr>	
                                            </thead>
                                            <tbody>	
                                                    <?php  
                                                    $fieldtypes_ids = '';
                                                    if(isset($fieldtypesList) && !empty($fieldtypesList)){
                                                        ?>
                                                            <?php foreach($fieldtypesList as $fieldtype){
//                                                                echo '<pre>';
//                                                                print_r($fieldtype);
//                                                                die;
                                                                if(empty($fieldtypes_ids)) { $fieldtypes_ids = $fieldtype['fieldtype_id'];} else{$fieldtypes_ids .= ','.$fieldtype['fieldtype_id']; }?>
                                                                    <tr class="fieldtype_<?php echo $fieldtype['fieldtype_id']; ?>">
                                                                            <input type="hidden" name="field_type[]" class="field_type" value="<?php echo $fieldtype['fieldtype_id']; ?>" />
                                                                            <td><?php echo $fieldtype['field_type']; ?></td>
                                                                            <td><a href="javascript:void(0);" onClick="remove_dialog_single_data('form-fieldtype-report','fieldtype','<?php echo $fieldtype['fieldtype_id']; ?>');" aria-label="remove"><em class="fa fa-close text-primary" title="Delete"></em></a></td>
                                                                    </tr>
                                                            <?php }?>
                                                    <?php } ?>
                                                    <input type="hidden" value="<?=$fieldtypes_ids?>" class="total_field_type_ids" >                                                    
                                            </tbody>
                                    </table>
                            <!-- End table -->
                    <div id="report-fieldtypes" class="has-error help-block"></div>
                    </div>
                    
            </div>
        </div>
    </div>    
</fieldset>
<div class="button-set text-right">
<?= Html::button($model->isNewRecord ? 'Cancel' : 'Delete', ['title' => $model->isNewRecord ? 'Cancel' : 'Delete', 'class' => 'btn btn-primary', 'onclick' => $model->isNewRecord ? 'loadReportFieldOperatorsCancel();' : 'removeReportSingleData("' . $model->id . '","' . $model->field_operator . '","field-operator");']) ?>    
<?= Html::button($model->isNewRecord ? 'Add' : 'Update', [ 'title' => $model->isNewRecord ? 'Add' : 'Update', 'class' => 'btn btn-primary','onclick'=>'savefieldoperators("'.$model->formName().'");']) ?>
</div>   
<div class="dialog" id="availabl-field-types" title="Add Available Field Types"></div>
<?php ActiveForm::end(); ?>
<script>
	/* Change Event */
	jQuery('input').bind('input', function(){
		$('#ReportsFieldOperators #is_change_form').val('1');
		$('#ReportsFieldOperators #is_change_form_main').val('1');
	});
	jQuery('textarea').bind('input', function(){ 
		$('#ReportsFieldOperators #is_change_form').val('1'); 
		$('#ReportsFieldOperators #is_change_form_main').val('1'); 
	});
	jQuery(document).ready(function () {
		$('document').ready(function(){ $("#active_form_name").val('ReportsFieldOperators'); }); // change form name
        $('#<?= $model->formName() ?>').submit(function (e) {
            savefieldoperators('<?= $model->formName() ?>');
        });
    });
    function savefieldoperators(form_id)
    {
        if($('#reportsfieldoperators-field_operator').val()==''){
                $("#reportsfieldoperators-field_operator").next().html('Please Enter Field Operator.');
                $("#reportsfieldoperators-field_operator").parent().parent().parent().addClass('has-error');
                return false;
        }           
        if($('#reportsfieldoperators-field_operator_use').val()==''){
                $("#reportsfieldoperators-field_operator_use").next().html('Please Enter Field Operator Use.');
                $("#reportsfieldoperators-field_operator_use").parent().parent().parent().addClass('has-error');
                return false;
        }
        if($('.field_type').length==0 || $('.field_type').length==''){                   
                $("#report-fieldtypes").html('Please Add Field Type.');                    
                $("#report-fieldtypes").parent().parent().parent().addClass('has-error');
                return false;
        }
        SubmitAjaxForm("<?= $model->formName() ?>", this, "loadReportFieldOperators()", "reportform_div");
    }
</script>
<noscript></noscript>
