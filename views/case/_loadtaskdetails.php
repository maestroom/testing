<div class="table-responsive">
   <table class="table table-striped table-hover">
    <thead>
    <tr>
		<th class="text-left" width="15%">Comment:</th>
		<th class="text-left"><?php echo $comment; ?></th>
	</tr>
	</thead>
	<tbody>
        <tr>
            <td align="left" width="15%">Service:</td>
            <td align="left"><?php echo $services; ?></td>
        </tr>
		<tr>
            <td align="left" width="15%">Submitted By:</td>
			<td align="left"><?php echo $submitted_by;?></td>
		</tr>
		<tr>
            <td align="left" width="15%">Submitted Date:</td>
			<td align="left"><?php echo $submitted_date;?></td>
		</tr>
	</tbody>
   </table>
</div>
<?php /*<table cellpadding="5" cellspacing="0" border="0" class="subtable" style="font-family: arial; font-size: 13px !important;  padding: 8px; width: 100%;">
    <tr>
        <td width="15%">Comment:</td>
        <td><label tabindex="0"><?php 
        $has_access_408=User::model()->checkAccess(4.08);
        echo Task::model()->findReadUnreadComment($viewClosedTask->id,$viewClosedTask->client_case_id,$has_access_408); ?></label></td>
    </tr>
    <tr>
        <td width="15%">Service:</td>
        <td><label tabindex="0"><?php echo $services; ?></label></td>
    </tr>
    <tr>
        <td width="15%">Submitted By:</td>
        <td><label tabindex="0"><?php if(!empty($viewClosedTask->createdUser)) { 
        		if((isset($viewClosedTask->createdUser->usr_first_name) && $viewClosedTask->createdUser->usr_first_name!="") && (isset($viewClosedTask->createdUser->usr_lastname) && $viewClosedTask->createdUser->usr_lastname!="")){
        			echo $viewClosedTask->createdUser->usr_first_name." ".$viewClosedTask->createdUser->usr_lastname;
        		}else{
        			echo $viewClosedTask->createdUser->usr_username;
        		}
        }; ?></label></td>
    </tr>
    <tr>
        <td width="15%">Submitted Date:</td>
        <td><label tabindex="0"><?php echo Options::model()->ConvertOneTzToAnotherTz($viewClosedTask->created,"UTC",$_SESSION["usrTZ"]); ?></label></td>
    </tr>
</table>*/?>
