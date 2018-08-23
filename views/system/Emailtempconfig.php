<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

		   <div class="right-main-container slide-open" id="maincontainer">
		   			
			<fieldset class="two-cols-fieldset">
			<div class="administration-main-cols">
			 <div class="administration-lt-cols pull-left">
			 	<button title="Expand/Collapse" id="controlbtn" class="slide-control-btn" onclick="WorkflowToggle();" aria-label="Expand or Collapse">
					<span>&nbsp;</span>
				</button>
			  <ul>
			   <li><a href="#" title="Email Templates" class="admin-main-title"><em title="Email Templates" class="fa fa-folder-open text-danger"></em> Email Templates</a>
			    <div class="manage-admin-left-module-list">
				<ul class="sub-links">
				<?php foreach ($templates as $template){ ?>
					<li class="emailtemp" title="<?= $template['email_name'] ?>" id="emailtemp_<?= $template['id']?>"><?= Html::a('<em title="'.$template['email_name'].'" class="fa fa-envelope text-danger"></em> '.$template['email_name'],'javascript:showEmailTemplateConfiguration('.$template['id'].','.$template['email_sort'].');',['class'=>'']) ?></li>
				<?php } ?>
				</ul>
				</div>
			   </li>
			  </ul>
			 </div>
			 <div class="administration-rt-cols pull-right" id="admin_right">
			 	<!-- This div will load email template -->
			 </div>
			</div>
			</fieldset>
		   </div>

<div id="preview-dialog" title="Preview Email Template">
  <div class="create-form" id="email_preview">
  	
  </div>		
</div>
<div id="field-dialog" title="Select Field Names">
  <div class="create-form" id="fields_content">
  	
  </div>		
</div>

		   
