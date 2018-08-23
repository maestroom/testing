<?php
use yii\helpers\Html;	
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\web\Session;
use app\models\User;
?>
<div class="row">
  <div class="col-md-12">
      <h1 id="page-title" role="heading" class="page-header"> <em class="fa fa-cogs" title="Today's Activity"></em><a href="javascript:void(0);" title="Today's Activity" class="tag-header-red"> Today's Activity </a></h1>
  </div>
</div>
<div class="row">
<div class="col-xs-12 col-sm-12 col-md-12 single-cols-container">
		<input type="hidden" id="activity_offset" value="0">
					<div class="tab-inner-fix">

						<div id="kv-grid-demo" class="grid-view">
							<div id="kv-grid-demo-container" class="kv-grid-container"
								style="overflow: auto;">
								<input type="hidden" value="0" id="noactivities" />
								<div  id="activity-log-dynamic"></div>
							</div>
						</div>
					</div>
</div>
</div>
<script>
$(document).ready(function() {
	changeTodaysActivity();
});
</script>