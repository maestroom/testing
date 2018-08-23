<?php
use yii\helpers\Html;
use app\models\User;
use app\models\FormBuilder;
use app\components\IsataskFormFlag;
$media_id=array();
\app\assets\SystemAsset::register($this);
//echo '<pre>',print_r($pricepoints);die;

$this->registerJsFile(Yii::$app->request->baseUrl.'/js/jquery.form.min.js');
$this->registerJsFile(Yii::$app->request->baseUrl.'/js/jquery.MultiFile.js');
$js = <<<JS
// get the form id and set the event
$('document').ready(function(){
	$('.T7').each(function(key) {
		var media_id = $(this).attr('data-mediaid');
		$('#T'+media_id).MultiFile({
		  list: '#T'+media_id+'-list',
		  STRING: {
			  remove:'<em class="fa fa-close text-danger" title="Remove"></em>',
		  },
		  maxsize:102400
		 });
	});
		
	$('.T8').each(function(key) {
		var media_id = $(this).attr('data-mediaid');
		$('#T'+media_id).MultiFile({
		  list: '#T'+media_id+'-list',
		  STRING: {
			  remove:'<em class="fa fa-close text-danger"></em>',
		  },
		  maxsize:102400
		});
	});
});

/* Remove Image */		
function remove_image(id,obj){
	removed = $("#remove_attachments").val();
	if(removed == ""){
	  removed = id;
	}else{
	  removed = removed + ','+ id;
	}	
	$("#remove_attachments").val(removed);
	$(obj).parent().remove();
}
JS;
$this->registerJs($js);
?>
<div class="create-form task-details-popup form-builder-ol" id="billing_data_div">
	<form name="frm_billing_data_div" id="frm_billing_data_div" method="post" enctype="multipart/form-data" autocomplete="off">
	<?= IsataskFormFlag::widget(); // change flag ?>
    	<?php if(!empty($medias['media'])){
    		foreach ($medias['media'] as $mediaModel){ $media_id[$mediaModel->id]=$mediaModel->id; ?>
    			<h2 class="head-title">Media # <?= $mediaModel->id?></h3>
    			<?php 
    				if(!empty($pricepoints) && $is_allow_security_adddata_bill) {
    			?>
    			<h3 class="head-title">Billing Items</h3>
    			 <div class="table-responsive">
			        <table class="table">
			          <thead>
			            <tr>
			              <th width="15%"><strong>Price Point</strong></th>
			              <th width="14%"><strong>Quantity</strong></th>
			              <th width="12%"><strong>Bill Date</strong></th>
			              <th width="30%"><strong>Custom Description</strong></th>
			              <th width="8%" class="text-center"><strong>Non-Billable</strong></th>
			            </tr>
			          </thead>
			          <tbody>
			          	<?php foreach($pricepoints as $pricepoint){ ?>
			            <tr>	
			              <td class="word-break"><?= $pricepoint['price_point'] ?></td>
			              <td class="word-break quantity-break">
							<input type="hidden" name="priceVal[<?=$mediaModel->id?>][<?=$pricepoint['id']?>][evid_num_id]" value="<?=$mediaModel->id?>">
							<input type="hidden" name="priceVal[<?=$mediaModel->id?>][<?=$pricepoint['id']?>][princing_id]" value="<?=$pricepoint['id']?>">
							<label for="priceVal_<?=$pricepoint['id']?>">&nbsp;</label>
							<input type="text" aria-label="Quantity for Price point <?= $pricepoint['price_point'] ?> for media #<?=$mediaModel->id?>" class="form-control input-small billing_units numeric-field-qu negative-key" name="priceVal[<?=$mediaModel->id?>][<?=$pricepoint['id']?>][quantity]" id="priceVal_<?=$pricepoint['id']?>" class="quantityClass" style="width:80px;">
								<span>
									<?php
										if ($listunitType[$pricepoint['unit_price_id']] != "") {
											echo "# " . $listunitType[$pricepoint['unit_price_id']];
										} else {
											echo "";
										}
									?>
								</span>
							</td>
							<td>
								<div class="input-group calender-group">
									<label for="bill_date_<?= $mediaModel->id ?>_<?= $pricepoint['id'] ?>"></label>
									<input aria-label="Bill Date for Price point <?= $pricepoint['price_point'] ?> for media #<?=$mediaModel->id?>" type="text" readonly="readonly" onclick="$(this).next('span').find('a').trigger('click');" maxlength="10" value="<?php echo date("m/d/Y"); ?>" placeholder="Select Bill Date" id="bill_date_<?=$mediaModel->id?>_<?=$pricepoint['id']?>" name="priceVal[<?=$mediaModel->id?>][<?=$pricepoint['id']?>][bill_date]" class="billing_dates form-control">
								</div>  
							</td>
			              <td>
			              <?php if ($pricepoint['is_custom'] == 1) { ?> 
                          	  <textarea rows="" aria-label="Custom Description for Price point <?= $pricepoint['price_point'] ?> for media #<?=$mediaModel->id?>" cols="" class="form-control" name="priceVal[<?= $mediaModel->id ?>][<?= $pricepoint['id'] ?>][desc]"><?php echo $pricepoint['cust_desc_template']; ?></textarea>
	                          <?php
	                          	} else {
	                          		echo "&nbsp";
	                          	}
	                          ?>
			              </td>
			              <td style="text-align:center;">
			              	<input aria-label="Non-Billable for Price point <?= $pricepoint['price_point'] ?> for media #<?=$mediaModel->id?>" type="checkbox" onclick="" value="<?php echo $pkey; ?>" id="todo_items_<?= $mediaModel->id ?>_<?=$pricepoint['id']?>" name="priceVal[<?=$mediaModel->id?>][<?=$pricepoint['id']?>][bill_todo_items]" rel="pricing_<?=$pricepoint['id']?>" class="todo_items">
			              	<label for="todo_items_<?=$mediaModel->id?>_<?= $pricepoint['id'] ?>" title="Select">&nbsp;</label>
			              </td>
			            </tr>
			            <?php }?>
			          </tbody>
			        </table>
			      </div>
    			<?php } ?>
    			
    			<?php if(!empty($formbuilder_data) && $is_allow_security_data_stat){ ?>
    			<h3 class="head-title">Task Outcome Items <!--Data Statistics--></h3>
    				<ol class="form-builder-task form-builder-ol<?= $mediaModel->id ?>">
						<li>
							<div class="row">
								<fieldset>
									<div class="col-sm-3"><label id="T<?= $mediaModel->id ?>-lbl" for="T<?= $mediaModel->id ?>">Attachment: </label></div>	
									<div class="col-sm-7"><span>Tip: File size cannot exceed 100 MB.</span>
										<input type="file"  aria-labelledby="T<?= $mediaModel->id ?>-lbl" name="TasksUnitsBilling[attachment][]" class="T7" id="T<?= $mediaModel->id ?>" title="Choose File" data-mediaid="<?= $mediaModel->id  ?>" multiple="multiple" />
										<div id='T<?= $mediaModel->id ?>-list' class="T7-list"></div>
									</div>
								</fieldset>
							</div>
						</li>
					</ol>
    				
	    			<div style="display:none">
		    		<!-- <form method="POST" id="custodian-edit<?php // $mediaModel->id?>" style=""> -->	
	    			<div id="custodian-edit<?= $mediaModel->id?>"> 
		    			<?php 
							foreach ($formbuilder_data as $ele_id=>$fdata) {
								if(($fdata['remove']==1 && $formValues[$fdata['form_builder_id']]!='') || $fdata['remove']==0) {
						?>
						<input type="hidden" name="<?=$ele_id?>[id]" value="<?=$ele_id?>">
						<input type="hidden" name="mediaId[]" value="<?=$mediaModel->id?>">
						<input type="hidden" name="<?=$ele_id?>[type]" value="<?=$fdata['type'] ?>">
						<input type="hidden" name="<?=$ele_id?>[label]" value="<?=Html::encode($fdata['label'])?>">
						<input type="hidden" name="<?=$ele_id?>[value]" value="<?php if(isset($formValues[$fdata['form_builder_id']])){echo Html::encode($formValues[$fdata['form_builder_id']]);} else {echo Html::encode($fdata['value']);}?>">
						<input type="hidden" name="<?=$ele_id?>[values]" value="<?php echo Html::encode($fdata['values']);?>">
						<input type="hidden" name="<?=$ele_id?>[values_ids]" value="<?=Html::encode($fdata['values_option_ids'])?>">
						<input type="hidden" name="<?=$ele_id?>[description]" value="<?=Html::encode($fdata['description'])?>">
						<input type="hidden" name="<?=$ele_id?>[required]" value="<?=$fdata['required'] ?>">
						<input type="hidden" name="<?=$ele_id?>[order]" value="<?=$fdata['order'] ?>">
						<input type="hidden" name="<?=$ele_id?>[text_val]" value="<?=Html::encode($fdata['text_val'])?>">
						<input type="hidden" name="<?=$ele_id?>[sync_prod]" value="<?=$fdata['sync_prod'] ?>">
						<!--<input type="hidden" name="<?php //$ele_id ?>[field_type]" value="<?php //$fdata['field_type'] ?>">-->
						<input type="hidden" name="<?=$ele_id?>[default_answer]" value="<?=$fdata['default_answer'] ?>">
						<input type="hidden" name="<?=$ele_id?>[default_unit]" value="<?=$fdata['default_unit'] ?>">
						<?php /*if(($fdata['type'] == 'checkbox') && isset($formValues[$fdata['form_builder_id']])) {?>
						<input type="hidden" name="<?=$ele_id?>[optionchk]" value="<?=$formValues[$fdata['form_builder_id']];?>">
						
						<?php }elseif(($fdata['type'] == 'dropdown') && isset($formValues[$fdata['form_builder_id']])) {
						$values=explode(";",$fdata['values']);
						$values = array_combine(range(1, count($values)), $values);
						?>
						<input type="hidden" name="<?=$ele_id?>[optionchk]" value="<?=$values[$formValues[$fdata['form_builder_id']]];?>">
						<?php }else{*/
						if(($fdata['type'] == 'checkbox' || $fdata['type'] == 'radio' || $fdata['type'] == 'dropdown')){
							$selected_value=array();
							$all_values =  explode(";",$fdata['values_option_ids']);
							$selected_options = (new FormBuilder)->getDefaultElementOption($fdata['form_builder_id']);
						?>
						<input type="hidden" name="<?=$ele_id?>[optionchk]" value="<?= implode(",",$selected_options); ?>">
						<?php
						} else {
						?>
						<input type="hidden" name="<?=$ele_id?>[optionchk]" value="<?=$fdata['optionchk'] ?>">
						<?php } ?>
						<input type="hidden" name="<?=$ele_id?>[qareportuse]" value="<?=$fdata['qareportuse'] ?>">
						<input type="hidden" name="<?=$ele_id?>[edit]" value="1">
						<?php }} ?>
					</div>
					<!-- </form> -->
					</div>
				<?php } 
				/* Start : Get All data from prodbates media wise */
				if(!empty($medias['evidprodbates'][$mediaModel->id])) {
					$evidprodbates = $medias['evidprodbates'][$mediaModel->id];
				?>
					<input type="hidden" class="media_field" name="mediaid" value="<?php echo $mediaModel->id; ?>">
					<input type="hidden" class="prod_bates_field" name="priceVal[<?php echo $mediaModel->id; ?>][epbid]" value="<?php echo $evidprodbates->id; ?>">
					<input type="hidden" class="prod_bates_field" name="priceVal[<?php echo $mediaModel->id; ?>][epbbegbate]" value="<?php echo $evidprodbates->prod_bbates; ?>">
					<input type="hidden" class="prod_bates_field" name="priceVal[<?php echo $mediaModel->id; ?>][epbendbate]" value="<?php echo $evidprodbates->prod_ebates; ?>">
					<input type="hidden" class="prod_bates_field" name="priceVal[<?php echo $mediaModel->id; ?>][epbvol]" value="<?php echo $evidprodbates->prod_vol; ?>">
				<?php 
   				}
   					/* End : Get All data from prodbates media wise */
    		 }
    	}else { // biiling data without media?>
            <?php if(!empty($pricepoints) && $is_allow_security_adddata_bill) { ?>
    			<h3 class="head-title">Billing Items</h3>
    			 <div class="table-responsive">
			        <table class="table">
			          <thead>
			            <tr>
			              <th width="15%"><strong>Price Point</strong></th>
			              <th width="14%"><strong>Quantity</strong></th>
			              <th width="12%"><strong>Bill Date</strong></th>
			              <th width="30%"><strong>Custom Description</strong></th>
			              <th width="8%" class="text-center"><strong>Non-Billable</strong></th>
			            </tr>
			          </thead>
			          <tbody>
			          	<?php foreach ($pricepoints as $pricepoint){ ?>
			            <tr>
			              <td class="word-break"><?= $pricepoint['price_point'] ?></td>
			              <td class="word-break quantity-break">
			              <input type="hidden" name="priceVal[0][<?=$pricepoint['id']?>][evid_num_id]" value="<?=$mediaModel->id?>">
						  <input type="hidden" name="priceVal[0][<?=$pricepoint['id']?>][princing_id]" value="<?=$pricepoint['id']?>">
						  <label for="priceVal_<?=$pricepoint['id']?>">&nbsp;</label>
                          <input type="text" aria-label="Quantity for Price point <?= $pricepoint['price_point'] ?>" class="form-control input-small billing_units numeric-field-qu negative-key" name="priceVal[0][<?=$pricepoint['id']?>][quantity]" id="priceVal_<?=$pricepoint['id']?>" class="quantityClass" style="width:80px;">
                          <span>
                                    <?php
                                    if ($listunitType[$pricepoint['unit_price_id']] != "") {
                                        echo "# " . $listunitType[$pricepoint['unit_price_id']];
                                    } else {
                                        echo "";
                                    }
                                    ?></span>
			              </td>
			              <td>
							<div class="input-group calender-group">
								<label for="bill_date_<?=$pricepoint['id']?>"></label>
								<input type="text" aria-label="Bill Date for Price point <?= $pricepoint['price_point'] ?>" readonly="readonly" onclick="$(this).next('span').find('a').trigger('click');" maxlength="10" value="<?php echo date("m/d/Y"); ?>" placeholder="Select Bill Date" id="bill_date_<?=$pricepoint['id']?>" name="priceVal[0][<?=$pricepoint['id']?>][bill_date]" class="billing_dates form-control">
							</div>
			              </td>
			              <td>
			              <?php if ($pricepoint['is_custom'] == 1) { ?> 
								<textarea rows="" aria-label="Custom Description for Price point <?= $pricepoint['price_point'] ?>" cols="" class="form-control" name="priceVal[0][<?=$pricepoint['id']?>][desc]"><?php echo $pricepoint['cust_desc_template']; ?></textarea>
								<?php
							} else {
								echo "&nbsp";
							}
							?>
			              </td>
			              
			              <td style="text-align:center;">
			              	<input type="checkbox" aria-label="Non-Billable for Price point <?= $pricepoint['price_point'] ?>" onclick="" value="<?php echo $pkey; ?>" id="todo_items<?=$pricepoint['id']?>" name="priceVal[0][<?=$pricepoint['id']?>][bill_todo_items]" rel="pricing_<?=$pricepoint['id']?>" class="todo_items">
			              	<label for="todo_items<?=$pricepoint['id']?>" title="Select">&nbsp;</label>
			              </td>
			            </tr>
			            <?php }?>
			          </tbody>
			        </table>
			      </div>
    			<?php } ?>
    			<?php 
    			if(!empty($formbuilder_data) && $is_allow_security_data_stat){?>
    			<h3 class="head-title">Task Outcome Items <!--Data Statistics--></h3>
				<ol class="form-builder-task form-builder-ol0">
					<li>
    					<div class="row">
							<fieldset>
								<div class="col-sm-3"><label id="T<?= $mediaModel->id ?>-lbl" for="T<?= $mediaModel->id ?>">Attachment: </label></div>	
								<div class="col-sm-7"><span>Tip: File size cannot exceed 100 MB.</span>
									<input aria-labelledby="T<?= $mediaModel->id ?>-lbl" type="file" name="TasksUnitsBilling[attachment][]" title="Choose File" class="T8" id="T<?= $mediaModel->id ?>" data-mediaid="<?= $mediaModel->id  ?>" multiple="multiple" />
									<div id='T<?= $mediaModel->id ?>-list'></div>
								</div>
							</fieldset>
						</div>
					</li>
				</ol>
    			<div style="display: none">
    			<!-- <form method="POST" id="custodian-edit0" style=""> -->
    			<div id="custodian-edit0">
				<?php foreach ($formbuilder_data as $ele_id=>$fdata) {
				if(($fdata['remove']==1 && $formValues[$fdata['form_builder_id']]!='') || $fdata['remove']==0){
				?>
				<input type="hidden" name="<?=$ele_id?>[id]" value="<?=$ele_id?>">
				<input type="hidden" name="mediaId[]" value="0">
				<input type="hidden" name="<?=$ele_id?>[type]" value="<?=$fdata['type'] ?>">
				<input type="hidden" name="<?=$ele_id?>[label]" value="<?=Html::encode($fdata['label'])?>">
				<input type="hidden" name="<?=$ele_id?>[value]" value="<?php if(isset($formValues[$fdata['form_builder_id']])){echo Html::encode($formValues[$fdata['form_builder_id']]);} else {echo Html::encode($fdata['value']);}?>">
				<input type="hidden" name="<?=$ele_id?>[values]" value="<?php echo Html::encode($fdata['values']);?>">
				<input type="hidden" name="<?=$ele_id?>[values_ids]" value="<?=Html::encode($fdata['values_option_ids'])?>">
				<input type="hidden" name="<?=$ele_id?>[description]" value="<?=Html::encode($fdata['description'])?>">
				<input type="hidden" name="<?=$ele_id?>[required]" value="<?=$fdata['required'] ?>">
				<input type="hidden" name="<?=$ele_id?>[order]" value="<?=$fdata['order'] ?>">
				<input type="hidden" name="<?=$ele_id?>[text_val]" value="<?=Html::encode($fdata['text_val'])?>">
				<input type="hidden" name="<?=$ele_id?>[sync_prod]" value="<?=$fdata['sync_prod'] ?>">
				<!--<input type="hidden" name="<?php //$ele_id ?>[field_type]" value="<?php //$fdata['field_type'] ?>">-->
				<input type="hidden" name="<?=$ele_id?>[default_answer]" value="<?=$fdata['default_answer'] ?>">
				<input type="hidden" name="<?=$ele_id?>[default_unit]" value="<?=$fdata['default_unit'] ?>">
				<?php /* if(($fdata['type'] == 'checkbox') && isset($formValues[$fdata['form_builder_id']])) {?>
				<input type="hidden" name="<?=$ele_id?>[optionchk]" value="<?=$formValues[$fdata['form_builder_id']];?>">
				
				<?php }elseif(($fdata['type'] == 'dropdown') && isset($formValues[$fdata['form_builder_id']])) {
				$values=explode(";",$fdata['values']);
				$values = array_combine(range(1, count($values)), $values);
				?>
				<input type="hidden" name="<?=$ele_id?>[optionchk]" value="<?=$values[$formValues[$fdata['form_builder_id']]];?>">
				<?php }else{?>
				<input type="hidden" name="<?=$ele_id?>[optionchk]" value="<?=$fdata['optionchk'] ?>">
				<?php } */ 
					if(($fdata['type'] == 'checkbox' || $fdata['type'] == 'radio' || $fdata['type'] == 'dropdown')){
						$selected_value=array();
						$all_values =  explode(";",$fdata['values_option_ids']);
						$selected_options = (new FormBuilder)->getDefaultElementOption($fdata['form_builder_id']);
					?>
					<input type="hidden" name="<?=$ele_id?>[optionchk]" value="<?= implode(",",$selected_options); ?>">
					<?php
						} else {
					?>
					<input type="hidden" name="<?=$ele_id?>[optionchk]" value="<?=$fdata['optionchk'] ?>">
						<?php }	?>
					<input type="hidden" name="<?=$ele_id?>[qareportuse]" value="<?=$fdata['qareportuse'] ?>">
					<input type="hidden" name="<?=$ele_id?>[edit]" value="1">
					<?php }
					} ?>
				<!-- </form> -->
				</div>
				</div>
				
				
			<?php } ?>
    	<?php }?>
    <input type="hidden" name="evid_num_id" value="<?php echo !empty($media_id) ? implode(",",$media_id) : 0; ?>" >
    </form>
</div>
<script type="text/javascript">

	
/* ChangeFlag */
$('input').bind('input', function(){
	$("#billing_data_div #is_change_form").val('1');
	$("#is_change_form_main").val('1');
});
jQuery("select").on("change",function(){
	$("#billing_data_div #is_change_form").val('1');
	$("#is_change_form_main").val('1');
});
jQuery(":checkbox").change(function(){
	$("#billing_data_div #is_change_form").val('1');
	$("#is_change_form_main").val('1');
});
jQuery(":radio").change(function(){
	$("#billing_data_div #is_change_form").val('1');
	$("#is_change_form_main").val('1');
});	
jQuery("textarea").bind("input",function(){
	$("#billing_data_div #is_change_form").val('1');
	$("#is_change_form_main").val('1');
});	
jQuery(document).ready(function($) {
        $('#active_form_name').val('frm_billing_data_div');
	<?php if(!empty($media_id) &&  $is_allow_security_data_stat) { foreach ($media_id as $id ) {?>
	
	Admin.formbuilder.init();
    var Url = Admin.formbuilder.BASEURL+'?action=element_display_bulk_billing';
		
  	$.ajax({
		url: Url,
		type:"post",
		data: $('#custodian-edit<?=$id?> :input').serialize(),
		cache: false,
		dataType:'json',
		success:function(result){
			$.each(result,function(key,val){
				var into = $(".form-builder-ol<?=$id?>");
				$(into).prepend(val);
				var $newrow = $(into).find('li:first');
				Admin.formbuilder.properties($newrow);
				Admin.formbuilder.layout($newrow);
				Admin.formbuilder.attr.update($newrow);
				//show
				$newrow.hide().slideDown('slow');
			});
			delete result;
		},
		complete:function(){
			$('#custodian-edit<?=$id?>').remove();
			$('.datepickers').each(function(e){
				var datepicker_id = $(this).attr('id');
				var formElements={};
				formElements[datepicker_id] = "%m/%d/%Y";
				datePickerController.createDatePicker({
					formElements: formElements,
					callbackFunctions:{
						 "datereturned":[changeflag]
					}
				});	
			});
		}
	});
	<?php }} else{
		if($is_allow_security_data_stat){
		?>
    Admin.formbuilder.init();
    var Url = Admin.formbuilder.BASEURL+'?action=element_display_bulk_billing';
	
	var into = $(".form-builder-ol0");
	$.ajax({
		url: Url,
		type:"post",
		data:$('#custodian-edit0 :input').serialize(),
		cache: false,
		dataType:'json',
		success:function(result){
			$.each(result,function(key,val){
				$(into).prepend(val);
				var $newrow = $(into).find('li:first');
				Admin.formbuilder.properties($newrow);
				Admin.formbuilder.layout($newrow);
				Admin.formbuilder.attr.update($newrow);
				//show
				$newrow.hide().slideDown('slow');
			});
			delete result;
		},
		complete:function(){
			$('#custodian-edit0').remove();
			$('.datepickers').each(function(e){
					var datepicker_id = $(this).attr('id');
					var formElements={};
					formElements[datepicker_id] = "%m/%d/%Y";
					datePickerController.createDatePicker({formElements: formElements,callbackFunctions:{
						 "datereturned":[changeflag]
					} 
				});	
			});
			$("input").customInput();
		}
	});
	<?php }
	} ?>
	$('.billing_dates').each(function(e){
		var datepicker_id = $(this).attr('id');
		var formElements={};
		formElements[datepicker_id] = "%m/%d/%Y";
		datePickerController.createDatePicker({formElements:formElements,callbackFunctions:{
				 "datereturned":[changeflag]
			}
		});	
});
	
	
	
	
});</script>
<noscript></noscript>
