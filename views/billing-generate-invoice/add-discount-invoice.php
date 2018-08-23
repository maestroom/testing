<?php 
	use yii\helpers\Html;
?>
<input type="hidden" name="billing_unit_id" id="billing_unit_id" value="<?php echo $data['billing_unit_id']; ?>" />
<div class="form-group">
    <div class="row">
        <div class="col-sm-5">
            <label for="" class="form_label">Discount<span class="require-asterisk">*</span> </label>
        </div>
        <div class="col-sm-6">
            <input aria-required="true" aria-label="discount" type="text" name="discount" id="discount" class="form-control" maxlength="4" value="<?php echo $data['discount']; ?>" />
        </div><label class="form_label">%</label>
    </div>
</div>
<div class="form-group">
    <div class="row">
        <div class="col-sm-5">
            <label for="" class="form_label">Price Adjustment Reason </label>
        </div>
        <div class="col-sm-7">
            <textarea type="text" aria-label="Price Adjustment Reason" name="discount_reason" class="form-control" id="discount_reason" cols="10" rows="5"><?php echo $data['discount_reason']; ?></textarea>
        </div>
    </div>
</div>
<script>
$(document).ready(function(){
	/**
	 * Discount Validation (Only digits are allow)
	 */
	$("#discount").keydown(function (e) {
		if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
			(e.keyCode == 65 && ( e.ctrlKey === true || e.metaKey === true ) ) || 
			(e.keyCode >= 35 && e.keyCode <= 40)) {
				 return;
		}
		if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
			e.preventDefault();
        }
        var text = $(this).val();
			if ((text.indexOf('.') != -1) &&
			(text.substring(text.indexOf('.')).length > 2) &&
			(e.keyCode != 0 && e.keyCode != 8 && e.keyCode != 9 && e.keyCode != 37 && e.keyCode != 39) &&
			($(this)[0].selectionStart >= text.length - 2)) {
				e.preventDefault();
			}
    });
    $(document).on("paste", '#discount',function (e) {
		var text = e.originalEvent.clipboardData.getData('Text');
		if ($.isNumeric(text)) {
			if ((text.substring(text.indexOf('.')).length > 3) && (text.indexOf('.') > -1)) {
				e.preventDefault();
				$(this).val(text.substring(0, text.indexOf('.') + 3));
		   }
		}
		else {
				e.preventDefault();
			 }
		});
});
</script>
<noscript></noscript>
