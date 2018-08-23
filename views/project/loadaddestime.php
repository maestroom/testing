<?php 
use yii\helpers\Url;
?>
 <div class="SectionMiddle form_top">
   <div class="FormLeftSpace" >
   <div id="EvidenceList">
   		<form enctype="multipart/form-data" id="attch-form" action="<?=Url::to(['task/addestime']);?>" method="post" onsubmit="return false;" autocomplete="off">
   			<div class="text_field_bx row">
			    	<div class="col-sm-4"><label class="form_label">Estimated Time (Hours)</label></div>
					<div class="col-sm-6">
			   			<input type="text" value="" id="estime" class="numeric-field-qu integer form-control" maxlength="2" autofocus>
						<input type="hidden" value="<?=$stask_id?>" id="stask_id">
					</div>
			</div>
		</form>
	</div>
	</div>
</div>
