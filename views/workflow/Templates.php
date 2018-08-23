<?php
/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\Select2;

$this->title = 'Add Template';
?>
<div class="right-main-container slide-open" id="maincontainer">
	<fieldset class="two-cols-fieldset workflow-management">
		<div class="administration-main-cols">
			<div class="administration-lt-cols pull-left">
				<button id="controlbtn" aria-label="Expand or Collapse" title="Expand/Collapse" class="slide-control-btn" onclick="jQuery('#maincontainer').toggleClass('slide-close');">
					<span>&nbsp;</span>
				</button>
				<ul>
					<li><a class="admin-main-title" title="Templates" href="javascript:templateworkflow();" ><em title="Templates" class="fa fa-folder-open text-danger"></em>Templates</a>
						<div class="select-items-dropdown">
							<?php 
								if(!empty($template_list)){ foreach ($template_list as $template_id=>$template_name){
									$template_list[$template_id] = Html::encode($template_name); 
								}}
								echo Select2::widget([
									'name' => 'select_box',
									'attribute' => 'select_box',
									'data' => $template_list,
									'options' => ['prompt' => 'Select Team','class' => 'form-control',"onchange"=>"templateworkselectbox(this.value);"],
									'pluginOptions' => [
									 // 'allowClear' => true
									]
								]);
							?>
						</div>
						<div class="left-dropdown-list">
							<div class="admin-left-module-list">
								<ul class="sub-links" id="tempalte_list">
									 <?php if(!empty($template_list)){ foreach ($template_list as $template_id=>$template_name){ ?>
										<li class="template_lis" data-id="<?=$template_id?>" id="template_<?=$template_id?>">
											<a href="javascript:templateworklists('<?=$template_id?>');" title="<?php echo htmlspecialchars_decode($template_name); ?>"><em title="<?php echo htmlspecialchars_decode($template_name); ?>" class="fa fa-tags text-danger"></em> <?php echo htmlspecialchars_decode($template_name); // Html::encode($template_name); ?></a>
										</li>
									 <?php }
									 } ?>
								</ul>
							</div>
						</div>
					</li>
				</ul>
			</div>
			<div class="administration-rt-cols pull-right" id="admin_right">
				<div class="sub-heading"><a href="javascript:void(0);" title="<?= Html::encode($this->title) ?>" class="tag-header-black"><?= Html::encode($this->title) ?></a></div>
			    <div id="form_div">
			    <?= $this->render('_teamplateform', [
			        'model' => $model,
			        'model_field_length' => $model_field_length
			    ]) ?></div>
			</div>
		</div>
	</fieldset>
</div>
<script type="text/javascript">
 /** Template Work Select Box on change event **/
 function templateworkselectbox(value){
	 var chk_status = checkformstatus("event",'');
	 if(chk_status == true){
		if(value == ''){
			commonAjax(baseUrl +'/workflow/templates','admin_main_container');
		}else{
			commonAjax(baseUrl +'/workflow/edittemplate&id='+value,'admin_right');
		}
	}
 }
 
 /** Header click Template Work flow **/
 function templateworkflow(){
	 var chk_status = checkformstatus("event",'');
	 if(chk_status == true)
		commonAjax(baseUrl +'/workflow/templates','admin_main_container');
 }
 
 /** Template Work lists  **/
 function templateworklists(template_id){
	 var chk_status = checkformstatus("event",'');
	 if(chk_status == true)
		commonAjax(baseUrl +'/workflow/edittemplate&id='+template_id,'admin_right');
 }
 
 var fixHelper = function(e, ui) {
		ui.children().each(function() {
		$(this).width($(this).width());
		});
		return ui;
		};

	$('#nolabel-2').change(function(){
		if(this.value == '0'){
			return false;
		}
	});
	
  $('#nolabel-2').change(function(){
	  jQuery.ajax({
	       url: baseUrl +'/workflow/edittemplate',
	       data:{sort_ids: sorder},
	       type: 'post',
	       success: function (data) {
	    	 /*  if(data != 'OK')
	    		  alert('Error'); */
	       }
	  });
  });
  $("#tempalte_list").sortable({
		helper: fixHelper,
		update: function(e,ui) { 
			var sorder="";
			var sort_arr = new Array();
			$("#tempalte_list > li ").each(function(i){ //new code for sorting
					sort_arr[i]=$(this).data('id');
					if(sorder == "")
						sorder = $(this).data('id');
					else
						sorder = sorder + ','  + $(this).data('id');
			});
			jQuery.ajax({
			       url: baseUrl +'/workflow/sorttempalte',
			       data:{sort_ids: sorder},
			       type: 'post',
			       success: function (data) {
			    	  /* if(data != 'OK')
			    		  alert('Error');*/
			       }
			  });
		}
	}).disableSelection(); 
</script>
<noscript></noscript>
