<?php 
//$client_id,",",$team_id,",",$pricing_type;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\web\JsExpression;
?>
<fieldset><legend class="sr-only">Price Point</legend>
<table class="table table-striped table-hover">
	<thead>
		<tr>
    		<th class="text-left" width="92%"><a href="#" title="Price Point">Price Point</a></th>
			<th>&nbsp;</th>
		</tr>
	</thead>
	<tbody>       
<?php 	       
	foreach ($models as $content){
	?>
		<tr>
		   <td class="text-left" width="80%"><?= $content->price_point; ?></td>
	       <td class="pull-right">
	         	<input class="child_<?=$content->id;?>" type="checkbox" name="team_pricepoint[<?= $team_id ?>][]" id="team_pricepoint_<?=$content->id;?>" value="<?= $content->id; ?>" onChange="selectedParentTeam(<?=$team_id;?>);" aria-label="team pricepoint" >
                        <label for="team_pricepoint_<?=$content->id;?>">&nbsp;&nbsp;<span class="sr-only">team pricepoint</span></label>
	       </td>
	    </tr>
<?php } ?>            
	</tbody>
</table>
</fieldset>
<script>
$('input').customInput();
</script>