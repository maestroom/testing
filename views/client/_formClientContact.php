<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\grid\GridView;
use kartik\widgets\Select2;
use app\components\IsataskFormFlag;
/* @var $this yii\web\View */
/* @var $model app\models\ClientContacts */
/* @var $form yii\widgets\ActiveForm */
$fullUrl=Yii::$app->request->hostInfo.Yii::$app->request->baseUrl.'/index.php?'.Yii::$app->request->queryString;
//print_r($model_field_length);die;
?>

<?php $form = ActiveForm::begin(['id' => $model->formName(),'enableAjaxValidation' => false,'enableClientValidation' => true]); ?>
<?= IsataskFormFlag::widget(); // change flag ?>
<div id="first">
<!--<div class="sub-heading">Section 1 : Contact Detail</div>-->
	<fieldset class="one-cols-fieldset">
	    <div class="create-form">
	    	<?php //$form->field($model, 'client_id')->hiddenInput(['value'=> $client_id])->label(false); ?>
	    	<input type="hidden" id="client_id" name="ClientContacts[client_id]" value="<?= $client_id ?>" />
	    	<?= $form->field($model, 'contact_type' ,['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->widget(Select2::classname(), [
					'data' => $contactTypeList,
					'options'=>['prompt'=>'Select Contact Type'],
					/*'pluginOptions' => [
						'allowClear' => true
					],*/]);?>
	    	
	    	<?= $form->field($model, 'lname',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['maxlength'=>$model_field_length['lname']]); ?>
	    	
	    	<?= $form->field($model, 'fname',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['maxlength'=>$model_field_length['fname']]); ?>
	    	
	    	<?= $form->field($model, 'mi',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['maxlength'=>$model_field_length['mi']]); ?>
	    	
	    	<?= $form->field($model, 'title',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['maxlength'=>$model_field_length['title']]); ?>
	    	
	    	<?= $form->field($model, 'phone_o',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['maxlength'=>$model_field_length['phone_o']]); ?>
	    	<?= $form->field($model, 'phone_m',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['maxlength'=>$model_field_length['phone_m']]); ?>
	    	<?= $form->field($model, 'email',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['maxlength'=>$model_field_length['email']]); ?>
	    	<?= $form->field($model, 'add_1',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n<span>Street Address, P.O. Box, Company Name, C/O</span>\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['maxlength'=>$model_field_length['add_1']]); ?>
	    	<?= $form->field($model, 'add_2',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n<span>Apartment, Suite, Unit, Building, Floor, Etc</span>\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['maxlength'=>$model_field_length['add_2']]); ?>
	    	<?= $form->field($model, 'city',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['maxlength'=>$model_field_length['city']]); ?>
	    	<?= $form->field($model, 'state',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['maxlength'=>$model_field_length['state']]); ?>
	    	<?= $form->field($model, 'zip',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(['maxlength'=>$model_field_length['zip']]); ?>
	    	
	    	<?= $form->field($model, 'country_id',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->widget(Select2::classname(), [
					'data' => $countryList,
					'options'=>['prompt'=>'Select Country'],
					/*'pluginOptions' => [
						'allowClear' => true
					],*/]);?>
	    	
	    	<?= $form->field($model, 'notes',['template' => "<div class='row input-field'><div class='col-md-3'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textArea(['rows' => '3','maxlength'=>$model_field_length['notes']]); ?>
	    </div>	
	</fieldset>
	<div class="button-set text-right">
	    <?= Html::button( 'Cancel', ['title' => 'Cancel','class' => 'btn btn-primary', 'onclick'=> 'loadClientContactListCancel();']) ?>
	    <?= Html::button( 'Next', ['title' => 'Next','class' =>  'btn btn-primary','onclick'=> 'ClientshowHideBlock("second","first");']) ?>
	</div>
</div>
<?php // if(!$model->isNewRecord) { ?>

<div id="second" style='display:none;'>
	<div class="tab-inner-fix">
		<?php echo GridView::widget([
			'id'=>'caselist-grid',
			'dataProvider'=> $caseDataProvider,
			'layout' => '{items}<div class="pagination-main">{summary}{pager}</div>',
			'columns' =>[
				['attribute' =>'case_name', 'headerOptions' => ['title' => 'Case Name']],	
				['class' => '\kartik\grid\CheckboxColumn', 'rowHighlight' => false,'name' => 'caselist', 'checkboxOptions' => function($model, $key, $index, $column) { return [ 'checked' => $model->iscontactexist==1?true:false, 'value' => $model->id, 'customInput'=>true ]; } ],
			 ],
			'floatHeader'=>false,
			'pjax'=>true,
			'pjaxSettings'=>[
				'options'=>['id'=>'caselist-grid-pajax','enablePushState' => false],
				'neverTimeout'=>true,
				'beforeGrid'=>'',
	        	'afterGrid'=>'',
	    	],
	    	'export'=>false,
			'responsive'=>true,
			'hover'=>true,
			'pager' => [
					'options'=>['class'=>'pagination'],   // set clas name used in ui list of pagination
					'prevPageLabel' => 'Previous',   // Set the label for the "previous" page button
					'nextPageLabel' => 'Next',   // Set the label for the "next" page button
					'firstPageLabel'=>'First',   // Set the label for the "first" page button
					'lastPageLabel'=>'Last',    // Set the label for the "last" page button
					'nextPageCssClass'=>'next',    // Set CSS class for the "next" page button
					'prevPageCssClass'=>'prev',    // Set CSS class for the "previous" page button
					'firstPageCssClass'=>'first',    // Set CSS class for the "first" page button
					'lastPageCssClass'=>'last',    // Set CSS class for the "last" page button
					'maxButtonCount'=>5,    // Set maximum number of page buttons that can be displayed
			],
			'rowOptions'=>['class'=>'sort'],
		]);
		?>	
	</div>
	<div class="button-set text-right">
		<?= Html::button('Previous', ['title' => 'Previous','class' =>  'btn btn-primary','onclick'=>'ClientshowHideBlock("first","second");']) ?>
	    <?= Html::button('Cancel', ['title' => 'Cancel','class' => 'btn btn-primary', 'onclick'=> 'loadClientContactListCancel();']) ?>
	    <?= Html::button( $model->isNewRecord ? 'Add' :'Update', ['title' =>$model->isNewRecord ? 'Add' : 'Update','class' =>  'btn btn-primary','onclick'=>'SubmitAjaxForm("'.$model->formName().'",this,"loadClientContactList()","clientcontactform");']) ?>
	</div>
</div>
<?php //} ?>
<?php ActiveForm::end(); ?>
<script>
$('input').customInput();
$('#module-url').val('<?=$fullUrl ?>');
$('#pajax_container').val('caselist-grid-pajax');
$(document).ready(function(){ // change form name
	$('#active_form_name').val('ClientContacts'); 
});
$('input').bind('input', function(){
	$('#ClientContacts #is_change_form').val('1'); 
	$('#ClientContacts #is_change_form_main').val('1'); 
}); 
$('select').on('change', function() {
	$('#ClientContacts #is_change_form').val('1');
	$('#ClientContacts #is_change_form_main').val('1'); 
});
$('textarea').bind('input', function(){ 
	$('#ClientContacts #is_change_form').val('1'); 
	$('#ClientContacts #is_change_form_main').val('1'); 
});
$('body').on('change', '#caselist-grid', function () {
	$('#ClientContacts #is_change_form').val('1'); 
	$('#ClientContacts #is_change_form_main').val('1'); 
	$('#active_form_name').val('ClientContacts'); // change form name
});
</script>
<noscript></noscript>
