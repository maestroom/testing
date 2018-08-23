<?php use yii\helpers\Html;
use yii\helpers\Url;
use app\models\Options;
use app\models\User;
use app\models\ProjectSecurity;

$checkAccess = (new ProjectSecurity)->checkTeamAccess($teamId,$team_loc);
?>

<!-- <div class="table-responsive">
	<table class="table table-striped table-hover">
		<thead>-->
		<?php if(isset($processTrackData['billing']) && !empty($processTrackData['billing'])){?>
			<tr>
				<th scope="col" class="text-left  track-exp-th-1" id="billing_items"><a href="#" title="Billing Items" style="width:20%;">Billing Items</a></th>
				<th scope="col" class="text-left  track-exp-th-2" id="billing_media"><a href="#" title="Media #" style="width:10%;">Media #</a></th>
                <th scope="col" class="text-left  track-exp-th-3" id="billing_description"><a href="#" title="Custom Description" style="width:30%;">Custom Description</a></th>
				<th scope="col" class="text-left  track-exp-th-4" id="billing_units"><a href="#" title="Units" style="width:10%;">Units</a></th>
				<th scope="col" class="text-left  track-exp-th-5" id="billing_first"><a href="#" title="Created By">Created By</a></th>
				<th scope="col" class="text-center  track-exp-th-6"  id="billing_invoiced" title="Invoiced" colspan="3"><a href="#" title="Invoiced">Invoiced</a></th>
<!--			<th scope="col" class="text-right track-exp-th-7" id="billing_second">&nbsp;</th>
				<th scope="col" class="text-right track-exp-th-8" id="billing_third">&nbsp;</th>-->
				<th scope="col" colspan="2" class="text-right track-exp-th-9" id="billing_action"><a href="#" title="Action">Action</a></th>
			</tr>
		<!-- </thead>
		<tbody>-->
		<?php

		foreach ($processTrackData['billing'] as $billing) {?>
			<tr <?php if($billing->invoiced==2){?> class="text-danger" <?php }?>>
            	<td class="v-align-top text-left track-exp-td-1 word-break" headers="billing_items"><?=$billing->pricing->price_point; ?></td>
            	 <td class="v-align-top text-left track-exp-td-2" headers="billing_media">
				 <?php if($billing->evid_num_id!=0) {
							if ((new User)->checkAccess(3)) {?>
								<a href="javascript:go_toMedia('<?= $billing->evid_num_id;?>')" title="Media #<?php echo $billing->evid_num_id; ?>"><?= $billing->evid_num_id;?></a>
				 	  <?php } else {
						   echo  $billing->evid_num_id;
					       }
				 	  }?>

				 </td>
                <td class="v-align-top text-left track-exp-td-3 word-break" headers="billing_description"><?=Html::encode($billing->billing_desc); ?></td>
                <td class="v-align-top text-left track-exp-td-4 word-break" headers="billing_units"><?=round($billing->quantity,2) ?> <?=$billing->pricing->unit->unit_name;?></td>
                <td class="v-align-top text-left track-exp-td-5 word-break" headers="billing_first">
				<?php
					$billingdate=(new Options)->ConvertOneTzToAnotherTz($billing->created, 'UTC', $_SESSION['usrTZ']);
					 if(!$checkAccess){
							echo "<span title='Created by'>User</span>";
						} else {
							echo "<span title='{$billingdate}'>".$billing->createdUser->usr_first_name.' '.$billing->createdUser->usr_lastname."</span>";}?>
				</td>
				<td class="v-align-top text-center track-exp-td-6" headers="billing_invoiced" colspan="3"><?php if($billing->invoiced==1){?><em title="Invoiced" class="fa fa-check text-danger"></em><?php }?></td>
                <!--<td class="text-right icon-group track-exp-td-7" headers="billing_second">&nbsp;</td>
                <td class="text-right icon-group track-exp-td-8" headers="billing_third">&nbsp;</td>-->
                <td class="v-align-top text-right td-no-pad track-exp-td-9"  title="Edit Item" headers="billing_action">
                <?php $onclick = "javascript:EditBilling(".$billing->id.");";
	    		if(!$checkAccess){
	    			$onclick = "javascript:alert('This action is available only to $team_name Team Members.');";
	    		}
	    		if($billing->invoiced==1){
	    		?>
	    		<!--<em class="fa fa-pencil text-dark track-icon" title="Already Invoiced Edit Not Allow"></em>-->
	    		<?php }else{?>
                <a href="<?=$onclick?>" class="text-primary track-icon"><em title="Edit" class="fa fa-pencil"></em></a>
                <?php }?>

                </td>
                <td class="v-align-top text-right td-no-pad track-exp-td-10" title="Delete Item" headers="billing_action">
                <?php if($billing->invoiced==1){?>
               <!-- <em class="fa fa-close text-dark track-icon" title="Already Invoiced Delete Not Allow"></em> -->
                <?php }else{
                	$onclick="javascript:alert('You do not have access to delete this item. Please contact your administrator if your access needs to be updated.');";
                	if (((new User)->checkAccess(4.0701) && $case_id != 0) || ((new User)->checkAccess(5.0601) && $team_id != 0)) {
						$onclick = "javascript:DeleteBilling(".$billing->id.",'".str_replace("'","\'",$billing->pricing->price_point)."');";
					}
                	if(!$checkAccess){
                		$onclick = "javascript:alert('This action is available only to $team_name Team Members.');";
                	}


                	?>
				<a href="<?=$onclick?>" class="text-primary track-icon"><em title="Remove" class="fa fa-close"></em></a><?php }?></td>

			</tr>

		<?php } } else {?>
			<tr class="hide-tr">
				<th  scope="col" class="text-left  track-exp-th-1 hide-td" id="billing_items"><a href="#" title="Billing Items" style="width:20%;">Billing Items</a></th>
				<th  scope="col" class="text-left  track-exp-th-2 hide-td" id="billing_media"><a href="#" title="Media #" style="width:10%;">Media #</a></th>
                <th  scope="col" class="text-left  track-exp-th-3 hide-td" id="billing_description"><a href="#" title="Custom Description" style="width:30%;">Custom Description</a></th>
				<th  scope="col" class="text-left  track-exp-th-4 hide-td" id="billing_units"><a href="#" title="Units" style="width:10%;">Units</a></th>
				<th  scope="col" class="text-left  track-exp-th-5 hide-td" id="billing_first"><a href="#" title="Created By">Created By</a></th>
				<th  scope="col" class="text-center  track-exp-th-6 hide-td"  id="billing_invoiced" title="Invoiced" colspan="3"><a href="#" title="Invoiced">Invoiced</a></th>
				<th  scope="col" colspan="2" class="text-right track-exp-th-9 hide-td" id="billing_action"><a href="#" title="Action">Action</a></th>
			</tr>
			<tr class="hide-tr">
            	<td  class="v-align-top text-left track-exp-td-1 word-break hide-td" headers="billing_items">&nbsp;</td>
            	 <td  class="v-align-top text-left track-exp-td-2 hide-td" headers="billing_media">&nbsp;</td>
                <td  class="v-align-top text-left track-exp-td-3 word-break hide-td" headers="billing_description">&nbsp;</td>
                <td  class="v-align-top text-left track-exp-td-4 word-break hide-td" headers="billing_units">&nbsp;</td>
                <td  class="v-align-top text-left track-exp-td-5 word-break hide-td" headers="billing_first">&nbsp;</td>
				<td  class="v-align-top text-center track-exp-td-6 hide-td" headers="billing_invoiced" colspan="3">&nbsp;</td>
                <td  class="v-align-top text-right td-no-pad track-exp-td-9 hide-td"  title="Edit Item" headers="billing_action">&nbsp;</td>
                <td  class="v-align-top text-right td-no-pad track-exp-td-10 hide-td" title="Delete Item" headers="billing_action">&nbsp;</td>

			</tr>
		<?php } ?>
		<!-- </tbody>
	</table>
</div>-->
