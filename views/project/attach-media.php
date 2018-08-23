<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use kartik\widgets\Select2;
use kartik\widgets\Typeahead;
use app\models\Evidence;
use yii\web\JsExpression;
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
<div id="info" class="hide" >Loading more media...</div>
<?php 
	$model = new Evidence();
	$form = ActiveForm::begin(['id' => $model->formName(), 'enableAjaxValidation' => false, 'enableClientValidation' => false]); ?>
<div class="col-sm-5"><?php 
echo $form->field($model, 'id',['template' => "<div class='row input-field'><div class='col-md-4'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['label'=>'Search Media','class'=>'form_label']])->widget(Select2::classname(), [
		'options' => ['prompt' => 'Search Media','nolabel'=>true],
		'pluginOptions' => [
				//'allowClear' => true,
				'dropdownParent' => new JsExpression('$(".field-evidence-id")'),
				'ajax' => [
						'url' =>  Url::toRoute(['project/search-media', 'case_id' => $case_id]),
						'dataType' => 'json',
						'data' => new JsExpression('function(params) { return {q:params.term}; }')
				],
		],]);
						/* echo Select2::widget([
									'name' => 'media_id',
									'attribute' => 'media_id',
									'options' => ['prompt' => 'Search Media','class' => 'form-control'],
									'pluginOptions' => [
									  'allowClear' => true,
									  'ajax' => [
								            'url' =>  Url::toRoute(['project/search-media', 'case_id' => $case_id]),
								            'dataType' => 'json',
								            'data' => new JsExpression('function(params) { return {q:params.term}; }')
								        ],
									]
								]); */
					?></div>
            <div class="col-sm-5"><?php 
                echo $form->field($model, 'enctype',['template' => "<div class='row input-field'><div class='col-md-4'>{label}</div><div class='col-md-8'>{input}\n{hint}\n{error}</div></div>",'labelOptions'=>['label'=>'Search Media Type','class'=>'form_label']])->widget(Select2::classname(), [
                    'options' => ['prompt' => 'Search Media Type','nolabel'=>true],
                    'pluginOptions' => [
				//'allowClear' => true,
                    'dropdownParent' => new JsExpression('$(".field-evidence-enctype")'),
                    'ajax' => [
                            'url' =>  Url::toRoute(['project/search-media-type', 'case_id' => $case_id]),
                            'dataType' => 'json',
                            'data' => new JsExpression('function(params) { return {q:params.term}; }')
                        ],
		],]);
                            /* echo Select2::widget([
                                                    'name' => 'media_type',
                                                    'attribute' => 'media_type',
                                                    'options' => ['prompt' => 'Search Media Type','class' => 'form-control'],
                                                    'pluginOptions' => [
                                                      'allowClear' => true,
                                                      'ajax' => [
                                                        'url' =>  Url::toRoute(['project/search-media-type', 'case_id' => $case_id]),
                                                        'dataType' => 'json',
                                                        'data' => new JsExpression('function(params) { return {q:params.term}; }')
                                                    ],
                                                    ]
                                            ]); */
                    ?></div>
<div class="col-sm-2"> <?= Html::button('Search', ['title'=>'Search Media','class' => 'btn btn-primary','onclick'=>'searchMedia('.$case_id.');']) ?>
<?php ActiveForm::end(); ?>
</div>

</div>
<input type="hidden" id="media_offset" value="0" />
<input type="hidden" id="nomore_media" value="0" />
<div id="media_view" class="mycontainer media_view_modal">
<fieldset>
<legend class="sr-only">Add Media to the Project</legend>
<?php if(!empty($case_media['media'])){ ?>
	<?php foreach ($case_media['media'] as $media_model){ ?>
		<div class="myheader">
                    <a href="javascript:void(0);">Media #<?= $media_model->id;?>  - <?=$media_model->evidencetype->evidence_name; ?>
                        <div class="my-head-th-title">Est Size: <?php if (isset($media_model->contents_total_size) && ($media_model->contents_total_size != 0 || $media_model->contents_total_size != "")) {	echo $media_model->contents_total_size . ' ' . $media_model->evidenceunit->unit_name;} else {  	echo $media_model->contents_total_size_comp . ' ' . $media_model->evidencecompunit->unit_name;}?></div></a>
                            <div class="pull-right header-checkbox">
                                <input type="checkbox" id="chk_<?= '_media_'.$media_model->id; ?>" value="<?= $media_model->id ?>" onclick="toggleCheckboxes('<?= '_media_'.$media_model->id ?>', this);" class="parent_<?=$media_model->id;?> media" name="Evidence[]" aria-label="Select all Contents of Media #<?= $media_model->id;?>" /><label for="chk_<?= '_media_'.$media_model->id ?>">&nbsp;<span class="sr-only">Select all Contents of Media #<?= $media_model->id;?></span></label>
                            </div>
                        </div>
                        <div class="content" style="padding:0px;">
                            <table class="table table-striped table-hover">
                            <thead>
                              <tr>
                                 <th class="text-left contents-td"><a href="#" title="Media #">Contents #</a></th>
                                     <th class="text-left custodian-td"><a href="#" title="Custodian">Custodian</a></th>
                                     <th class="text-left data-type-td"><a href="#" title="Data Type">Data Type</a></th>
                                 <th class="text-left data-est-size-td"><a href="#" title="Est Size">Data Type Est Size</a></th>
                                 <th>&nbsp;<span class="sr-only">Select Media</span></th>
                              </tr>
                            </thead>
			<tbody>
                        <?php if(!empty($case_media['media_content'])){ ?>
			<?php 	  
				$row = 0;     
				foreach ($case_media['media_content'] as $media_content){
						if($media_content->evid_num_id == $media_model->id){
							$row = 1;
					?>
                                    <fieldset>
                                    <legend class="sr-only">Add Media to the Project, Select Content #<?=$media_content->id;?> of Media # <?= $media_model->id ?></legend>
							<tr>
							   <td class="text-left"><?= Html::img('@web/images/join-line.png',['alt'=>'line']);?> <?=$media_content->id; ?></td>
						       <td class="text-left"><?php if (isset($media_content->cust_id)){echo $media_content->evidenceCustodians->cust_lname .' '.$media_content->evidenceCustodians->cust_fname,' ,'.$media_content->evidenceCustodians->cust_mi;} ?></td>
						       <td class="text-left"><?=$media_content->datatype->data_type; ?></td>
					           <td class="text-left"><?php if (isset($media_content->data_size)) { echo $media_content->data_size . ' ' .$media_content->evidenceContentUnit->unit_name; } ?></td>
						       <td class="pull-right last-td">
						         	<input class="child_media_<?=$media_model->id;?> Evidence_contents " style="left: 7px;" type="checkbox" name="Evidence_contents[]" id="Evidence_content_<?=$media_content->id;?>" value="<?=$media_model->id.'_'.$media_content->id; ?>" data-value="<?=$media_content->id; ?>"  onclick="selectedParentForMedia(<?=$media_model->id;?>);" aria-label="Select Content #<?=$media_content->id;?> of Media # <?= $media_model->id ?>" >
									<label for="Evidence_content_<?=$media_content->id;?>">&nbsp;<span class="sr-only">Select Content #<?=$media_content->id;?> of Media # <?= $media_model->id ?></span></label>
						       </td>
                                                        </tr></fieldset>
						
				<?php } }
                                } else{ ?>
                                                    <tr>
					<td colspan="5" class="text-left">No records found.</td>
				</tr>
                    
                <?php } ?>
                                </tbody>
				</table>
			</div>
<?php }
}else{?>
<div>There is no media associated with this case.</div>
<?php }?>
</fieldset>
</div>

<script>
$("#media_view").find(".myheader a").click(function () {
    $header = $(this).parent();
    if($header.next().hasClass('content')){
        $content = $header.next();
        $content.slideToggle(500, function () {
            $header.text(function () {
              //  change text based on condition
              //return $content.is(":visible") ? "Collapse" : "Expand";
            });
        });	
    }
});

/**
 * Header span
 */
$("#media_view").find('.myheader').on('click',function(){
	if($(this).hasClass('myheader-selected-tab')){
		$(this).removeClass('myheader-selected-tab');
	}else{
		$(this).addClass('myheader-selected-tab');
	}	
});
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
function searchMedia(case_id){
	var media_id = $('#evidence-id').val();
	var media_type = $('#evidence-enctype').val();
	if(media_id!="" || media_type!=""){
		$.ajax({
	        url: httpPath + "project/get-searchmedia&case_id="+case_id,
	        data:{media_id:media_id,media_type:media_type,'attach_media':$('#attach_media').val(),'attach_media_content':$('#attach_media_content').val()},
	        cache: false,
	        dataType: 'html',
	        beforeSend:function (data) {showLoader();},
	        success: function (data) {
	        	hideLoader();
	            if (data != "") {
	                $('#attach-media-popup').find('#media_view').html(data);
	                $( "#attach-media-popup input" ).customInput();
	                $("#media_view").find(".myheader a").click(function () {
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
	                $("#media_view").find('.myheader').on('click',function(){
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
		 $.ajax({
	        url: httpPath + "project/get-searchmedia&case_id="+case_id,
	        data:{offset:$("#media_offset").val(),'attach_media':$('#attach_media').val(),'attach_media_content':$('#attach_media_content').val()},
	        cache: false,
	        dataType: 'html',
	        beforeSend:function (data) {showLoader();},
	        success: function (data) {
	        	hideLoader();
	            if (data != "") {
	                $('#attach-media-popup').find('#media_view').html(data);
	                $( "#attach-media-popup input" ).customInput();
	                $("#media_view").find(".myheader a").click(function () {
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
	                $("#media_view").find('.myheader').on('click',function(){
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
$('#media_view').on('scroll',function(){
	var obj=this;
	if( obj.scrollTop == (obj.scrollHeight - (obj.offsetHeight))) {
		//console.log('kithi var undar aata hai...');
		yHandler('<?php echo $case_id?>');
	}
});
function yHandler(case_id){
	var media_id = $('#evidence-id').val();
	var media_type = $('#evidence-enctype').val();
	//$('#media_view').off('scroll');
	$("#info").removeClass('hide');
	$("#media_offset").val((parseInt($("#media_offset").val())+100));
	var nomore_media= parseInt($('#nomore_media').val());
	if(nomore_media == 0){
	$.ajax({
        url: httpPath + "project/get-searchmedia&case_id="+case_id,
        data:{offset:(parseInt($("#media_offset").val())),media_id:media_id,media_type:media_type,'attach_media':$('#attach_media').val(),'attach_media_content':$('#attach_media_content').val()},
        cache: false,
        async:true,
        dataType: 'html',
        beforeSend:function (data) {showLoader();},
        success: function (data) {
        	hideLoader();
        	$("#info").addClass('hide');
            if (data != "") {
                $('#attach-media-popup').find('#media_view').append(data);
                $( "#attach-media-popup input" ).customInput();
                $("#media_view").find(".myheader a").unbind('click').bind('click',function () {
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
                $("#media_view").find('.myheader').unbind('click').bind('click',function(){
                	if($(this).hasClass('myheader-selected-tab')){
                		$(this).removeClass('myheader-selected-tab');
                	}else{
                		$(this).addClass('myheader-selected-tab');
                	}	
                });
                //$('#media_view').on('scroll',yHandler('<?php echo $case_id?>'));
            }else{
                $('#nomore_media').val(1);
            }
            
        }
        });
	}
}
function selectedParentForMedia(id){
	if($('.child_media_'+id+':checked').length > 0){
		$('.parent_'+id).attr('checked',true);
		$('.parent_'+id).next('label').addClass('checked');
	}else{
		$('.parent_'+id).attr('checked',false);
		$('.parent_'+id).next('label').removeClass('checked');
	}
}
</script>
<noscript></noscript>
