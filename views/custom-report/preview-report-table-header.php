<?php
use app\models\Options;
$params = Yii::$app->params['task_status'];


//echo "<pre>",print_r($header_data),"</pre>";
//echo "<pre>",print_r($criteria),"</pre>";
//die;
?>
<table class="table table-striped">
	<tbody>
		<tr>
			<td>Report Type</td>
			<td><?php echo $model->report_type;?></td>
		</tr>
		<?php if(isset($header_data['ReportsUserSaved']['custom_report_name']) && $header_data['ReportsUserSaved']['custom_report_name']!="") {?>
		<tr>
			<td>Custom Report Name</td>
			<td><?php echo $header_data['ReportsUserSaved']['custom_report_name']?>
		</tr>
		<?php }?>
		<?php if(isset($header_data['ReportsUserSaved']['title']) && $header_data['ReportsUserSaved']['title']!="") {?>
		<tr>
			<?php if(isset($header_data['chart_report']) && $header_data['chart_report']=="chart_report"){?>
				<td>Chart Title</td>
			<?php }else{?>
				<td>Related Chart Title</td>
			<?php }?>
			<td><?=$header_data['ReportsUserSaved']['title']?></td>
		</tr>	
		<?php }?>
		<?php if(isset($header_data['ReportsUserSaved']['date_range_start']) && $header_data['ReportsUserSaved']['date_range_start']!=''){ ?>
		<tr>
			<td>Date Range Field</td>
			<td><?php echo $header_data['ReportsUserSaved']['date_range_start']; ?></td>
		</tr>
		<?php } ?>
		<?php if(!empty($criteria)) { ?>
		<tr>
			<td valign="top">Filters</td>
			<td>
					
					<?php 
					$j=0;
					foreach($criteria as $key => $c){
						echo $column_display_data[$key];
						if(is_array($c)){
							$i=0;
							if(!is_array($c['opt']))
								echo " ".strtolower($c['opt'])." ";
							//else
								//echo " ".strtolower($c['opt'][$j])." ";

							foreach($c['val'] as $kk=>$cc){
								if(is_array($c['opt'])){
									echo " ".strtolower($c['opt'][$kk])." ";
								}
								if(isset($lookup_criteria_values[$key][$cc])){
									echo $lookup_criteria_values[$key][$cc];
								}else{
									if($reportTypeFields[$key] == 'DATETIME'){
											if(trim(str_replace("-"," ",$cc))=='T'){
												echo date('m/d/Y');
												echo " - ".date('m/d/Y');
											}else if(trim(str_replace("-"," ",$cc))=='Y'){
												echo date('m/d/Y',strtotime("-1 days"));
												echo " - ".date('m/d/Y',strtotime("-1 days"));
											}else if(trim(str_replace("-"," ",$cc))=='W'){
												echo date('m/d/Y',strtotime("-7 days"));
												echo " - ".date('m/d/Y');	
											}else if(trim(str_replace("-"," ",$cc))=='M'){
												echo date('m/d/Y',strtotime("first day of last month")); 
												echo " - ".date('m/d/Y',strtotime('last day of last month'));	
											}else{
												echo $cc;
											}
									}else{
										echo $cc; 
									}
								}
									
								if($i != (count($c['val'])-1))
									echo "; ";
									
								$i++;
							}
							
						}
					echo "</td></tr><tr><td></td><td>";	
					$j++;
					}
					?>
			
		<?php } ?>
	</tbody>
</table>