<?php
	use yii\helpers\Html;
?>
	<div class="mycontainer">
	<?php 
		if(!empty($pricePoint_data)){ 
                    ?>
            <fieldset><legend class="sr-only">Select All Pricepoints for Team</legend>
            <?php
			$k=1;
			foreach($pricePoint_data as $key => $prices){ 
				foreach($prices as $innerkey => $teamsprice){ ?> 
				<div class="myheader">
                                    <a href="javascript:void(0);"><?php echo $innerkey; ?></a>
                                    <div class="pull-right header-checkbox">
                                        <input type="checkbox" id="teams_<?php echo $k; ?>" data-teams="<?php echo $innerkey; ?>" value="<?php echo $key; ?>" class="team-price-point teams_<?php echo $k; ?>" name="pricepoint_<?php echo $key; ?>" onclick="selectall_pricepoint('<?php echo $k; ?>');"  aria-label="<?=$innerkey?>" /> 
                                        <label for="teams_<?php echo $k; ?>" class="teams_<?php echo $k; ?>"><span class="sr-only"><?=$innerkey?></span></label> 
                                    </div>
				</div>
				<div class="content">
                                    <fieldset><legend class="sr-only"><?=$innerkey?></legend>
                                    <ul id="service_task_container">
                                        <?php $i=1; foreach ($teamsprice as $ks => $val){ ?>
                                            <li>
                                                <!--<span id="lbl-stc-<?=$i?>"><?php echo $val; ?></span>-->
                                                <label for="pricepoint_<?php echo $k; ?>_<?php echo $i; ?>" class="chkbox-global-design pricepoint_<?php echo $k; ?>"><?php echo $val; ?></label>
                                                <div class="pull-right"> 
                                                    <input type="checkbox" data-pricepoint="<?php echo $val; ?>" name="pricepoint_<?php echo $k; ?>_<?php echo $i; ?>" id="pricepoint_<?php echo $k; ?>_<?php echo $i; ?>" value="<?php echo $ks; ?>" data-team="<?php echo $key; ?>" class="price-point pricepoint_<?php echo $k; ?>" onclick="inner_pricepoint('<?php echo $i; ?>','<?php echo $k; ?>');" aria-label="<?php echo $val; ?>" />
                                                    <!--<label for="pricepoint_<?php echo $k; ?>_<?php echo $i; ?>" class="pricepoint_<?php echo $k; ?>"><span class="sr-only"><?php echo $val; ?></span></label>-->
                                                </div>
                                            </li>
                                        <?php $i++; } ?>    
                                    </ul>
                                    </fieldset>
				</div>  
			<?php $k++;
					}
				}
                                ?>
                </fieldset>
                <?php
			} 
	?>
	</div>
<script>
$(function() {
	$('input').customInput();
});

$('.myheader').on('click',function(){
	if($(this).hasClass('myheader-selected-tab')){
		$(this).removeClass('myheader-selected-tab');
	} else {
		$(this).addClass('myheader-selected-tab');
	}	
});

$(".myheader a").click(function () {
    $header = $(this).parent();
    $content = $header.next();
    $content.slideToggle(500, function () {
        $header.text(function () {
            //change text based on condition
            //return $content.is(":visible") ? "Collapse" : "Expand";
        });
    });

});

// inner price point
function inner_pricepoint(lop1,lop2){
	if($('#pricepoint_'+lop2+'_'+lop1).is(':checked')){
		$('.teams_'+lop2).prop('checked',true);
		$('.teams_'+lop2).addClass('checked');
	} else {
		$('.teams_'+lop2).prop('checked',false);
		$('.teams_'+lop2).removeClass('checked');
	}
}

// teams checkbox
function selectall_pricepoint(loop){
	if($('#teams_'+loop).is(':checked')){
		$('.pricepoint_'+loop).prop('checked',true);
		$('.pricepoint_'+loop).addClass('checked');
	} else {
		$('.pricepoint_'+loop).prop('checked',false);
		$('.pricepoint_'+loop).removeClass('checked');
	}
}
</script>
<noscript></noscript>
