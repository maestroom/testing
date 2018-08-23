<?php
use yii\helpers\Html;
//echo "<pre>",print_r($data),"</pre>";
?>
<div id="show_by_content" class="mycontainer">
	<div class="pull-right header-checkbox">
		Select All<input type="checkbox" id="chkselectall" name="chkselectall" onclick="all_checkall(this.checked);"/> 
		<label for="chkselectall" class="">&nbsp;</label> 
	</div>
	
	<?php if(isset($fieldcalculationselectList)){?>
	<div class="myheader">
		<a href="javascript:void(0);">Calculation Fields</a>
		<div class="pull-right header-checkbox">
			<input type="checkbox" id="formbuilder_1" name="formbuilder[]" value="instruction" class="chk formbuilder_1" onClick="inner_checkall(1);" /> 
			<label for="formbuilder_1" class="formbuilder_1">&nbsp;</label> 
		</div>
	</div>
	<div class="content">
		<ul>
		<?php if(isset($fieldcalculationselectList)){ ?>
			<li><input type="text" placeholder="Search" class="search_showby form-control"></li>
			<?php foreach ($fieldcalculationselectList as $key => $field) { ?>
				<li class="other_li"><span><?= Html::encode($field); ?></span>
					<div class="pull-right"> 
						<input type="checkbox" name="form_field[]" id="form_field_1_<?= $key ?>" value="<?= $key ?>" data-id="1" class="inner_chk chk form_field_1" />
						<label for="form_field_1_<?= $key ?>" class="form_field_1" >&nbsp;</label>
					</div>
				</li>
			<?php } ?>
		<?php } ?>
		</ul>
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
