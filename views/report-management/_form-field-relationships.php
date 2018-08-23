<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\widgets\Select2;
use app\components\IsataskFormFlag;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
$form = ActiveForm::begin(['id' => $modelReportTables->formName(), 'enableAjaxValidation' => false, 'enableClientValidation' => true]);
?>
<?= IsataskFormFlag::widget(); // change flag ?>
<fieldset class="one-cols-fieldset">
    <div class="create-form">
		<div id="main_fields">
		<?php
		if($modelReportTables->isNewRecord){
			echo $form->field($modelReportTables, 'table_name',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-9'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])
			->widget(Select2::classname(), [
				'model' => $modelReportTables,
				'attribute' => 'table_name',
				'data' => $tableList,
				'options' => ['placeholder' => 'Select Table Name', 'id' => 'table_name','title'=>'Select Table Name','nolabel'=>true, 'aria-required' => 'true'],
				'pluginOptions' => [
					'allowClear' => false,
				],
			])->label('Table Name');
		}else{?>
			<div class="form-group field-reportstables-table_name">
<div class="row input-field"><div class="col-md-3"><label class="form_label required" for="table_name">Table Name</label></div><div class="col-md-9">
	<input id="table_name" class="form-control" name="ReportsTables[table_name]" value="<?php echo $modelReportTables->table_name;?>" aria-required="true" type="hidden">
	<?php echo $modelReportTables->table_name;?>
<div class="help-block"></div></div></div>
</div>

		<?php }
		?>
        <?php echo $form->field($modelReportTables, 'table_display_name', ['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-9'>{input}\n{hint}\n{error}</div></div>", 'labelOptions' => ['class' => 'form_label required']])->textInput(['maxlength'=>$model_field_length['table_display_name']]); ?>
        </div>
    </div>
    <div id="nextstep-fieldrelationship"></div>
</fieldset>
<div class="button-set text-right">
	<?= Html::button('Cancel', ['title' => 'Cancel', 'class' => 'btn btn-primary', 'onclick' => 'loadReportFieldRelationshipsCancel();']) ?>
	<?= Html::button('Previous', ['title' => 'Previous', 'class' => 'btn btn-primary','style'=>'display:none;','id'=>'btn_show_prev', 'onclick' => 'showprevstep();']) ?>
<?php if(!$modelReportTables->isNewRecord){ echo  Html::button('Delete', ['title' => 'Delete', 'class' => 'btn btn-primary',  'onclick' => 'removeReportFieldRelationships("' . $modelReportTables->id . '");']);} ?>
	<?= Html::button('Next', ['class' => 'btn btn-primary','id'=>'btn_show_next','onclick'=>'shownextstep();']) ?>
<?php if($modelReportTables->isNewRecord){ ?>
	<?= Html::button('Add', ['title' => 'Add', 'class' => 'btn btn-primary','style'=>'display:none;','id'=>'btn_show_add', 'onclick' => 'submitReportRelationship(0);']) ?>
<?php } else { ?>
	<?= Html::button('Update', ['title' => 'Update', 'class' => 'btn btn-primary','style'=>'display:none;','id'=>'btn_show_add', 'onclick' => 'submitReportRelationship('.$modelReportTables->id.');']) ?>
<?php } ?>
</div>
<div class="dialog" id="availabl-field-types" title="Add Available Field Types"></div>
<?php ActiveForm::end(); ?>
<script>
	/* input change event */
	jQuery('input').bind('input', function(){
		$('#ReportsTables #is_change_form').val('1');
		$('#ReportsTables #is_change_form_main').val('1');
	});
	jQuery('select').on('change',function(){
		$('#ReportsTables #is_change_form').val('1');
		$('#ReportsTables #is_change_form_main').val('1');
	});
    jQuery(document).ready(function () {
		$('#active_form_name').val('ReportsTables'); // form name
		<?php if($modelReportTables->isNewRecord){ ?>
			$('.admin-left-module-list ul li').removeClass('active');
		<?php } ?>
	});
	function removeReportFieldRelationships(id){
		if(confirm("Are you sure, You want to delete Table Relationship & Lookups for :"+$('#table_name').val()+"?")){
			jQuery.ajax({
			   url:baseUrl +'/report-management/delete-reports-table&id='+id,
			   type: 'get',
			   beforeSend:function (data) {showLoader();},
			   success: function (data) {
				   console.log(data)
				   hideLoader();
				   loadReportFieldRelationships();
				   //addReportTableRelationships('create-field-relationships');
			   }
			});
		}
	}
	function submitReportRelationship(id){
		var url = baseUrl +'/report-management/save-field-relationships';
		if(id!=0){
			url = baseUrl +'/report-management/modify-field-relationships&id='+id;
		}
		jQuery.ajax({
		   url:url,
		   type: 'post',
		   data:$('form#ReportsTables').serialize(),
		   beforeSend:function (data) {showLoader();},
		   success: function (data) {
			   console.log(data)
			   hideLoader();
			   loadReportFieldRelationships();
			   // addReportTableRelationships('create-field-relationships');
		   }
		});
	}
	function showprevstep(){
		$('.sub-heading').html('Add Table Fields');
		$("#btn_show_prev").hide();
		$("#btn_show_add").hide();
		$("#btn_show_next").show();
		jQuery('#nextstep-fieldrelationship').hide();
		$("#main_fields").show();

	}
    function shownextstep(){
		has_error=false;
		if($('#table_name').val()==""){
			$('.field-reportstables-table_name').addClass('has-error');
			$('#table_name').parent('div').find('.help-block').html('Table Name cannot be blank.');
			has_error=true;
		}
		if($('#reportstables-table_display_name').val()==""){
			$('#reportstables-table_display_name').trigger('blur');
			has_error=true;
		}
		if(has_error==true){
			return false;
		}
		if(jQuery('#nextstep-fieldrelationship').html()==""){
		$('.sub-heading').html('Add Field Relationships<div class="pull-right"><a href="javascript:void(0);" onclick="addLookup()"><em class="fa fa-search text-"></em></a>&nbsp;<a href="javascript:void(0);" onclick="addRelation()"><em class="fa fa-sitemap text-danger"></em></a></div>');
		var URL = baseUrl +'/report-management/nextstep-field-relationship&table_name='+$('#table_name').val()+'&table_display_name='+$('#reportstables-table_display_name').val();
		<?php if(!$modelReportTables->isNewRecord){ ?>
		var URL = baseUrl +'/report-management/nextstep-field-relationship&table_name='+$('#table_name').val()+'&table_display_name='+$('#reportstables-table_display_name').val()+'&id=<?=$modelReportTables->id?>';
		<?php }?>
			jQuery.ajax({
			   url: URL,
			   type: 'get',
			   beforeSend:function (data) {showLoader();},
			   success: function (data) {
				   hideLoader();
				   $("#main_fields").hide();
				   $("#btn_show_next").hide();
				   $("#btn_show_prev").show();
				   $("#btn_show_add").show();
				   jQuery('#nextstep-fieldrelationship').html(data);
			   }
			});
		}else{
			 $('.sub-heading').html('Add Field Relationships<div class="pull-right"><a href="javascript:void(0);" onclick="addLookup()"><em class="fa fa-search text-"></em></a>&nbsp;<a href="javascript:void(0);" onclick="addRelation()"><em class="fa fa-sitemap text-danger"></em></a></div>');
			 $("#main_fields").hide();
			 jQuery('#nextstep-fieldrelationship').show();
			 $("#btn_show_next").hide();
			 $("#btn_show_prev").show();
			 $("#btn_show_add").show();
		}
	}
	$('select#table_name').on('change',function(){
		var myStr = $(this).val()
		myStr=myStr.toLowerCase().replace('tbl_','');
		myStr=myStr.replace(/(^\s+|[^a-zA-Z0-9 ]+|\s+$)/g," ");   //this one
		$('input[name="ReportsTables[table_display_name]"]').val(myStr.capitalize());
	});
	String.prototype.capitalize = function(){
        return this.toLowerCase().replace( /\b\w/g, function (m) {
            return m.toUpperCase();
        });
    };
</script>
<noscript></noscript>
