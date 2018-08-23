<?php
use yii\helpers\Html;
?>
<style>
.custom-checkbox{
float: right;left: -591px;margin-top: -4px;
}
</style>
<fieldset class="one-cols-fieldset">
    <div class="create-form">
            <div class="row buildexpressin-row">
				<div class="form-group col-sm-12 form-group">
                    <div class="custom-full-width">
                        <span id="groupbyfilter" class="tag-header-black">Group By:</span>
                        <span>
                            <label for="grp" title="Group By"><span class="sr-only">Group By</span></label>
                            <input type="checkbox" name="grp" aria-labelledby="groupbyfilter" id="grp" <?php if(isset($post_data['grp']) && $post_data['grp']==1) { echo 'checked="checked"';}else{ if($model->is_grp==1){echo 'checked="checked"';}}?>>
                        </span>
                    </div>
                </div>
            </div>  
            <h3>Where Condition:</h3> 
            <div class="row buildexpressin-row form-group">
				<div class="col-sm-12">
					<label><a href="javascript:void(0);" class="tag-header-black" title="Operators">Select Operators</a></label>
					<div class="opt-buttonset">
					<?php foreach(Yii::$app->params['exp'] as $exp){ if($exp==""){ continue; }?>
						<button value="<?=$exp?>" onclick="addOp(this);" type="button"><?=$exp?></button>
					<?php }?>
					</div>
				</div>
			</div>
            <div class="row buildexpressin-row">
				<div class="form-group col-sm-12 form-group">
                    <label for="formula"><a href="javascript:void(0);" class="tag-header-black" title="Formula">Build Where Condition</a></label>
					<textarea id="formula" class="form-control" name="formula"  rows="6" aria-required="true" placeholder="Build Where Condition"><?php if(isset($post_data['filter']) && $post_data['filter']!="") { echo $post_data['filter'];}else{ echo $model->report_condition;}?></textarea>
					<div class="help-block"></div><br />
				</div>
			</div>
    </div>
</fieldset>   
<script>
    $('input').customInput();

function addOp(obj){
    var field_name='<?=$post_data["field_name"]?>';
    var appendstr="";
    if($(obj).val() == 'AND' || $(obj).val() == 'OR'){
        appendstr=$(obj).val();
    }else{
        appendstr=field_name + ' ' + $(obj).val();
    }
	var formula = $('#formula').val() + ' ' + appendstr;
	$('#formula').val(formula);
}
</script>
<noscript></noscript>