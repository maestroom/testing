<style>
*{
	font-family:arial!important;
}
table, table tr, table th, #datatable_section h6{
	font-size:13px!important;
}
table td{
	font-size:12px!important;
}
</style>

<?php
if($datatable_location=='')
	$style="display:none";
elseif($datatable_location=='R' && $legend_location=='R')
	$style="float:right;width:26%;margin-right:14px;position: absolute;right: 21px;";
elseif($datatable_location=='R' && $legend_location=='B')
	$style="float:right;width:26%;margin-right:14px;position: absolute;right: 21px;";	
else
	$style="width:74%;";
?>

<div class="chart-box" id="chart-box">
	<div id="dynamic_chart" class="clsperiodchart chart-container" style="<?php if($datatable_location=='R' && $legend_location=='R'){?>width:90%;<?php } else if($datatable_location=='R' && $legend_location=='B') { ?> float:left;width:70%;<?php } else {?>width:95%;<?php } ?>">
	<img src='<?=$image_data?>' />
	</div>
	<div id="datatable_section" style="<?php echo $style; ?>">
		<?php  if(!empty($table_data)){ ?>
		<h6 align="<?php if($title_location=='TL'){?>left<?php }else {?>center<?php }?>" style="font-family:arial!important;"><b><?=$title?></b></h6>
		<table id="chart_datatable" class="table" style="<?php echo $style;?>" >
			<?php 
				$i=0;
				foreach($table_data as $key=>$val) {
					$column=[];
					if($i==0){
				?>
			<thead>
			<tr>
				<?php 
				foreach($val as $k=>$v){
					$column[$k]=$k;
				?>
				<th style="background: #e9e7e8 none repeat scroll 0 0;border-bottom: 1px solid #e9e7e8;border-right: 1px solid #e9e7e8;font-size: 14px;font-weight: normal;font-family:arial!important;"><?=$k?></th>
				<?php }?>
			</tr>
			</thead>
			<?php }?>
				<tr>
					<?php foreach($val as $k=>$v){?>
					<td style="border-bottom: 1px solid #e9e7e8;border-right: 1px solid #e9e7e8;line-height: 1.42857;padding: 5px;vertical-align: middle;word-break: break-all;font-family:arial!important;"><?=$v?></td>
					<?php }?>
				</tr>
			<?php $i++; } ?>
		</table>
	<?php }?>
	</div>
</div>