<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\web\JsExpression;
use kartik\widgets\Select2;
use kartik\widgets\Typeahead;
use app\models\EvidenceProduction;
$prod_ids=array();
$evid_ids=array();
$evid_content_ids=array();
if(!empty($modelInstruct->taskInstructEvidences)){
	foreach ($modelInstruct->taskInstructEvidences as $task_instruct_evid){
		$prod_ids[$task_instruct_evid->prod_id]=$task_instruct_evid->prod_id;
		$evid_ids[$task_instruct_evid->evidence_id]=$task_instruct_evid->evidence_id;
		$evid_content_ids[$task_instruct_evid->evidence_contents_id]=$task_instruct_evid->evidence_contents_id;
	}
}?>
<div>
<div id="info" class="hide" >Loading more production(s)...</div>
<?php 
$model = new EvidenceProduction();
$form = ActiveForm::begin(['id' => $model->formName(), 'enableAjaxValidation' => false, 'enableClientValidation' => false]); ?>
<div class="col-sm-5"><?=$form->field($model, 'id',['template' => "<div class='row input-field'><div class='col-md-5'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['label'=>'Search Production','class'=>'form_label']])->widget(Select2::classname(), [
		'options' => ['prompt' => 'Search Production','nolabel'=>true],
		'pluginOptions' => [
				//'allowClear' => true,
				'dropdownParent' => new JsExpression('$(".field-evidenceproduction-id")'),
				'ajax' => [
						'url' =>  Url::toRoute(['project/search-production', 'case_id' => $case_id]),
						'dataType' => 'json',
						'data' => new JsExpression('function(params) { return {q:params.term}; }')
				],
		]]);?></div>
<div class="col-sm-5">
<?php 
$req=[];
if($model->isAttributeRequired('staff_assigned')){
	$req=['aria-required'=>'true'];
}?>
<?=$form->field($model, 'staff_assigned',['template' => "<div class='row input-field'><div class='col-md-5'>{label}</div><div class='col-md-7'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['label'=>'Search Produced By','class'=>'form_label']])->widget(Select2::classname(), [
		'options' => array_merge(['prompt' => 'Search Produced By','nolabel'=>true],$req),
		'pluginOptions' => [
				//'allowClear' => true,
				'dropdownParent' => new JsExpression('$(".field-evidenceproduction-staff_assigned")'),
				'ajax' => [
								            'url' =>  Url::toRoute(['project/search-production-by', 'case_id' => $case_id]),
								            'dataType' => 'json',
								            'data' => new JsExpression('function(params) { return {q:params.term}; }')
								        ],
		]]);?></div>
<div class="col-sm-2"> <?= Html::button('Search', ['title'=>'Search Production','class' => 'btn btn-primary','onclick'=>'searchProduction('.$case_id.');']) ?>
<?php ActiveForm::end(); ?>
</div>

</div>
<input type="hidden" id="production_offset" value="0" />
<input type="hidden" id="nomore_production" value="0" />
<div id="production_view" class="mycontainer media_view_modal" style="overflow-x:hidden;">
    <fieldset>
<legend class="sr-only">Add Production(s) to the Project</legend>
<?php	
if(!empty($case_productions)){?>
<?php foreach($case_productions as $key => $val) 
{
	if(!empty($val->productionmedia)) 
	{
?>	
<div class="myheader">
<a href="javascript:void(0);">Production #<?= $val->id ?> - Received <?= date("m/d/Y",strtotime($val->prod_rec_date)) ?>
<div class="my-head-th-title">Production By <?= $val->prod_party ?></div></a>
<div class="pull-right header-checkbox">
    <input type="checkbox" id="chk_<?= $val->id ?>" value="<?= $val->id ?>" onclick="checkProdChild(<?php echo $val->id;?>);" class="parent_<?php echo $val->id;?> Production prod_<?= $val->id ?>" name="Production[<?= $val->id ?>]" <?php if(in_array($val->id,$prod_ids)){?> checked="checked" <?php }?> aria-label="Select all Media & contents of Production # <?= $val->id ?>"/><label for="chk_<?= $val->id ?>" class="form_label"><span class="sr-only">Select all Media & contents of Production #<?= $val->id ?></span></label>
</div></div>

<div class="content" style="padding:0px;">
    
    <table class="table table-striped table-hover attach-prodution-projectprocess">
		<thead>
             <tr>
               <th class="text-left media-td"><a href="#" title="Media #">Media #</a></th>
	       	   <th class="text-left onhold-td"><a href="#" title="On Hold?">On Hold?</a></th>
               <th class="text-left media-type-td"><a href="#" title="Media Type">Media Type</a></th>
               <th class="text-left media-dir-cus-td"><a href="#" title="Media Description / Custodian">Media Description / Custodian</a></th>
               <th class="text-center quantity-td"><a href="#" title="Quantity">Quantity</a></th>
               <th class="text-left est-size-td"><a href="#" title="Est Size">Est Size</a></th>
               <th>&nbsp;<span class="sr-only">Select Media</span></th>
             </tr>
       </thead>
       <tbody id="child_<?php echo $val->id;?>">
       	<?php 
       	if(!empty($val->productionmedia)) {
       	foreach ($val->productionmedia as $mediaModel){ if(in_array($val->id.'_'.$mediaModel->evid_id,explode(",",$attach_production_media))){ continue;} if(in_array($mediaModel->proevidence->status,array(3,5))) {continue;}?>	
       		<fieldset>
                    <legend class="sr-only">Add Production(s) to the Project, Select all contents of Media #<?=$mediaModel->evid_id?> for production #<?= $val->id ?></legend><tr>
				<td align="left"><?= $mediaModel->evid_id;?></td>
				<td align="left"><?php if($mediaModel->on_hold==1) { ?><em class="fa fa-check text-danger"></em><?php } else{?>&nbsp;<?php }?></td>
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
				<td class="pull-right last-td">
					<input type="checkbox" id="mediachk_<?= $val->id ?>_<?= $mediaModel->evid_id ?>" value="<?= $mediaModel->evid_id ?>" rel="<?= $val->id ?>" <?php if(in_array($mediaModel->evid_id,$evid_ids)){?> checked="checked" <?php }?> class="child_<?php echo $val->id;?> child1_<?php echo $mediaModel->evid_id?> prod_<?=$val->id?>_<?= $mediaModel->evid_id?> media" name="Production[<?=$val->id?>][<?= $mediaModel->evid_id?>]" onclick="checkParent(<?php echo $val->id;?>);checkChildCont(<?php echo $val->id;?>,<?php echo $mediaModel->evid_id;?>);" aria-label="Select all contents of Media #<?=$mediaModel->evid_id?> for production #<?= $val->id ?>" />
					<label for="mediachk_<?= $val->id ?>_<?= $mediaModel->evid_id ?>"><span class="sr-only">Select all contents of Media #<?=$mediaModel->evid_id?> for Production #<?= $val->id ?></span></label>
				</td>
                    </tr></fieldset>
			
			<?php foreach ($mediaModel->proevidence->evidencecontent as $contentMediaModel){ if ($contentMediaModel->evid_num_id!=$mediaModel->proevidence->id){continue;}
			if(in_array($val->id.'_'.$mediaModel->evid_id.'_'.$contentMediaModel->id,explode(",",$attach_production_media_content))){ continue;}
			?>
                    <fieldset>
                    <legend class="sr-only">Add Production(s) to the Project, Select Content #<?=$contentMediaModel->id;?> of Media #<?= $mediaModel->evid_id ?> for Production #<?= $val->id ?></legend>
			<tr class="child_conts_<?php echo $mediaModel->evid_id;?>" >
				<td align="left"><?= Html::img('@web/images/join-line.png',['alt'=>'line']);?> <?=$contentMediaModel->id; ?></td>
				<td>&nbsp;</td>
				<td><?=$contentMediaModel->datatype->data_type; ?></td>
				<td align="left"><?php if (isset($contentMediaModel->cust_id)){echo $contentMediaModel->evidenceCustodians->cust_lname .' '.$contentMediaModel->evidenceCustodians->cust_fname,' ,'.$contentMediaModel->evidenceCustodians->cust_mi;} ?> </td>
				<td>&nbsp;</td>
				<td><?php if (isset($contentMediaModel->data_size)) { echo $contentMediaModel->data_size . ' ' . $contentMediaModel->dataunit->unit_name; } ?></td>
				<td class="pull-right last-td">
                                    <input rel="<?= $val->id ?>_<?= $mediaModel->evid_id ?>" class="child_cont_<?php echo $mediaModel->evid_id;?> prod_<?=$val->id?>_<?= $mediaModel->evid_id ?>_<?=$contentMediaModel->id?> Evidence_contents " style="left: 7px;" type="checkbox" name="Production[<?=$val->id?>][<?=$mediaModel->evid_id?>][<?=$contentMediaModel->id?>]" id="prod_evidence_content_<?=$val->id.'_'.$mediaModel->evid_id.'_'.$contentMediaModel->id;?>" value="<?=$contentMediaModel->id;?>" <?php if(in_array($contentMediaModel->id,$evid_content_ids)){?> checked="checked" <?php }?> onclick="selectedParentForProd(<?php echo $val->id; ?>,<?php echo $mediaModel->evid_id;?>);" aria-label="Select Content #<?=$contentMediaModel->id;?> of Media #<?= $mediaModel->evid_id ?> for Production #<?= $val->id ?>">
                                    <label for="prod_evidence_content_<?=$val->id.'_'.$mediaModel->evid_id.'_'.$contentMediaModel->id;?>"><span class="sr-only">Select Content #<?=$contentMediaModel->id;?> of Media #<?= $mediaModel->evid_id ?> for Production #<?= $val->id ?></span></label>
				</td>
                        </tr></fieldset>
		   <?php } ?>
		<?php }
		}	
		else
		{
		?>
                    		<tr>
    					<td colspan="7" class="text-left">No records found.</td>
				</tr>
		<?php
	}?>
       </tbody>
    </table>
</div>
<?php  } } }else{?>
<div>There is no Production media associated with this case.</div>
<?php }?>
    </fieldset>
</div>
<script>

$("#production_view").find(".myheader a").click(function () {
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
$("#production_view").find('.myheader').on('click',function(){
	if($(this).hasClass('myheader-selected-tab')){
		$(this).removeClass('myheader-selected-tab');
	}else{
		$(this).addClass('myheader-selected-tab');
	}	
});
function checkProdChild(parent_id){
	 if($("#production_view .parent_"+parent_id).is(':checked')){ 
		 $('#production_view #child_'+parent_id+' input[type="checkbox"]').each(function() {
				 $(this).prop('checked',true);
				 $(this).next('label').addClass('checked');
				// console.log($(this).attr('checked'));
		 });
	 }
	 if($("#production_view .parent_"+parent_id).is(":not(':checked')")){
		$('#production_view #child_'+parent_id+' input[type="checkbox"]').each(function() {
				$(this).prop('checked',false);
				$(this).next('label').removeClass('checked');
			//	console.log($(this).attr('checked'));
		});
	}
}
//this function is written to check parent Id
function checkParent(parent_id){
		console.log($(".child_"+parent_id).is(':checked'));
		 if($(".child_"+parent_id).is(':checked')){ 
			$('#production_view .parent_'+parent_id).attr('checked',true);
			$('#production_view .parent_'+parent_id).next('label').addClass('checked');
		 }else{ 
		 	if ($('.child_'+parent_id+' input:checked').length == 0){  
		 		 $('#production_view .parent_'+parent_id).attr('checked',false);
		 		 $('#production_view .parent_'+parent_id).next('label').removeClass('checked');
		 	}
		 }
}
function checkChildCont(prod_id,parent_id){
	if($("#child_"+prod_id+" .child1_"+parent_id).is(':checked')){
		$("#child_"+prod_id+" .child_cont_"+parent_id).attr("checked",true);
		$("#child_"+prod_id+" .child_cont_"+parent_id).next('label').addClass('checked');
	}else{
		$("#child_"+prod_id+" .child_cont_"+parent_id).attr("checked",false);
		$("#child_"+prod_id+" .child_cont_"+parent_id).next('label').removeClass('checked');
	}
}
function selectedParentForProd(prod_id,parent_id){
	if($("#child_"+prod_id+ " .child_cont_"+parent_id).is(':checked')){
		$("#child_"+prod_id+" .child1_"+parent_id).attr("checked",true);
		$("#child_"+prod_id+" .child1_"+parent_id).next('label').addClass('checked');
		checkParent(prod_id);
	}else{
		if($('#production_view .parent_'+prod_id+' .child_conts_'+parent_id+' .Evidence_contents').is(':checked')){
		} else {
			$("#child_"+prod_id+" .child1_"+parent_id).attr("checked",false);
			$("#child_"+prod_id+" .child1_"+parent_id).next('label').removeClass('checked');
		}
		checkParent(prod_id);
	}
}
function toggleCheckboxes(id,obj){
	if($(obj).is(':checked')){
		$(".child"+id).attr("checked",true);
		$(".child"+id).next('label').addClass('checked');
	}else{
		$(".child"+id).attr("checked",false);
		$(".child"+id).next('label').removeClass('checked');		
	}
}
function searchProduction(case_id){
	var prod_id = $('#evidenceproduction-id').val();
	var prod_by = $('#evidenceproduction-staff_assigned').val();
	if(prod_id!="" || prod_by!=""){
		$.ajax({
	        url: httpPath + "project/get-searchproduction&case_id="+case_id,
	        data:{prod_id:prod_id,prod_by:prod_by,'attach_production':$('#attach_production').val(),'attach_media':$('#attach_production_media').val(),'attach_media_content':$('#attach_production_media_content').val()},
	        cache: false,
	        dataType: 'html',
	        beforeSend:function (data) {showLoader();},
	        success: function (data) {
	        	hideLoader();
	            if (data != "") {
	                $('#attach-production-popup').find('#production_view').html(data);
	                $( "#attach-production-popup input" ).customInput();
	                $("#production_view").find(".myheader a").click(function () {
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
	                $("#production_view").find('.myheader').on('click',function(){
	                	if($(this).hasClass('myheader-selected-tab')){
	                		$(this).removeClass('myheader-selected-tab');
	                	}else{
	                		$(this).addClass('myheader-selected-tab');
	                	}	
	                });
	            }
            }
            });
	}else{
		var offset = parseInt($("#production_offset").val());
		 $.ajax({
	        url: httpPath + "project/get-searchproduction&case_id="+case_id,
	        data:{offset:offset,'attach_production':$('#attach_production').val(),'attach_media':$('#attach_production_media').val(),'attach_media_content':$('#attach_production_media_content').val()},
	        cache: false,
	        dataType: 'html',
	        beforeSend:function (data) {showLoader();},
	        success: function (data) {
	        	hideLoader();
	            if (data != "") {
	                $('#attach-production-popup').find('#production_view').html(data);
	                $( "#attach-production-popup input" ).customInput();
	                $("#production_view").find(".myheader a").click(function () {
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
	                $("#production_view").find('.myheader').on('click',function(){
	                	if($(this).hasClass('myheader-selected-tab')){
	                		$(this).removeClass('myheader-selected-tab');
	                	}else{
	                		$(this).addClass('myheader-selected-tab');
	                	}	
	                });
	            }
            }
            }); 
	}
}
$('#production_view').on('scroll',function(){
	var obj=this;
	if( obj.scrollTop == (obj.scrollHeight - (obj.offsetHeight))) {
		//console.log('kithi var undar aata hai...');
		yHandler('<?php echo $case_id?>');
	}
});
function yHandler(case_id){
	var prod_id = $('#evidenceproduction-id').val();
	var prod_by = $('#evidenceproduction-staff_assigned').val();
	//$('#media_view').off('scroll');
	var nomore_production = parseInt($('#nomore_production').val());
	if(nomore_production == 0){
	$("#info").removeClass('hide');
	var plusval=100;
	$("#production_offset").val(parseInt($("#production_offset").val())+100);
	var offset = parseInt($("#production_offset").val());
	$.ajax({
        url: httpPath + "project/get-searchproduction&case_id="+case_id,
        data:{offset:offset,prod_id:prod_id,prod_by:prod_by,'attach_production':$('#attach_production').val(),'attach_media':$('#attach_production_media').val(),'attach_media_content':$('#attach_production_media_content').val()},
        cache: false,
        async:true,
        dataType: 'html',
        beforeSend:function (data) {showLoader();},
        success: function (data) {
        	hideLoader();
        	$("#info").addClass('hide');
            if (data != "") {
                $('#attach-production-popup').find('#production_view').append(data);
                $( "#attach-production-popup input" ).customInput();
                $("#production_view").find(".myheader a").unbind('click').bind('click',function () {
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
                $("#production_view").find('.myheader').unbind('click').bind('click',function(){
                	if($(this).hasClass('myheader-selected-tab')){
                		$(this).removeClass('myheader-selected-tab');
                	}else{
                		$(this).addClass('myheader-selected-tab');
                	}	
                });
            }else{
                $('#nomore_production').val(1);
            }
            
        }
        });
    }
}
</script>
<noscript></noscript>
