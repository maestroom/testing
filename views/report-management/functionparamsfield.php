<?php
use yii\helpers\Html;
if(!empty($tableFieldParamsList)){
			foreach($tableFieldParamsList as $fnparams_inner){?>
					<li data-fieldname="<?=$fnparams_inner->field_name?>" data-fieldtype="<?=$fnparams_inner->reportsFieldType->field_type?>">
						<div class="pull-left">
							<input data-tablename="<?=$fnparams_inner->reportsTables->table_name?>" data-fieldname="<?=$fnparams_inner->field_name?>" data-fieldid="<?=$fnparams_inner->id?>" data-fieldtype="<?=$fnparams_inner->reportsFieldType->field_type?>" id="<?=$fnparams_inner->reportsTables->table_name.'.'.$fnparams_inner->field_name?>" name="<?=$fnparams_inner->reportsTables->table_name.'.'.$fnparams_inner->field_name?>" value="<?=$fnparams_inner->reportsTables->table_name.'.'.$fnparams_inner->field_name?>" type="checkbox" class="inner_check">
							<label class="form_label chkbox-global-design-left" for="<?=$fnparams_inner->reportsTables->table_name.'.'.$fnparams_inner->field_name?>"><?=$fnparams_inner->field_name." <b>(".$fnparams_inner->reportsFieldType->field_type.")</b>";?></label>
						</div>
						<!--<span class="pull-left"><?=$fnparams_inner->field_name." <b>(".$fnparams_inner->reportsFieldType->field_type.")</b>";?></span>-->
						<div class="clearfix"></div>
					</li>
				<?php }?>
<?php }?>
