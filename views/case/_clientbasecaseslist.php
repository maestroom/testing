<?php
/* @var $this yii\web\View */
use yii\helpers\Html;
?>
<?php if($client_id != 0) { ?>

<div class="admin-left-module-list">
	<?php 
	if(!empty($caseList)){
	?>
		<ul class="sub-links">
			<?php  
				
				foreach ($caseList as $case){
					$color = 'text-danger';
					if($case->is_close)
						$color = 'text-gray';
			?>
					<li><a href="javascript:updateCaseSelect(<?php echo $case->id; ?>);" title="<?=html_entity_decode($case->case_name); ?>"><em title="<?=html_entity_decode($case->case_name); ?>" class="fa fa-briefcase <?php echo $color ?>"></em> <?=html_entity_decode($case->case_name); ?></a></li>
			<?php 
				}
			?>
		</ul>
	<?php 
	}
	?>
</div>
<script>
addCase();

/**
 * Selected li 
 */
var selector = '.sub-links li';
$(selector).on('click', function(){
    $(selector).removeClass('active');
    $(this).addClass('active');
});
</script>
<noscript></noscript>
<?php } else { ?>
<script>
jQuery('#admin_right').html('');
</script>
<noscript></noscript>
<?php } ?>
