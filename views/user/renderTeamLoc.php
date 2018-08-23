<?php
/* @var $this yii\web\View */
use yii\helpers\Html;
?>
<?php if(!empty($model)) { ?>
	<li class="clear custom-full-width" id="header">
		<input type="text" class="col-sm-12 form-control" id="filterTeamLocs" title="Filter By Team Location" placeholder="Filter List"/>
	</li>
	<?php foreach($model as $teamLocs) { ?>
		<li class="clear custom-full-width" id="teamLocList_<?=$teamLocs->team_id.'_'.$teamLocs->team_loc?>">
			<label title="<?= Html::encode($teamLocs->team->team_name).'-'.Html::encode($teamLocs->teamlocationMaster->team_location_name); ?>" class="pull-left" for="teamLocList_<?=$teamLocs->team_id.'_'.$teamLocs->team_loc?>">
				<span class="sername_div"><?= Html::encode($teamLocs->team->team_name).'-'.Html::encode($teamLocs->teamlocationMaster->team_location_name); ?></span>
			</label>
			<input value="<?= $teamLocs->team_loc ?>" name="teamLocs[<?= $teamLocs->team_id ?>][]" type="hidden">
			<a class="icon-set pull-right" href="javascript:void(0);" onclick="$('li#teamLocList_<?=$teamLocs->team_id.'_'.$teamLocs->team_loc?>').remove();" aria-label="Remove">
				<span class="fa fa-close text-primary" title="Remove"></span>
			</a>
		</li>
	<?php } ?>
<?php } ?>

<script>
$('#filterTeamLocs').keyup(function () {
	var filter = $(this).val();
	$("ul.teamDataToPost li").each(function () {
		if($(this).attr('id')!='header'){
			if ($(this).find('label span.sername_div').html().search(new RegExp(filter, "i")) < 0) {
				$(this).hide();
			} else {
				$(this).show();
			}
		}
	});
});
</script>
<noscript></noscript>
