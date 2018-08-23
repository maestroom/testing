<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%reports_fields_relationships}}".
 *
 * @property integer $id
 * @property integer $rela_base_field
 * @property integer $rela_type
 * @property string $rela_base_table
 * @property string $rela_join_string
 * @property string $rela_table
 * @property string $rela_base_field
 * @property string $rela_data
 * @property string $rela_table
 * 
 * @property string $lookup_table
 * @property string $lookup_fields
 * @property string $lookup_field_separator
 *
 * @property ReportsFields $reportFields
 */
class ReportsFieldsRelationships extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%reports_fields_relationships}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['rela_base_field', 'rela_type'], 'required'],
            [['rela_base_field', 'rela_type'], 'integer'],
            [['rela_join_string','rela_field', 'rela_table','rela_base_table', 'rela_join_string', 'lookup_field_separator'], 'string'],
            [['rela_base_table', 'lookup_table', 'lookup_fields'], 'string', 'max' => 255],
            [['rela_base_field'], 'exist', 'skipOnError' => true, 'targetClass' => ReportsFields::className(), 'targetAttribute' => ['rela_base_field' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'rela_type' => 'Rela Type',
            'rela_base_table' => 'Rela Base Table',
            'rela_join_string' => 'Rela Join String',
            'rela_table' => 'Rela Table',
            'rela_base_field' => 'Report Fields ID',
            'rela_field'=>'Rela Field',
            'lookup_table' => 'Lookup Table',
            'lookup_fields' => 'Lookup Fields',
            'lookup_field_separator'=>'Lookup Field Separator'
        ];
    }
	public function getFilterLookup($report_type_id=0,$id=0,$field_name="",$sql="",$finallookup=array()){
		//echo $report_type_id," ==== ",$id;
		if(empty($finallookup)){
			$finallookup=array();
			$reportTypeFields=ReportsReportTypeFields::findOne($id);
			$finallookup['ids'][$reportTypeFields->reports_fields_id]=$reportTypeFields->reports_fields_id;
		}
		if($id!=0){
			/**
			 * 
			 * 1=lookup, 
			 * 2=lookup custom, 
			 * 5=field lookup
			 * 
			 * */
			 if($report_type_id == 0){
				$whr = "(tbl_reports_report_type_fields.id=$id)";
			}else{
				$whr = "(tbl_reports_report_type_fields.id=$id AND tbl_reports_report_type_fields.report_type_id =$report_type_id)";
			}
			$lookup_data=$this->find()->joinWith('reportTypeFields')->where(''.$whr.' OR (rela_base_field ='.$id.')')->one();
			//$lookup_data=$this->find()->joinWith('reportTypeFields')->where('tbl_reports_report_type_fields.i='.$id.' AND tbl_reports_report_type_fields.report_type_id ='.$report_type_id)->one();
			//echo $report_type_id,$id;
			//echo "<pre>",print_r($lookup_data),"</pre>";
			//echo "<prE>",print_r($finallookup),"</prE>";die;
			if(!empty($lookup_data)){
				//echo "here";die;
				$finallookup['type'] = $lookup_data->rela_type;
				if($lookup_data->rela_type==1){ //Table Lookup
					$primary_key="id";
					if(Yii::$app->db->driverName=='mysql'){
						$primary_query="SHOW KEYS FROM {$lookup_data->lookup_table} WHERE Key_name = 'PRIMARY'";
						$filterdata = \Yii::$app->db->createCommand($primary_query)->queryOne();
						if(!empty($filterdata)){
								$primary_key=$filterdata['Column_name'];
						}
					}
					$field=$lookup_data->lookup_fields;
					
					$exp_fields=explode(",",$lookup_data->lookup_fields);
					$sep=$lookup_data->lookup_field_separator;
					if($sep==NULL || $sep==''){
						$sep=' ';
					}
					$final_fields=array();
					foreach($exp_fields as $x_field){
						$nextid=$this->checkHasLookup($lookup_data->lookup_table.'.'.$x_field,$report_type_id);
						if($nextid!=0 && !in_array($nextid,$finallookup['ids'])){
							$finallookup['ids'][$nextid]=$nextid;
							$finallookupdata = $this->getFilterLookup($report_type_id,$nextid,$x_field,'',$finallookup);
							$x_field_name=str_replace(".","_",$x_field);
							if(isset($finallookupdata[$x_field_name])){
								$x_field = $finallookupdata[$x_field_name];
								$final_fields[$x_field]=$x_field;
							}else{
								$final_fields[$x_field]=$x_field;
							}
						}else{
							$final_fields[$x_field]=$x_field;
						}
					}
					if(count(explode(",",$lookup_data->lookup_fields)) > 1){
						$field="CONCAT(".str_replace(",",",'".$sep."',",implode(",",$final_fields)).")";
					}
					if(isset($field_name) && $field_name!=""){
						$field_name=str_replace(".","_",$field_name);
						$finallookup[$field_name]=$custom_sql;
					}else{
						$finallookup['primary']=$primary_key;
						$finallookup['sql']=$field;
					}
					$finallookup['from'] = $lookup_data->lookup_table;
					
				}
				else if($lookup_data->rela_type==2) { //CUSTOM
					$field=$lookup_data->lookup_fields;
					if(!empty($lookup_data->reportsFieldsRelationshipsLookups)){
						$custom_sql="";
						$lookupfinal_data=array();
						$custom_field = $field_name;
						if(isset($field_name) && $field_name!=""){
							$custom_field = $field_name;
						}else{
							$custom_field = $lookup_data->reportFields->reportsTables->table_name.'.'.$lookup_data->reportFields->field_name;
						}
						//$field_name
						foreach($lookup_data->reportsFieldsRelationshipsLookups as $reportsLookupValues){
							$lookupfinal_data[$reportsLookupValues->field_value]=$reportsLookupValues->lookup_value;
							if($custom_sql==""){
							$custom_sql="(CASE WHEN {$custom_field}={$reportsLookupValues->field_value} THEN '{$reportsLookupValues->lookup_value}'";
							}else{
								$custom_sql.=" WHEN {$custom_field}={$reportsLookupValues->field_value} THEN '{$reportsLookupValues->lookup_value}'";
							}
							//$lookupfinal_data[$reportsLookupValues->field_value]=$reportsLookupValues->lookup_value;
						}
						//echo "<prE>",print_r($lookupfinal_data),"</prE>";
						$finallookup['data']=$lookupfinal_data;
						$custom_sql.=" ELSE {$custom_field} END )";
						if(isset($field_name) && $field_name!=""){
							$field_name=str_replace(".","_",$field_name);
							$finallookup[$field_name]=$custom_sql;
						}else{
							$finallookup['sql']=$custom_sql;
						}
					}
				} 
				else if($lookup_data->rela_type==3){ //field
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
				
					$exp_fields=explode(",",$lookup_data->lookup_fields);
					$sep=$lookup_data->lookup_field_separator;
					if($sep==NULL || $sep==''){
						$sep=' ';
					}
					$final_fields=array();
					foreach($exp_fields as $x_field){
						$nextid=$this->checkHasLookup($x_field,$report_type_id);
						if($nextid!=0 && !in_array($nextid,$finallookup['ids'])){
							$finallookup['ids'][$nextid]=$nextid;
							$finallookupdata = $this->getFilterLookup($report_type_id,$nextid,$x_field,'',$finallookup);
							$x_field_name=str_replace(".","_",$x_field);
							if(isset($finallookupdata[$x_field_name])){
								$str = $finallookupdata[$x_field_name];
								$old = $x_field;
								$new = 'inner_'.$x_field;
								$tmpOldStrLength = strlen($old);
								$str = str_replace($old,$new,$str);
								$x_field=$str;
								$final_fields[$x_field]=$x_field;
							}else{
								$final_fields["inner_".$x_field]="inner_".$x_field;
							}
						}else{
							$final_fields["inner_".$x_field]="inner_".$x_field;
						}
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
				$sql="SELECT {$field} as name FROM {$lookup_data->lookup_table} as inner_{$lookup_data->lookup_table} {$join_string}";// ORDER BY {$field}";
				$finallookup['from'] = $lookup_data->lookup_table;
				$finallookup['join'] = $join_string;
				//$sql="SELECT {$fields} as id,{$fields} as name FROM {$lookup_data->lookup_table} {$join_string} ORDER BY {$fields}";
				$custom_sql = "($sql WHERE inner_{$lookup_data->lookup_table}.{$primary_key}={$lookup_data->lookup_table}.{$primary_key} GROUP BY name)";
				if(isset($field_name) && $field_name!=""){
					$field_name=str_replace(".","_",$field_name);
					$finallookup[$field_name]=$custom_sql;
				}else{
					$finallookup['primary']=ReportsFields::findOne($lookup_data->rela_base_field)->field_name;
					$finallookup['sql']=$custom_sql;
				}
			} 
			//echo "bbbbb";
			//if(!empty($finallookup))die("sad");
			}
		}	
		return $finallookup;
	}
	public function checkHasLookup($field,$report_type_id){
		//echo $field;die;
		$id=0;
		$sql="SELECT tbl_reports_fields.id FROM tbl_reports_fields INNER JOIN tbl_reports_tables on tbl_reports_tables.id=tbl_reports_fields.report_table_id WHERE CONCAT(table_name,'.',field_name)='".$field."'";
		$filterdata = \Yii::$app->db->createCommand($sql)->queryOne();
		if(!empty($filterdata)){
			$id=$filterdata['id'];
		}
		return $id;
	}
	
	public function getReportTypeFields(){
		return $this->hasOne(ReportsReportTypeFields::className(), ['reports_fields_id' => 'rela_base_field']);
	}
	/**
     * @return \yii\db\ActiveQuery
     */
    public function getReportFields()
    {
        return $this->hasOne(ReportsFields::className(), ['id' => 'rela_base_field']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReportsFieldsRelationshipsLookups()
    {
        return $this->hasMany(ReportsFieldsRelationshipsLookups::className(), ['reports_fields_relationships_id' => 'id']);
    }
}
