<?php 
use yii\helpers\Html;
$joinType = Yii::$app->params['join_type'];
$lookupType = array(1=>'Table',2=>'Custom');
?>
<div class="table-responsive">
    <table class="table table-striped table-hover">
	<?php if($keyValue == 'fields'){ ?>
		<thead class="tbl-field-header">    
			<tr>
				<th class="first-th">&nbsp;</th>
				<th align="left" class="text-left"><a href="javascript:void(0);" title="Field Source" class="tag-header-black">Field Source</a></th>
				<th align="left" class="text-left"><a href="javascript:void(0);" title="Field Display Name" class="tag-header-black">Field Display Name</a></th>
				<th class="th-related"><a href="javascript:void(0);" title="Related?" class="tag-header-black">Related?</a></th>
				<th class="third-th"><a href="javascript:void(0);" title="Actions" class="tag-header-black">Actions</a></th>
			</tr>
		</thead>
		<tbody>
			<?php if(!empty($field_list)){
				foreach($field_list as $key => $field){
					
					if(isset($post_data['ReportsFields']['field_display_name']) && array_search($field, $post_data['ReportsFields']['field_name']) !== false){
						$keyIndex = array_search($field, $post_data['ReportsFields']['field_name']);
						$field_display_name = $post_data['ReportsFields']['field_display_name'][$keyIndex];
					} else {
						$field_display_name = ucwords(str_replace("_"," ",$field));
					}
			?>
					<tr>
						<td class="first-td">&nbsp;</td>
						<td align="left" class="text-left">
							<?=$field?>
							<input type="hidden" value="<?=$field?>" name="ReportsFields[field_name][]" id="field_name_<?=$field?>" />
						</td>
						<td align="left" class="text-left">
							<div class="field_display_name_<?=$field?>"><?=$field_display_name;?></div>
							<input type="hidden" value="<?=$field_display_name?>" name="ReportsFields[field_display_name][]" class="field_display_name_<?=$field?>" />
							<input type="hidden" value="<?=isset($post_data['ReportsFields']['id'][$field])?$post_data['ReportsFields']['id'][$field]:0;?>" name="ReportsFields[id][<?=$field?>]" class="field_id_<?=$field?>" />
						</td>
						<td class="td-related" align="center" id="related_tr_<?=$field?>">
							<div id="related_<?=$field?>" style="display:<?=(isset($post_data['related_table_name'][$field]) && $post_data['related_table_name'][$field]!='')?'block':'none';?>"><em class="fa fa-sitemap text-danger"></em></div>
<?php 
							if(!empty($post_data) && !empty($post_data['related_table_name'][$field]))
							{
									foreach($post_data['related_table_name'][$field] as $key => $val)
									{
										if(isset($val) && $val != "")
										{
?>
											<input type="hidden" name="related_table_name[<?php echo $field;?>][<?php echo $key;?>]" id="related_table_name_<?php echo $field;?>_<?php echo $key;?>" value="<?php echo $val;?>" />
											<input type="hidden" name="related_base_table[<?php echo $field;?>][<?php echo $key;?>]" id="related_base_table_<?php echo $field;?>_<?php echo $key;?>" value="<?php echo $post_data['related_base_table'][$field][$key];?>" />
											<input type="hidden" name="related_field_name[<?php echo $field;?>][<?php echo $key;?>]" id="related_field_name_<?php echo $field;?>_<?php echo $key;?>" value="<?php echo $post_data['related_field_name'][$field][$key];?>" />
											<input type="hidden" name="related_type[<?php echo $field;?>][<?php echo $key;?>]" id="related_type_<?php echo $field;?>_<?php echo $key;?>" value="<?php echo $post_data['related_type'][$field][$key];?>" />
<?php
										}
									}
							}
?>
							
							<div id="lookup_<?=$field?>" style="display:<?=(isset($post_data['lookup_filter_table'][$field]) && $post_data['lookup_filter_table'][$field]!='')?'block':'none';?>"><em class="fa fa-search text-danger"></em></div>
							<input type="hidden" name="lookup_type[<?=$field?>]" id="lookup_type_<?=$field?>" value="<?=isset($post_data['lookup_type'][$field])?$post_data['lookup_type'][$field]:'';?>" />
							<input type="hidden" name="lookup_filter_table[<?=$field?>]" id="lookup_filter_table_<?=$field?>" value="<?=isset($post_data['lookup_filter_table'][$field])?$post_data['lookup_filter_table'][$field]:'';?>" />
							<input type="hidden" name="lookup_filter_field[<?=$field?>]" id="lookup_filter_field_<?=$field?>" value="<?=isset($post_data['lookup_filter_field'][$field])?$post_data['lookup_filter_field'][$field]:'';?>" />
							<input type="hidden" name="lookup_table[<?=$field?>]" id="lookup_table_<?=$field?>" value="<?=isset($post_data['lookup_table'][$field])?$post_data['lookup_table'][$field]:'';?>" />
							<input type="hidden" name="lookup_field[<?=$field?>]" id="lookup_field_<?=$field?>" value="<?=isset($post_data['lookup_field'][$field])?$post_data['lookup_field'][$field]:'';?>" />
							<input type="hidden" name="lookup_custom[<?=$field?>]" id="lookup_custom_<?=$field?>" value='<?=isset($post_data['lookup_custom'][$field])?$post_data['lookup_custom'][$field]:'';?>' />
							
							<input type="hidden" name="lookup_custom_field[<?=$field?>]" id="lookup_custom_field_<?=$field?>" value='<?=isset($post_data['lookup_custom_field'][$field])?$post_data['lookup_custom_field'][$field]:'';?>' />
							
							<input type="hidden" name="lookup_field_separator[<?=$field?>]" id="lookup_field_separator_<?=$field?>" value='<?=isset($post_data['lookup_field_separator'][$field])?$post_data['lookup_field_separator'][$field]:'';?>' />
						</td>
						<td align="center" class="third-td"><a aria-label="Edit" href="javascript:EditReportFieldDisplayName('<?=$field?>','<?=ucwords(str_replace("_"," ",$field))?>');" class="icon-fa" title="Edit" aria-label="Edit Report Field Display Name"><em class="fa fa-pencil text-primary"></em></a></td>
					</tr>
				<?php }
			} ?>
		</tbody>
	<?php } else if($keyValue == 'field_relationship'){ ?>
		<tbody>
		<?php 
			if(!empty($relations)){
				foreach($relations as $keyField => $values){ 
					foreach($values as $key => $single){
		?>
				<tr class="table_relation_expand_tr">	
					<td class="first-td">&nbsp;</td>
					<td align="left" class="report_relation_field" colspan="3">
						<?= $single['primary_table_name'].'.'.$single['primary_field_name'].'->'.$single['secondary_table_name'].'.'.$single['secondary_field_name'].'&nbsp;&nbsp;<strong>('.$joinType[$single['join_type']].')</strong>'; ?>
						<input type="hidden" value="<?=isset($post_data['ReportsFieldsRelationships']['relation_id'][$single['primary_field_name']][$key])?$post_data['ReportsFieldsRelationships']['relation_id'][$single['primary_field_name']][$key]:0;?>" name="ReportsFieldsRelationships[relation_id][<?=$single['primary_field_name']?>][<?=$key?>]" class="all_relationships" />
						<input type="hidden" value="<?=$single['primary_table_name']?>" id="primary_table_name_<?=$keyField?>_<?=$key?>" />
						<input type="hidden" value="<?=$single['secondary_table_name']?>" id="secondary_table_name_<?=$keyField?>_<?=$key?>" />
						<input type="hidden" value="<?=$single['secondary_field_name']?>" id="secondary_field_name_<?=$keyField?>_<?=$key?>" />
						<input type="hidden" value="<?=$single['primary_field_name']?>" id="primary_field_name_<?=$keyField?>_<?=$key?>" />
						<input type="hidden" value="<?=$single['join_type']?>" id="join_type_<?=$keyField?>_<?=$key?>" />
					</td>
					<td class="report_field_action third-td" align="center">
						<?= Html::a('<em class="fa fa-close text-primary"></em>', 'javascript:void(0);', [
								'title' => Yii::t('yii', 'Delete'),
                                                                'aria-label' => Yii::t ( 'yii', 'Delete' ),
								'class' => 'icon-set',
								'onclick'=>'javascript:remove_field_relationship("'.$keyField.'","'.$key.'");'
						]); ?>
					</td>
		<?php 	}	
			  }
			}
		?>
		</tbody>
	<?php } else if($keyValue == 'field_lookup'){ ?>
		<tbody>
		<?php 
			if(!empty($lookup)){
				foreach($lookup as $keyField => $single){ 
					if($single['lookup_type']==1){
		?>
				<tr class="table_relation_expand_tr">	
					<td class="first-td">&nbsp;</td>
					<td align="left" class="report_relation_field" colspan="3">
						<?php 
							if(isset($single['lookup_relationship_table']) && $single['lookup_relationship_table']){
								echo $single['lookup_filter_table'].'.'.$single['lookup_filter_field'].'->'.$single['lookup_relationship_table'].'.'.$single['lookup_relationship_field'];
							}else{
								echo $single['lookup_filter_table'].'.'.$single['lookup_filter_field'];//.'->'.$single['lookup_table'].'.'.$single['lookup_field']; 
							}
							//.'&nbsp;&nbsp;<strong>('.$lookupType[$single['lookup_type']].')</strong>';
							if(count(explode(',',$single['lookup_field'])) > 1){
								$sep=((isset($single['lookup_field_separator']) && $single['lookup_field_separator']!="")?$single['lookup_field_separator']:' ');
								echo " <strong>(display: ".implode($sep,explode(',',$single['lookup_field'])).") (Table)</strong>";
							}else{
								echo " <strong>(display: ".$single['lookup_table'].".".$single['lookup_field'].") (Table)</strong>";
							}
						?>
						<input type="hidden" value="<?= isset($post_data['ReportsFieldsRelationships']['lookup_id'][$single['lookup_filter_field']])?$post_data['ReportsFieldsRelationships']['lookup_id'][$single['lookup_filter_field']]:0;?>" name="ReportsFieldsRelationships[lookup_id][<?=$single['lookup_filter_field']?>]" />
					</td>
					<td class="report_field_action third-td" align="center">
						<?= Html::a('<em class="fa fa-close text-primary"></em>', 'javascript:void(0);', [
								'title' => Yii::t('yii', 'Delete'),
                                                                'aria-label' => Yii::t ( 'yii', 'Delete' ),
								'class' => 'icon-set',
								'onclick'=>'javascript:remove_field_lookup("'.$keyField.'");'
						]); ?>
					</td>
		<?php }else if($single['lookup_type']==2){?>
				<tr class="table_relation_expand_tr">	
					<td class="first-td">&nbsp;</td>
					<td align="left" class="report_relation_field" colspan="3">
						<?= $single['lookup_filter_table'].'.'.$single['lookup_filter_field'].'&nbsp;&nbsp;<strong>('.$lookupType[$single['lookup_type']].')</strong>'; ?>
						<?php $data=json_decode($single['lookup_custom'],true);
							if(!empty($data)){
								foreach($data as $da){
									echo "<br>";
									echo $da['field_value']." => ".$da['lookup_value'];
									
								}
							}
						?>
						<input type="hidden" value="<?= isset($post_data['ReportsFieldsRelationships']['lookup_id'][$single['lookup_filter_field']])?$post_data['ReportsFieldsRelationships']['lookup_id'][$single['lookup_filter_field']]:0;?>" name="ReportsFieldsRelationships[lookup_id][<?=$single['lookup_filter_field']?>]" />
					</td>
					<td class="report_field_action third-td" align="center">
						<?= Html::a('<em class="fa fa-close text-primary"></em>', 'javascript:void(0);', [
								'title' => Yii::t('yii', 'Delete'),
                                                                'aria-label' => Yii::t ( 'yii', 'Delete' ),
								'class' => 'icon-set',
								'onclick'=>'javascript:remove_field_lookup("'.$keyField.'");'
						]); ?>
					</td>
		<?php } else if($single['lookup_type']==3){ ?>
			<tr class="table_relation_expand_tr">	
					<td class="first-td">&nbsp;</td>
					<td align="left" class="report_relation_field" colspan="3">
						<?= $single['lookup_filter_table'].'.'.$single['lookup_filter_field']; ?>
						
						<?php if(count(explode(',',$single['lookup_custom_field'])) > 1){ 
							$sep=((isset($single['lookup_field_separator']) && $single['lookup_field_separator']!="")?$single['lookup_field_separator']:' ');
								echo " <strong>(display: ".implode($sep,explode(',',$single['lookup_custom_field'])).") (Field)</strong>";
							}else{
								echo " <strong>(display: ".$single['lookup_custom_field'].") (Field)</strong>";
							}
						?>
						<input type="hidden" value="<?= isset($post_data['ReportsFieldsRelationships']['lookup_id'][$single['lookup_filter_field']])?$post_data['ReportsFieldsRelationships']['lookup_id'][$single['lookup_filter_field']]:0;?>" name="ReportsFieldsRelationships[lookup_id][<?=$single['lookup_filter_field']?>]" />
					</td>
					<td class="report_field_action third-td" align="center">
						<?= Html::a('<em class="fa fa-close text-primary"></em>', 'javascript:void(0);', [
								'title' => Yii::t('yii', 'Delete'),
                                                                'aria-label' => Yii::t ( 'yii', 'Delete' ),
								'class' => 'icon-set',
								'onclick'=>'javascript:remove_field_lookup("'.$keyField.'");'
						]); ?>
					</td>
		<?php } } } ?>
		</tbody>
	<?php } ?>	
	</table>
</div>
<script>
function EditReportFieldDisplayName(field_name, actual_value){
	var field_display_name = $('input.field_display_name_'+field_name).val();
        var field_max_length = "<?php echo $model_field_length['field_display_name'];?>";       
	if($('body').find('#edit-table-field-name').length == 0){
		$('body').append('<div class="dialog" id="edit-table-field-name" title="Edit Field Display Name"><div class="form-group"><div class="row input-field"><div class="col-md-3"><label for="reportsfieldtype-field_type" class="form_label">Display Names</label></div><div class="col-md-7"><input type="text" name="ReportsFieldType[field_type]" class="form-control" id="field-reportsfieldtype-label" value="'+field_display_name+'" maxlength="'+field_max_length+'"><div class="help-block"></div></div></div></div></div>');
	}
	$('#edit-table-field-name').dialog({ 
		modal: true,
		width:'40em',
		height:302,
		title:'Edit Field Display Name',
		create: function(event, ui) { 						  
			 $('.ui-dialog-titlebar-close').append('<span class="ui-button-icon-primary ui-icon"></span>');
                         $('.ui-dialog-titlebar-close').attr("title", "Close");
                        $('.ui-dialog-titlebar-close').attr("aria-label", "Close");
		},
		close:function(){
			$(this).dialog('destroy').remove();
		},
		buttons: [
					{ 
					  text: "Default", 
					  "class": 'btn btn-primary pull-left',
					  "title": 'Default',
					  click: function () { 
						  $('#field-reportsfieldtype-label').val(actual_value);
					  } 
				  },
					{ 
					  text: "Cancel", 
					  "class": 'btn btn-primary',
					  "title": 'Cancel',
					  click: function () { 
						  $(this).dialog('destroy').remove();
					  } 
				  },
				{ 
					text: "Update", 
					"class": 'btn btn-primary',
					"title": 'Update',
					click: function () {
						var display_name = $('#field-reportsfieldtype-label').val();
						$('.field_display_name_'+field_name).html(display_name);
						$('.field_display_name_'+field_name).val(display_name);
						/*change form flag*/
 						$("#ReportsTables #is_change_form").val('1'); $("#is_change_form_main").val('1');
						$(this).dialog('destroy').remove();											  
					}
				}
		]
	});			
}

function remove_field_relationship(primary_tbl_field, key){
	if(confirm("Are you sure, You want to delete relationship for field:"+primary_tbl_field+"?")){
		var primary_table_name=$('#primary_table_name_'+primary_tbl_field+'_'+key).val();
		var secondary_table_name=$('#secondary_table_name_'+primary_tbl_field+'_'+key).val();
		var secondary_field_name=$('#secondary_field_name_'+primary_tbl_field+'_'+key).val();
		var primary_field_name=$('#primary_field_name_'+primary_tbl_field+'_'+key).val();
		var type=$('#join_type_'+primary_tbl_field+'_'+key).val();
		jQuery.ajax({
		   url: baseUrl +'/report-management/check-relationshipinuse',
		   type: 'post',
		   data:{'primary_table_name':primary_table_name,'secondary_table_name':secondary_table_name,'secondary_field_name':secondary_field_name,'primary_field_name':primary_field_name,'type':type},
		   beforeSend:function (data) {showLoader();},
		   success: function (response) {
			   var data = JSON.parse(response);
				if(data.inuse == 0){
					$('#related_'+primary_tbl_field+'_'+key).hide();
					$('#related_table_name_'+primary_tbl_field+'_'+key).val('');
					$('#related_field_name_'+primary_tbl_field+'_'+key).val('');
					$('#related_type_'+primary_tbl_field+'_'+key).val('');
					
					jQuery.ajax({
					   url: baseUrl +'/report-management/nextstep-field-relationship&table_name='+$('#table_name').val()+'&table_display_name='+$('#reportstables-table_display_name').val(),
					   type: 'post',
					   data:$('form#ReportsTables').serialize(),
					   beforeSend:function (data) {showLoader();},
					   success: function (data) {
						   hideLoader();
						   $("#main_fields").hide();
						   $("#btn_show_prev").show();
						   jQuery('#is_change_form').val('1');
						   jQuery('#is_change_form_main').val('1');
						   jQuery('#nextstep-fieldrelationship').html(data);	    	   
					   } 
					});
				}else{
					hideLoader();
					alert("Relationship is already in use, Please remove it from report type to perform this action.");
					return false;
				}
		   }
		});
		
	}
}
function remove_field_lookup(primary_tbl_field){
	if(confirm("Are you sure, You want to delete lookup for field:"+primary_tbl_field+"?")){
		$('#lookup_'+primary_tbl_field).show();
		$('#lookup_type_'+primary_tbl_field).val('');
		$('#lookup_filter_table_'+primary_tbl_field).val('');
		$('#lookup_filter_field_'+primary_tbl_field).val('');
		$('#lookup_table_'+primary_tbl_field).val('');
		$('#lookup_field_'+primary_tbl_field).val('');
		$('#lookup_custom_'+primary_tbl_field).val('');
		$('#lookup_custom_field_'+primary_tbl_field).val('');
		jQuery.ajax({
		   url: baseUrl +'/report-management/nextstep-field-relationship&table_name='+$('#table_name').val()+'&table_display_name='+$('#reportstables-table_display_name').val(),
		   type: 'post',
		   data:$('form#ReportsTables').serialize(),
		   beforeSend:function (data) {showLoader();},
		   success: function (data) {
			   hideLoader();
			   $("#main_fields").hide();
			   $("#btn_show_prev").show();
			   $('#is_change_form').val('1'); // change flag to 1
			   $('#is_change_form_main').val('1'); // chagne flag to 1
			   jQuery('#nextstep-fieldrelationship').html(data);	    	   
		   } 
		});
	}
}
</script>		
<noscript></noscript>
