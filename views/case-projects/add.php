<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
/* @var $this yii\web\View */
/* @var $model app\models\Tasks */

$this->title = 'Add Project';
$this->params['breadcrumbs'][] = ['label' => 'Case Projects', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->registerJsFile(Yii::$app->request->baseUrl.'/js/jquery.form.min.js');
?>
<div id="project_container" class="right-main-container">
    <div class="sub-heading" class="two-cols-fieldset"><?= Html::encode($this->title) ?></div>
    <div id="form_div"  class="two-cols-fieldset">
       <?php 
$form = ActiveForm::begin(['action'=> $model->isNewRecord ?Url::to(['case-projects/add']):Url::to(['case-projects/edit']),'id' => $model->formName(),'enableAjaxValidation' => false,'enableClientValidation' => true,'options' => ['enctype'=>'multipart/form-data','onsubmit'=>'return validateproject();'],]); ?>
<fieldset class="one-cols-fieldset">
    <div id="wf-tabs">
		<ul>
			<li><a href="#tabs-deadlinedetails">Enter Deadline Details</a></li>
			<li><a href="#tabs-attachmedia">Attach Media</a></li>
			<li><a href="#tabs-selectworkflow">Select Workflow</a></li>
			<li><a href="#tabs-formdetails">Enter Form Details</a></li>
			<li style="width:350px;">Display By:&nbsp;&nbsp;<input type="radio" value="M" name="display_by" id="mediadisplay_by" <?php if($post_data['chkmediadisplay_by'] == 'M') {?>checked="checked" <?php }?> onclick="showHideMBy(this.value);" /> Media<label for="mediadisplay_by"></label>&nbsp;&nbsp;&nbsp;<input type="radio" value="PM" name="display_by" id="productiondisplay_by" <?php if($post_data['chkmediadisplay_by'] == 'PM') {?>checked="checked" <?php }?> onclick="showHideMBy(this.value);" /> <label for="productiondisplay_by">Production</label></li>
		</ul>
<div id="tabs-deadlinedetails">
			<div class="tab-inner-fix">    
    <?= $form->field($instruct_model, 'project_name',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(); ?>             
    
    <div class="form-group" >
     <div class='row input-field'>
        <div class='col-md-2'>Project Priority&nbsp;<span class="required-field">*</span></div>
        <div class='col-md-8'>      
                     <?php  
                   echo Select2::widget([
                    'model' => $instruct_model,
                    'attribute' => 'task_priority',
                    'data' => ArrayHelper::map($priorityList, 'id', 'priority'),
                    'options' => ['prompt' => false, 'id' => 'priority', 'class' => 'form-control'],
                    /*'pluginOptions' => [
                      'allowClear' => true
                    ]*/
                    ]); ?>
            </div>
     </div>
    </div>
    <?= $form->field($instruct_model, 'requestor',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->textInput(); ?>        
    <div class="form-group" >
     <div class='row input-field'>
        <div class='col-md-2'>Project Request Type</div>
        <div class='col-md-8'>      
                     <?php  
                   echo Select2::widget([
                    'model' => $instruct_model,
                    'attribute' => 'task_projectreqtype',
                    'data' => array('internal','external'),
                    'options' => ['prompt' => false, 'id' => 'id', 'class' => 'form-control'],
                   /* 'pluginOptions' => [
                      'allowClear' => true
                    ]*/
                    ]); ?>
            </div>
     </div>
    </div>
    </div>
    <div class="button-set text-right">
        <?= $form->field($model, 'id')->hiddenInput()->label(false); ?>
	<?= Html::button('Cancel', ['class' =>  'btn btn-primary','onclick'=>'location.href="index.php?r=case-projects/index&case_id='.$case_id.'";','title'=>'Cancel']) ?>
        <?= Html::submitButton($model->isNewRecord ? 'Add' : 'Update', ['class' =>'btn btn-primary','title'=>$model->isNewRecord ? 'Add' : 'Update']) ?>        
    </div>
</div>
<div id="tabs-attachmedia">
<div class="mycontainer">
<?php	
foreach($case_productions as $key => $val) 
{
?>	
<div class="myheader"><a href="javascript:void(0);">Production #<?= $val->id ?> - Received <?= $val->prod_rec_date ?> - Production By <?= $val->prod_party ?></a><div class="pull-right header-checkbox"><input type="checkbox" id="chk_<?= $val->id ?>" value="<?= $val->id ?>" onclick="toggleCheckboxes('<?= $val->id ?>', this);" aria-label="Select Production #<?= $val->id ?>" /><label for="chk_<?= $val->id ?>">&nbsp;</label></div></div>

<div class="content">
    
    <table class="table table-striped table-hover">
		<thead>
             <tr>
               <th class="text-left"><a href="#" title="Media #">Media #</a></th>
	       <th class="text-left"><a href="#" title="On Hold">On Hold</a></th>
               <th class="text-left"><a href="#" title="Media Type">Media Type</a></th>
               <th class="text-left"><a href="#" title="Media Desc/Custodian">Media Desc/Custodian</a></th>
               <th class="text-left"><a href="#" title="Quantity">Quantity</a></th>
               <th class="text-left"><a href="#" title="Est Size">Est Size</a></th>
	       <th>&nbsp;</th>
             </tr>
       </thead>
       <tbody>
       	<?php foreach ($val->productionmedia as $mediaModel){?>	
       		<tr>
				<td align="left"><?= $mediaModel->evid_id;?></td>
				<td align="left"><?php if($mediaModel->on_hold==1) { ?><em title="On Hold" class="fa fa-check text-danger"></em><?php } else{?>&nbsp;<?php }?></td>
				<td align="left"><?=$mediaModel->proevidence->evidencetype->evidence_name; ?></td>
				<td align="left"><?=$mediaModel->proevidence->evid_desc?></td>
				<td align="center"><?=$mediaModel->proevidence->quantity?></td>
				<td align="left">
				<?php 
				 if (isset($mediaModel->proevidence->contents_total_size) && ($mediaModel->proevidence->contents_total_size != 0 || $mediaModel->proevidence->contents_total_size != "")) {
					echo $mediaModel->proevidence->contents_total_size . ' ' . $mediaModel->proevidence->evidenceunit->unit_name;
				} else {
                	echo $mediaModel->proevidence->contents_total_size_comp . ' ' . $mediaModel->proevidence->evidencecompunit->unit_name;
                }?>
				</td>
                                <td><div class="pull-right header-checkbox"><input type="checkbox" id="mediachk_<?= $mediaModel->id ?>" value="<?= $mediaModel->id ?>" class="chk_<?= $val->id ?>" onclick="toggleCheckboxes('<?= $mediaModel->id ?>');" aria-label="Select Media" /><label for="mediachk_<?= $mediaModel->id ?>"><span class="sr-only">Select Media</span></label></div></td>
			</tr>
			<?php foreach ($mediaModel->proevidence->evidencecontent as $contentMediaModel){ if ($contentMediaModel->evid_num_id!=$mediaModel->proevidence->id){continue;}?>
			<tr>
				<td align="left"><?= Html::img('@web/images/join-line.png',['alt'=>'line']);?> <?=$contentMediaModel->id; ?></td>
				<td>&nbsp;</td>
				<td><?=$contentMediaModel->datatype->data_type; ?></td>
				<td align="left"><?php if (isset($contentMediaModel->cust_id)){echo $contentMediaModel->evidenceCustodians->cust_lname .' '.$contentMediaModel->evidenceCustodians->cust_fname,' ,'.$contentMediaModel->evidenceCustodians->cust_mi;} ?> </td>
				<td>&nbsp;</td>
				<td><?php if (isset($contentMediaModel->data_size)) { echo $contentMediaModel->data_size . ' ' . $contentMediaModel->dataunit->unit_name; } ?></td>
				<td>&nbsp;</td>
           </tr>
		   <?php } ?>
       	<?php }?>
       </tbody>
    </table>
</div>
<?php }?>
</div>

</div>
<div id="tabs-selectworkflow"></div>
<div id="tabs-formdetails"></div>
</div>
<div class="button-set text-right">
        <?= $form->field($model, 'id')->hiddenInput()->label(false); ?>
	<?= Html::button('Cancel', ['class' =>  'btn btn-primary','onclick'=>'location.href="index.php?r=case-projects/index&case_id='.$case_id.'";','title'=>'Cancel']) ?>
        <?= Html::submitButton($model->isNewRecord ? 'Add' : 'Update', ['class' =>'btn btn-primary','title'=>$model->isNewRecord ? 'Add' : 'Update']) ?>        
    </div>
</fieldset>

    <?php ActiveForm::end(); ?>
    </div>
</div>

<script>
$(function() {

    $(".myheader a").click(function () {
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
		$('.myheader').on('click',function(){
			if($(this).hasClass('myheader-selected-tab')){
				$(this).removeClass('myheader-selected-tab');
			}else{
				$(this).addClass('myheader-selected-tab');
			}	
		});

    $( "#wf-tabs" ).tabs({
      beforeActivate: function (event, ui) {
    	    /*if(ui.newPanel.attr('id') == 'tabs-attachmedia'){
    	    	jQuery.ajax({
 			       url: baseUrl +'/case-projects/attachmedia',
 			       data:{team_id: 1},
 			       type: 'post',
 			       beforeSend:function (data) {showLoader();},
 			       success: function (data) {
 			       	 hideLoader();
 			       	 $('#tabs-attachmedia').html(data);
 			       }
 			  });
        	}
    	    if(ui.newPanel.attr('id') == 'tabs-selectworkflow'){
    	    	jQuery.ajax({
 			       url: baseUrl +'/case-projects/selectworkflow',
 			       data:{team_id: 1},
 			       type: 'get',
 			       beforeSend:function (data) {showLoader();},
 			       success: function (data) {
 			       	 hideLoader();
 			       	 $('#tabs-selectworkflow').html(data);
 			       }
 			  });
        	}
        	if(ui.newPanel.attr('id') == 'tabs-formdetails'){
    	    	jQuery.ajax({
 			       url: baseUrl +'/case-projects/formdetails',
 			       data:{team_id: 1},
 			       type: 'post',
 			       beforeSend:function (data) {showLoader();},
 			       success: function (data) {
 			       	 hideLoader();
 			       	 $('#tabs-formdetails').html('<div id="form_div">'+data+'</div>');
 			       }
 			  });
        	}*/
      },
      beforeLoad: function( event, ui ) {
        ui.jqXHR.error(function() {
          ui.panel.html(
            "Error loading current tab." );
        });
      }
    });
});
    function validateproject()
    {
        $('div.help-block').remove();
        $('div.has-error').removeClass('has-error');
        if($('#priority').val() == ''){
            if($(this).parent().find("div.help-block").length==0)
            {
                $('#priority').parent().append('<div class="help-block">Project Priority cannot be blank.</div>');
                $('#priority').parent().parent().parent().addClass('has-error');
            }  
            return false;
        }
        return true;
    }
    $('#priority').change(function() {
        if($(this).val()!="")
        {
            if($(this).parent().find("div.help-block").length>0)
            {
                $(this).parent().find("div.help-block").remove();
                $(this).parent().parent().parent().removeClass("has-error");
            }
        }  
        else
        {
            if($(this).parent().find("div.help-block").length==0)
            {
                $(this).parent().append('<div class="help-block">Project Priority cannot be blank.</div>');
                $(this).parent().parent().parent().addClass('has-error');
            }
        }
    });
    function toggleCheckboxes(prod_id, obj) {
	$('.chk_'+prod_id).prop('checked',obj.checked); if(obj.checked){ $('#chk_'+prod_id).next().addClass('checked'); $('.chk_'+prod_id).each(function(){ $(this).next().addClass('checked');});}else{$('#chk_'+prod_id).next().removeClass('checked');$('.chk_'+prod_id).each(function(){ $(this).next().removeClass('checked');});}
    }	
</script>
<noscript></noscript>
