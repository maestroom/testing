<?php
if(isset($table_data) && $table_data!=""){
$column=[];
foreach($table_data as $data) {
	$column=array_keys($data);
	break;
}
if(isset($header_data['table_data_title']) && $header_data['table_data_title']!=""){?>
<table>
	<thead>
		<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td colspan="<?=(count($column)-1)?>"><?=$header_data['table_data_title']?></td>
		</tr>
	</thead>
</table>	
<?php }
?>
<table>
	<thead>
		<tr>
			<td>&nbsp;</td>
			<?php foreach($column as $col){?><td><?=$col?></td><?php }?>
		</tr>
	</thead>
	<tbody>
		<?php foreach($table_data as $data) { ?>
			<tr>
			<td>&nbsp;</td>
			<?php foreach($column as $col){?><td><?=$data[$col]?></td><?php }?>
			</tr>
		<?php }?>
	</tbody>
</table>
<?php }?>