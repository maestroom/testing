<?php
/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Price List';

?>
<div class="right-main-container slide-open" id="maincontainer">
	<fieldset class="two-cols-fieldset">
		<div class="administration-main-cols">
			<div class="administration-lt-cols pull-left">
				<button id="controlbtn" aria-label="Expand or Collapse" class="slide-control-btn" title="Expand/Collapse" onclick="PriceListToggle();">
					<span>&nbsp;</span>
				</button>
				<ul>
					<li>
						<a class="admin-main-title" href="javascript:void(0);"><em title="Teams" class="fa fa-folder-open text-danger"></em>Teams</a>
						<input type="hidden" id="team_id" value="<?= $team_id; ?>"/>
						<div class="manage-admin-left-module-list">
							<ul class="sub-links" id="team_list">
								<?php if(!empty($teamList)){ foreach ($teamList as $team){ ?>
								 <li class="teamlist <?= $team_id==$team->id?'active':''?>" data-id="<?=$team->id?>">
								 	<a title="<?=Html::encode($team->team_name); ?>" href="javascript:loadTeamPricingBilling(<?=$team->id?>);"><em class="fa fa-sitemap <?php if($team->team_type==1) {?>text-gray<?php } else {?>text-danger<?php }?>" title="<?=$team->team_name?>"></em> <?=Html::encode($team->team_name); ?></a>
								 </li>
								 <?php }	
								 } ?>
							</ul>
						</div>
					</li>
				</ul>
			</div>
			<div class="administration-rt-cols pull-right" id="admin_right">
				<?= $this->render('index-team-pricing',['filter_type'=>$filter_type,'filterWidgetOption'=>$filterWidgetOption,'searchModel' => $searchModel,'dataProvider' => $dataProvider, 'team_id' => $team_id]); ?>
			</div>
		</div>
	</fieldset>
</div>
