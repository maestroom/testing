<?php
/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\Select2;
?>
<div class="right-main-container slide-open" id="maincontainer">
	<fieldset class="two-cols-fieldset workflow-management">
		<div class="administration-main-cols">
			<div class="administration-lt-cols pull-left">
				<button title="Expand/Collapse" aria-label="Expand or Collapse" id="controlbtn" class="slide-control-btn" onclick="WorkflowToggle();">
					<span>&nbsp;</span>
				</button>
				<input type="hidden" value="<?= $TEAM_ID;?>" id="team_id">
				<ul>
					<li><a href="javascript:void(0);" title="Teams" class="admin-main-title"><em title="Teams" class="fa fa-folder-open text-danger"></em>Teams</a>
					<div class="select-items-dropdown">
					<?php
					if(!empty($teamList)){ foreach ($teamList as $team){
							$team_dropdown[$team->id] = Html::encode($team->team_name);
						}
						echo Select2::widget([
							'name' => 'select_box',
							'attribute' => 'select_box',
							'data' => $team_dropdown,
							'options' => ['prompt' => 'Select Team','class' => 'form-control','onchange'=>'javascript:updateTeamSelect(this.value);'],
							/*'pluginOptions' => [
							  'allowClear' => true
							]*/
							]);
						}
					 ?>	
				</div>
						<div class="left-dropdown-list">
							<div class="admin-left-module-list">
							<ul class="sub-links">
				<?php if(!empty($teamList)){ foreach ($teamList as $team){?>
				 <li class="active"><a href="javascript:updateTeamSelect(<?=$team->id?>);" title="<?=Html::encode($team->team_name); ?>"><em title="<?=Html::encode($team->team_name); ?>" class="fa fa-sitemap text-danger"></em> <?=Html::encode($team->team_name); ?></a></li>
				 <?php }}?>
				</ul>
						</div></div></li>
				</ul>
			</div>
			<div class="administration-rt-cols pull-right" id="admin_right"></div>
		</div>
	</fieldset>
</div>
<script type="text/javascript">
TeamServide(<?= $TEAM_ID;?>);
</script>
<noscript></noscript>
