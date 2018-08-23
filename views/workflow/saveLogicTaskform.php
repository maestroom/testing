<?php
$startlogic=Yii::$app->params['startlogic'];
$endarlogic = Yii::$app->params['endlogic'];
$duration=Yii::$app->params['duration'];
$new_timings=array();
if(isset($action) && $action!=""){
	$data['actionType']=$action;
	
}
?>
<?php 
if(!empty($data['TeamserviceSla']['team_loc_id'])){
foreach($data['TeamserviceSla']['team_loc_id'] as $loc_id){
	if($data['actionType'] != 'Edit'){
		$id = 'logic_random'.rand(pow(10, 4), pow(10, 5)-1);
	}else{
		$id = $data['TeamserviceSla']['id'];
	}
if($data['actionType'] != 'Edit'){?><tr id="sla_logic_content_<?= $id; ?>"><?php }?>
  					<td>
                                            <?php
	                                    $start_unit = $listUnit[$data['TeamserviceSla']['size_start_unit_id']];
	                                    $end_unit = $listUnit[$data['TeamserviceSla']['size_end_unit_id']];
	                                    $endlogic = "";
	                                    $endlogic = " AND " . $endarlogic[$data['TeamserviceSla']['end_logic']] . " " . $data['TeamserviceSla']['end_qty'] . " " . $end_unit;
	                                    echo $logic_name = $teamLocation[$loc_id] . " - " . $startlogic[$data['TeamserviceSla']['start_logic']] . " " . $data['TeamserviceSla']['start_qty'] . " " . $start_unit . $endlogic;
                                     ?>
   					</td>
   					<td><?= $data['TeamserviceSla']['del_qty'] . " " . $duration[$data['TeamserviceSla']['del_time_unit']]; ?></td>
   					<td><?= $projectPriority[$data['TeamserviceSla']['project_priority_id']]; ?></td>
   					<td>
   						<a href='javascript:slalogiccontentaction("edit","<?= $id ?>");' class="icon-fa icon-set" title="Edit SLA logic content" aria-label="Edit SLA logic content"><em title="Edit SLA logic content" class="fa fa-pencil text-primary"></em></a> 
   						<a href='javascript:slalogiccontentaction("delete","<?= $id ?>","<?=$logic_name?>");' class="icon-fa icon-set" title="Delete SLA logic content" aria-label="Delete SLA logic content"><em title="Delete SLA logic content" class="fa fa-close text-primary"></em></a>
   					</td>
   					<?php
                         echo "<input type='hidden' name='TeamserviceSla[{$id}][id]' value='{$id}'/>";
                         echo "<input type='hidden' name='TeamserviceSla[{$id}][teamservice_id]' value='{$data['TeamserviceSla']['teamservice_id']}'/>";
                         echo "<input type='hidden' name='TeamserviceSla[{$id}][team_loc_id]' value='{$loc_id}'/>";
                         echo "<input type='hidden' name='TeamserviceSla[{$id}][start_logic]' value='{$data['TeamserviceSla']['start_logic']}'/>";
                         echo "<input type='hidden' name='TeamserviceSla[{$id}][start_qty]' value='{$data['TeamserviceSla']['start_qty']}'/>";
                         echo "<input type='hidden' name='TeamserviceSla[{$id}][size_start_unit_id]' value='{$data['TeamserviceSla']['size_start_unit_id']}'/>";
                         echo "<input type='hidden' name='TeamserviceSla[{$id}][end_logic]' value='{$data['TeamserviceSla']['end_logic']}'/>";
                         echo "<input type='hidden' name='TeamserviceSla[{$id}][end_qty]' value='{$data['TeamserviceSla']['end_qty']}'/>";
                         echo "<input type='hidden' name='TeamserviceSla[{$id}][size_end_unit_id]' value='{$data['TeamserviceSla']['size_end_unit_id']}'/>";
                         echo "<input type='hidden' name='TeamserviceSla[{$id}][del_qty]' value='{$data['TeamserviceSla']['del_qty']}'/>";
						 echo "<input type='hidden' name='TeamserviceSla[{$id}][del_time_unit]' value='{$data['TeamserviceSla']['del_time_unit']}'/>";
		    			 echo "<input type='hidden' name='TeamserviceSla[{$id}][project_priority_id]' value='{$data['TeamserviceSla']['project_priority_id']}'/>";
			?>
<?php if($data['actionType'] != 'Edit'){?></tr><?php }?>
<?php } }?>
