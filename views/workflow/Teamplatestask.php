<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\assets\ManageUserAccessAsset;
//\app\assets\CustomInputAsset::register($this);

ManageUserAccessAsset::register($this);
/* @var $this yii\web\View */
/* @var $searchModel app\models\EvidenceTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Servicetask';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="mycontainer">
<div id="wftree" class="tree-class"></div>
<textarea name="servicetask_id" id="servicetask_id" style="visibility: hidden; height: 0px; margin: 0px; padding: 0px;"></textarea>
<?php 
    /*if(!empty($teamService)){
	foreach($teamService['teamservice'] as $teamservice_id=>$team_service_task) {
		if(is_array($team_service_task)){
                    foreach ($team_service_task as $loc=>$teamservice_data) {
                        $unique_id=uniqid();
	?> 
    <div class="myheader">
    	<a href="javascript:void(0);"><?php echo $teamservice_data['teamservice']; if($teamservice_data['location']!="") { echo ' - '. $teamservice_data['location'];} ?></a>
    	<div class="pull-right header-checkbox">
            <input  type="checkbox" id="teamservice_<?=$teamservice_id."_".$loc.$unique_id ?>" name="teamservice[<?=$teamservice_id."_".$loc ?>]" onclick=" $('.chk_<?php echo $teamservice_id."_".$loc; ?>').prop('checked',this.checked); if(this.checked){ $('.chk_<?php echo $teamservice_id."_".$loc; ?>').each(function(){ $(this).next().addClass('checked');});}else{$('.chk_<?php echo $teamservice_id."_".$loc; ?>').each(function(){ $(this).next().removeClass('checked');});} " value="<?php echo $teamservice_id."_".$loc; ?>" class="parent_<?php echo $teamservice_id."_".$loc; ?>" onChange="selectall_pricepoint('<?php echo $teamservice_id."_".$loc; ?>');"/> 
            <label class="parent_<?= $teamservice_id."_".$loc ?>" for="teamservice_<?=$teamservice_id."_".$loc.$unique_id ?>"><span class="sr-only"><?php echo $teamservice_data['teamservice']; if($teamservice_data['location']!="") { echo ' - '. $teamservice_data['location'];} ?></span></label> 
    	</div>
    </div>
    <div class="content">
	<fieldset>
            <legend class="sr-only">Add Available Service Tasks <?php echo $teamservice_data['teamservice']; if($teamservice_data['location']!="") { echo ' - '. $teamservice_data['location'];} ?></legend>
            <ul>
            <?php 
                foreach ($teamService['servicetask'][$teamservice_id][$loc] as $service_data){ 
                    $unique_id=uniqid();
            ?>
                <li>    <span id="chk_lbl_<?=$teamservice_id."_".$loc."_".$service_data['servicetask_id'].$unique_id ?>"></span>
                    <div class="pull-left wrkflowtemp_508 "> 
                        <label class="chk_<?= $teamservice_id."_".$loc; ?>" for="chk_<?=$teamservice_id."_".$loc."_".$service_data['servicetask_id'].$unique_id ?>"><?php echo $service_data['service_task']; ?></label>
                        <input aria-labelledby="chk_lbl_<?=$teamservice_id."_".$loc."_".$service_data['servicetask_id'].$unique_id ?>" onclick="i=0;$('.chk_<?php echo $teamservice_id."_".$loc; ?>:checked').each(function(){ i++; if(i==$('.chk_<?php echo $teamservice_id."_".$loc; ?>').length){ $('#teamservice_<?=$teamservice_id."_".$loc.$unique_id ?>').next().addClass('checked');} else {$('#teamservice_<?=$teamservice_id."_".$loc.$unique_id ?>').next().removeClass('checked'); }});"  rel="<?php echo $service_data['servicetask_id']?>" data-service="<?php echo $service_data['service_task']; ?>" data-teamservice="<?php echo $teamservice_data['teamservice']; if($teamservice_data['location']!="") { echo ' - '. $teamservice_data['location'];} ?>" data-loc=<?php echo $loc?> id="chk_<?=$teamservice_id."_".$loc."_".$service_data['servicetask_id'].$unique_id ?>" type="checkbox" class="service_checkbox chk_<?php echo $teamservice_id."_".$loc; ?>"  name="services[<?= $service_data['servicetask_id']?>][<?=$loc?>]" value="<?=$service_data['servicetask_id']?>" onChange="inner_pricepoint('<?=$teamservice_id."_".$loc ?>')">
                        
                    </div>
                </li>
            <?php }?>    
        </ul>
		</fieldset>
    </div>  
<?php }	} }}*/?>
</div>
<script>
var treeData = <?= json_encode($serviceList); ?>;
$(function(){
	$("#wftree").dynatree({
		checkbox: true,
		selectMode: 3,
		children: treeData,
		onSelect: function(select, node) {
			var clientcaseAr = [];
			var selKeys = $.map(node.tree.getSelectedNodes(), function(node){
				if(node.childList===null)
					return node.data.key.toString();
			});
			mystring = JSON.stringify(selKeys);
            newTemp = mystring.replace(/"/g, "'");
			$('#servicetask_id').val(newTemp);
		},
		onDblClick: function(node, event) {
			node.toggleSelect();
		},
		onKeydown: function(node, event) {
			if( event.which == 32 ) {
				node.toggleSelect();
				return false;
			}
		},
	});
    $("#btnDeselectAll").click(function(){
        $("#wftree").dynatree("getRoot").visit(function(node){
            node.select(false);
        });
        return false;
    });
    $("#btnSelectAll").click(function(){
        $("#wftree").dynatree("getRoot").visit(function(node){
            node.select(true);
        });
        return false;
    });
});
</script>
<noscript></noscript>
