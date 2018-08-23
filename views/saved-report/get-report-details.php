<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

use app\models\Options;
use app\models\ReportsUserSaved;
use app\models\User;

/* @var $this yii\web\View */
/* @var $model app\models\ReportsUserSaved */
/* @var $form yii\widgets\ActiveForm */

?>
<table class="table table-stripped" width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td headers="created_date" style="width:20%;"><a href="javascript:void(0);" title="Format" class="tag-header-black"><strong>Format </strong></a></td>
		<td headers="created_date_val" style="width:80%;"><?= (new ReportsUserSaved)->getFormatTypeIcon($report_details['report_format_id'], $report_details['chart_format_id']) ?></td>
	</tr>
	<tr>
		<td headers="created_date" style="width:20%;"><a href="javascript:void(0);" title="Created Date" class="tag-header-black"><strong>Created Date </strong></a></td>
		<td headers="created_date_val" style="width:80%;"><?= (new Options)->ConvertOneTzToAnotherTz($report_details['created'], "UTC", $_SESSION["usrTZ"]); ?></td>
	</tr>
	<tr>
		<td headers="modified_date" style="width:20%;"><a href="javascript:void(0);" title="Modified Date" class="tag-header-black"><strong>Modified Date </strong></a></td>
		<td headers="modified_date_val" style="width:80%;"><?= (new Options)->ConvertOneTzToAnotherTz($report_details['modified'], "UTC", $_SESSION["usrTZ"]); ?></td>
	</tr>
	<tr>
		<td headers="modified_date" style="width:20%;"><a href="javascript:void(0);" title="Modified By" class="tag-header-black"><strong>Modified By </strong></a></td>
		<td headers="modified_date_val" style="width:80%;"><?= $report_details['modifiedUser']['usr_first_name'].' '.$report_details['modifiedUser']['usr_lastname']; ?></td>
	</tr>
	<?php if((new User)->checkAccess(11.4)){ ?>
	<tr>
		<td headers="access" style="width:20%;"><a href="javascript:void(0);" title="Access" class="tag-header-black"><strong>Access </strong></a></td>
		<td headers="access_val" style="width:80%;">
		<?php 
		
		if(!empty($final_result)){
			if($report_details['report_save_to']==2){
				if($report_details['share_report_by']==1){
					echo '<strong>Role:</strong> ';
				}
				if($report_details['share_report_by'] ==2){
					echo '<strong>Client/Case:</strong> ';
				}
				if($report_details['share_report_by'] ==3){
					echo '<strong>Team Location:</strong> ';
				}
				if($report_details['share_report_by'] ==4){
					echo '<strong>User:</strong> ';
				}
				echo implode(", ",$final_result);
			}
			if($report_details['report_save_to']==1){
				echo "Private";
			}
			if($report_details['report_save_to']==3){
				echo "Public";
			}
			
			}else{
			if($report_details['report_save_to']==1){
				echo "Private";
			}
			if($report_details['report_save_to']==3){
				echo "Public";
			}	
			} ?></td>
	</tr>
	<?php }?>
</table>
