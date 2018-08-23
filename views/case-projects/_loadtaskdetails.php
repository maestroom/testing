<div class="table-responsive">
   <table class="table table-striped table-hover">
   <?php if(isset($flag) && $flag=='load-prev') {?>
        <tr>
            <th scope="row" id="service_<?=$task_id?>" align="left" width="15%"><a href="javascript:void(0);" class="tag-header-black tag-header-cursor-default" title="Service">Service</a></th>
            <td headers="service_<?=$task_id?>" align="left"><?php echo $services; ?></td>
        </tr>
   <?php } else{?>
    <tr>
	<th scope="row" id="comments_<?=$task_id?>" align="left" width="15%"><a href="javascript:void(0);" class="tag-header-black tag-header-cursor-default" title="Comments">Comments</a></th>
	<td headers="comments_<?=$task_id?>" align="left"><?php echo $comment; ?></td>
    </tr>
	
        <tr>
            <th scope="row" id="services_<?=$task_id?>" align="left" width="15%"><a href="javascript:void(0);" class="tag-header-black tag-header-cursor-default" title="Service">Service</a></th>
            <td headers="services_<?=$task_id?>" align="left"><?php echo $services; ?></td>
        </tr>
		<tr>
            <th scope="row" id="submitted_by_<?=$task_id?>" align="left" width="15%"><a href="javascript:void(0);" class="tag-header-black tag-header-cursor-default" title="Submitted By">Submitted By</a></th>
			<td headers="submitted_by_<?=$task_id?>" align="left"><?php echo $submitted_by;?></td>
		</tr>
		<tr>
            <th scope="row" id="submitted_date_<?=$task_id?>" align="left" width="15%"><a href="javascript:void(0);" class="tag-header-black tag-header-cursor-default" title="Submitted Date">Submitted Date</a></th>
			<td headers="submitted_date_<?=$task_id?>" align="left"><?php echo $submitted_date;?></td>
		</tr>
		<?php }?>
   </table>
</div>
