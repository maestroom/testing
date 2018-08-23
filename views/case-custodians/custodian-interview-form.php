<?php 
use yii\helpers\Html;
use app\components\IsataskFormFlag;
if(isset($getCustomFromId->formref_id) && $getCustomFromId->formref_id != ''){
	$this->title = "Edit Custodian Interview Form";
}
else{
	$this->title = "Add Custodian Interview Form";
}
?>
<div class="sub-heading"><a href="javascript:void(0);" class="tag-header-black" title="<?= Html::encode($this->title) ?>"><?= Html::encode($this->title) ?></a></div>
<div id="form_div">
    <fieldset class="one-cols-fieldset">
    <div class="create-form">
    <div class="custodian-overview-main">
    	<form id="frminterview-from" method="post" action="<?php echo $_SERVER['REQUEST_URI']?>" autocomplete="off">
    	<?= IsataskFormFlag::widget(); // change flag ?>
				<div class="custodian-form-group ">
					<div class="row input-field">
					<div class="col-md-3">
						<label for="list_cust_form_template_<?php echo $cust->cust_id; ?>" class="form_label">Select Template</label>
					</div>
					<div class="col-md-7">
						<select name="list_cust_form_template_<?php echo $cust->cust_id; ?>" id="list_cust_form_template_<?php echo $cust->cust_id; ?>" class="cust_SelectDropDown form-control" <?php if(isset($getCustomFromId->formref_id)){?> disabled="disabled" <?php }?>>
						<option value="">Choose a Custodian Form</option>
						<?php 
							foreach ($form_list as $key=>$list){
								$selected="";
								if(isset($getCustomFromId->formref_id) && $getCustomFromId->formref_id == $key){
									$selected="selected='selected'";
								}	
								echo '<option value="'.$key.'" '.$selected.'>'.$list.'</option>';
							}
						?>
						</select>
						<input type="hidden" name="form_id" id="form_id" value="<?php if(isset($getCustomFromId->formref_id) && $getCustomFromId->formref_id > 0){ echo $getCustomFromId->formref_id; }?>">
						<div class="help-block"></div>
					</div>
					</div>
				</div>

				<div id="template_loading_<?php echo $cust->cust_id; ?>" class="custodian-template-loading">
					
				</div>
    	</form>
    </div>	
    </div>
    </fieldset>
    <div class="button-set text-right">
    <?= Html::button('Cancel', ['title'=>'Cancel','class' => 'btn btn-primary', 'id' => 'cancel-custodian-interview-form']) ?>
    <?php if(isset($getCustomFromId->formref_id)){?>
    <?= Html::button('Delete', ['title'=>'Delete','class' => 'btn btn-primary','onclick'=>'DeleteInterViewForm('.$cust->cust_id.');']) ?>
    <?php }?>
	<?= Html::button( 'Update', ['title'=> 'Update','class' =>  'btn btn-primary','id'=>'submitInterviewFrom' ]) ?>
	</div>
</div>
<script>
/** Cancel btn event **/
$('#cancel-custodian-interview-form').click(function(){
	location.reload();
});
$(document).ready(function(){
	<?php if(isset($getCustomFromId->formref_id)){ ?>
	$.ajax({
		   url: baseUrl +'/case-custodians/getinterviewformwithvalue&id=<?php echo $getCustomFromId->formref_id?>',
	       type: 'get',
	       data:{cust_id:<?php echo $cust->cust_id; ?>},
	       beforeSend:function (data) {showLoader();},
	       success: function (data) {
	    	   hideLoader();
	    	   jQuery("#template_loading_<?php echo $cust->cust_id; ?>").html(data);
	    	   $('.datepickers').each(function(e){
					var datepicker_id = $(this).attr('id');
					var formElements={};
					formElements[datepicker_id] = "%m/%d/%Y";
					datePickerController.createDatePicker({formElements: formElements,
						callbackFunctions: {
								"datereturned" : [changeflag],
							},
					 });	
				});
				$("#template_loading_<?php echo $cust->cust_id; ?> input").customInput();
				
	       },
	       complete: function(){
			   /* changeFormFlag */
			   jQuery("#template_loading_<?php echo $cust->cust_id; ?>").bind('input',function(){
					$("#frminterview-from #is_change_form").val('1');
					$("#frminterview-from #is_change_form_main").val('1');
				});
				jQuery("#template_loading_<?php echo $cust->cust_id; ?>").change(function(e){
					$("#frminterview-from #is_change_form").val('1');
					$("#frminterview-from #is_change_form_main").val('1');
				});
		   }
	});
	<?php } ?>
	$('.cust_SelectDropDown').on('change',function(){
		if(this.value==''){
			$("#template_loading_<?php echo $cust->cust_id; ?>").html(null);
			$("#form_id").val(null);
		}else{
			$("#form_id").val(this.value);
			$.ajax({
				   url: baseUrl +'/case-custodians/getinterviewform&id='+this.value,
			       type: 'get',
			       beforeSend:function (data) {showLoader();},
			       success: function (data) {
					  
			    	   jQuery("#template_loading_<?php echo $cust->cust_id; ?>").html(data);
			       },
			       complete: function(){
					   /* changeFormFlag */
					   jQuery("#template_loading_<?php echo $cust->cust_id; ?>").bind('input',function(){
							$("#frminterview-from #is_change_form").val('1');
							$("#frminterview-from #is_change_form_main").val('1');
						});
						jQuery("#template_loading_<?php echo $cust->cust_id; ?>").change(function(e){
							$("#frminterview-from #is_change_form").val('1');
							$("#frminterview-from #is_change_form_main").val('1');
						});
				   }
			});
		}
	});
	$("#submitInterviewFrom").on('click',function(){        
		template= $('.cust_SelectDropDown').val();
		if(template == ""){
			alert('Please select a Custodian Form to perform this action.');
		}else{
			if(!validateFormBuilder()){
				var form = $("#frminterview-from");
				jQuery.ajax({
			        url    : form.attr('action'),
			        cache: false,
			        type   : 'post',
			        data   : form.serialize(),
			        beforeSend : function() {
			        	showLoader();
			        },
			        success: function (response){
			        	 hideLoader();
			        	 location.reload();
			        }
			    });
		    }
		}
	});
});
</script>
<noscript></noscript>
