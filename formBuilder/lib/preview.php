<?php
	//include('formbuilder.php');
	include '../formBuilder/formbuilder.php';

	//echo "<pre>";
	//print_r($_POST);
	//die("</pre>");
	
	$items = $fb->build($_POST);
			
			//echo "<pre>";
			//print_r($items);
			//echo "</pre>";
	if ($items):
?>
<div style="height:auto !important; max-height:350px !important;overflow-x: hidden;overflow-y: auto;">
<form class="fancy" id="fancy_perview" name="fancy" action="#" onsubmit="return validate(this);" autocomplete="off">
	<fieldset>
		<legend>Form Preview</legend>
		<ol>
			<?php foreach($items as $item): 
					
		//	echo "<pre>";
		//	print_r($item);
		//	echo "</pre>";
		
			?>
			<li>
			<?php 
			if(isset($item['label']) && !empty($item['label'])){
				
			?>
				
				<label ><?php echo $item['label'];?></label>
			<?php 
			}else { 
				if($item['type'] != 'textarea' && $item['type'] != 'text'){
			?>
			<label ><?php echo 'No Label';?></label>
			
			<?php } }
			if(isset($item['text_val'])){
				if($item['type']=='text'){
			?>
			<div  style="margin-left:0px!important;">
			<?php  $contents=html_entity_decode(urldecode($item['text_val']));
				   $contents = str_replace(",","",$contents);
				   echo $contents;
			?>
			</div>
			<?php }else{?> 
			<div class='block'>
			<?php  echo $item['html'];?>
			</div>
			
			<?php } 
			}else{?>
			<div class='block'>
			<?php  echo $item['html'];?>
			</div>
			
			<?php }?>
			</li>
		  <?php endforeach; ?>
		</ol>
	</fieldset>
	<!-- <div style="float:right"><input type="Submit" name="Submit" class="button" value="Submit"></div> -->
</form>
	
<?php else: ?>
	<div class='warning'>No elements in form!</div>
<?php endif;?>
</div>

<script type="text/javascript">
function validate()
{

	var err='';
	var msg='';
	var data = $('#fancy_perview').find('*:input');
	for(i=0;i<(data.length-1);i++)
	{
			if($(data[i]).attr('class')!='')
			{
					if($(data[i]).is('.required'))
					{
						if($(data[i]).val()=="")
						{
							msg=$(data[i]).parents().find('label').html()+' Field is Required';
							alert(msg);							
							data[i].focus();
							data[i].style.border='1px solid red';
							err++;
							//return false;
						}
						else
						{
							data[i].style.border='1px solid #889CA6';
						}
					}
					if(data[i].type=='select-one')
					{
						//alert(data[i].value);
						if($(data[i]).val()=="")
						{
							msg=$(data[i]).parents().find('label').html()+' Field is Required';
							alert(msg);
							data[i].focus();
						//	data[i].style.border='1px solid red';
							err++;
							//return false;
						}
						else
						{
							//data[i].style.border='1px solid #889CA6';
						}
					}
					if($(data[i]).is('.required{email}'))
					{
						if($(data[i]).val()=="")
						{
							data[i].focus();
							data[i].style.border='1px solid red';
							err++;
						}
						else
						{
							var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
							if(!emailReg.test(data[i].value))
							{
								$('#'+data[i].name).html('Invalid Email');
								data[i].focus();
								data[i].style.border='1px solid red';
								err++;
							}
							else
							{
								data[i].style.border='1px solid #889CA6';
							}	
						}
					}
					if($(data[i]).is('.required{number}'))
					{
						if($(data[i]).val()=="")
						{
							
							data[i].focus();
							data[i].style.border='1px solid red';
							err++;
							//return false;
						}
						else
						{
								patroon = /[0-9]+/
								if(!patroon.test(data[i].value))
								{
								$('#'+data[i].name).html('Allow Number Only');
								data[i].focus();
								data[i].style.border='1px solid red';
								err++;
								}
								else
								{
									data[i].style.border='1px solid #889CA6';
								}
						}
					}
			}
	}	

	$('#fancy_perview').find(':input').each(function(){
		  //alert($(this).attr('type'));
		  if($(this).prop("tagName")=='SELECT')
		  {
			  if($(this).attr('class')!= undefined)
			  {
				  if($(this).attr('class')=='required')
				  {
					  if($(this).val()==0)
					  {
						  var options = this.getElementsByTagName("option");
						  var optionHTML = options[this.selectedIndex].innerHTML;  
						  if(optionHTML=='Please Select')
						  {
							  msg=$(this).parents().find('label').html()+' Field is Required';
							  this.style.borderColor = 'red';
							  alert(msg);
						  }
						  else
						  {
							  this.style.borderColor = '#889CA6';
						  }
					  }
					  else
					  {
						  this.style.borderColor = '#889CA6';
					  }
				  }
			  }
		  }
		});
	return false;
}
</script>
<noscript></noscript>