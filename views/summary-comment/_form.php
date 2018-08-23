<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SummaryComment */
/* @var $form yii\widgets\ActiveForm */

$this->registerJsFile(Yii::$app->request->baseUrl.'/js/jquery.form.min.js',['depends' => [\yii\web\JqueryAsset::className()],'position'=>\yii\web\View::POS_HEAD]);
$this->registerJsFile(Yii::$app->request->baseUrl.'/js/jquery.MultiFile.js',['depends' => [\yii\web\JqueryAsset::className()],'position'=>\yii\web\View::POS_HEAD]);
\app\assets\SystemCustomWordingAsset::register($this);
$js = <<<JS
// get the form id and set the event
$(function() {
	$('.multi-pt').MultiFile({
		STRING: { 
		  remove:'<em class="fa fa-close text-danger" title="Remove"></em>', 
		},
		maxsize:102400	
	});
});
JS;
$this->registerJs($js);
?>
<div class="summary-comment-form">
   <?php $form = ActiveForm::begin(['id'=>$model->formName(),'options'=>['enctype'=>'multipart/form-data']]); ?>
    <!-- <div class="sub-heading">Add Comment</div>-->
			 <div class="comments-area">
			 	<div class="x_panel custom-wording-editer">
                            <div class="x_content">
								<label class="sr-only" for="summarycomment-comment" style="padding:0;height:0px;mergin:0px;">Comment</label>
								<?php 
									if(!$model->isNewRecord){
										$model->comment=htmlspecialchars($model->comment);
									}
								?>
								<?= $form->field($model, 'comment',['inputOptions'=>['style'=>"display:block;width:100%;"],'template' => "<div class='col-md-0'>{label}</div><div class='col-md-12' style='padding:0px;'>{input}\n{hint}\n{error}</div>",'labelOptions'=>['class'=>'form_label']])->textArea(['rows'=>2,'placeholder'=>"Leave a comment..."])->label(false);?>
							</div>
				</div>				
			 	<div class="editor-attached">
				 	<div class="col-sm-12">
				 		<div class="col-sm-7">
				  	 		<?= $form->field($model, 'attachment[]',['template' => "<div class='row input-field'><div class='col-md-2'>{label}</div><div class='col-md-10'>{input}<div id='comments-attachment_list' class='MultiFile-list text-left'><small>Tip: File size cannot exceed 100 MB.</small></div>\n{hint}\n{error}</div></div>",'labelOptions'=>['class'=>'form_label']])->fileInput(['multiple' => true,'id'=>uniqid(),'class'=>'multi-pt','title'=>'Choose File']) ?>
							   <?php
								$attachment_array['T'.$model->Id]='T'.$model->Id.'-list';
								if (!empty($model->attachments)) {
					               foreach ($model->attachments as $filename) {
					               ?>
					               <div class="MultiFile-label selected-file-list text-left attach_<?php echo $model->Id?>" id="attach_<?php echo $filename->id; ?>">
					                   <a href="#T7" class="MultiFile-remove" onclick="edit_remove_image('<?php echo $filename->id; ?>', this,'<?php echo $model->Id?>');" title="Delete attachment"><em class="fa fa-close text-danger"></em></a>
					                   <span title="File selected: " class="MultiFile-title">
					                       <?php echo $filename->fname;?>
					                   </span>
					               </div>
					               <?php
					                   } 
					               }
					               ?>  
								<input type="hidden" name="remove_name_<?php echo $model->Id?>" id="remove_name_<?php echo $model->Id?>" />
				  	 	</div>
				  	 	<div class="col-sm-5 text-right">
				  			 <div class="recepients right"></div>
				  	 	</div>
				   </div>
			   </div>
               <div class="shared">
                    <div id="shared_team_name"></div>
                    <input type="hidden" name="shared_team" id="shared_team" value="">
                </div>
             </div>
   <?php ActiveForm::end(); ?>
</div>
<script>
$(function () {
	$('#summarycomment-comment').jqte({source: false});
});
function edit_remove_image(id,obj,comment_id){
	removed = $("#remove_name_"+comment_id).val();
	if(removed == ""){
	  removed = id;
	}else{
	  removed = removed + ','+ id;
	}
	$("#remove_name_"+comment_id).val(removed);
	$("#attach_"+id).hide();
}
</script>
<noscript>
</noscript>