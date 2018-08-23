<div class="table-responsive">
   <table class="table table-striped table-hover">
	<tbody>
        <tr>
            <th scope="row" align="left" width="15%"><a href="javascript:void(0);" class="tag-header-black tag-header-cursor-default" title="Due Date">Due Date</a></th>
            <td headers="duedate" align="left"><?=$duedate?></td>
        </tr>
        <tr>
            <th scope="row" align="left" width="15%"><a href="javascript:void(0);" class="tag-header-black tag-header-cursor-default" title="Past Due Project">Past Due Project</a></th>
            <td headers="PastDueProject" align="left">
            <?php 
            if ($ispastduetask){
    		echo $imghtml1 = '&nbsp;<span tabindex="0" class="fa fa-exclamation text-danger" title="Past Due Project"></span>';
    	    }
            ?>
            
            </td>
        </tr>
        <tr>
            <th scope="row" align="left" width="15%"><a href="javascript:void(0);" class="tag-header-black tag-header-cursor-default" title="Service">Service</a></th>
            <td headers="service" align="left"><?=$services?></td>
        </tr>
        <tr>
            <th scope="row" align="left" width="15%"><a href="javascript:void(0);" class="tag-header-black tag-header-cursor-default" title="Assigned To">Assigned To</a></th>
            <td headers="assign_to" align="left"><?=$assigedUser?></td>
        </tr>
        <tr>
            <th scope="row" align="left" width="15%"><a href="javascript:void(0);" class="tag-header-black tag-header-cursor-default" title="Submitted By">Submitted By</a></th>
			<td headers="submitted_by" align="left"><?=$submitted_by;?></td>
		</tr>
		<tr>
            <th scope="row" align="left" width="15%"><a href="javascript:void(0);" class="tag-header-black tag-header-cursor-default" title="Submitted Date">Submitted Date</a></th>
			<td headers="submitted_date" align="left"><?=$submitted_date;?></td>
		</tr>
		<tr>
            <th scope="row" align="left" width="15%"><a href="javascript:void(0);" class="tag-header-black tag-header-cursor-default" title="Completed Date">Completed Date</a></th>
			<td headers="completed_date" align="left"><?=$completed_date;?></td>
		</tr>
	</tbody>
   </table>
</div>
