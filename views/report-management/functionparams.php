<?php
use yii\helpers\Html;
if(!empty($tableList)){?>
	<fieldset>
	<legend class="sr-only">Select Field Params</legend>
	<ul class="email-recipients-list">
	<?php foreach($tableList as $id=>$table_name){
		
	?>
		<li id="<?=$table_name?>" class="clearfix">
			<div class="pull-left">
                            <input id="table_<?=$table_name?>" name="table_<?=$table_name?>" value="<?=$table_name?>" type="checkbox" class="main_tables" onClick="inner_checkall(this);" >
                            <label class="form_label chkbox-global-design-left" for="table_<?=$table_name?>"><?=$table_name?></label>
			</div>
			<!--<span class="pull-left"><?php //echo $table_name?></span>-->
			<div class="clearfix"></div>
			<ul class="email-recipients-list" id="inner_<?= $table_name ?>" style="display:none;">
			<?php /*
			$tableFieldParamsList = ReportsFields::find()->joinWith(['reportsTables','reportsFieldType'])->select(['tbl_reports_fields.id','tbl_reports_fields.report_table_id','tbl_reports_fields.reports_field_type_id','tbl_reports_tables.table_name','tbl_reports_fields.field_name','tbl_reports_field_type.field_type'])->where(['table_name'=>$table_name])->orderBy('tbl_reports_tables.table_name ASC, tbl_reports_fields.id ASC')->all();
			foreach($tableFieldParamsList as $fnparams_inner){
				if($table_name==$fnparams_inner->reportsTables->table_name){?>
                                    <li data-fieldname="<?=$fnparams_inner->field_name?>" data-fieldtype="<?=$fnparams_inner->reportsFieldType->field_type?>">
                                        <div class="pull-left">
                                                <input data-tablename="<?=$table_name?>" data-fieldname="<?=$fnparams_inner->field_name?>" data-fieldid="<?=$fnparams_inner->id?>" data-fieldtype="<?=$fnparams_inner->reportsFieldType->field_type?>" id="<?=$table_name.'.'.$fnparams_inner->field_name?>" name="<?=$table_name.'.'.$fnparams_inner->field_name?>" value="<?=$table_name.'.'.$fnparams_inner->field_name?>" type="checkbox" class="inner_check">
                                                <label class="form_label" for="<?=$table_name.'.'.$fnparams_inner->field_name?>"></label>
                                        </div>
                                        <span class="pull-left"><?=$fnparams_inner->field_name." <b>(".$fnparams_inner->reportsFieldType->field_type.")</b>";?></span>
                                        <div class="clearfix"></div>
                                    </li>
				<?php }}*/ ?>
			</ul>
		</li>
	<?php 
	}?>
</ul>
</fieldset>
<?php }?>
<script>
function inner_checkall(obj){
	$('#inner_'+$(obj).val()).toggle();
	if($('#inner_'+$(obj).val()).is(":visible")){
		$('#inner_'+$(obj).val()).html('<li><em class="fa fa-spinner fa-pulse fa-2x"></em><span class="sr-only">Loading...</span></li>');
		$.ajax({
			url:baseUrl+'report-management/functionparamsfield&table_name='+$(obj).val(),
			type:'get',
			beforeSend:function (data) {},
			success:function(response){
					$('#inner_'+$(obj).val()).html(response);
					$('#inner_'+$(obj).val()).find('input').customInput();
			}
		});
	}
}
</script>
<noscript></noscript>
