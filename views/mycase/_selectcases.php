<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\grid\GridView;
use kartik\grid\datetimepicker;
use yii\web\JsExpression;
use app\models\User;
use app\models\Options;
use app\models\EvidenceCustodians;
use app\models\Servicetask;
?>
<div class="mycontainer">
    <?php 
	if(!empty($client_data))
	{
	    foreach ($client_data as $key=>$val) 
	    { 	
		?>
		<div class="myheader" id="myheader_<?php echo $val['id'];?>">
		    <a href="javascript:void(0);" id="<?php echo $val['id']?>"><?php echo $val['client_name'];?></a>
		    <div class="pull-right header-checkbox">
			<input <?php if($val['id'] == $client_id){ echo "checked=checked"; } ?> type="checkbox" aria-label="<?php echo $val['client_name'];?>" id="client_<?=$val['id'] ?>" name="client[<?=$val['id'] ?>]" onclick="doToggleClientCases('<?=$val['id'] ?>',this.checked);" value="<?php echo $val['id']; ?>" class="chk_clients" /> 
			<label for="client_<?=$val['id'] ?>">&nbsp;</label> 
		    </div>
		</div>    
		<div class="content li_cases custom-inline-block-width" id="inner_case_details_<?php echo $val['id']?>"></div>  
   <?php     }
	} ?>
</div>
<script>
    function get_cases_details(client_id, type, ischecked)
    {
	  	    
	    if($('#inner_case_details_'+client_id+' ul li').length == 0){
		    
		    
			    $.ajax({
				    type: "POST",
				    url: httpPath+"mycase/getcaselistbyclient",
			data: {"client_id":client_id,"case_ids":"<?php echo $case_ids;?>"},
			dataType:'html',
				    cache: false,
			    success:function(data){
					 //   $('#inner_case_details_'+client_id).show();	
					    $('#inner_case_details_'+client_id).html(data);
					    $('input').customInput();
					    
					    $('.content').not('#inner_case_details_'+client_id).hide();
					   
					   // $header = $('#'+client_id).parent();
					   // $content = $header.next();
					    $('#inner_case_details_'+client_id).slideToggle(500, function () {
						
					    });
					    
					    if(type=='checkbox') {
		    
						$('.chk_'+client_id).prop('checked', ischecked); 

						if(ischecked){ 
						    $('.chk_clients').not('#client_'+client_id).prop('checked', false);
						    $('.chk_clients').not('#client_'+client_id).each(function() { 
							$(this).next().removeClass('checked'); 
							$(this).parent().parent().parent().next().find('label').removeClass('checked'); 
						    });
						    
						    $('.chk_'+client_id).each(function(){ 
							$(this).next().addClass('checked');
						    });
						}else{
						    
						    $('.chk_'+client_id).each(function(){ 
							$(this).next().removeClass('checked');
						    });
						}
					    }
									
					    
					    
					    
				    },
				    beforeSend:function(){
					    //$('#inner_case_details_'+client_id).show();	
				    }
			    });		
		   
	    } else {
		  //  $('#inner_case_details_'+client_id).show();
		  $('.content').not('#inner_case_details_'+client_id).hide();
		  if(type=='checkbox')
		  {
		      var isOpen = $('#inner_case_details_'+client_id).is(':visible');
		      if(!isOpen)
			$('#inner_case_details_'+client_id).slideToggle(500, function () {});
		  }
		  else
		  {
		    $('#inner_case_details_'+client_id).slideToggle(500, function () {});
		  } 
		if(type=='checkbox') {
		    
		    $('.chk_'+client_id).prop('checked', ischecked); 

		    if(ischecked){ 
			$('.chk_clients').not('#client_'+client_id).prop('checked', false);
			$('.chk_clients').not('#client_'+client_id).each(function() { 
			    $(this).next().removeClass('checked'); 
			    $(this).parent().parent().parent().next().find('label').removeClass('checked'); 
			});
			
			$('.chk_'+client_id).each(function(){ 
			    $(this).next().addClass('checked');
			});
		    }else{
			
			$('.chk_'+client_id).each(function(){ 
			    $(this).next().removeClass('checked');
			});
		    }
		}			    
	    }
	    
    }
    <?php if(isset($client_id) && $client_id!=0) {?>
    get_cases_details('<?php echo $client_id;?>');
    <?php } ?>
    
    $('.myheader').on('click',function(){
	$('.myheader').removeClass('myheader-selected-tab');
	/*if($(this).hasClass('myheader-selected-tab')){
		$(this).removeClass('myheader-selected-tab');
	} else {*/
		$(this).addClass('myheader-selected-tab');
	//}	
});

$(".myheader a").click(function () {
    
    get_cases_details($(this).attr("id"));
});
function doToggleClientCases(client_id, ischecked)
{
    //$(this).parent().parent().prev().trigger('click'); 
    get_cases_details(client_id, 'checkbox', ischecked);
    
}
function hideotherclientcheckboxes(client_id, case_id, ischecked) {
    if(ischecked) {
	$('.chk_clients').not('#client'+client_id).prop('checked',false);
	
	$('.chk_clients').not('#client'+client_id).each(function(){ 
	    $(this).next().removeClass('checked');
	});
	$('.case_checkbox').not('.chk_'+client_id).prop('checked',false);
	$('.case_checkbox').not('.chk_'+client_id).each(function(){ 
	    $(this).next().removeClass('checked');
	});
    }	
}
</script>
<noscript></noscript>
