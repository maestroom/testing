<?php
	use yii\helpers\Html;
?>
	<div class="mycontainer">
	<?php 
		if(isset($taxcodeclients) && !empty($taxcodeclients)){ ?>
            <fieldset><legend class="sr-only">Select Client</legend>
            <?php
			foreach($taxcodeclients as $key => $client){  ?>
				<div class="myheader">
					<a href="javascript:void(0);"><?php echo $client['client_name']; ?></a>
					<div class="pull-right header-checkbox">
						<input type="checkbox" id="clients_<?php echo $client['id']; ?>" data-client="<?php echo $client['client_name']; ?>" value="<?php echo $client['id']; ?>" class="client-tax-code" name="clients_<?php echo $client['id']; ?>" aria-label="<?=$client['client_name']?>" /> 
                                                <label for="clients_<?php echo $client['id']; ?>" class="clients_<?php echo $client['id']; ?>"><span class="sr-only"><?=$client['client_name']?></span></label> 
					</div>
				</div>
		<?php }?>
                </fieldset>
                <?php 
	}	?>
	</div>
<script>
$(function() {
	$('input').customInput();
});
</script>
<noscript></noscript>
