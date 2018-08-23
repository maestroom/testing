<?php if(!empty($process_activity)) { ?>
<table	class="kv-grid-table table table-hover table-condensed table-striped today-activity-table">
									<!--<thead>
										<tr class="kartik-sheet-style">
											<th colspan="2" title="Today's Activity" >Today's Activity</th>
										</tr>
									</thead>-->
									<tbody>
<?php  foreach ($process_activity as $activity) { ?>
	
			<tr data-key="9">
				<td width="5%" class="text-center first">
					<a href="javascript:void(0);" title="<?php echo $activity['title']; ?>"><em class="fa fa-<?php echo $activity['image']; ?> text-danger" title="<?php echo $activity['title']; ?>"></em></a>
				</td>
				<td><?php echo $activity['activity']." "; ?><span class="text-danger"><?php echo $activity['time'];  ?></span></td>
			</tr>
	
<?php } ?>
</tbody>
</table>
<?php } exit; ?>											
<script type="text/javascript">


</script>
<noscript></noscript>
