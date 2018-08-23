<?php
use app\models\Options;
$params = Yii::$app->params['task_status'];
//echo "<pre>",print_r($selected_field); //die();
//echo "<pre>",print_r($column_data_alias),"</pre>";
if(!empty($format)){
	foreach($format as $id=>$fom){
		$fdata=json_decode($fom,true);
		$format_data[$fdata['id']]=$fdata;
	}
}
//echo "<pre>",print_r($format_data),"</pre>";
?>
<table class="table table-striped" id="custom_report_preview_table">
	<thead>
		<tr>
			<?php $column_names = array();foreach($column_display_data as $key => $column){ $fieldexplode = explode(".",$column_data[$key]); if($fieldexplode[0]=='Calc'){ $column_names[$key] = str_replace(" ","_",strtolower($fieldexplode[1])); } else {$column_names[$key] = $fieldexplode[1];}?>
				<th class="th_custom_report_preview_table white-space"> <?php if(isset($flag) && $flag=='pdf'){?><b><?= $column; ?></b><?php } else {?><a href="javascript:void(0);" title="<?= $column; ?>" class="tag-header-black"><?= $column; ?></a><?php }?> </th>
			<?php } ?>
		</tr>
	</thead>
	<tbody>
		<?php 
		//echo "<pre>",print_r($column_names),"</pre>";
		if(!empty($report_data)){
			foreach($report_data as $k => $data){ 
		?>
		<tr>
			<?php
			$flipped = array_flip($selected_field_keys); 
			if(!empty($column_names)){
				foreach($column_names as $col_type => $column){ 
					//$key=trim($column_data_alias[$col_type]);
					if(isset($change_ids[$col_type]))
					$key=trim($flipped[$col_type]);
					else
					$key=trim($flipped[$col_type]);
					
					if(isset($data[$key])){}else{
						if(strpos($column_data[$col_type], '.') !== false){
							$key=$flipped[$col_type];
							//str_replace(".","_",$column_data[$col_type]);
						}
					}
					// echo $key."<br>";
			?> 
				<td class="td_custom_report_preview_table">
				<?php 
					if(isset($reportTypeFields[$col_type]) && $reportTypeFields[$col_type] == 'DATETIME'){
						echo $data[$key] != '0000-00-00 00:00:00' && $data[$key] != NULL?(new Options)->ConvertOneTzToAnotherTz($data[$key], 'UTC', $_SESSION['usrTZ']):''; 
					} else {
						if($format_data[$col_type]) {
							//echo $data[$key]."here";
							if(isset($data[$key]) && (is_numeric($data[$key]) || is_float($data[$key]) )) {
								if($format_data[$col_type]['group-display-by'] == 2){ // number
									if(isset($format_data[$col_type]['group-display-number-sp']) && $format_data[$col_type]['group-display-number-sp']==1){
										echo number_format($data[$key],$format_data[$col_type]['group-display-number-dp']);
									} else {
										echo number_format($data[$key],$format_data[$col_type]['group-display-number-dp'],'.','');
									}
								}else if($format_data[$col_type]['group-display-by'] == 3){ //currency
									echo $format_data[$col_type]['display_by_currency_smb'].number_format($data[$key],$format_data[$col_type]['group-display-currency-dp']);
								}else if($format_data[$col_type]['group-display-by'] == 4){ //percentage
									echo number_format($data[$key],$format_data[$col_type]['group-display-per-dp'],'.','').'%';
								}else{
									echo html_entity_decode(htmlspecialchars($data[$key]));
								}
							}else{
								echo html_entity_decode(htmlspecialchars($data[$key]));	
							}
						}
						else if(isset($data[$key]) && $data[$key]!=''){
							echo html_entity_decode(htmlspecialchars($data[$key]));
						}
					}
				?>
				</td>
			<?php 
				} 
			} 
			?>
		</tr>
		<?php 
			} 
		} else {
		?>
		<tr>
			<td colspan="<?= count($column_names); ?>" align="center">No Records Found</td>
		</tr>	
		<?php	
		}
		?>
	</tbody>
</table>
