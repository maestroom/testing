<?php
// helper html
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $model app\models\Tasks */
/* @var $form yii\widgets\ActiveForm */
$byRole=array('case_manager'=>'Case Manager','team_member'=>'Team Member','both_case_team_manager'=>'Both Case Manager & Team Member Roles','users'=>'Users')
?>
<?php if(!empty($data)){ ?>
<div id="show_by_content" class="mycontainer">
<?php if(isset($data)){ ?>
	<div class="pull-right header-checkbox">
		Select All<input type="checkbox" id="chkselectall" name="chkselectall" onclick="all_checkall(this.checked);"/> 
		<label for="chkselectall" class="">&nbsp;</label> 
	</div>
	<?php $i=1; foreach($data as $key => $value){
		
		if(!empty($value)){ ?>
				<?php 
					// outer checked
					$outer_checked='';
					if(!empty($filter_data)){ 
						foreach($filter_data as $filter_key => $filter_val){
							if($key == $filter_key){
								$outer_checked = "checked='checked'";
							}
						}
					}
				?>
				<div class="myheader">
					<a href="javascript:void(0);"><?= (isset($client_data[$key]) && $client_data[$key]!="")?$client_data[$key]:(isset($team_data[$key])?$team_data[$key]:$byRole[$key]);?></a>
					<div class="pull-right header-checkbox">
						<input type="checkbox" id="table_name_show_by_<?= $i ?>" <?= $outer_checked ?> name="show_by_main[]" value="<?= $show_by ?>" class="chk table_name_<?= $i ?>" onClick="inner_checkall(<?= $i ?>);" /> 
						<label for="table_name_show_by_<?= $i ?>" class="table_name_show_by_<?= $i ?>">&nbsp;</label> 
					</div>
				</div>
				
					<!-- Content -->
					<div class="content">
						<?php if(!empty($value)){?>
							<ul>
							<li><input type="text" placeholder="Search" class="search_showby form-control"></li>
						<?php foreach($value as $k => $val){ ?>
							<?php 
								// inner checked 
								$inner_checked='';
								if(!empty($filter_data)){ 
									foreach($filter_data as $filter_key => $filter_val){
										foreach($filter_val as $v){
											if($k == $v){
												$inner_checked = "checked='checked'";
											}
										}
									}
								}
								$t=uniqid();
							?>
							<li  class="other_li"><span><?= Html::encode($val); ?></span>
									<div class="pull-right"> 
										<input type="checkbox" <?= $inner_checked ?> name="show_by[<?=$show_by?>][]" id="field_name_show_by_team_<?=$t?>" value="<?php if((isset($client_data[$key]) && $client_data[$key]!="") || (isset($team_data[$key]) && $team_data[$key]!="")){ echo Html::encode($key).'_'.Html::encode($k);} else { echo Html::encode($k);} ?>" data-id="<?=$i?>" class="inner_chk chk table_field field_name_show_by_<?= $i ?>" />
										<label for="field_name_show_by_team_<?=$t?>" class="field_name_show_by_<?= $i ?>" >&nbsp;</label>
									</div>
								</li>
							
						<?php } ?>	
						</ul>
			<?php }?>
					</div>  
		<?php $i++; } }?>	
	<?php //} ?>
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
	if($('#table_name_show_by_'+loop).is(':checked')){
		$('.field_name_show_by_'+loop).prop('checked',true);
		$('.field_name_show_by_'+loop).addClass('checked');
	}else{
		$('.field_name_show_by_'+loop).prop('checked',false);
		$('.field_name_show_by_'+loop).removeClass('checked');
	}
	//alert($('.chk:checked').length);
	if($('.chk:checked').length==0){
		$('#chkselectall').prop('checked',false);
		$('#chkselectall').next('label').removeClass('checked');
	}
	if($('.chk:checked').length==$('.chk').length){
		$('#chkselectall').prop('checked',true);
		$('#chkselectall').next('label').addClass('checked');
	}
}
function all_checkall(stat){
	if(stat){
		$('.chk').each(function(){
			$(this).prop('checked',true);
			$(this).next('label').addClass('checked');
		});
	} else {
		$('.chk').each(function(){
			$(this).prop('checked',false);
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
    $content.slideToggle(500, function () {
        $header.text(function () {
			// sorting text
			//console.log();
        });
    });
});
$('.inner_chk').on('click',function(){
	id=$(this).data('id');
	if($('.field_name_show_by_'+id+':checked').length==0){
		$('#table_name_show_by_'+id).prop('checked',false);
		$('#table_name_show_by_'+id).next('label').removeClass('checked');
		return false;
	}if($('.field_name_show_by_'+id+':checked').length>0){
		$('#table_name_show_by_'+id).prop('checked',true);
		$('#table_name_show_by_'+id).next('label').addClass('checked');
	}
});
</script>
<noscript></noscript>
<?php }?>
