<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "tbl_reports_report_type".
 *
 * @property integer $id
 * @property string $report_type
 * @property string $report_type_description
 */
class ReportsReportType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_reports_report_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['report_type'], 'required'],
            [['report_type'], 'string', 'max' => 255],
            [['report_type_description'], 'string', 'max' => 1000],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'report_type' => 'Report Type',
            'report_type_description' => 'Report Type Description',
        ];
    }
    
    public function prepareCustomReportQuery($post_data = array(),$limit=''){
		//echo "<pre>",print_r($post_data),"</pre>";
		$query = '';
		if(!empty($post_data)){
			//Get All Table use in report type in order
			$reportsReportTypeFields = ReportsReportTypeFields::find()->where(['report_type_id'=>$post_data['ReportsUserSaved']['report_type_id']]);
			$sql= "SELECT id, rela_base_table, CONCAT(rela_join_string,' JOIN ', rela_table,' ON ', obj.joinstr, ' = ', obj.masterstr) as sqlstr FROM (
				SELECT tbl_reports_fields_relationships.id, rela_base_table, tbl_reports_fields_relationships.rela_join_string, tbl_reports_fields_relationships.rela_table,
				CONCAT(tbl_reports_fields_relationships.rela_table,'.',tbl_reports_fields_relationships.rela_field) as joinstr,
				CONCAT(tbl_reports_fields_relationships.rela_base_table, '.',tbl_reports_fields.field_name) as masterstr
				FROM tbl_reports_fields_relationships 
				INNER JOIN tbl_reports_fields ON tbl_reports_fields.id = tbl_reports_fields_relationships.rela_base_field
				WHERE tbl_reports_fields_relationships.id IN (SELECT reports_fields_relationships_id FROM tbl_reports_report_type_fields WHERE report_type_id = {$post_data['ReportsUserSaved']['report_type_id']} AND reports_fields_relationships_id!=0 GROUP BY reports_fields_relationships_id)
			) as obj ORDER BY obj.id";
			
			$resultdata = Yii::$app->db->createCommand($sql)->queryAll();
			$join_string="";
			$from = '';
			
			if(!empty($resultdata)){
				foreach($resultdata as $key => $sql){
					$from = ($key == 0)?$sql['rela_base_table']:$from;
					$join_string .= ' '.$sql['sqlstr'].' ';
				}
			} else {
				$sql = "SELECT table_name FROM tbl_reports_tables
				INNER JOIN tbl_reports_fields ON tbl_reports_fields.report_table_id = tbl_reports_tables.id
				INNER JOIN tbl_reports_report_type_fields ON tbl_reports_report_type_fields.reports_fields_id = tbl_reports_fields.id
				WHERE report_type_id = {$post_data['ReportsUserSaved']['report_type_id']}
				GROUP BY table_name";
				$from = Yii::$app->db->createCommand($sql)->queryScalar();
			}
			$fieldoperatorList = ArrayHelper::map(ReportsFieldOperators::find()->select(['id','field_operator'])->orderBy('id')->all(),'id','field_operator');        
		    $select_fields = $post_data['fieldval'];
		    $key = array_search('tbl_tasks.task_complete_date',$select_fields);
		    if($key !== false){
				$select_fields[$key] = "(CASE WHEN tbl_tasks.task_status = 4 THEN tbl_tasks.task_complete_date ELSE NULL END) as task_complete_date";
			}
			//Look up and calcukation field
			$fields_in_lookup=array();
			if(!empty($select_fields)){
				foreach($select_fields as $key=>$field){
					$selectModified = $this->getLookupSql($field, $key, 'tabular');
					
					$post_fields[$key] = $field;
					if(!empty($selectModified)){
						$select_fields[$key] = $selectModified[$key];
						$post_fields[$key] = $select_fields[$key];
					} else {
						$filed_exp = explode(".",$field);
						if($filed_exp[0]==$from){
							continue;
						}
						$fields_in_lookup[$key] = $field;	
						$post_fields[$key] = $field;
						
					}
				}
				$converted_table_alias=array();
				
				foreach($post_data['fieldval'] as $key=>$field){
					$filed_exp = explode(".",$field);
					if($filed_exp[0]==$from){
						continue;
					}
					$tablealias_sql = "SELECT CONCAT(tbl_reports_tables.table_name,'_',tbl_reports_fields.field_name) as tablealias
FROM `tbl_reports_report_type_fields` 
INNER JOIN tbl_reports_fields_relationships ON tbl_reports_fields_relationships.id = tbl_reports_report_type_fields.reports_fields_relationships_id
INNER JOIN tbl_reports_fields ON tbl_reports_fields.id = tbl_reports_fields_relationships.rela_base_field 
INNER JOIN tbl_reports_tables ON tbl_reports_tables.id = tbl_reports_fields.report_table_id
WHERE tbl_reports_report_type_fields.reports_fields_id IN (SELECT `tbl_reports_fields`.id FROM `tbl_reports_fields` INNER JOIN `tbl_reports_tables` ON `tbl_reports_fields`.`report_table_id` = `tbl_reports_tables`.`id` WHERE CONCAT(table_name,'.',field_name) = '{$field}') AND tbl_reports_report_type_fields.report_type_id = ".$post_data['ReportsUserSaved']['report_type_id'];
				  $tablealias=Yii::$app->db->createCommand($tablealias_sql)->queryOne();
				  //echo $filed_exp[1]." = ".$tablealias['tablealias'],"<br/>";
				  if(isset($tablealias['tablealias']) && $tablealias['tablealias']!=''){
					  $filed_exp = explode(".",$field);
					  $converted_table_alias[$field]=$tablealias['tablealias'];
					  //$select_fields[$key]=$tablealias['tablealias'].'.'.$filed_exp[1]. ' as '.$tablealias['tablealias'].'_'.$filed_exp[1];
					  //$post_fields[$key] = $tablealias['tablealias'].'_'.$filed_exp[1];
				  }
				}
			}
			//echo "<pre>",print_r($select_fields),"</pre>";
			//die;
		    $where='1=1';
		    
			$select=implode(", ",$select_fields);
			$timezoneOffset = (new Options)->getOffsetOfCurrenttimeZone();
			$order="";
			
			if(!empty($post_data['filter_value'])){
				foreach($post_data['filter_value'] as $filter_data){
					$filters=json_decode($filter_data,true);
					//echo "<pre>",print_r($filters),"</pre>";die;
					$where_field=$post_data['fieldval'][$filters['id']];
					
					//if(isset($converted_table_alias[$where_field]))
						//$where_field=$converted_table_alias[$where_field].'.'.explode(".",$where_field)[1];
					
					//echo "<pre>",print_r($filters),"</pre>";	die;
					
					$opreator_field_values=$filters['operator_value'];
					$opreator_field_values2=isset($filters['operator_value_new'][0]) && $filters['operator_value_new'][0]!=''?$filters['operator_value_new']:'';
					//echo "<pre>",print_r($filters),"</pre>";
					$opreators=$filters['operator_field_value'];
					if(!empty($opreators)){
						$i = 0;
						$opwhere = '';
						foreach($opreators as $opt_key=>$opt){
							$opreator_name=$fieldoperatorList[$opt];
							$opt_symbol="";
							switch($opreator_name){
								case 'Greater than':
									if($i > 0)
										$opwhere.=" OR {$where_field} > {$opreator_field_values[$opt_key]}";
									else
										$opwhere.=" AND ({$where_field} > {$opreator_field_values[$opt_key]}";
								break;
								case 'Greater than or equal to':
									if($i > 0)
										$opwhere.=" OR {$where_field} >= {$opreator_field_values[$opt_key]}";
									else 
										$opwhere.=" AND ({$where_field} >= {$opreator_field_values[$opt_key]}";	
								break;
								case 'Less than':
									if($i > 0)
										$opwhere.=" OR {$where_field} < {$opreator_field_values[$opt_key]}";
									else 
										$opwhere.=" AND ({$where_field} < {$opreator_field_values[$opt_key]}";
								break;
								case 'Less than or equal to':
									if($i > 0)
										$opwhere.=" OR {$where_field} <= {$opreator_field_values[$opt_key]}";
									else
										$opwhere.=" AND ({$where_field} <= {$opreator_field_values[$opt_key]}";
								break;
								case 'Between':
									if($i > 0){
										$opwhere.=" OR ({$where_field} BETWEEN '{$opreator_field_values[$opt_key]}' AND '{$opreator_field_values2[$opt_key]}')";
									} else {
										$opwhere.=" AND (({$where_field} BETWEEN '{$opreator_field_values[$opt_key]}' AND '{$opreator_field_values2[$opt_key]}')";
									}
									//echo $opwhere;die;
								break;
								case 'Equals':
									//echo "<pre>",print_r($opreator_field_values[$opt_key]),"</pre>";
									if($i > 0){ 
										
										if(is_numeric($opreator_field_values[$opt_key])){
											if(count(explode(",",$opreator_field_values[$opt_key])) >1){
													$opwhere.=" OR {$where_field} IN ({$opreator_field_values[$opt_key]})";
											}else{
												$opwhere.=" OR {$where_field} = {$opreator_field_values[$opt_key]}";
											}
										}else{
											if(count(explode(",",$opreator_field_values[$opt_key])) >1){
													$opwhere.=" OR {$where_field} IN ('" . implode("', '", explode(",",$opreator_field_values[$opt_key])) . "')";
											}else{
												$opwhere.=" OR {$where_field} = '{$opreator_field_values[$opt_key]}'";
											}
										}
									} else {
										if(is_numeric($opreator_field_values[$opt_key])){
											if(count(explode(",",$opreator_field_values[$opt_key])) >1){
													$opwhere.=" AND ({$where_field} IN ({$opreator_field_values[$opt_key]})";
											}else{
												$opwhere.=" AND ({$where_field} = {$opreator_field_values[$opt_key]}";
											}
										}else{
											if(count(explode(",",$opreator_field_values[$opt_key])) >1){
													$opwhere.=" AND ({$where_field} IN ('" . implode("', '", explode(",",$opreator_field_values[$opt_key])) . "')";
											}else{
												$opwhere.=" AND ({$where_field} = '{$opreator_field_values[$opt_key]}'";
											}
										}
									}
								break;
								case 'Not equal to':
									if($i > 0){
										if(is_numeric($opreator_field_values[$opt_key])){
											if(count(explode(",",$opreator_field_values[$opt_key])) >1){
												$opwhere.=" OR {$where_field} NOT IN ({$opreator_field_values[$opt_key]})";
											}else{
												$opwhere.=" OR {$where_field} != {$opreator_field_values[$opt_key]}";
											}
										}else{
											if(count(explode(",",$opreator_field_values[$opt_key])) >1){
												$opwhere.=" OR {$where_field} NOT IN ('" . implode("', '", explode(",",$opreator_field_values[$opt_key])) . "')";
											}else{
												$opwhere.=" OR {$where_field} != '{$opreator_field_values[$opt_key]}'";	
											}
										}
									} else {
										if(is_numeric($opreator_field_values[$opt_key])){
											if(count(explode(",",$opreator_field_values[$opt_key])) >1){
												$opwhere.=" AND ({$where_field} NOT IN ({$opreator_field_values[$opt_key]})";
											}else{
												$opwhere.=" AND ({$where_field} != {$opreator_field_values[$opt_key]}";
											}
										}else{
											if(count(explode(",",$opreator_field_values[$opt_key])) >1){
												$opwhere.=" AND ({$where_field} NOT IN ('" . implode("', '", explode(",",$opreator_field_values[$opt_key])) . "')";
											}else{
												$opwhere.=" AND ({$where_field} != '{$opreator_field_values[$opt_key]}'";	
											}
										}
									}
								break;
								case 'Contains':
									if($i > 0)
										$opwhere.=" OR {$where_field} LIKE '%{$opreator_field_values[$opt_key]}%'";
									else
										$opwhere.=" AND ({$where_field} LIKE '%{$opreator_field_values[$opt_key]}%'";
								break;
								case 'Does not contain':
									if($i > 0)
										$opwhere.=" OR {$where_field} NOT LIKE '%{$opreator_field_values[$opt_key]}%'";
									else
										$opwhere.=" AND ({$where_field} NOT LIKE '%{$opreator_field_values[$opt_key]}%'";
								break;
								case 'Includes':
									if($i > 0)
										$opwhere.=" OR {$where_field} IN ('{$opreator_field_values[$opt_key]}')";
									else	
										$opwhere.=" AND ({$where_field} IN ('{$opreator_field_values[$opt_key]}')";
								break;
								case 'Excludes':
									if($i > 0)
										$opwhere.=" OR {$where_field} NOT IN ('{$opreator_field_values[$opt_key]}')";
									else
										$opwhere.=" AND ({$where_field} NOT IN ('{$opreator_field_values[$opt_key]}')";
								break;
									
							}
							$i++;
						}
						
						if($opwhere!='')
							$where .= "{$opwhere})";
					}
				}
			}
			
			$sort_order_arr=array(1=>'ASC',2=>'DESC');
			if(!empty($post_data['sorting_value']) ){
				$orders=array();
				foreach($post_data['sorting_value'] as $sorting_data){
					$sorting=json_decode($sorting_data,true);
					$sort_field=$post_data['fieldval'][$sorting['id']];
					$orders[$sorting['sort-type']]=$sort_field ." ". $sort_order_arr[$sorting['sort-order']];
				}
				if(!empty($orders)){
					asort($orders);
					$order=implode(", ",$orders);
				}
			}
			
			$group="";
			if(!empty($post_data['grouping_value']) ){
				$group_type = array('1' => 'Group By','2' => 'Sum','3' => 'Count');
				$i = 0;
				$aggregateFunction = array();
				foreach($post_data['grouping_value'] as $group_data){
					$groupdataAr = json_decode($group_data,true);
					//echo "\n".$group_type[$groupdataAr['group-type']]."\n";
					if($group_type[$groupdataAr['group-type']] == 'Count' || $group_type[$groupdataAr['group-type']] == 'Sum'){
						if(strpos($post_data['fieldval'][$groupdataAr['id']], ' as ') !== false){
							$ogfieldAr = explode(" as ",$post_data['fieldval'][$groupdataAr['id']]);
							$ogfield = $ogfieldAr[0];
							$aliasval = $ogfieldAr[1];
						} else {
							$ogfield = $post_data['fieldval'][$groupdataAr['id']];
							$aliasval = str_replace(".","_",$ogfield);
							//strtolower($group_type[$groupdataAr['group-type']]).'_'.str_replace(".","_",$ogfield);
							
						}
						$select_fields[$groupdataAr['id']] = strtoupper($group_type[$groupdataAr['group-type']]).'('.$ogfield.') as '.$aliasval;
						$aggregateFunction[$groupdataAr['id']] = $ogfield;
					}
					if($group_type[$groupdataAr['group-type']] == 'Group By'){
						$i++;
						$groups[$i]=$post_data['fieldval'][$groupdataAr['id']];
					}
					$select=implode(", ",$select_fields);
				}
				
				if(!empty($groups) || !empty($aggregateFunction)) {
					foreach($post_data['fieldval'] as $id=>$field) {
						if(!empty($groups) && !empty($aggregateFunction)){
							if(!in_array($field,$groups) && !in_array($field,$aggregateFunction)) {
								$i++;
								$groups[$i]=$field;
							}
						}
						else if (!empty($groups)){
							if(!in_array($field,$groups)) {
								$i++;
								$groups[$i]=$field;
							}
						}
						else if (!empty($aggregateFunction)){
							if(!in_array($field,$aggregateFunction)) {
								$i++;
								$groups[$i]=$field;
							}
						}
					}
					if(!empty($groups)){
						$group=implode(", ",$groups);
					}
				}	
				//echo "<pre>",print_r($select_fields),print_r($post_data['grouping_value']),"</pre>";
			}
			$top='';
			if(Yii::$app->db->driverName == 'mssql' && $limit != '')
				$top = ' TOP '.$limit;
			
			//group by {$select}
			$query = "SELECT {$top} {$select} FROM {$from} {$join_string} WHERE {$where} ";
			if($group!=""){
				$query .= " GROUP BY {$group}"; 	
			}
			if($order!=""){
				$query .= " ORDER BY {$order}"; 	
			}
			
			if(Yii::$app->db->driverName == 'mysql' && $limit != '')
				$query .= ' limit '.$limit;
		}
		
		foreach($post_fields as $key => $fields){
			$pos = strrpos($fields,' as ');
			if($pos){
				$post_fields[$key] = substr($fields, $pos+3, strlen($fields));
			}else{
				$filed_exp = explode(".",$fields);
				if(trim($filed_exp[0])==trim($from)){
					//echo $filed_exp[0]."--".$from,"<br>";
					$post_fields[$key] =$filed_exp[1];		
				}
			}	
		}
		
		$postdata['fieldval_alias'] = $post_fields;
		$postdata['fieldval_select'] = $select_fields;
		$postdata['sql'] = $query;
		
		//die;
		return $postdata;
	}
    
    public function createCustomReportCriteria($post_data = array()){
		$criteria = array();
		$fieldoperatorList = ArrayHelper::map(ReportsFieldOperators::find()->select(['id','field_operator'])->orderBy('id')->all(),'id','field_operator');     
		if(!empty($post_data['filter_value'])){
			foreach($post_data['filter_value'] as $filter_data){
				$filters=json_decode($filter_data,true);
				$where_field=$post_data['fieldval'][$filters['id']];
				$opreator_field_values=$filters['operator_value'];
				//$opreator_field_values2=$filters['operator_field_value_new'];
				$opreator_field_values2=isset($filters['operator_field_value_new'][0]) && $filters['operator_field_value_new'][0]!=''?$filters['operator_field_value_new']:'';
				$opreators=$filters['operator_field_value'];
				if(!empty($opreators)){
					foreach($opreators as $opt_key=>$opt){
						$opreator_name=$fieldoperatorList[$opt];
						
						switch($opreator_name){
								case 'Between':
									$criteria[$filters['id']][] = $opreator_field_values[$opt_key]." - ". $opreator_field_values2[$opt_key];
								break;
								default:
									$criteria[$filters['id']][] = $opreator_field_values[$opt_key];
								break;
						}
					}
				}
			}
		}
		return $criteria;
	}
	
	public function getLookupSql($field, $key, $reportType)
	{
		//echo $field," KEY == ", $key, " ReportType ",  $reportType;die;
		$select_fields = array();
		$val_table_name_field=explode(".",$field);
		if($val_table_name_field[0]=='Calc'){
			$getcalcuationfield_query=ReportsFieldCalculations::findOne($key)->calculation;
			$val_table_name_field[1]=strtolower(str_replace(" ","_",$val_table_name_field[1]));  
			if($reportType == 'chart') {
				$select_fields[$key] = " ($getcalcuationfield_query) ";
			} else {
				$select_fields[$key] = " ($getcalcuationfield_query) as {$val_table_name_field[1]} "; 
			}
			return $select_fields;
		}else{
			$data=(new ReportsFieldsRelationships())->getFilterLookup(0,$key);
			//echo "<pre>",print_r($data),"</pre>";die;
			if(isset($data['sql']) && $data['sql']!=""){
				if(isset($data['join']) && $data['join']!=""){
					if($data['type']==1){
						$field = " SELECT ".$data['sql']." FROM ".$data['from']." ".$data['join']." WHERE {$field}={$data['from']}.{$data['primary']}";
					} else{
						$field = $data['sql'];
					}
				}else{	
					if($data['type']==1){
						$field = " SELECT ".$data['sql']." FROM ".$data['from']." ".$data['join']." WHERE {$field}={$data['from']}.{$data['primary']}";
					} else {
						$field = $data['sql'];
					}
				}
					
				if($reportType == 'chart') {
					$select_fields[$key] = "(".$field.")" ;
				}else{
					$select_fields[$key] = "(($field) as {$val_table_name_field[1]})";
				}
				return $select_fields;
			}
		}
		$check_has_lookup=ReportsFieldsRelationships::find()
		->innerJoinWith([
			'reportFields' => function (\yii\db\ActiveQuery $query) use($val_table_name_field){
				$query->where(['field_name' => $val_table_name_field[1]]);
				$query->innerJoinWith([
					'reportsTables' => function (\yii\db\ActiveQuery $query2) use($val_table_name_field){
						$query2->where(['table_name' => $val_table_name_field[0]]);
					}
				]);
			}
		])
		->where(['in','rela_type',[1,2,3,4]]);
		if($check_has_lookup->count()){
			$lookup_data=$check_has_lookup->one();
			//echo "<pre>",print_r($lookup_data->attributes),"</pre>";
			if($lookup_data->rela_type==1){// || $lookup_data->rela_type==3){//table
				$primary_key="id";
				if(Yii::$app->db->driverName=='mysql'){
					$primary_query="SHOW KEYS FROM {$lookup_data->lookup_table} WHERE Key_name = 'PRIMARY'";
					$filterdata = \Yii::$app->db->createCommand($primary_query)->queryOne();
					if(!empty($filterdata)){
						$primary_key=$filterdata['Column_name'];
					}
				}
				$field_lookup=$lookup_data->lookup_fields;
				if(count(explode(",",$lookup_data->lookup_fields)) > 1){
					$sep=$lookup_data->lookup_field_separator;
					if($sep==NULL || $sep==''){
						$sep=' ';
					}
					$field_lookup="CONCAT(".str_replace(",",",'".$sep."',",$lookup_data->lookup_fields).")";
				}
				if($reportType == 'chart') {
					$select_fields[$key] = "(SELECT {$field_lookup} FROM {$lookup_data->lookup_table} WHERE {$field}={$lookup_data->lookup_table}.{$primary_key})";
				}else{
					$select_fields[$key] = "(SELECT {$field_lookup} FROM {$lookup_data->lookup_table} WHERE {$field}={$lookup_data->lookup_table}.{$primary_key}) as {$val_table_name_field[1]}";
				}
				
			}else if($lookup_data->rela_type==2){ //custom
				$custom_sql="";
				//echo "<pre>",print_r($lookup_data->reportsFieldsRelationshipsLookups),"</pre>";
				if(!empty($lookup_data->reportsFieldsRelationshipsLookups)){
					foreach($lookup_data->reportsFieldsRelationshipsLookups as $reportsLookupValues){
						if($custom_sql==""){
							$custom_sql="(CASE WHEN {$field}={$reportsLookupValues->field_value} THEN '{$reportsLookupValues->lookup_value}'";
						}else{
							$custom_sql.=" WHEN {$field}={$reportsLookupValues->field_value} THEN '{$reportsLookupValues->lookup_value}'";
						}
					}
				}
				
				if($custom_sql!=""){
					if($reportType == 'chart') {
						$custom_sql.=" ELSE {$field} END )";
					} else {
						$custom_sql.=" ELSE {$field} END ) as {$val_table_name_field[1]}";
					}
					$select_fields[$key] =$custom_sql;
				}
				//echo $custom_sql;
			}else if($lookup_data->rela_type==3){ //field
				$primary_key=$lookup_data->lookup_table."id";
				if(Yii::$app->db->driverName=='mysql'){
					$primary_query="SHOW KEYS FROM {$lookup_data->lookup_table} WHERE Key_name = 'PRIMARY'";
					$filterdata = \Yii::$app->db->createCommand($primary_query)->queryOne();
					if(!empty($filterdata)){
							$primary_key=$filterdata['Column_name'];
					}
				}
				$field="inner_".$lookup_data->lookup_fields;
				$related_tables = array();
				//if(count(explode(",",$lookup_data->lookup_fields)) > 1)
				{
					$exp_fields=explode(",",$lookup_data->lookup_fields);
					$sep=$lookup_data->lookup_field_separator;
					if($sep==NULL || $sep==''){
						$sep=' ';
					}
					$final_fields=array();
					foreach($exp_fields as $x_field){
						$final_fields["inner_".$x_field]="inner_".$x_field;
						$related_table_fields = explode(".",$x_field);
						if($lookup_data->lookup_table != $related_table_fields[0])
							$related_tables[$related_table_fields[0]] = $related_table_fields[0];
					}
					if(count(explode(",",$lookup_data->lookup_fields)) > 1){
						$field="CONCAT(".str_replace(",",",'".$sep."',",implode(",",$final_fields)).")";
					}
					if(!empty($related_tables)){
						$relatedtablesvalue = implode("','",$related_tables);
						$sql = "SELECT tbl_reports_fields_relationships.id, tbl_reports_fields_relationships.rela_join_string, tbl_reports_fields_relationships.rela_table, tbl_reports_fields_relationships.rela_field, tbl_reports_fields_relationships.rela_base_table, tbl_reports_fields.field_name FROM tbl_reports_fields_relationships INNER JOIN tbl_reports_fields ON tbl_reports_fields.id = tbl_reports_fields_relationships.rela_base_field WHERE ((tbl_reports_fields_relationships.rela_base_table = '{$lookup_data->lookup_table}' AND tbl_reports_fields_relationships.rela_table IN ('{$relatedtablesvalue}') AND rela_type = 0) OR (tbl_reports_fields_relationships.rela_base_table IN ('{$relatedtablesvalue}') AND tbl_reports_fields_relationships.rela_table = '{$lookup_data->lookup_table}' AND rela_type = 0))";
						
						$get_relationship_data=Yii::$app->db->createCommand($sql)->queryAll();
						$join_string='';
						if(!empty($get_relationship_data)) {
							foreach($get_relationship_data as $relationship_data) {
								if($relationship_data['rela_base_table'] == $lookup_data->lookup_table) {
									$join_string.=" {$relationship_data['rela_join_string']} JOIN {$relationship_data['rela_table']} as inner_{$relationship_data['rela_table']} ON inner_{$relationship_data['rela_table']}.{$relationship_data['rela_field']} = inner_{$relationship_data['rela_base_table']}.{$relationship_data['field_name']} ";
								} else {
									$rela_string = ($relationship_data['rela_join_string'] == 'LEFT')?'RIGHT':($relationship_data['rela_join_string'] == 'RIGHT')?'LEFT':'INNER';
									$join_string.=" {$rela_string} JOIN {$relationship_data['rela_base_table']} as inner_{$relationship_data['rela_base_table']} ON inner_{$relationship_data['rela_table']}.{$relationship_data['rela_field']} = inner_{$relationship_data['rela_base_table']}.{$relationship_data['field_name']} ";
								}
							}
						}
					}
				}
				$sql="SELECT {$field} as name FROM {$lookup_data->lookup_table} as inner_{$lookup_data->lookup_table} {$join_string}";// ORDER BY {$field}";
				if($reportType == 'chart') {
					$custom_sql = "($sql WHERE inner_{$lookup_data->lookup_table}.{$primary_key}={$lookup_data->lookup_table}.{$primary_key} GROUP BY name)";
				}else{
					$custom_sql = "($sql WHERE inner_{$lookup_data->lookup_table}.{$primary_key}={$lookup_data->lookup_table}.{$primary_key} GROUP BY name) as {$val_table_name_field[1]}";
				}
				$select_fields[$key] = $custom_sql;
			}
		}
		/*SELECT tbl_client.id, (SELECT CONCAT(inner_tbl_client.client_name,'-',inner_tbl_tasks.client_id) as name FROM tbl_client as inner_tbl_client LEFT JOIN tbl_tasks as inner_tbl_tasks ON inner_tbl_tasks.client_id = inner_tbl_client.id WHERE inner_tbl_client.id=tbl_client.id) as client_name, tbl_client.description, tbl_client_case.id, (SELECT CONCAT(inner_tbl_client.client_name,'-',inner_tbl_client_case.case_name) as name FROM tbl_client_case as inner_tbl_client_case LEFT JOIN tbl_client as inner_tbl_client ON inner_tbl_client_case.client_id = inner_tbl_client.id WHERE inner_tbl_client_case.id=tbl_client_case.id) as case_name FROM tbl_client LEFT JOIN tbl_client_case ON tbl_client_case.client_id = tbl_client.id WHERE 1=1 limit 5*/
		
		//echo "<pre>",print_r($select_fields),"</pre>";
		return $select_fields;
	}
	
	public function prepareCustomChartReportQuery($post_data = array(),$limit='')
	{
		$query = '';
		$post_fields=array();
		if(!empty($post_data)){
			$reportsReportTypeFields = ReportsReportTypeFields::find()->where(['report_type_id'=>$post_data['ReportsUserSaved']['report_type_id']]);
			
			$records = ReportsReportTypeFields::find()->where(['report_type_id'=>$post_data['ReportsUserSaved']['report_type_id']])
			->innerJoinWith([
				'reportsField' => function(\yii\db\ActiveQuery $query2){
					$query2->innerJoinWith(['reportsFieldType' => function(\yii\db\ActiveQuery $query){
						$query->select(['tbl_reports_field_type.id','tbl_reports_field_type.field_type']);
					}]);
				}
			])->all();
			
			$reportTypeFields = ArrayHelper::map($records,'id',function($model, $defaultValue) {
				return $model->reportsField->reportsFieldType->field_type;
			});
			
			$resultdata = Yii::$app->db->createCommand("SELECT id, rela_base_table, CONCAT(rela_join_string,' JOIN ', rela_table,' ON ', obj.joinstr, ' = ', obj.masterstr) as sqlstr FROM (
				SELECT tbl_reports_fields_relationships.id, rela_base_table, tbl_reports_fields_relationships.rela_join_string, tbl_reports_fields_relationships.rela_table,
				CONCAT(tbl_reports_fields_relationships.rela_table,'.',tbl_reports_fields_relationships.rela_field) as joinstr,
				CONCAT(tbl_reports_fields_relationships.rela_base_table, '.',tbl_reports_fields.field_name) as masterstr
				FROM tbl_reports_fields_relationships 
				INNER JOIN tbl_reports_fields ON tbl_reports_fields.id = tbl_reports_fields_relationships.rela_base_field
				WHERE rela_base_field IN (SELECT reports_fields_id FROM tbl_reports_report_type_fields WHERE report_type_id = {$post_data['ReportsUserSaved']['report_type_id']}) AND tbl_reports_fields_relationships.id IN (SELECT reports_fields_relationships_id FROM tbl_reports_report_type_fields WHERE report_type_id = {$post_data['ReportsUserSaved']['report_type_id']} AND reports_fields_relationships_id!=0 GROUP BY reports_fields_relationships_id)
			) as obj ORDER BY obj.id")->queryAll();
			
			$join_string="";
			$from = '';
			
			if(!empty($resultdata)) {
				foreach($resultdata as $key => $sql) {
					$from = ($key == 0)?$sql['rela_base_table']:$from;
					$join_string .= ' '.$sql['sqlstr'].' ';
				}
			} else {
				$sql = "SELECT table_name FROM tbl_reports_tables
				INNER JOIN tbl_reports_fields ON tbl_reports_fields.report_table_id = tbl_reports_tables.id
				INNER JOIN tbl_reports_report_type_fields ON tbl_reports_report_type_fields.reports_fields_id = tbl_reports_fields.id
				WHERE report_type_id = {$post_data['ReportsUserSaved']['report_type_id']}
				GROUP BY table_name";
				$from = Yii::$app->db->createCommand($sql)->queryScalar();
			}
			
			$selectedChartFormat = ReportsChartFormat::findOne($post_data['ReportsUserSaved']['chart_format_id'])->chart_format;
			$selectedChartFormat = strtolower($selectedChartFormat);
			$fieldoperatorList = ArrayHelper::map(ReportsFieldOperators::find()->select(['id','field_operator'])->orderBy('id')->all(),'id','field_operator');        
		    $select_fields = $post_data['fieldval'];
		    $x ='';
		    $y ='';
		    $dispay_by = '';
		    $datefield = '';
		    $legend = '';
		    $chart_fields=array();
		    $date_field_xy="x";
		    $interval_range="";
		    $view_dispay="";
		    $having_field = '';
		    $orgchartdate=array();
		    
		    foreach($select_fields as $keyt=>$field) {
				if(isset($post_data['chart_values_'.$keyt]) && $post_data['chart_values_'.$keyt]!='') {
					$chartvalues = json_decode($post_data['chart_values_'.$keyt],true);
					
					if($display_by=='' && isset($chartvalues['display_by']) && $chartvalues['display_by']!='') {
						 $display_by = strtolower(ReportsChartFormatDisplayBy::findOne($chartvalues['display_by'])->chart_display_by);
					}

					if($display_by == 'date') {
						
						if($reportTypeFields[$keyt] == "DATE" || $reportTypeFields[$keyt] == "DATETIME"){
							$interval_range=$chartvalues['interval_range'];
							$view_dispay=$chartvalues['view_display'];
							$datefield=$post_data['fieldval'][$keyt];
						
							if($chartvalues['axis'] == 'x') {
								$date_field_xy='x';
							}else if($chartvalues['axis'] == 'y') {
								$date_field_xy='y';
							}
						}
					}
					
					if($x=='' && $chartvalues['axis'] == 'x') {
						
						$selectModified = $this->getLookupSql($field, $keyt, 'chart');
						if(!empty($selectModified))
							$select_fields[$keyt] = $selectModified[$keyt];
						
						$x = $keyt;
						if(isset($chartvalues['manipulation_by']) && $chartvalues['manipulation_by']!=''){
							$manipulation_field=$post_data['fieldval'][$keyt];
							$orgchartdate['x']=$post_data['fieldval'][$keyt];
							$chart_fields['x']= $chartvalues['manipulation_by'].'('.$manipulation_field.') as x';
							$having_field = 'x';
						}else{
							$chart_fields['x']= $select_fields[$keyt].' as x';
							$orgchartdate['x']=$select_fields[$keyt];
							if($display_by == 'text') {
								$datefield=$post_data['fieldval'][$keyt];
							}
						}
					} else if($y=='' && $chartvalues['axis'] == 'y') {
						
						$selectModified = $this->getLookupSql($field, $keyt, 'chart');
						if(!empty($selectModified))
							$select_fields[$keyt] = $selectModified[$keyt];
						
						$y = $keyt;
						if(isset($chartvalues['manipulation_by']) && $chartvalues['manipulation_by']!=''){
							$manipulation_field=$post_data['fieldval'][$keyt];
							$orgchartdate['y']=$post_data['fieldval'][$keyt];
							$chart_fields['y']= $chartvalues['manipulation_by'].'('.$manipulation_field.') as y';
							$having_field = 'y';
						}else{
							$orgchartdate['y']=$select_fields[$keyt];
							$chart_fields['y']= $select_fields[$keyt].' as y';
							if($display_by == 'text') {
								$datefield=$post_data['fieldval'][$keyt];
							}
						}
					}
				} 
			}
			$is_modified=array();
			//echo $datefield;die('hh');
		    $fields_in_lookup=array();
			if(!empty($select_fields)){
				foreach($select_fields as $key=>$field){
					$selectModified = $this->getLookupSql($field, $key, 'chart');
					$post_fields[$key] = $field;
					if(!empty($selectModified)){
						$select_fields[$key] = $selectModified[$key];
						$post_fields[$key] = $select_fields[$key];
						$is_modified[$key]=$key;
					} else {
						$filed_exp = explode(".",$field);
						if($filed_exp[0]==$from){
							continue;
						}
						//$fields_in_lookup[$key] = $field;	
						$post_fields[$key] = $field;
						
					}
				}
				$converted_table_alias=array();
				
				foreach($post_data['fieldval'] as $key=>$field){
					$filed_exp = explode(".",$field);
					if($filed_exp[0]==$from){
						//continue;
						$selectModified = $this->getLookupSql($field, $key, 'chart');
						if(!empty($selectModified)){
							$select_fields[$key] = $selectModified[$key];
						}
					}
				}
			}
			//echo $post_data['fieldval'][$post_data['chart_legend']];
			
			//die;
			$where='1=1';
			if(isset($post_data['chart_legend']) && $post_data['chart_legend']!='' ) {
				$select_fields[$post_data['chart_legend']] = $post_data['fieldval'][$post_data['chart_legend']];
				if(isset($post_fields[$post_data['chart_legend']])){
					$pos = strrpos($post_fields[$post_data['chart_legend']],' as ');
					if($pos !== FALSE && !in_array($post_data['chart_legend'],$is_modified)){
						$str = substr($post_fields[$post_data['chart_legend']], 0,$pos);
						$chart_fields['legend'] = $str.' as legend';
						$legend = $str;
					}else{
						$chart_fields['legend'] = $post_fields[$post_data['chart_legend']].' as legend';
						$legend = $post_fields[$post_data['chart_legend']];
					}
				}else{
					$legend = $select_fields[$post_data['chart_legend']];
					$chart_fields['legend'] = $select_fields[$post_data['chart_legend']].' as legend';
				}
			}
			//echo $chart_fields['legend'];die;
			
			
			
			$select=implode(", ",$chart_fields);
			$mysql_view_display = Yii::$app->params['mysql_view_display'];
			if($display_by == 'date'){
				$date_field_org="";
				$timezoneOffset = (new Options)->getOffsetOfCurrenttimeZone();
				if($datefield!=""){
					$date_field_org=$datefield;
					
					if(strtolower($interval_range) == 'day'){
						$interval_range="DAY";
						if (Yii::$app->db->driverName == 'mysql') {
							$dateFormat = array_search($view_dispay,$mysql_view_display);
							if($dateFormat!== false){
								$datefield = "(DATE_FORMAT(CONVERT_TZ(".$datefield.",'+00:00','{$timezoneOffset}'), '{$dateFormat}'))";
								$select .= ", ".$datefield." as ".strtolower($interval_range);	
							}
						}
					}
					
					if(strtolower($interval_range) == 'week'){
						$display_by="WEEK";
						if (Yii::$app->db->driverName == 'mysql') {
							$datefield_start = " DATE_ADD(CONVERT_TZ(".$datefield.",'+00:00','{$timezoneOffset}'), INTERVAL(1-DAYOFWEEK(CONVERT_TZ(".$datefield.",'+00:00','{$timezoneOffset}'))) DAY) as startdate";
							$datefield_end = " DATE_ADD(CONVERT_TZ(".$datefield.",'+00:00','{$timezoneOffset}'), INTERVAL(7-DAYOFWEEK(CONVERT_TZ(".$datefield.",'+00:00','{$timezoneOffset}'))) DAY) as enddate";
							$select .= ", ".$datefield_start.", ".$datefield_end ;
							$datefield ="DATE_FORMAT(CONVERT_TZ(".$datefield.",'+00:00','{$timezoneOffset}'))";
						}
					}
					
					if(strtolower($interval_range) == 'month'){
						$interval_range="MONTH";
						if (Yii::$app->db->driverName == 'mysql') {
							$dateFormat = array_search($view_dispay,$mysql_view_display);
							if($dateFormat!== false){
								$datefield = "(DATE_FORMAT(CONVERT_TZ(".$datefield.",'+00:00','{$timezoneOffset}'), '{$dateFormat}'))";
								$select .= ", ".$datefield." as ".strtolower($interval_range);	
							}
						}
					}
					
					if(strtolower($interval_range) == 'year'){
						$display_by="YEAR";
						if (Yii::$app->db->driverName == 'mysql') {
							$datefield = "(DATE_FORMAT(CONVERT_TZ(".$datefield.",'+00:00','{$timezoneOffset}'), '%Y'))";
							$select .= ", ".$datefield." as ".strtolower($display_by);
						}
					}
				}
			}
			$sdate="";
			$edate="";
			$order="";
			if(!empty($post_data['filter_value'])){
				foreach($post_data['filter_value'] as $filter_data){
					$filters=json_decode($filter_data,true);
					
					$where_field=$post_data['fieldval'][$filters['id']];
					$opreator_field_values=$filters['operator_value'];
					$opreator_field_values2=isset($filters['operator_value_new'][0]) && $filters['operator_value_new'][0]!=''?$filters['operator_value_new']:'';
					$opreators=$filters['operator_field_value'];
					
					if($reportTypeFields[$filters['id']] == 'DATETIME' || $reportTypeFields[$filters['id']] == 'DATE'){
						$vals = explode("/",$opreator_field_values[0]);
						//echo "<pre>",print_r($opreator_field_values),"</pre>";
						$opreator_field_values[0] = $vals[2]."-".$vals[0]."-".$vals[1];
						if(!empty($opreator_field_values2)){
							$vals1 = explode("/",$opreator_field_values2[0]);
							$opreator_field_values2[0] = $vals1[2]."-".$vals1[0]."-".$vals1[1];
						}
						if(trim($orgchartdate[$date_field_xy]) == trim($where_field))
						{
							//set sdzate zand ydate here
							$sdate=$opreator_field_values[0];
							$edate=isset($opreator_field_values2[0])?$opreator_field_values2[0]:$opreator_field_values[0];
						}	
					}
					
					if(!empty($opreators)){
						$i = 0;
						$opwhere = '';
						foreach($opreators as $opt_key=>$opt){
							$opreator_name=$fieldoperatorList[$opt];
							$opt_symbol="";
							switch($opreator_name){
								case 'Greater than':
									if($i > 0)
										$opwhere.=" OR {$where_field} > {$opreator_field_values[$opt_key]}";
									else
										$opwhere.=" AND ({$where_field} > {$opreator_field_values[$opt_key]}";
								break;
								case 'Greater than or equal to':
									if($i > 0)
										$opwhere.=" OR {$where_field} >= {$opreator_field_values[$opt_key]}";
									else 
										$opwhere.=" AND ({$where_field} >= {$opreator_field_values[$opt_key]}";	
								break;
								case 'Less than':
									if($i > 0)
										$opwhere.=" OR {$where_field} < {$opreator_field_values[$opt_key]}";
									else 
										$opwhere.=" AND ({$where_field} < {$opreator_field_values[$opt_key]}";
								break;
								case 'Less than or equal to':
									if($i > 0)
										$opwhere.=" OR {$where_field} <= {$opreator_field_values[$opt_key]}";
									else
										$opwhere.=" AND ({$where_field} <= {$opreator_field_values[$opt_key]}";
								break;
								case 'Between':
									if($i > 0){
										$opwhere.=" OR ({$where_field} >= '{$opreator_field_values[$opt_key]}' AND {$where_field} <= '{$opreator_field_values2[$opt_key]}')";
									} else {
										$opwhere.=" AND (({$where_field} >= '{$opreator_field_values[$opt_key]}' AND {$where_field} <= '{$opreator_field_values2[$opt_key]}')";
									}
									//echo $opwhere;die;
								break;
								case 'Equals':
									//echo "<pre>",print_r($opreator_field_values[$opt_key]),"</pre>";
									if($i > 0){ 
										
										if(is_numeric($opreator_field_values[$opt_key])){
											if(count(explode(",",$opreator_field_values[$opt_key])) >1){
													$opwhere.=" OR {$where_field} IN ({$opreator_field_values[$opt_key]})";
											}else{
												$opwhere.=" OR {$where_field} = {$opreator_field_values[$opt_key]}";
											}
										}else{
											if(count(explode(",",$opreator_field_values[$opt_key])) >1){
													$opwhere.=" OR {$where_field} IN ('" . implode("', '", explode(",",$opreator_field_values[$opt_key])) . "')";
											}else{
												$opwhere.=" OR {$where_field} = '{$opreator_field_values[$opt_key]}'";
											}
										}
									} else {
										if(is_numeric($opreator_field_values[$opt_key])){
											if(count(explode(",",$opreator_field_values[$opt_key])) >1){
													$opwhere.=" AND ({$where_field} IN ({$opreator_field_values[$opt_key]})";
											}else{
												$opwhere.=" AND ({$where_field} = {$opreator_field_values[$opt_key]}";
											}
										}else{
											if(count(explode(",",$opreator_field_values[$opt_key])) >1){
													$opwhere.=" AND ({$where_field} IN ('" . implode("', '", explode(",",$opreator_field_values[$opt_key])) . "')";
											}else{
												$opwhere.=" AND ({$where_field} = '{$opreator_field_values[$opt_key]}'";
											}
										}
									}
								break;
								case 'Not equal to':
									if($i > 0){
										if(is_numeric($opreator_field_values[$opt_key])){
											if(count(explode(",",$opreator_field_values[$opt_key])) >1){
												$opwhere.=" OR {$where_field} NOT IN ({$opreator_field_values[$opt_key]})";
											}else{
												$opwhere.=" OR {$where_field} != {$opreator_field_values[$opt_key]}";
											}
										}else{
											if(count(explode(",",$opreator_field_values[$opt_key])) >1){
												$opwhere.=" OR {$where_field} NOT IN ('" . implode("', '", explode(",",$opreator_field_values[$opt_key])) . "')";
											}else{
												$opwhere.=" OR {$where_field} != '{$opreator_field_values[$opt_key]}'";	
											}
										}
									} else {
										if(is_numeric($opreator_field_values[$opt_key])){
											if(count(explode(",",$opreator_field_values[$opt_key])) >1){
												$opwhere.=" AND ({$where_field} NOT IN ({$opreator_field_values[$opt_key]})";
											}else{
												$opwhere.=" AND ({$where_field} != {$opreator_field_values[$opt_key]}";
											}
										}else{
											if(count(explode(",",$opreator_field_values[$opt_key])) >1){
												$opwhere.=" AND ({$where_field} NOT IN ('" . implode("', '", explode(",",$opreator_field_values[$opt_key])) . "')";
											}else{
												$opwhere.=" AND ({$where_field} != '{$opreator_field_values[$opt_key]}'";	
											}
										}
									}
								break;
								case 'Contains':
									if($i > 0)
										$opwhere.=" OR {$where_field} LIKE '%{$opreator_field_values[$opt_key]}%'";
									else
										$opwhere.=" AND ({$where_field} LIKE '%{$opreator_field_values[$opt_key]}%'";
								break;
								case 'Does not contain':
									if($i > 0)
										$opwhere.=" OR {$where_field} NOT LIKE '%{$opreator_field_values[$opt_key]}%'";
									else
										$opwhere.=" AND ({$where_field} NOT LIKE '%{$opreator_field_values[$opt_key]}%'";
								break;
								case 'Includes':
									if($i > 0)
										$opwhere.=" OR {$where_field} IN ('{$opreator_field_values[$opt_key]}')";
									else	
										$opwhere.=" AND ({$where_field} IN ('{$opreator_field_values[$opt_key]}')";
								break;
								case 'Excludes':
									if($i > 0)
										$opwhere.=" OR {$where_field} NOT IN ('{$opreator_field_values[$opt_key]}')";
									else
										$opwhere.=" AND ({$where_field} NOT IN ('{$opreator_field_values[$opt_key]}')";
								break;
									
							}
							$i++;
						}
						
						if($opwhere!='')
							$where .= "{$opwhere})";
					}
				}
			}
			
			$sort_order_arr=array(1=>'ASC',2=>'DESC');
			if(!empty($post_data['sorting_value']) ){
				$orders=array();
				foreach($post_data['sorting_value'] as $sorting_data){
					$sorting=json_decode($sorting_data,true);
					//$sort_field=$post_data['fieldval'][$sorting['id']];
					if(isset($converted_table_alias[$post_data['fieldval'][$sorting['id']]])){
							$sort_field=$converted_table_alias[$post_data['fieldval'][$sorting['id']]].'.'.explode(".",$post_data['fieldval'][$sorting['id']])[1];
						}else{
							$sort_field=$post_data['fieldval'][$sorting['id']];
						}
					$orders[$sorting['sort-type']]=$sort_field ." ". $sort_order_arr[$sorting['sort-order']];
				}
				if(!empty($orders)){
					asort($orders);
					$order=implode(", ",$orders);
				}
			}
			$top='';
			if(Yii::$app->db->driverName == 'sqlsrv' && $limit != '')
				$top = ' TOP '.$limit;
			
			
			//echo $join_string;die;
			//echo $date_field_org;die;
			
			$query = "SELECT {$top} {$select} FROM {$from} {$join_string} WHERE {$where}";
			$query_start = "SELECT min($date_field_org) as start_date FROM {$from} {$join_string} WHERE {$where}";
			$query_end = "SELECT max($date_field_org) as end_date FROM {$from} {$join_string} WHERE {$where}";
			$group = '';
			//echo $datefield;die;
			if($legend != ''){
				if($display_by == 'date'){
				$query .= " GROUP BY {$datefield}, {$legend}";
				//$query_start  .= " GROUP BY {$datefield}, {$legend}";
				//$query_end  .= " GROUP BY {$datefield}, {$legend}";
				$group = $datefield." , ".$legend;
				}else{
					$group = $datefield." , ".$legend;
				}
			}else{
				$group = $datefield;
			}
			
			if($order!=""){
				$query .= " ORDER BY {$order}"; 	
			}
			if($display_by == 'date' && $sdate=='' && $edate==''){
				$report_data = \Yii::$app->db->createCommand($query_start)->queryOne();
				$sdate = $report_data['start_date'];
				$report_data = \Yii::$app->db->createCommand($query_end)->queryOne();
				$edate = $report_data['end_date'];
			}
		}
		//echo $chart_fields['legend'];die;
		$legend = $x = $y="";
		$pos = strrpos($chart_fields['legend'],' as ');
		if($pos !== FALSE){
			$str = substr($chart_fields['legend'], 0,$pos);
			$legend = $str;
		}
		$posx = strrpos($chart_fields['x'],' as ');
		if($posx !== FALSE){
			$str = substr($chart_fields['x'], 0,$posx);
			$x = $str;
		}
		//echo $chart_fields['y'];
		$posy = strrpos($chart_fields['y'],' as ');
		if($posy !== FALSE){
			$str = substr($chart_fields['y'], 0,$posy);
			$y = $str;
		}
		if($group!=''){
			$group = $group." HAVING ".$having_field." > 0";
		}else{
			//$where .= " AND ".$having_field." > 0";	
		}
		//echo $query;
		//die;
		return $final_data=array(
			'sdate'=>$sdate,
			'edate'=>$edate,
			'x'=>$x,
			'y'=>$y,
			'legend'=>$legend,
			'display_by'=>$display_by,
			'interval_range'=>$interval_range,
			'view_display'=>$view_dispay,
			'datefield_xy'=>$date_field_xy,
			'fmr_with_join'=>$from.' '.$join_string,
			'whr'=>$where,
			'grp'=>$group
		);
		
		//return $query;
	}
    
    public function getTables($select='TABLE_NAME,COLUMN_NAME,DATA_TYPE', $where='', $order='', $group='', $prefix='', $suffix=''){
    	$connection = Yii::$app->db;
    	$dbs = explode("=",$connection->dsn);
        $dbname = $dbs[count($dbs)-1];
        
		$columnname = 'TABLE_SCHEMA';
		if(Yii::$app->db->driverName == 'sqlsrv')
			$columnname = 'TABLE_CATALOG';
    
		$sql = $prefix."SELECT $select FROM INFORMATION_SCHEMA.COLUMNS WHERE {$columnname} = :dbname $where $order $group".$suffix;
    	return $connection->createCommand($sql,[':dbname'=>$dbname])->queryAll();
    }
    
    public function getTablesFromReports($select='tableslist.id, tableslist.table_name, tableslist.table_display_name', $where='1=1', $order='', $group='GROUP BY tableslist.id, tableslist.table_name, tableslist.table_display_name', $prefix='', $suffix=''){
    	
		$sql = $prefix."SELECT $select FROM tbl_reports_tables as tableslist 
		INNER JOIN tbl_reports_fields as tablefields ON tablefields.report_table_id = tableslist.id 
		LEFT JOIN tbl_reports_fields_relationships as fieldsrelation ON fieldsrelation.rela_base_field = tablefields.id 
		LEFT JOIN tbl_reports_fields_relationships_lookups as fieldsrelationlookup ON fieldsrelationlookup.reports_fields_relationships_id = fieldsrelation.id
		WHERE $where $group $order".$suffix;
		
		$connection = Yii::$app->db;
    	return $connection->createCommand($sql)->queryAll();
    }
    public function getBarChartByDate($report_data,$query,$interval_range,$view_display){
		$new_chart_data = $categories = $chart_data  = array();
		if(!empty($report_data)){
			foreach($report_data as $rp_data){
				if(strtolower($interval_range) == 'week' || strtolower($interval_range) == 'day'){
					$categories[date('m-d-y',strtotime($rp_data['start_date']))]=date('m-d-y',strtotime($rp_data['start_date']));
				}else if(strtolower($interval_range) == 'month'){
					if($view_display=="MM"){
						$categories[date('m',strtotime($rp_data['start_date']))]=date('m',strtotime($rp_data['start_date']));
					}else{
						$categories[date('m-Y',strtotime($rp_data['start_date']))]=date('m-Y',strtotime($rp_data['start_date']));
					}
				}else{
					$categories[date('Y',strtotime($rp_data['start_date']))]=date('Y',strtotime($rp_data['start_date']));
				}
			}
			asort($categories);
			foreach($report_data as $rp_data){
				foreach($categories as $cat){
					if(is_numeric($rp_data['LEGEND']))
						$rp_data['LEGEND'] = $rp_data['LEGEND']." ";
						
					if(strtolower($interval_range) == 'week' || strtolower($interval_range) == 'day'){
						if($cat==date('m-d-y',strtotime($rp_data['start_date']))){
							$chart_data[html_entity_decode($rp_data['LEGEND'])][$cat]=(int) $rp_data['Y'];
						}
					} else if(strtolower($interval_range) == 'month'){
						if($view_display=="MM"){
							if($cat==date('m',strtotime($rp_data['start_date']))){
								$chart_data[html_entity_decode($rp_data['LEGEND'])][$cat]=(int) $rp_data['Y'];
							}
						}else{
							if($cat==date('m-Y',strtotime($rp_data['start_date']))){
								$chart_data[html_entity_decode($rp_data['LEGEND'])][$cat]=(int) $rp_data['Y'];
							}
						}
					} else {
						if($cat==date('Y',strtotime($rp_data['start_date']))){
							$chart_data[html_entity_decode($rp_data['LEGEND'])][$cat]=(int) $rp_data['Y'];
						}
					}
				}
			}
			foreach($chart_data as $k=>$data){
				foreach($categories as $cat){
					if(!isset($chart_data[$k][$cat])){
						$chart_data[$k][$cat]=0;
					}
				}
			}
			foreach($chart_data as $k=>$data){
				$mydata=$data;
				ksort($mydata);
				$new_chart_data[]=array('name'=>$k,'data'=>array_values($mydata));
			}
		}
		return array('data'=>$new_chart_data,'categories'=>$categories);
	}
	function getBarChartByText($report_data,$query){
		$new_chart_data = $categories = $chart_data  = array();
			if(!empty($report_data)){
			foreach($report_data as $rp_data){
					$categories[$rp_data['X']]=$rp_data['X'];
			}
			asort($categories);
			foreach($report_data as $rp_data){
				foreach($categories as $cat){
					if($cat==$rp_data['X']){
						if(is_numeric($rp_data['LEGEND']))
						$rp_data['LEGEND'] = $rp_data['LEGEND']." ";
						
						$chart_data[html_entity_decode($rp_data['LEGEND'])][$cat]=(int) $rp_data['Y'];
					}
				}
			}
			foreach($chart_data as $k=>$data){
				foreach($categories as $cat){
					if(!isset($chart_data[$k][$cat])){
						$chart_data[$k][$cat]=0;
					}
				}
			}
			foreach($chart_data as $k=>$data){
				$mydata=$data;
				ksort($mydata);
				$new_chart_data[]=array('name'=>$k,'data'=>array_values($mydata));
			}
		}
		return array('data'=>$new_chart_data,'categories'=>$categories);
	}
    public function getBarChartData($report_data,$query){
		$data=array();
		$categories=array();
		$chart_data=array();
		$interval_range=$query['interval_range'];
		$view_display=$query['view_display'];
		$display_by=$query['display_by'];
			if(strtolower($display_by) == 'date'){
				$data = $this->getBarChartByDate($report_data,$query,$interval_range,$view_display);
			}
			if(strtolower($display_by) == 'text'){
				$data = $this->getBarChartByText($report_data,$query);
			}
		return $data;
	} 
	function getLineColumnChartByText($report_data,$query){
		$new_chart_data = $categories = $chart_data  = array();
			if(!empty($report_data)){
			foreach($report_data as $rp_data){
					$categories[$rp_data['Y']]=$rp_data['Y'];
			}
			asort($categories);
			foreach($report_data as $rp_data){
				foreach($categories as $cat){
					if($cat==$rp_data['Y']){
						if(is_numeric($rp_data['LEGEND']))
						$rp_data['LEGEND'] = $rp_data['LEGEND']." ";
						
						$chart_data[html_entity_decode($rp_data['LEGEND'])][$cat]=(int) $rp_data['X'];
					}
				}
			}
			foreach($chart_data as $k=>$data){
				foreach($categories as $cat){
					if(!isset($chart_data[$k][$cat])){
						$chart_data[$k][$cat]=0;
					}
				}
			}
			foreach($chart_data as $k=>$data){
				$mydata=$data;
				ksort($mydata);
				$new_chart_data[]=array('name'=>$k,'data'=>array_values($mydata));
			}
		}
		return array('data'=>$new_chart_data,'categories'=>$categories);
	}
	public function getLineColumnChartByDate($report_data,$query,$interval_range,$view_display){
		$new_chart_data = $categories = $chart_data  = array();
		if(!empty($report_data)){
			foreach($report_data as $rp_data){
				if(strtolower($interval_range) == 'week' || strtolower($interval_range) == 'day'){
					$categories[date('m-d-y',strtotime($rp_data['start_date']))]=date('m-d-y',strtotime($rp_data['start_date']));
				}else if(strtolower($interval_range) == 'month'){
					if($view_display=="MM"){
						$categories[date('m',strtotime($rp_data['start_date']))]=date('m',strtotime($rp_data['start_date']));
					}else{
						$categories[date('m-Y',strtotime($rp_data['start_date']))]=date('m-Y',strtotime($rp_data['start_date']));
					}
				}else{
					$categories[date('Y',strtotime($rp_data['start_date']))]=date('Y',strtotime($rp_data['start_date']));
				}
			}
			asort($categories);
			foreach($report_data as $rp_data){
				foreach($categories as $cat){
					if(is_numeric($rp_data['LEGEND']))
						$rp_data['LEGEND'] = $rp_data['LEGEND']." ";
						
					if(strtolower($interval_range) == 'week' || strtolower($interval_range) == 'day'){
						if($cat==date('m-d-y',strtotime($rp_data['start_date']))){
							$chart_data[html_entity_decode($rp_data['LEGEND'])][$cat]=(int) $rp_data['X'];
						}
					} else if(strtolower($interval_range) == 'month'){
						if($view_display=="MM"){
							if($cat==date('m',strtotime($rp_data['start_date']))){
								$chart_data[html_entity_decode($rp_data['LEGEND'])][$cat]=(int) $rp_data['X'];
							}
						}else{
							if($cat==date('m-Y',strtotime($rp_data['start_date']))){
								$chart_data[html_entity_decode($rp_data['LEGEND'])][$cat]=(int) $rp_data['X'];
							}
						}
					} else {
						if($cat==date('Y',strtotime($rp_data['start_date']))){
							$chart_data[html_entity_decode($rp_data['LEGEND'])][$cat]=(int) $rp_data['X'];
						}
					}
				}
			}
			foreach($chart_data as $k=>$data){
				foreach($categories as $cat){
					if(!isset($chart_data[$k][$cat])){
						$chart_data[$k][$cat]=0;
					}
				}
			}
			foreach($chart_data as $k=>$data){
				$mydata=$data;
				ksort($mydata);
				$new_chart_data[]=array('name'=>$k,'data'=>array_values($mydata));
			}
		}
		return array('data'=>$new_chart_data,'categories'=>$categories);
	}
	public function getLineColumnChartData($report_data,$query){
		$data=array();
		$categories=array();
		$chart_data=array();
		$interval_range=$query['interval_range'];
		$view_display=$query['view_display'];
		$display_by=$query['display_by'];
			if(strtolower($display_by) == 'date'){
				$data = $this->getLineColumnChartByDate($report_data,$query,$interval_range,$view_display);
			}
			if(strtolower($display_by) == 'text'){
				$data = $this->getLineColumnChartByText($report_data,$query);
			}
		return $data;
	} 
    
}
