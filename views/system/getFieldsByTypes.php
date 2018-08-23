<?php
use yii\helpers\Html;
//echo "<pre>",print_r($data),"</pre>;
?>
<div id="show_by_content" class="mycontainer">
	<div class="header-checkbox"> <!-- pull-right -->
		Select All/None
		<input type="checkbox" id="chkselectall" title="Select All/None" name="chkselectall" onclick="all_checkall(this.checked);"/> 
		<label for="chkselectall" class=""><span class="sr-only">Select All/None</span></label> 
	</div>
	
	<?php if(isset($data['instruction_form'])){?>
	<div class="myheader">
            <a href="javascript:void(0);">Instruction Form Fields</a>
            <div class="pull-right header-checkbox">
                <input type="checkbox" aria-label="Instruction Form Fields" id="formbuilder_1" name="formbuilder[]" value="instruction" class="chk formbuilder_1" onClick="inner_checkall(1);" /> 
                <label for="formbuilder_1" class="formbuilder_1">&nbsp;</label> 
            </div>
	</div>
	<div class="content">
		<fieldset>
		<legend class="sr-only">Instruction Form Fields</legend>
		<ul>
			<?php if(isset($data['instruction_form'])){ ?>
			<li><input type="text" placeholder="Search" class="search_showby form-control"></li>
			<?php foreach ($data['instruction_form'] as $key => $field) { ?>
				<li class="other_li"><span><?= Html::encode($field); ?></span>
					<div class="pull-right"> 
						<input type="checkbox" name="form_field[]" id="form_field_1_<?= $key ?>" value="<?= $key ?>" data-id="1" class="inner_chk chk form_field_1" />
						<label for="form_field_1_<?= $key ?>" class="form_field_1" ><span class="sr-only"><?= Html::encode($field); ?></span></label>
					</div>
				</li>
			<?php } ?>
			<?php } ?>
		</ul>
		</fieldset>
	</div>
	<?php } ?>
	<?php if(isset($data['data_form'])){?>
	<div class="myheader">	
		<a href="javascript:void(0);">Task Outcome Form Fields</a>
		<div class="pull-right header-checkbox">
			<input type="checkbox" id="formbuilder_2" name="formbuilder[]" value="data" class="chk formbuilder_2" onClick="inner_checkall(2);" /> 
			<label for="formbuilder_2" class="formbuilder_2"><span class="sr-only">Task Outcome Form Fields</span></label> 
		</div>
	</div>
	<div class="content">
		<fieldset>
		<legend class="sr-only">Task Outcome Form Fields</legend>
		<ul>
			<?php if(isset($data['data_form'])){?>
			<li><input type="text" placeholder="Search" class="search_showby form-control"></li>
			<?php foreach ($data['data_form'] as $key => $field) { ?>
				<li class="other_li"><span><?= Html::encode($field); ?></span>
					<div class="pull-right"> 
						<input type="checkbox" name="form_field[]" id="form_field_2_<?= $key ?>" value="<?= $key ?>" data-id="2" class="inner_chk chk form_field_2" />
						<label for="form_field_2_<?= $key ?>" class="form_field_2" ><span class="sr-only"><?= Html::encode($field); ?></span></label>
					</div>
				</li>
			<?php } ?>
			<?php }?>
		</ul>
		</fieldset>
	</div>
	<?php }  ?>
	<?php if(isset($data['custodian_form'])){ ?>
	<div class="myheader">
            <a href="javascript:void(0);">Custodian Form Fields</a>
            <div class="pull-right header-checkbox">
                <input type="checkbox" id="formbuilder_3" name="formbuilder[]" value="data" class="chk formbuilder_3" onClick="inner_checkall(3);" /> 
                <label for="formbuilder_3" class="formbuilder_3"><span class="sr-only">Custodian Form Fields</span></label> 
            </div>
	</div>
	<div class="content">
		<fieldset>
		<legend class="sr-only">Custodian Form Fields</legend>
		<ul>
		<?php if(isset($data['custodian_form'])){?>
			<li><input type="text" placeholder="Search" class="search_showby form-control"></li>
			<?php foreach ($data['custodian_form'] as $key => $field) { ?>
				<li class="other_li"><span><?= Html::encode($field); ?></span>
					<div class="pull-right"> 
						<input type="checkbox" name="form_field[]" id="form_field_3_<?= $key ?>" value="<?= $key ?>" data-id="3" class="inner_chk chk form_field_3" />
						<label for="form_field_3_<?= $key ?>" class="form_field_3" ><span class="sr-only"><?= Html::encode($field); ?></span></label>
					</div>
				</li>
			<?php } ?>
		<?php }?>
		</ul>
		</fieldset>
	</div>
	<?php } ?>
</div>
<script>
$(function() {
	$('#show_by_content input').customInput();
	$('.search_showby').on('keyup',function(){
		var search = this.value.toLowerCase();
		var ul=$(this).parent().closest('ul');
		 jQuery(ul).find("li.other_li").each(function () {
			 var search_span = (jQuery(this).find('span').html().toLowerCase());
			 if(search_span != undefined){
				if (search_span.search(new RegExp(search, "i")) < 0) {
					jQuery(this).hide();
				} else {
					jQuery(this).show();
				}
			}
		});
	});
});
function inner_checkall(loop){
	if($('#formbuilder_'+loop).is(':checked')){
		$('.form_field_'+loop).prop('checked',true);
		$('.form_field_'+loop).attr('checked',true);
		$('.form_field_'+loop).addClass('checked');
	}else{
		$('.form_field_'+loop).prop('checked',false);
		$('.form_field_'+loop).attr('checked',false);
		$('.form_field_'+loop).removeClass('checked');
	}
	//alert($('.chk:checked').length);
	if($('.chk:checked').length==0){
		$('#chkselectall').prop('checked',false);
		$('#chkselectall').attr('checked',false);
		$('#chkselectall').next('label').removeClass('checked');
	}
	if($('.chk:checked').length==$('.chk').length){
		$('#chkselectall').prop('checked',true);
		$('#chkselectall').attr('checked',true);
		$('#chkselectall').next('label').addClass('checked');
	}
}
function all_checkall(stat){
	if(stat){
		$('.chk').each(function(){
			$(this).prop('checked',true);
			$(this).attr('checked',true);
			$(this).next('label').addClass('checked');
		});
	} else {
		$('.chk').each(function(){
			$(this).prop('checked',false);
			$(this).attr('checked',false);
			$(this).next('label').removeClass('checked');
		});
	}
}
$('#show_by_content .myheader').on('click',function(){
	if($(this).hasClass('myheader-selected-tab')){
		$(this).removeClass('myheader-selected-tab');
	} else {
		$(this).addClass('myheader-selected-tab');
	}	
});

$("#show_by_content .myheader a").click(function () {
    $header = $(this).parent();
    $content = $header.next();
    $content.slideToggle(500);
});
$('.inner_chk').on('click',function(){
	id=$(this).data('id');
	if($('.form_field_'+id+':checked').length==0){
		$('#formbuilder_'+id).prop('checked',false);
		$('#formbuilder_'+id).attr('checked',false);
		$('#formbuilder_'+id).next('label').removeClass('checked');
	}if($('.form_field_'+id+':checked').length>0){
		$('#formbuilder_'+id).prop('checked',true);
		$('#formbuilder_'+id).attr('checked',false);
		$('#formbuilder_'+id).next('label').addClass('checked');
	}
	
	if($('.chk:checked').length==0){
		$('#chkselectall').prop('checked',false);
		$('#chkselectall').attr('checked',false);
		$('#chkselectall').next('label').removeClass('checked');
	}
	if($('.chk:checked').length==$('.chk').length){
		$('#chkselectall').prop('checked',true);
		$('#chkselectall').attr('checked',true);
		$('#chkselectall').next('label').addClass('checked');
	}
});
</script>
<noscript></noscript>  
