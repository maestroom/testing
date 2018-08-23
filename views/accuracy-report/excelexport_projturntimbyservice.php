<?php
	$projstatus = array(0=>'Not Started',1=>'Started',3=>'OnHold',4=>'Completed');
?>
<table cellpadding="0" cellspacing="0" width="100%" border="1">
    <tr style="background: none repeat scroll 0 0 #CEDF7B;">
        <td style="width:30%;">SLA Turn-Time by Client/Cases</td>
        <td style="width:30%;">Project Submitted Start Date</td>
        <td style="width:30%;">Project Submitted End Date</td>
        <td colspan="14"></td>
    </tr>
    <tr>
        <td align='left'></td>
        <td><?php echo date('m/d/Y',strtotime($startdate)); ?></td>
        <td><?php echo date('m/d/Y',strtotime($enddate)); ?></td>
        <td colspan="14">
        <?php 
        		$sts="";
        		foreach ($_REQUEST['task_status'] as $st)
	        	{  
	        		if($sts==""){
	        			$sts=$projstatus[$st];
	        		}else {
	        			$sts.=",".$projstatus[$st];
	        		}
	        	}
	     		echo $sts;
        ?>
	    </td>
    </tr>
    <tr>
        <td colspan="17"></td>
    </tr>	
    <tr>
        <th align='left'>Client</th>
        <th align='left'>Case</th>
        <th align='left'>Project #</th>
        <th align='left'>Date Submitted</th>
        <th align='left'>DueDate</th>
        <th align='left'>Data Size (In)</th>
        <th align='left'>Project Status</th>
        <th align='left'>Project Completion Date</th>
        <th align='left'>Service</th>
        <th align='left'>Location</th>
        <th align='left'>SLA Business Days Allotted</th>
        <th align='left'>SLA Business Days Spent</th>
        <th align='left'>Business Days in Follow-up</th>
        <th align='left'>Business Days in Stop-Clock</th>
        <th align='left'>SLA Business Days Spent (minus Stop Clock)</th>
        <th align='left'>SLA Business Days Late</th>
        <th align='left'>Service Status by Task</th>
	<!--<th align='left'>Service Completed Date</th>-->
        <th align='left'>Last Service Task Completed Date</th>
    </tr>
    <?php
    if (!empty($final_data)) {
    		foreach ($final_data as $key=>$data){?>
    		    <?php 
		        if(!empty($data['sla'])){?>
		        
		        <?php foreach ($data['sla'] as $sla){?>
		        	<tr>
		        		<td align='left'><?php echo $data['client']?></td>
				        <td align='left'><?php echo $data['case']?></td>
				        <td align='left'><?php echo $key;?></td>
				        <td align='left'><?php echo $data['date_submitted']?></td>
				        <td align='left'><?php echo $data['duedate']?></td>
				        <td align='left'><?php echo $data['data_size'];?></td>
				        <td align='left'><?php echo $data['status']?></td>
				        <td align='left'><?php echo $data['completion_date']?></td>
		        		<td align='left'><?php echo $sla['name'];?></td>
				        <td align='left'><?php echo $sla['location'];?></td>
				        <td align='left'><?php echo $sla['allotted_days'];?></td>
				        <td align='left'><?php echo $sla['day_spent'];?></td>
				        <td align='left'><?php echo $sla['toto_followup_days'];?></td>
				        <td align='left'><?php echo $sla['stop_clk_business_days'];?></td>
				        <td align='left'><?php echo $sla['days_spent_minus_followup'];?></td>
				        <td align='left'><?php echo $sla['days_late'];?></td>
				        <td align='left'>
				        <?php 
				        	if(!empty($sla['completed'])){
				        		if(isset($sla['completed'][4]) && !empty($sla['completed'][4])){
				        			echo "Completed : ".implode(",",$sla['completed'][4]);
				        			echo isset($sla['completed'][1]) && !empty($sla['completed'][1])?" / <br style='mso-data-placement:same-cell;'/> ":"";
				        		}
				        		if(isset($sla['completed'][1]) && !empty($sla['completed'][1]))
				        			echo "Started : ".implode(",",$sla['completed'][1]);
		        			} else {
		        				echo "Not Started";	
		        			}
		        		?>
		        		</td>
				        <td align='left'><?php if($sla['completed']) echo $sla['completed_date']?></td>	
		        	</tr>
		        <?php }?>
		        
		        <?php }?>
		    
    <?php }}?>
</table>
<?php
/* $filename = "SLATurnTimeByClientCases_" . date('m_d_Y', time()) . ".xls";
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"$filename\""); */
?>