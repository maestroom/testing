<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\grid\GridView;
use yii\helpers\Url;
use app\components\IsataskFormFlag;
use kartik\widgets\Select2;

/* @var $this yii\web\View */
/* @var $model app\models\ReportsFieldCalculations */
/* @var $form yii\widgets\ActiveForm */
$form = ActiveForm::begin(['id' => $model->formName(), 'enableAjaxValidation' => false, 'enableClientValidation' => true]);
$report_type_id = $model->id;
?>
<?= IsataskFormFlag::widget(); // change flag ?>
<div id="first">
    <fieldset class="one-cols-fieldset ">
        <div class="create-form">
            <?= $form->field($model, 'report_type', ['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>", 'labelOptions' => ['class' => 'form_label required']])->textInput(['maxlength'=>$model_field_length['report_type']]); ?>
            <?= $form->field($model, 'report_type_description', ['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>", 'labelOptions' => ['class' => 'form_label required']])->textArea(['rows' => '6','maxlength'=>$model_field_length['report_type_description']]); ?>
			<?= $form->field($model, 'sp_name',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\nSelect if you want to get result of report type by Stored Procedure, leave blank otherwise.\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->widget(Select2::classname(), [
			'data' => ['MediaOut'=>'MediaOut(* Table : "tbl_tasks_units")','SlaDataByServices'=>'SlaDataByServices(* Table : "tbl_tasks_units")'],
			'options' => ['multiple' => false,'prompt' => 'Stored Procedure','aria-label'=>'Stored Procedure','nolabel'=>true],
			'pluginOptions' => [
				'allowClear' => true,
			],
			]);?>
            <?php /*?>
            <div class='row input-field'>
            <div class="form-group clearfix">
                    <div class='col-md-3'>
                        <label class='form_label'>
                            Field Calculations
                        </label>
                    </div>
                    <div class='col-md-7'>
						<?= Html::Button('Add Field Calculation', ['title' => 'Associate Field Calculations','class' => 'btn btn-primary', 'id' => 'field-calculation-list', 'onClick' => 'getallfieldcalculation();']) ?>
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
											<th title="Associated Field Calculations"><strong>Associated Field Calculations</strong></th>
											<th title="Action"><strong>Action</strong></th>
										</tr>
									</thead>
									<tbody>
										<?php
										if(isset($calculationList) && !empty($calculationList)){
											foreach($calculationList as $single){
										?>
														<tr class="report_type_calculation_<?php echo $single['field_calculation_id']; ?>">
															<input type="hidden" name="field_calculation[]" class="report_field_calculation" value="<?php echo $single['field_calculation_id']; ?>" />
															<td><?php echo $single['calculation_name']; ?></td>
															<td>
																<a href="javascript:void(0);" onClick="remove_dialog_single_data('ReportsReportType','report_type_calculation','<?php echo $single['field_calculation_id']; ?>');">
																	<em class="fa fa-close text-primary" title="Delete"></em>
																</a>
															</td>
														</tr>
												<?php }?>
										<?php } ?>
									</tbody>
							</table>
                            <!-- End table -->
                    <div id="report-fieldtypes" class="has-error help-block"></div>
                    </div>

            </div>
        </div>
        <?php */?>
        </div>
    </fieldset>
    <div class="button-set text-right">
        <?= Html::button($model->isNewRecord ? 'Cancel' : 'Delete', ['title' => $model->isNewRecord ? 'Cancel' : 'Delete', 'class' => 'btn btn-primary', 'onclick' => $model->isNewRecord ? 'loadReportsReportTypeCancel();' : 'removeReportSingleData("' . $model->id . '","' . $model->report_type . '","report-type");']) ?>
        <?= Html::button('Next', ['title'=>"Next",'class' => 'btn btn-primary','onclick'=>'NextRT();']) ?>
    </div>
</div>

<div id="second" style="display: none" class="report-type-main next">
    <div class="form-builder-title">
     <h2 class="pull-left" id="secondTitle">Report Type Form</h2>
	</div>
    <fieldset class="one-cols-fieldset">
	    <div class="template_wrkflow-right">
			<div id="primary_table_fields">  
            <?php if(isset($dataProvider)){  ?>
			<?php 
				echo   GridView::widget([                        
		  'id'=>'get-report-type-grid',
		  'dataProvider' => $dataProvider,
		  'layout' => '{items}',
		  'columns' => [
				['class' => '\kartik\grid\ExpandRowColumn', 'format'=>'raw',  'detail' => function($dataProvider, $key, $index, $column)use($field_relationships,$tables,$model,$report_typefield_data){return $this->render('get-primary-table-fields', ['dataprovider'=>$dataProvider, 'keyValue'=>$key, 'index'=>$index, 'field_relationships'=>$field_relationships,'tables'=>$tables,'model'=>$model,'report_typefield_data'=>$report_typefield_data]);},'headerOptions'=>['title'=>'Expand/Collapse All','class'=>'first-th'],'contentOptions'=>['title'=>'Expand/Collapse Row','class' => 'first-td text-center'], 'expandIcon' => '<a href="javascript:void(0);" aria-label="Expand Raw"><span class="glyphicon glyphicon-plus"></span></a>', 'collapseIcon' => '<a href="javascript:void(0);" aria-label="Collapse Raw"><span class="glyphicon glyphicon-minus"></span></a>', 'expandTitle' => 'Expand', 'collapseTitle' => 'Collapse', 'expandAllTitle' => 'Expand All', 'collapseAllTitle' => 'Collapse All', 'mergeHeader'=>true, 'value' => function ($model) { return 1;}],
				['attribute' => 'table_full_name', 
					'label' => '', 
					'header'=>'<a href="#" class="tag-header-black" title="Table Display Name">Table Display Name</a>' ,
					'headerOptions'=>['title'=>'Table Display Name','class'=>'table_display_name_th'], 
					'contentOptions' => ['class' => 'table_display_name_td'], 
					'format' => 'raw', 
					'value' => function($dataProvider, $key, $index, $column) use($tables) {
						if(!isset($tables[$key]['id'])){
							return "<a href='javascript:void(0);' title='Calculation Fields' class='tag-header-black'><span class='tableList".$key."'>Calculation Fields</span></a>";
						} else {
							return "<input type='hidden' name='table_name[]' class='tablesList' value='".$tables[$key]['id']."'/><a href='javascript:void(0);' title='".$tables[$key]['table_display_name']."' class='tag-header-black'><span class='tableList".$key."'>".$tables[$key]['table_display_name']."</span></a>";
						}
					}
				],
				['attribute' => 'table_name', 
					'label' => '', 
					'header'=>'<a href="#" class="tag-header-black" title="Table Source">Table Source</a>', 
					'headerOptions'=>['title'=>'','class'=>'table_name_th'],
					'contentOptions' => ['class' => 'table_name_td'], 
					'format' => 'html', 
					'value' => function($dataProvider,$key,$index, $column){
					  if($key!='calcutions'){ return '<a href="#" title="'.$key.'" class="tag-header-black">'.$key.'</a>';}
					}
				],
				['class' => 'kartik\grid\ActionColumn',
				'header' => '<span title="Add Table or Calculated Fields" class="fa fa-plus text-primary" style="cursor: pointer;padding:5px;display:none;" onClick="javascript:add_primary_table(0,\'\');"></span>',
				'headerOptions' => ['class'=>'third-th table_name_action_th','title'=>'Actions'],
				'contentOptions' => ['class' => 'third-td table_name_action_td'],
				'mergeHeader'=>false,
				'template'=>'{add}{relationship}{delete}',
				'buttons'=>[
						'delete'=>function($dataProvider, $key, $index) use($tables,$report_type_id){
							if($tables[$index]['id'] > 0)
							{
								return Html::a('<em class="fa fa-close text-primary "></em>', 'javascript:void(0)', [
									'title' => Yii::t('yii', 'Remove'),
                                                                        'aria-label' => Yii::t ( 'yii', 'Delete' ),
									'class' => 'icon-set',
									'onClick' => 'remove_grid_report_relationship_table(this,"'.$tables[$index]['table_display_name'].'","'.$index.'",'.$report_type_id.',"report-type-table");',
								]);
							}
						},
						'relationship'=>function($dataProvider, $key, $index) use($tables){
							if($tables[$index]['id'] > 0)
							{
								return Html::a('<em class="fa fa-sitemap text-danger "></em>', 'javascript:void(0)', [
									'title' => Yii::t('yii', 'Add Related Table'),
									'class' => 'icon-set',
									'onClick' => 'add_primary_table("'.$tables[$index]['id'].'","relationship");',
								]);
							}
						},
						'add'=>function($dataProvider, $key, $index) use($tables){
							if($tables[$index]['id'] > 0){
								return Html::a('<em class="fa fa-plus text-primary "></em>', 'javascript:void(0)', [
									'title' => Yii::t('yii', 'Add Additional Fields'),
									'class' => 'icon-set',
									'onClick' => 'add_primary_table("'.$tables[$index]['id'].'","addfields");',
								]);
							}else{
								return Html::a('<em class="fa fa-plus text-primary "></em>', 'javascript:void(0)', [
									'title' => Yii::t('yii', 'Add Caclulation Fields'),
									'class' => 'icon-set',
									'style'	=> ' margin-left: 61px',
									'onClick' => 'add_calculation("'.$tables[$index]['id'].'");',
										]);
									}
								}
							],
						 ],
					  ],
					  'export'=>false,
					  'floatHeader'=>false,
					  'pjax'=>true,
					  'responsive'=>false,
					  'floatHeaderOptions' => ['top' => 'auto'],
					  'persistResize'=>false,
					  'resizableColumns'=>false,
					  'pjaxSettings'=>[
					  'options'=>['id'=>'get-report-type-grid-pajax','enablePushState' => false],
					  'neverTimeout'=>true,
					  'beforeGrid'=>'',
					  'afterGrid'=>'',
				],
				'rowOptions'=>['class'=>'sort'],
		  ]);
				} else {?>
				<div class="head-title">
					Add Table or Calculated Fields <span title="Add Fields From Primary Table" class="fa fa-plus text-primary pull-right" style="cursor:pointer;font-size:14px;padding:2px;text-align:center;width:93px;" onClick="javascript:add_primary_table(0,'');"></span>
				</div>  
				<?php } ?>   
			</div> 
		</div>
    </fieldset>
    <div class="button-set text-right">
		<?= Html::button('Previous', ['title'=>"Previous",'class' => 'btn btn-primary','onclick'=>'report_type_Prev();']) ?>
		<?= Html::button($model->isNewRecord ? 'Cancel' : 'Delete', ['title' => $model->isNewRecord ? 'Cancel' : 'Delete', 'class' => 'btn btn-primary', 'onclick' => $model->isNewRecord ? 'loadReportsReportTypeCancel();' : 'removeReportSingleData("' . $model->id . '","' . $model->report_type . '","report-type");']) ?>
		<?= Html::button($model->isNewRecord ? 'Add' : 'Update', [ 'title' => $model->isNewRecord ? 'Add' : 'Update', 'class' => 'btn btn-primary submit-btn-handler','id'=>'btn_add_edit','onclick'=>'validateAdd();']) ?>
    </div>
</div>
<div class="dialog" id="availabl-primary-tables" title="Add Fields From Primary Table"></div>
<?php ActiveForm::end(); ?>
<script>
	/* change input event */
	$('input').bind('input', function(){
		$('#ReportsReportType #is_change_form').val('1');
		$('#ReportsReportType #is_change_form_main').val('1');
	});
	$('textarea').bind('input',function(){
		$('#ReportsReportType #is_change_form').val('1');
		$('#ReportsReportType #is_change_form_main').val('1');
	});
	
    $(document).ready(function () {
		$('document').ready(function(){ $("#active_form_name").val('ReportsReportType'); });
        $("#second").hide();
        $('#<?= $model->formName() ?>').submit(function () {
            SubmitAjaxForm("<?= $model->formName() ?>", this, "loadReportsReportType()", "reportform_div");
        });
    $('input').customInput();
    var fixHelper = function(e, ui) {
            ui.children().each(function() {
                   $(this).width($(this).width());
            });
            return ui;
     };
     $('.primary_table_radio').on('click',function(){
		$('#primary_table').val(this.value);
	 });
    });
    function validateAdd(){
		var tablelists = $('.tablesList');
		var sp_name = $('#reportsreporttype-sp_name').val();
		if(tablelists.length == 0){
			alert("Please add atleast one table to save Report Type");
			return false;
		}else{
			if(sp_name=='MediaOut' || sp_name=='SlaDataByServices'){
				has_req_tbl=false;
				$('.table_name_td').each(function(){
					if($(this).text()=='tbl_tasks_units'){
						has_req_tbl=true;
						return false;
					}
				});
				if(has_req_tbl){
					SubmitAjaxForm("<?=$model->formName()?>",$('btn_add_edit'),"loadReportsReportType()","reportform_div");
				}else{
					alert("'tbl_tasks_units' table is required for '"+sp_name+"' Stored Procedure.");
					return false;
				}
			}else{
				SubmitAjaxForm("<?=$model->formName()?>",$('btn_add_edit'),"loadReportsReportType()","reportform_div");
			}
		}
	}
    function NextRT(){
		if($('#reportsreporttype-report_type').val()==''){
			$("#reportsreporttype-report_type").trigger('blur');
		}else{
			$("#first").hide();
			$("#second").show();
			$("#secondTitle").text($("#reportsreporttype-report_type").val());
		}
	}
</script>
<style>
#get-report-type-grid-container{height: 100%;}
</style>
