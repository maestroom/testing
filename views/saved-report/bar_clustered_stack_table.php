<?php
if(isset($post_data['ReportsUserSaved']['y_data_display']) && ($post_data['ReportsUserSaved']['y_data_display']=='Weeks' || $post_data['ReportsUserSaved']['y_data_display']=='Days' || $post_data['ReportsUserSaved']['y_data_display']=='Months' || $post_data['ReportsUserSaved']['y_data_display']=='Years')){
			   $LEG=[];	
			   $new_array=[];	
			   $i=0;
			   foreach($report_data as $rdata){
			   	$datekey='';
			   		if(isset($post_data['ReportsUserSaved']['y_data_display']) && ($post_data['ReportsUserSaved']['y_data_display']=='Weeks')){
									$datekey=date("m/d/Y",strtotime($rdata['start_date']))."-".date("m/d/Y",strtotime($rdata['end_date']));
					}elseif(isset($post_data['ReportsUserSaved']['y_data_display']) && ($post_data['ReportsUserSaved']['y_data_display']=='Days')){
										 $datekey='"'.date("m/d/Y",strtotime($rdata['start_date'])).'"';
					}elseif(isset($post_data['ReportsUserSaved']['y_data_display']) && ($post_data['ReportsUserSaved']['y_data_display']=='Months')){
										$datekey=date("m/Y",strtotime($rdata['start_date']));
					}elseif(isset($post_data['ReportsUserSaved']['y_data_display']) && ($post_data['ReportsUserSaved']['y_data_display']=='Years')){
										$datekey=date("Y",strtotime($rdata['start_date']));
					}
									
			   	$new_array[$datekey][$rdata['LEGEND']]=$rdata['X'];
			   	$i++;
			   }
				?>
					<thead>
					<th width="30%"><?=ucwords($ytable_fields_detail['field_display_name'])?></th>
					<?php foreach($report_data as $rdata){ 
						if(in_array($rdata['LEGEND'],$LEG)){continue;}?>
						<th><?=$rdata['LEGEND']?></th>
					<?php $LEG[$rdata['LEGEND']]=$rdata['LEGEND']; 
					}?>		
					</thead>
					<?php foreach($new_array as $dk=>$la){?>
						<tr>
							<td><?=$dk?></td>
							<?php foreach($LEG as $lg){?>
							<td>	<?php 
									if(isset($la[$lg])){ 
										echo $la[$lg]; 
									}else{ 
										echo "0";
									}?>
							</td>
							<?php }?>		
						</tr>
					<?php } ?>
<?php }else{

			   $new_array=array();	
			   $i=0;
			   foreach($report_data as $rdata){
			   	$datekey='';
			   	if($rdata[0]==0){continue;}
				$new_array[$rdata[1]][$rdata[2]]=$rdata[0];
			   	}?>
<thead>
					<th width="30%"><?=ucwords($ytable_fields_detail['field_display_name'])?></th>
					<?php $LEG=array();	
					foreach($report_data as $rdata){ 
						if($rdata[0]==0){continue;}
						else if(in_array($rdata[2],$LEG)){continue;}?>
						<th><?=$rdata[2]?></th>
					<?php $LEG[$rdata[2]]=$rdata[2]; 
					}?>		
					</thead>
					<?php foreach($new_array as $dk=>$la){?>
						<tr>
							<td><?=$dk?></td>
							<?php foreach($LEG as $lg){?>
							<td>	<?php 
									if(isset($la[$lg])){ 
										echo $la[$lg]; 
									}else{ 
										echo "0";
									}?>
							</td>
							<?php }?>
						</tr>	
					<?php }?>
<?php }?>

<!--
<thead>	
<tr>
	<th>Client Name</th>
	<th>January</th>
	<th>February</th>
	<th>March</th>
</tr>
</thead>
<tr>
	<td>ABC Company</td>
	<td>1</td>
	<td>3</td>
	<td>2</td>
</tr>
<tr>
	<td>XYZ Incorporated</td>
	<td>5</td>
	<td>5</td>
	<td>5</td>
</tr>
<tr>
	<td>LSK Partners</td>
	<td>2</td>
	<td>2</td>
	<td>2</td>
</tr>-->