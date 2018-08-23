<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\grid\GridView;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $model app\models\ReportsFieldCalculations */
/* @var $form yii\widgets\ActiveForm */

$joinType=Yii::$app->params['join_type'];
$report_type_id = $model->id;
//$this->registerJs("$('a.icon-set').tooltip([{html:true}])");
?>
<div class="table-responsive">
    <table class="table table-striped table-hover">
		<?php if($tables[$index]['id'] > 0){?>
		<thead class="tbl-field-header">     
			<th class="first-th">&nbsp;</th>  
			<th align="left" class="text-left">Field Display Name</th>
			<th align="left" class="text-left">Field Source</th>
			<th class="th-related">Related?</th>
			<th class="third-th">Actions</th>
		</thead>
		<?php }?>
        <?php if(isset($dataprovider)){ $counter = 1;?>           
			<?php
			foreach($dataprovider as $key => $single) 
			{ 
				//echo "<pre>",print_r($report_typefield_data),print_r($field_relationships),
				//echo "<pre>",print_r($single),"</pre>";
				?>
				<?php /**/?>
					<tr class="field_raw_<?=$single['id']?> table_<?php echo $single['reportsTables']['table_name'];?>">
						<td class="first-td">&nbsp;</td>  
						<td align="left" class="text-left">
							<span class="display_name_<?=$unique_id?>" data-index="<?=$key?>" data-tbl_name="<?=$single['reportsTables']['table_name'];?>">
								<?php echo $single['field_display_name']; ?>
							</span>
							<?php if( $single['field_name']=='cal') {?>
							<input type="hidden" name="field_calculation[]" class="report_field_calculation" value="<?php echo $single['id']; ?>" />
							<?php } else {?>
							<input type='hidden' name='field_lists[<?= $single['reportsTables']['id']; ?>][]' class='fieldsList' value="<?= $single['id']?>" />
							<input type='hidden' name='field_lists_relationship[<?= $single['id']?>]' class='fieldsList_relation' value="<?= $index!=0 && isset($field_relationships[$single['id']])?$field_relationships[$single['id']]:0; ?>" />
							<input type='hidden' id="reportypefilter_<?= $single['id']?>" name='field_filter[<?= $single['reportsTables']['id']; ?>][]' class='fieldsfilterList' value="<?= $single['filter']?>" />
							<input type='hidden' id="reportypegrp_<?= $single['id']?>" name='field_grp[<?= $single['reportsTables']['id']; ?>][]' class='fieldsgrpList' value="<?= $single['grp']?>" />
							<?php }?>
						</td>
						<td align="left" class="text-left" id="field_name_<?= $single['id']?>">                    
							<?php if( $single['field_name']!='cal') {echo $single['field_name']; } ?>                    
						</td>
						<td class="td-related" align="center" >
							
							<?php
								if(!empty($single['reportsFieldsRelationships'])){
									$has_relation="N";
									$has_lookup="N";
									foreach($single['reportsFieldsRelationships'] as $reportsFieldsRelationships){
										if($reportsFieldsRelationships['rela_type']==0){
											$title.=$single['reportsTables']['table_name'].'.'.$single['field_name'].'->'.$reportsFieldsRelationships['rela_table'].'.'.$reportsFieldsRelationships['rela_field'].'('.$reportsFieldsRelationships['rela_join_string'].')&#x0a;';
											$has_relation="Y";
										}else{
											$has_lookup="Y";
										}
									}
									if($has_relation=="Y" && $has_lookup=="Y"){
										echo Html::a('<em class="fa fa-sitemap text-danger"></em>', 'javascript:void(0);', [
											//'title' => Yii::t('yii', $title),
											'title' => 'Relation',
											'class' => 'icon-set',	
										]);
										echo "<a href='javascript:void(0);'  class = 'icon-set' title='Lookup'><em class='fa fa-search text-danger'></em></a>";
									}
									if($has_relation=="Y" && $has_lookup=="N"){
										echo Html::a('<em class="fa fa-sitemap text-danger"></em>', 'javascript:void(0);', [
											//'title' => Yii::t('yii', $title),
											'title' => 'Relation',
											'class' => 'icon-set',	
										]);
									}
									if($has_relation=="N" && $has_lookup=="Y"){
										echo "<a href='javascript:void(0);'  class = 'icon-set' title='Lookup'><em class='fa fa-search text-danger'></em></a>";
									}
									/*else {
										/*$title="";
										foreach($single['reportsFieldsRelationships'] as $reportsFieldsRelationships){
											if($reportsFieldsRelationships['rela_type'] > 0){
												
												//$title.=$single['reportsTables']['table_name'].'.'.$single['field_name'].'->'.$reportsFieldsRelationships['rela_table'].'.'.$reportsFieldsRelationships['rela_field'].'('.$reportsFieldsRelationships['rela_join_string'].')';
											}
										}
										if(isset($single['reportsFieldsRelationships']['reportsFieldsRelationshipsLookups']) && !empty($single['reportsFieldsRelationships']['reportsFieldsRelationshipsLookups'])){ 
											$title=$single['reportsTables']['table_name'].'.'.$single['field_name'].'(Custom)';
											foreach($single['reportsFieldsRelationships']['reportsFieldsRelationshipsLookups'] as $da){
												$title.='&#x0a;'.$da['field_value']."=>".$da['lookup_value'];
											}
										}else{
											$title=$single['reportsTables']['table_name'].'.'.$single['field_name'].'->'.$single['reportsFieldsRelationships']['lookup_table'].'.'.$single['reportsFieldsRelationships']['lookup_field'].'(Table)';
										
										}*/
										//echo "<a href='javascript:void(0);' title='".$title."' class = 'icon-set'><em class='fa fa-search text-danger'></em></a>";
										 /*Html::a('<em class="fa fa-search text-danger"></em>', 'javascript:void(0);', [
											'title' => $title,
											'data-toggle'=>"tooltip",
											'class' => 'icon-set',	
										]);*/	
									/*}*/
								}
							?>                        
						</td>
						<td align="right" class="third-td" style="padding-right:11px!important;">
						<?php
						if($report_type_id == ""){
								$report_type_id = 0;
							}	

						if( $single['field_name']!='cal') {?>
						<a class="icon-set" id="atagfiletr_<?= $single['id']?>" href="javascript:void(0);" title="Filter" aria-label="Filter" data-id="<?= $single['id']?>" onclick="filterfield_pop_up(<?= $single['id']?>,<?=$report_type_id?>);">
							<em class="glyphicon glyphicon-filter <?php if((isset($single['filter']) &&  $single['filter']!="") || (isset($single['grp']) &&  $single['grp']==1)){?>text-danger<?php }else{ ?>text-primary<?php }?>"></em>
						</a>	
						<?php
								}
							echo Html::a('<em class="fa fa-close text-primary"></em>', 'javascript:void(0);', [
								'title' => Yii::t('yii', 'Delete'),
                                                                'aria-label' => Yii::t ( 'yii', 'Delete' ),
								'class' => 'icon-set',
								'data-id'=>(isset($single['id'])?$single['id']:0),
								'onclick'=>'javascript:remove_dialog_report_relationship_field_data('.$single['id'].',"'.$single['reportsTables']['table_display_name'].'","'.$single['field_display_name'].'",'.$report_type_id.',"report-type-field");'
							]);
						?>
					</td>
				</tr>
			<?php /**/
			} 
        }  ?>
    </table>
</div>

