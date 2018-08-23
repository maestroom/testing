<?php 
use yii\helpers\Html;
// assign data
?>
<input type="hidden" name="isnonbillable" id="isnonbillable" value="<?php echo $data['isnonbillable']; ?>" />
<input type="hidden" name="billing_unit_id" id="billing_unit_id" value="<?php echo $data['billing_unit_id']; ?>" />
<input type="hidden" name="pre_rate" id="pre_rate" value="<?php echo (isset($data['final_rate']) && $data['final_rate']!='')?number_format($data['final_rate'],2,'.',''):'0.00'; ?>" />
<div class="form-group">
<div class="row">
    <div class="col-sm-4">
      <label for="" class="form_label">Rate<span class="require-asterisk">*</span> </label>
    </div>
    <div class="col-sm-6">
      <input type="text" name="temp_rate" aria-required="true" aria-label="rate" id="rate" class="form-control numeric-field-qu negative-key" value="<?php echo (isset($data['final_rate']) && $data['final_rate']!='')?number_format($data['final_rate'],2,'.',''):'0.00'; ?>" <?php echo $data['istieredrate']==1?'disabled="disabled"':''; ?>/>
      </div> <label class="form_label">$</label>
</div>
</div>

<div class="form-group">
<div class="row">
    <div class="col-sm-4">
        <label for="" class="form_label">Quantity<span class="require-asterisk">*</span> </label>
    </div>
    <div class="col-sm-6">
      <input type="text" name="quantity" aria-required="true" aria-label="quantity" id="quantity" class="form-control numeric-field-qu negative-key" value="<?php echo number_format($data['quantity'],3,'.',''); ?>" />
      </div><label class="form_label"><?php echo $data['unit_name']; ?></label>
</div>
</div>


<?php if(isset($data['description']) && $data['description']!=''){ ?>
    <div class="form-group">
        <div class="row">
            <div class="col-sm-4">
              <label for="" class="form_label">Custom Description </label>
            </div>
            <div class="col-sm-8">
                <textarea type="text" name="description" aria-label="description" id="description" class="form-control" rows="5" cols="15"><?php echo html_entity_decode($data['description']); ?></textarea>
            </div>
        </div>
    </div>
<?php } ?>

<div class="form-group">
<div class="row">
    <div class="col-sm-4">
      <label for="" class="form_label">Non-Billable Item </label>
    </div>
    <div class="col-sm-6">
      <input type="checkbox" name="non_billable" aria-label="Non-Billable Item" id="non_billable" style="margin-top:10px;" value="<?php echo $data['isnonbillable']==1?2:''; ?>" <?php echo $data['isnonbillable']==1?"checked":""; ?> aria-label="Non-Billable Item"/>
      <label for="non_billable"><span class="sr-only">Non-Billable Item</span></label>
    </div>
</div>
</div>

<script>
	$('input').customInput();
</script>
<noscript></noscript>
