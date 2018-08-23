<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$projstatus =array(0=>"Not Started",1 => "Started",3=> "OnHold", 4 =>"Completed");
$max_val=array();
//echo count($mychart_data2[0]),"<pre>",print_r($mychart_data2);die;
foreach ($mychart_data2 as $mdata){
	$max_val[count($mdata)]=count($mdata);
}
$maxvals=max($max_val);


?>
<table cellpadding="0" cellspacing="0" width="100%" border="1">
    <tr style="background: none repeat scroll 0 0 #CEDF7B;">
        <td style="width:30%;">SLA Turn-time by Service</td>
        <td style="width:30%;">Projects Submitted Start Date</td>
        <td style="width:30%;">Projects Submitted End Date</td>
        <td style="width:30%;">Projects Status</td>
        <td style="width:30%;">Service Status</td>
    </tr>
    <tr>
        <td></td>
        <td><?php echo date('m/d/Y',strtotime($startdate)); ?></td>
        <td><?php echo date('m/d/Y',strtotime($enddate)); ?></td>
        <td><?php $sts="";foreach ($status as $st){ if($sts=="")$sts=$projstatus[$st]; else $sts.=",".$projstatus[$st];} echo $sts;?></td>
        <td>Completed</td>
    </tr>
    <tr>
        <td colspan="16"></td>
    </tr>	
    <tr>
        <td>Service</td>
        <?php for($i=1;$i<=$maxvals;$i++){?>
        <td><?php echo $i?> Day</td>
        <?php }?>
    </tr>
    <?php
    foreach ($chart_data as $cdata){ ?>
	    <tr>
	        <td><?php foreach ($cdata as $cda)
	        		{ 
	        			echo $cda['label'];
	        			break;
	        		} 
	        	?></td>
	        	<?php 
	        		for($i=1;$i<=$maxvals;$i++){ 
	        			foreach ($cdata as $cda){ 
	        				if($i==$cda[0]){
	        					//echo $cda[1];
	        	?>
	        	<td><?php echo $cda[1];?></td>
	        	<?php 
	        		} 
	        	}
	        	if(count($cdata) < $maxvals){
	        		if($i > count($cdata)){ ?>
	        			<td>0</td>
	        		<?php }
	        	}
	        ?>
	        <?php }?>
	    </tr>
    <?php }?>
</table>
<?php
//die();
/* $filename = "SLATurnTimeByService_" . date('m_d_Y', time()) . ".xls";
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"$filename\""); */
?>