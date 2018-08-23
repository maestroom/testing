<?php
use app\models\Options;
$params = Yii::$app->params['task_status'];
?>
<table class="table table-striped">
	<tbody>
		<tr>
			<td>Tabular Report Name</td>
			<td><?php echo $model->report_type;?></td>
		</tr>
		<?php if((isset($image_data) && $image_data!="")  && (isset($header_data['ReportsUserSaved']['title']) && $header_data['ReportsUserSaved']['title']!="")) {?>
		<tr>
			<td>Custom Report Name</td>
			<td><?php echo $header_data['ReportsUserSaved']['title'];?></td>
		</tr>
		<?php }?>
		<?php if(isset($header_data['ReportsUserSaved']['date_range_start']) && $header_data['ReportsUserSaved']['date_range_start']!=''){ ?>
		<tr>
			<td>Date Range Field</td>
			<td><?php echo $header_data['ReportsUserSaved']['date_range_start']; ?></td>
		</tr>
		<?php } ?>
	</tbody>
</table>
<table class="table table-striped">
	<tbody>
		<?php if(!empty($criteria)) { ?>
		<tr>
			<td>Filters</td>
			<td colspan="2">
				<table>
					<?php 
					foreach($criteria as $key => $c){
						echo "<tr>";
						echo "<td>",$column_display_data[$key],"</td>";
						if(is_array($c)){
							echo "<td>";
							$i=0;
							foreach($c as $cc){
								if(isset($lookup_criteria_values[$key][$cc])){
									echo $lookup_criteria_values[$key][$cc];
								}else{
									if($reportTypeFields[$key] == 'DATETIME')
										echo $cc;
									else	
										echo $cc; 
								}
									
								if($i != (count($c)-1))
									echo ", ";
									
								$i++;
							}
							echo "</td>";
						}
						echo "</tr>";
					}
					?>
				</table>
			</td>
		</tr>
		<?php } ?>
		<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
	</tbody>
</table>
