<?php
	use yii\helpers\Html;
	// html
?>
<fieldset>
<legend class="sr-only">Export Format</legend>
<div class="row">
	<div class="form-group custom-full-width">
		<input aria-setsize="4" aria-posinset="1" type="radio" name="export" id="pdfexport" value="0" checked /> <label for="pdfexport"> PDF</label>
	</div>
	<div class="form-group custom-full-width">
		<input aria-setsize="4" aria-posinset="2" type="radio" name="export" id="pdfsummaryexport" value="1" /> <label for="pdfsummaryexport"> PDF w/ Supporting Notes</label>
	</div>
	<div class="form-group custom-full-width">
		<input aria-setsize="4" aria-posinset="3" type="radio" name="export" id="excelexport" value="2" /> <label for="excelexport"> Excel</label>
	</div>
	<div class="form-group custom-full-width">
		<input aria-setsize="4" aria-posinset="4" type="radio" name="export" id="excelsummaryexport" value="3" /> <label for="excelsummaryexport"> Excel w/ Supporting Notes</label>
	</div>
</div>
</fieldset>
<script>
	$('input').customInput();
</script>
<noscript></noscript>
