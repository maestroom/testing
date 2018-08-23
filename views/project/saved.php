<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\components\IsataskFormFlag;
/* @var $this yii\web\View */
/* @var $model app\models\Tasks */
$this->title = 'Add Project';
$this->params['breadcrumbs'][] = ['label' => 'Case Projects', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->registerJsFile(Yii::$app->request->baseUrl.'/js/form-builder/admin.formbuilder.js');
$this->registerJsFile(Yii::$app->request->baseUrl.'/js/jquery.MultiFile.js');
$this->registerJsFile(Yii::$app->request->baseUrl.'/js/project.js');
?>
<div id="project_container" class="right-main-container">
	<fieldset class="two-cols-fieldset section-add-new-project">
    <div id="tabs">
    	<ul>
			<li><a href="#tabs-deadlinedetails" title="Step 1: Enter Details">Enter Details</a></li>
			<li><a href="#tabs-attachmedia" title="Step 2: Attach Media">Attach Media</a></li>
			<li><a href="#tabs-selectworkflow" title="Step 3: Build Workflow">Build Workflow</a></li>
			<li><a href="#tabs-formdetails" title="Step 4: Add Instructions">Add Instructions</a></li>
		</ul>
		<?php $form = ActiveForm::begin(['id' => $model->formName(),'enableAjaxValidation' => false,'enableClientValidation' => true,'options' => ['enctype' => 'multipart/form-data']]); ?>
		<?= IsataskFormFlag::widget(); // change flag ?>
		<div id="tabs-deadlinedetails">
			<div id="form_div_deadlinedetails">
			<?=$this->render('_deadlinedetailsform',[
					'model' => $model,
					'modelInstruct'=>$modelInstruct,
					'case_id'=>$case_id,
					'priorityList'=>$priorityList,
					'projectReqType_data'=>$projectReqType_data,
					'listSalesRepo'=>$listSalesRepo,
					'form'=>$form,
					'flag'=>'Saved',
					
			]);?>
			</div>
		</div>
		<div id="tabs-attachmedia">
			<div id="form_div_attachmedia"><?=$this->render('_attachmediaform',[
				'case_productions' => $case_productions,
				'case_media'=>$case_media,
				'form'=>$form,
				'case_id'=>$case_id,
				'modelInstruct'=>$modelInstruct,
				'flag'=>'Saved',
			]);?></div>
		</div>
		<div id="tabs-selectworkflow">
			<div id="form_div_selectworkflow">
				<?=$this->render('_selectworkflowform',[
					'modelInstruct'=>$modelInstruct,
					'case_id'=>$case_id,
					'serviceTaskTemplate_data' => $serviceTaskTemplate_data,
					'teamservice_locations' => $teamservice_locations,
					'teamserviceName' => $teamserviceName,
					'teamLocation' => $teamLocation,
					'form' => $form,
					'holidayAr' => $holidayAr,
					'flag' => 'Saved',
					'optionModel'=>$optionModel,
					'filtersavedlocnames'=>$filtersavedlocnames
			]);?>
			</div>
		</div>
		<div id="tabs-formdetails">
			<div id="form_div_formdetails">
				<?=$this->render('_enterformdetailsform',[
					'form'=>$form,
					'case_id'=>$case_id,
					'flag'=>'Saved',
				]);?>
			</div>		
		</div>
		<input type="hidden" id="flag" value="" name="flag">
		<input type="hidden" id="diffslahours" value="0">
		<input type="hidden" id="triggerChange" value="0">
		<input type="hidden" id="totalHours" value="0" >
		<input type="hidden" id="case_id" value="<?=$case_id?>" name="case_id">
		<?php ActiveForm::end(); ?>
	</div>	
	</fieldset>
</div>
<script>
	  /* form value */
	  $('select').on('change', function(){
		  $('#Tasks #is_change_form').val('1'); // change flag
		  $('#is_change_form_main').val('1'); // change flag value
	  });
	  $('input').bind('input', function(){
		  $('#Tasks #is_change_form').val('1'); // change flag
		  $('#is_change_form_main').val('1'); // change flag value
	  });
	  /*$(".myheader a").click(function () {
		    $header = $(this).parent();
		    $content = $header.next();
		    $content.slideToggle(500, function () {
			$header.text(function () {
			  //  change text based on condition
			  //return $content.is(":visible") ? "Collapse" : "Expand";
			});
		    });	
		});

		/**
		 * Header span
		 */
		/*$('.myheader').on('click',function(){
			if($(this).hasClass('myheader-selected-tab')){
				$(this).removeClass('myheader-selected-tab');
			}else{
				$(this).addClass('myheader-selected-tab');
			}	
		});*/
$( "#tabs" ).tabs({
	  disabled: [ 1, 2, 3 ],
    beforeActivate: function (event, ui) {
	      
    },
    beforeLoad: function( event, ui ) {
      ui.jqXHR.error(function() {
        ui.panel.html(
          "Error loading current tab." );
      });
    }
  });	

</script>
<noscript></noscript>
