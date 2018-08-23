<?php
/**
 * View: Index
 * @abstract This view is define for Evidence Index Page
 * @package views
 * @subpackage evidence
 * @author Jayant (mali1545@gmail.com)
 * @copyright � 2011-2012 Inovitech, LLC, All Rights Reserved.
 * @todo Ensure all comment block and methods are complete
 * @version 1.0.0 Initial release
 *
 */
// --------------------------------------------------------------------------------------
// PRODUCTION CODE STARTS HERE
// --------------------------------------------------------------------------------------
//$bUrl=Yii::app()->baseUrl; //base URL
//$tUrl=Yii::app()->theme->baseUrl;
//$actionId=Yii::app()->controller->action->id;// get action Id
//$controllerId=Yii::app()->controller->id; //get controller Id
//$roleId=Yii::app()->user->role_id;
//$uid=Yii::app()->user->id;
//$transType=Yii::app()->params['evid_trans_type'];

?>
<div class="sub-heading"><a href="javascript:void(0);" class="tag-header-black" title="Perform Transaction">Perform Transaction</a></div>
<div id="form_div"  class="two-cols-fieldset">
 <?= $this->render('_frmaddcheckoutin', [
        'model' => $model,
        'transType' => $transType,
        'UserName' => $UserName,
        'listEvidenceTo'=>$listEvidenceTo,
        'listEvidenceLoc'=>$listEvidenceLoc,
        'evidNum'=>$evidNum,
        'model_field_length' => $model_field_length
    ]) ?>
 </div>
 <script type="text/javascript">
	// $('#move_to').hide();
	// $('#spn_Apply_Transaction_to_both').hide();
	// $('#trans_to').hide();
	$('#evidencetransaction-trans_type').change(function(){
            var str_val=$(this).val();
            transTypeHandler(str_val);
		/*if(str_val==4)
			$('#move_to').show();
		else
			$('#move_to').hide();
			
		if(str_val==3)
			$('#spn_Apply_Transaction_to_both').show();
		else
			$('#spn_Apply_Transaction_to_both').hide();
			
		if(str_val==2 || str_val==5)
			$('#trans_to').show();
		else
			$('#trans_to').hide();*/		
	});
	function transTypeHandler(trans_type){
		$.ajax({
			url    : baseUrl+'media/get-evidence-type-fields',
			type   : 'POST',
			data   : {trans_type:trans_type,id:"<?php echo $evidNum; ?>"},
			//data   : "trans_type="+trans_type+"&id="+"<?php echo $evidNum; ?>",
			beforeSend : function()    {
				//$('.btn').attr('disabled','disabled');
			},
			success: function (response){
				//console.log(response);
				//alert(trans_type);
				$("#form_div").html(response);
				/*if(response == 'OK'){
					commonAjax(baseUrl +'/system/form&sysform='+form_id,'admin_main_container');
				}else{
					$('.btn').removeAttr("disabled");
				}*/
			},
			error  : function (){
				console.log('internal server error');
			}
		});
	}

	/* IRT 366 Confirm box added for Destroy Dropdown */
	function validateForms() {
		var trans_type = $('#evidencetransaction-trans_type').val();
		
		var str = true;
		if(trans_type == 3) 
			str = confirm("Are you sure you want to Destroy Media <?=$evidNum?>?  This action is permanent. Destroyed media cannot be transferred or used in new projects.");

		if(str == true) {
                    $.ajax({
                        url:baseUrl+'media/validate-evidence-type-fields',
                        type:'post',
                        data:$("#EvidenceTransaction").serialize(),
                        success:function(response) {
                                if(response.length==0) {
                                        $.ajax({
                                                url		: baseUrl+'media/change-status&id='+'<?= $evidNum ?>',
                                                type   : 'POST',
                                                data	:$("#EvidenceTransaction").serialize(),
                                                beforeSend : function() {
                                                        $('.btn').attr('disabled','disabled');
                                                },
                                                success: function (response){
                                                        console.log(response);						
                                                },
                                                error  : function (){
                                                        console.log('internal server error');
                                                }
                                        });
                                } else {
                                        for (var key in response) {
                                                if(key == 'is_duplicate'){
                                                        $("#evidencetransaction-"+key).parent().parent().find('.help-block').html(response[key]);
                                                        $("#evidencetransaction-"+key).parent().closest('div.form-group').addClass('has-error');
                                                }else{
                                                        $("#evidencetransaction-"+key).parent().find('.help-block').html(response[key]);
                                                        $("#evidencetransaction-"+key).closest('div.form-group').addClass('has-error');
                                                }

                                        }
                                }
                        }
                });
            }
	}	
</script>
<noscript></noscript>	
 
