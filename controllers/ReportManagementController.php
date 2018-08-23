<?php
namespace app\controllers;
use Yii;
use yii\web\Controller;
use yii\helpers\ArrayHelper;
use yii\helpers\HtmlPurifier;
use yii\helpers\Json;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\widgets\ActiveForm;

use app\models\ReportsFieldType;
use app\models\ReportsFieldOperators;
use app\models\ReportsFieldTypeOperatorLogic;
use app\models\ReportsFieldCalculations;
use app\models\ReportsChartFormat;
use app\models\ReportsChartFormatDisplayBy;
use app\models\ReportsChartFormatDisplayLogic;
use app\models\ReportsReportFormat;
use app\models\ReportsReportType;
use app\models\ReportsReportTypeFields;
use app\models\ReportsFieldTypeTheme;
use app\models\ReportsReportTypeFieldCalculation; 
use app\models\ReportsUserSavedFields;
use app\models\ReportsReportTypeSql;
use app\models\ReportsLookups;
use app\models\ReportsFieldsRelationshipsLookups;//ReportsLookupValues;
use app\models\ReportsTables;
use app\models\ReportsFields;
use app\models\ReportsFieldsRelationships;
use app\models\ReportsFieldCalculationTable;
use app\models\ReportsCalculationFunction;
use app\models\ReportsCalculationFunctionTable;
use app\models\ReportsCalculationSp;
use app\models\ReportsCalculationSpTable;
use app\models\ReportsCalculationFunctionParams;
use app\models\ReportsCalculationSpParams;
use app\models\ReportsUserSaved;
use app\models\User;

class ReportManagementController extends \yii\web\Controller
{
    /**
    * Index action will be used to load main container view to manage Reports.
    * @return mixed
    */
    public function actionIndex()  {        
    }

    /**
     * Creates a new Fields Type .
     * If creation is successful, the browser will be redirected to the 'Reports Fileds type list' page.
     * @return mixed
     */
    public function actionIndexFieldType()
    {
        $model = new ReportsFieldType();                           
        $fieldstypeList = ReportsFieldType::find()->select(['id', 'field_type','field_type_theme_id'])->orderBy('field_type_theme_id, field_type ASC')->all();                        
        $themeList = ReportsFieldTypeTheme::find()->select(['id', 'field_type_theme'])->orderBy('id')->asArray()->all();                                                  
        $themeLists = [];
        foreach ($themeList as $single) {
           // print_r($single);
            $themeLists[$single['id']] = $single['field_type_theme'];
        }       
//        print_r($themeLists);
//        die;
        return $this->renderAjax('index-field-type', [
    		'fieldstypeList' => $fieldstypeList,
                'themeList' => $themeLists
    	]);
    }
    /*
     * 
     */
    public function actionAjaxFieldTheme($id){
        if($id == 0 ){
            $fieldtypesList = ReportsFieldType::find()->select(['id','field_type','field_type_theme_id'])->orderBy('field_type_theme_id, field_type ASC')->all();                    
        } else {
            $fieldtypesList = ReportsFieldType::find()->select(['id','field_type','field_type_theme_id'])->where('field_type_theme_id='.$id)->orderBy('field_type_theme_id, field_type ASC')->all();                    
        }        
        return $this->renderAjax('ajax-field-theme', [                     
            'fieldstypeList' => $fieldtypesList
    	]);
    }
    
    public function actionIndexFieldLookup(){
		
		$model  =  new ReportsLookups();      
		$fieldlookupList      =   ArrayHelper::map(ReportsLookups::find()->select(['id','lookup_name'])->orderBy('lookup_name')->all(),'id','lookup_name');        
		return $this->renderAjax('index-field-lookup',[
            'model' => $model,
            'fieldlookupList'=>$fieldlookupList,
        ]);
	}
	/*
     *   Creates a new Field Lookup model.
     * If creation is successful, the browser will be redirected to the 'Field operators list' page.
     * @return mixed
     */
    public function actionCreateFieldLookup(){
        $model = new ReportsLookups();
        $current_table = ArrayHelper::map((new ReportsReportType)->getTables('TABLE_NAME','','','GROUP BY TABLE_NAME'),'TABLE_NAME','TABLE_NAME'); // params = select, where, order, group
        if ($model->load(Yii::$app->request->post())) {
			 $post_data=Yii::$app->request->post();
			 //echo "<pre>",print_r($post_data),"</pre>";die;
			 if(!empty($post_data['ReportsLookups']['lookup_field'])){
				$model->lookup_field=implode(",",$post_data['ReportsLookups']['lookup_field']);
			 }else{
				 $model->lookup_table=NULL;
				 $model->lookup_field=NULL;
			 }
             if($model->save()){ 
				 $id=Yii::$app->db->getLastInsertId();
				 if(!empty($post_data['ReportsLookupValues'])){
					 foreach($post_data['ReportsLookupValues']['field_value'] as $key=>$val){
						 $modelReportsLookupValues = new ReportsLookupValues();
						 $modelReportsLookupValues->reports_lookup_id=$id;
						 $modelReportsLookupValues->lookup_value=$post_data['ReportsLookupValues']['lookup_value'][$key];
						 $modelReportsLookupValues->field_value=$val;
						 $modelReportsLookupValues->save();
					 }
				 }
                echo "OK";
            } else {
               return $this->renderAjax('create-field-lookup', [
                'model' => $model,
                'current_table'=>$current_table
                           	
            ]); 
            }
            die();
         }
        return $this->renderAjax('create-field-lookup', [
                'model' => $model,
                'current_table'=>$current_table            	
            ]); 
    }
	public function actionUpdateFieldLookup($id){
		$model = ReportsLookups::findOne($id);
        $current_table = ArrayHelper::map((new ReportsReportType)->getTables('TABLE_NAME','','','GROUP BY TABLE_NAME'),'TABLE_NAME','TABLE_NAME'); // params = select, where, order, group
        
        $table_name=$model->filter_table;
		$where = "AND TABLE_NAME='{$table_name}'";
		$filter_table_data = ArrayHelper::map((new ReportsReportType)->getTables("COLUMN_NAME as name",$where,"",""),'name','name'); 
		$lookup_table_data = array();
		if($model->type==1){
			$table_name=$model->lookup_table;
			$where = "AND TABLE_NAME='{$table_name}'";
			$lookup_table_data = ArrayHelper::map((new ReportsReportType)->getTables("COLUMN_NAME as name",$where,"",""),'name','name'); 	
		}
		
        if ($model->load(Yii::$app->request->post())) {
			 $post_data=Yii::$app->request->post();
			 $delete = ReportsLookupValues::deleteAll('reports_lookup_id='.$id);
			 if(!empty($post_data['ReportsLookups']['lookup_field'])){
				$model->lookup_field=implode(",",$post_data['ReportsLookups']['lookup_field']);
			 }else{
				 $model->lookup_table=NULL;
				 $model->lookup_field=NULL;
			 }
             if($model->save()){ 
				 if(!empty($post_data['ReportsLookupValues'])){
					 foreach($post_data['ReportsLookupValues']['field_value'] as $key=>$val){
						 $modelReportsLookupValues = new ReportsLookupValues();
						 $modelReportsLookupValues->reports_lookup_id=$id;
						 $modelReportsLookupValues->lookup_value=$post_data['ReportsLookupValues']['lookup_value'][$key];
						 $modelReportsLookupValues->field_value=$val;
						 $modelReportsLookupValues->save();
					 }
				 }
                echo "OK";
            } else {
               return $this->renderAjax('update-field-lookup', [
                'model' => $model,
                'current_table'=>$current_table,
                
                           	
            ]); 
            }
            die();
         }
        return $this->renderAjax('update-field-lookup', [
                'model' => $model,
                'current_table'=>$current_table,
                'filter_table_data'=>$filter_table_data,
                'lookup_table_data'=>$lookup_table_data            	
            ]);
	}
    public function actionFiledMapPopUpOption(){
		$post_data=Yii::$app->request->post();
		$description = "";
		$table_name=$post_data['ReportsLookups']['filter_table'];
		$column_name=$post_data['ReportsLookups']['filter_field'];
		$where = "AND TABLE_NAME='{$table_name}' AND COLUMN_NAME='{$column_name}'";
		/*$current_table = (new ReportsReportType)->getTables("COLUMN_COMMENT",$where,"",""); 
		if(!empty($current_table)){
			$description = $current_table[0]['COLUMN_COMMENT'];
		}*/
		return $this->renderAjax('filed-map-popup-option', [
			 'table_name'=>$table_name,
			 'column_name'=>$column_name,
             'description'=>$description,             	
            ]); 
	}
	public function actionChkLooupExist(){
		$id=Yii::$app->request->post('id');
		$table_name=Yii::$app->request->post('table_name');
		$field_name=Yii::$app->request->post('field_name');
		if(ReportsLookups::find()->where("filter_table='{$table_name}' AND filter_field='{$field_name}' AND id NOT IN({$id})")->count()){
			echo "Exist";
		}
		return;
	}
    public function actionGettablefield(){
		$table_name=Yii::$app->request->post('depdrop_parents')[0];
		$where = "AND TABLE_NAME='{$table_name}'";
		$current_table = (new ReportsReportType)->getTables("COLUMN_NAME as id, COLUMN_NAME as name",$where,"",""); 
		echo Json::encode(['output'=>$current_table, 'selected'=>'']);
	    return;
	}
	public function actionGetrelatedtable(){
		$table_name=Yii::$app->request->post('depdrop_parents')[0];
		$post_data = Yii::$app->request->post();  
        $filter_data = array();
        $where = $where_outer = '1=1';	
        if(!empty($post_data)){
			if(isset($table_name) && !empty($table_name)){
				$table_list = $table_name;
				$whereAttach = '1=1';
				$tableNotInclude = $table_list;
				$whereAttach .= " AND t1.id NOT IN ($tableNotInclude)";
				$prefix = 'SELECT * FROM (';
				$suffix = ') as t1 WHERE '.$whereAttach;
				$wherePart = '';
				$sqlpart = "SELECT table_name FROM tbl_reports_tables WHERE id IN ({$table_list})";
				$wherePart = "tableslist.table_name IN (SELECT tbl_reports_fields_relationships.rela_table FROM tbl_reports_fields_relationships WHERE rela_base_table IN ($sqlpart) UNION ALL SELECT tbl_reports_fields_relationships.rela_base_table FROM tbl_reports_fields_relationships WHERE rela_table IN ($sqlpart))";
				$where .= " AND ($wherePart)";
			}
		}
		$current_table = (new ReportsReportType)->getTablesFromReports('tableslist.id, tableslist.table_name as name', $where, '', 'GROUP BY tableslist.id, tableslist.table_name, tableslist.table_display_name',$prefix,$suffix); // params = select, where, order, group
		echo Json::encode(['output'=>$current_table, 'selected'=>'']);
	    return;
	}
	
	public function actionGetallrelatedtablefield(){
		$related_ids=Yii::$app->request->post('relation_ship_ids',0);
		$table_name=Yii::$app->request->post('table_name');
		$final_lookup_tables=array($table_name);
		if($table_name!=''){
			$table_names=ArrayHelper::map(ReportsFieldsRelationships::find()->select(['rela_table'])->where("rela_base_table IN ('{$table_name}') AND rela_type=0")->all(),'rela_table','rela_table'); 
			$table_names_rela=ArrayHelper::map(ReportsFieldsRelationships::find()->select(['rela_base_table'])->where("rela_table IN ('{$table_name}') AND rela_type=0")->all(),'rela_base_table','rela_base_table'); 
			/*if(!empty($table_names)){
				//$table_names = $table_names + $table_name;
				array_push($table_names,$table_name);
			}
			if(!empty($table_names_rela)){
				array_push($table_names_rela,$table_name);
			}*/
			
			$final_lookup_tables=array_merge($table_names,$table_names_rela);
			$final_lookup_tables[$table_name] = $table_name;
		}
		//echo "<pre>",print_r($final_lookup_tables),"</pre>";
		/*$where = 'AND TABLE_NAME IN ("' . implode('", "', $final_lookup_tables) . '")';
		$current_table = (new ReportsReportType)->getTables("CONCAT(TABLE_NAME,'.',COLUMN_NAME) as id, CONCAT(TABLE_NAME,'.',COLUMN_NAME) as name",$where,"",""); */
		$where = "AND TABLE_NAME IN ('" . implode("','",$final_lookup_tables) . "')";

		if(Yii::$app->db->driverName == 'mysql')
		{
			$current_table = (new ReportsReportType)->getTables("CONCAT(TABLE_NAME,'.',COLUMN_NAME) as id, CONCAT(TABLE_NAME,'.',COLUMN_NAME) as name",$where,"",""); 
		}
		else
		{
			$current_table = (new ReportsReportType)->getTables("TABLE_NAME+'.'+COLUMN_NAME as id, TABLE_NAME + '.'+COLUMN_NAME as name ",$where,"",""); 
		}
		echo Json::encode(['output'=>$current_table, 'selected'=>'']);
	    return;
	}
	
    /*
     * Index of Report Field Operators
     * List of All Fields operators with add new form
     * @return mixed
     */
    public function actionIndexFieldOperator(){
        $model  =  new ReportsFieldOperators();      
        $fieldoperatorList  =   ReportsFieldOperators::find()->select(['id','field_operator','field_operator_use'])->orderBy('id')->all();        
        $fieldoperatorboxList   =   [];
        foreach($fieldoperatorList as $single){
            $fieldoperatorboxList[$single['id']] = $single['field_operator'];
        }        
        return $this->renderAjax('index-field-operator',[
            'model' => $model,
            'fieldoperatorboxList' =>$fieldoperatorboxList,
            'fieldoperatorList' => $fieldoperatorList
        ]);        
    }
    /*
     * Index of Chart Display By
     * List of All Chart display By with add new form
     * @return mixed
     */
    public function actionIndexChartDisplayBy(){
        $model  =  new ReportsChartFormatDisplayBy();      
        $chartdisplayList = ReportsChartFormatDisplayBy::find()->select(['id','chart_display_by'])->orderBy('id')->all();
        $chartdisplayselectList = ArrayHelper::map($chartdisplayList,'id','chart_display_by');
        return $this->renderAjax('index-chart-display-by',[
            'model'=>$model,
            'chartdisplayList' => $chartdisplayList,
            'chartdisplayselectList' => $chartdisplayselectList
        ]);        
    }
    /*
     * Index of Report Field Calculations
     * List of All Field Calculations with add new form
     * @return mixed
     */
    public function actionIndexFieldCalculation(){
        $model  =  new ReportsFieldCalculations();         
        $fieldcalculationList = ReportsFieldCalculations::find()->select(['id','calculation_name'])->orderBy('calculation_name ASC')->all();
        $fieldcalculationselectList = ArrayHelper::map($fieldcalculationList,'id','calculation_name');
        return $this->renderAjax('index-field-calculation',[
            'model'=>$model,
            'fieldcalculationList' => $fieldcalculationList,
            'fieldcalculationselectList'=>$fieldcalculationselectList
        ]);        
    }
    /*
     * Index of Report Field Calculations
     * List of All Field Calculations with add new form
     * @return mixed
     */
    public function actionIndexCalculationFunction(){
        $model  =  new ReportsCalculationFunction();         
        $calculationFunctionList = ReportsCalculationFunction::find()->select(['id','function_name'])->orderBy('function_name ASC')->all();
        $calculationFunctionselectList = ArrayHelper::map($calculationFunctionList,'id','function_name');
        return $this->renderAjax('index-calculation-function',[
            'model'=>$model,
            'calculationFunctionList' => $calculationFunctionList,
            'calculationFunctionselectList'=>$calculationFunctionselectList
        ]);        
    }
    /*
     * Index of Report Field Calculations
     * List of All Field Calculations with add new form
     * @return mixed
     */
    public function actionIndexCalculationSp(){
        $model  =  new ReportsCalculationSp();         
        $calculationSpList = ReportsCalculationSp::find()->select(['id','sp_name'])->orderBy('sp_name ASC')->all();
        $calculationSpselectList = ArrayHelper::map($calculationSpList,'id','sp_name');
        return $this->renderAjax('index-calculation-sp',[
            'model'=>$model,
            'calculationSpList' => $calculationSpList,
            'calculationSpselectList'=>$calculationSpselectList
        ]);        
    }
     /*
     * Index of Report Type
     * List of All Report Type with add new form
     * @return mixed
     */
    public function actionIndexReportType(){
        $model  =  new ReportsReportType();         
        $reporttypeList = ReportsReportType::find()->select(['id','report_type'])->orderBy('report_type')->all();
        $reporttypeselectList = ArrayHelper::map($reporttypeList,'id','report_type');
        return $this->renderAjax('index-report-type',[
            'reporttypeList' => $reporttypeList,
            'model'=>$model,
            'reporttypeselectList'=>$reporttypeselectList
        ]);        
    }
    /*
     * Index of Report Chart Format
     * List of All Field Calculations with add new form
     * @return mixed
     */
    public function actionIndexChartFormat(){
        $model  =  new ReportsChartFormat();         
        $chartformatList = ReportsChartFormat::find()->select(['id','chart_format'])->orderBy('chart_format ASC')->all();
        $chartformatselectList = ArrayHelper::map($chartformatList,'id','chart_format');
        return $this->renderAjax('index-chart-format',[
            'model'=>$model,
            'chartformatselectList'=>$chartformatselectList,
            'chartformatList' => $chartformatList
        ]);        
    }
    /*
     * Index of Report Format
     * List of All Report Format with add new form
     * @return mixed
     */
    public function actionIndexReportFormat(){
        $model  =  new ReportsReportFormat();         
        $reportformatList = ReportsReportFormat::find()->select(['id','report_format'])->orderBy('id')->all();
        $reportformatselectList = ArrayHelper::map($reportformatList,'id','report_format');
        return $this->renderAjax('index-report-format',[
            'model'=>$model,
            'reportformatList' => $reportformatList,
            'reportformatselectList'=>$reportformatselectList
        ]);        
    }
    /**
     * Creates a new Report field Type model.
     * If creation is successful, the browser will be redirected to the 'Reports Fileds type list' page.
     * @return mixed
     */
    public function actionCreateFieldType(){
        $model = new ReportsFieldType();
        $fieldsthemeList = ReportsFieldTypeTheme::find()->select(['id', 'field_type_theme'])->orderBy('id')->asArray()->all();
        $model_field_length = (new User)->getTableFieldLimit($model->tableSchema->name);                                   
         if ($model->load(Yii::$app->request->post())) {
             if($model->save()){                 
                 return 'OK';
             }else {                 
                return $this->renderAjax('_form-field-type', [
                    'model' => $model,
                    'themeList' => $fieldsthemeList,
                    'model_field_length'=>$model_field_length
                ]);
            }
         }         
        return $this->renderAjax('create-field-type', [
                'model' => $model ,
                'themeList' => $fieldsthemeList,
                'model_field_length'=>$model_field_length                   
            ]);        
    }
    /**
     * Creates a new Report Format model.
     * If creation is successful, the browser will be redirected to the 'Reports format list' page.
     * @return mixed
     */
    public function actionCreateReportFormat(){
        $model = new ReportsReportFormat();
        $model_field_length = (new User)->getTableFieldLimit($model->tableSchema->name); 
         if ($model->load(Yii::$app->request->post())) {
             if($model->save()){               
                 return 'OK';
             }else {                 
                    return $this->renderAjax('_form-report-format', [
	                'model' => $model,	            	
                        'model_field_length'=>$model_field_length
	            ]);
        	}
         }
        return $this->renderAjax('create-report-format', [
                'model' => $model,
                'model_field_length'=>$model_field_length
            ]);        
    }
    /**
     * Creates a new Report Chart Format model.
     * If creation is successful, the browser will be redirected to the 'Reports Chart Formats list' page.
     * @return mixed
     */
    public function actionCreateChartFormat() {
        $model = new ReportsChartFormat();
        $model_field_length = (new User)->getTableFieldLimit($model->tableSchema->name);
        if (Yii::$app->request->post()) {
            $post_data = Yii::$app->request->post();
            $post_data['ReportsChartFormat']['chart_axis'] = '';
            if (isset($post_data['axis']) && $post_data['axis'] != '') {
                $post_data['ReportsChartFormat']['chart_axis'] = implode(',', $post_data['axis']);
            }
        }

        if ($model->load($post_data)) {
            if ($model->save()) {
                return 'OK';
            } else {
                return $this->renderAjax('_form-chart-format', [
                            'model' => $model,
                            'model_field_length'=>$model_field_length
                ]);
            }
        }
        return $this->renderAjax('create-chart-format', [
                    'model' => $model,
                    'model_field_length'=>$model_field_length
        ]);
    }

    /**
     * Creates a new Report Field Calculation model.
     * If creation is successful, the browser will be redirected to the 'Reports Fileds Calculation list' page.
     * @return mixed
     */
    public function actionCreateFieldCalculation(){
        $model = new ReportsFieldCalculations();
        $model_field_length = (new User)->getTableFieldLimit($model->tableSchema->name); 
        $tableList = ArrayHelper::map(ReportsTables::find()->select(['id','table_name'])->orderBy('table_name')->all(),'id','table_name');
        $functions = ArrayHelper::map(ReportsCalculationFunction::find()->all(),'id','function_name');
        $formula="";
        if ($model->load(Yii::$app->request->post())) {
			$post_data = Yii::$app->request->post();
			//echo "<pre>",print_r($post_data),"</pre>";die;
			if($post_data['ReportsFieldCalculations']['calculation_type']==2){
				$function=ReportsCalculationFunction::findOne($post_data['function_id']);
				$post_data['ReportsFieldCalculations']['calculation_primary']=$post_data['function_id'];
				$fn_name = $function->function_name;
				$reportFields=ReportsFields::find()->select(['id','field_name','report_table_id'])->where("id IN (SELECT report_fields_id FROM tbl_reports_calculation_function_params WHERE function_id={$post_data['ReportsFieldCalculations']['calculation_primary']})")->all();
				$params="";
				if(!empty($reportFields)){
					foreach($reportFields as $rfield){
							if($params=="")
								$params=$rfield->reportsTables->table_name.".".$rfield->field_name;
							else
								$params.=",".$rfield->reportsTables->table_name.".".$rfield->field_name;
					}
				}
				$post_data['ReportsFieldCalculations']['select_sql']=$fn_name."(".$params.")";
				$model->calculation_primary=$post_data['function_id'];
				$model->load($post_data);
			}else{
				$formula=$post_data['formula'];
				$calc_table = array();
				if($post_data['ReportsFieldCalculations']['calculation_type']==1){
					$post_data['ReportsFieldCalculations']['calculation_primary']=$post_data['table_id'];
					foreach($tableList as $table_id=>$table_name){
						if(strpos($post_data['formula'], $table_name)!==false){
							$calc_table[$table_id]=$table_id;
						}
					}
					if(!empty($calc_table)){
						$post_data['ReportsFieldCalculations']['select_sql']=$post_data['formula'];
					}	
				}
				$model->load($post_data);
				$model->calculation_primary=$post_data['table_id'];
			}
			if($model->save()){ 
				 $id=$model->id;
				 if($post_data['ReportsFieldCalculations']['calculation_type']==2){
					$sql="SELECT report_fields_id FROM tbl_reports_calculation_function_params WHERE function_id=".$post_data['ReportsFieldCalculations']['calculation_primary'];
					$calc_table = ArrayHelper::map(ReportsFields::find()->where("id IN ($sql)")->all(),'report_table_id','report_table_id');
					if(!empty($calc_table)){
						foreach($calc_table as $table){
							$modelReportsFieldCalculationTable=new ReportsFieldCalculationTable();
							$modelReportsFieldCalculationTable->table_id=$table;
							$modelReportsFieldCalculationTable->field_cal_id=$id;
							$modelReportsFieldCalculationTable->save();
						}
					}
				}
				if($post_data['ReportsFieldCalculations']['calculation_type']==1){
					foreach($calc_table as $table){
						$modelReportsFieldCalculationTable=new ReportsFieldCalculationTable();
						$modelReportsFieldCalculationTable->table_id=$table;
						$modelReportsFieldCalculationTable->field_cal_id=$id;
						$modelReportsFieldCalculationTable->save();
					}
				}
			    return 'OK';
            }else {
                    return $this->renderAjax('_form-field-calculation', [
	                'model' => $model,	  
	                'tableList'=>$tableList,
	                'functions'=>$functions,
	                'formula'=>$formula,
	                'flag'=>'next',
                        'model_field_length'=>$model_field_length                          
	            ]);
        	}
        }       
        return $this->renderAjax('create-field-calculation', [
                'model' => $model,
                'tableList'=>$tableList,
                'functions'=>$functions,
                'model_field_length'=>$model_field_length                          
            ]);        
    }
    /**
     * Creates a new Report Calculation Function model.
     * If creation is successful, the browser will be redirected to the 'Reports Function Calculation list' page.
     * @return mixed
     */
    public function actionCreateCalculationFunction(){
        $model = new ReportsCalculationFunction();
        $model_field_length = (new User)->getTableFieldLimit($model->tableSchema->name); 
        $tableList = ArrayHelper::map(ReportsTables::find()->select(['id','table_name'])->orderBy('table_name')->all(),'id','table_name');
        if ($model->load(Yii::$app->request->post())) {
			$post_data = Yii::$app->request->post();
			$req_params=Yii::$app->request->post('params',[0]);
			try{
				if(Yii::$app->db->driverName == 'mysql'){
					Yii::$app->db->createCommand("DROP FUNCTION IF EXISTS ".trim($post_data['ReportsCalculationFunction']['function_name']).";")->execute();	
					Yii::$app->db->createCommand("{$post_data['ReportsCalculationFunction']['mysql_function_code']}")->execute();
				}else{
					Yii::$app->db->createCommand("IF object_id(N'".trim($post_data['ReportsCalculationFunction']['function_name'])."', N'FN') IS NOT NULL
					DROP FUNCTION ".trim($post_data['ReportsCalculationFunction']['function_name']))->execute();	
					Yii::$app->db->createCommand("{$post_data['ReportsCalculationFunction']['mssql_function_code']}")->execute();
				}
				if($model->save()){ 
					 $id=$model->id;
					 /*if(isset($post_data['ReportsCalculationFunction']['primary_tables']) && $post_data['ReportsCalculationFunction']['primary_tables']){
						foreach($post_data['ReportsCalculationFunction']['primary_tables'] as $table){
								$modelReportsFieldCalculationTable=new ReportsCalculationFunctionTable();
								$modelReportsFieldCalculationTable->table_id=$table;
								$modelReportsFieldCalculationTable->function_id=$id;
								$modelReportsFieldCalculationTable->save();
						}
					 }*/
					 if(isset($post_data['params']) && $post_data['params']){
						foreach($post_data['params'] as $key=>$par){
							$modelReportsFunctionParams=new ReportsCalculationFunctionParams();
							$modelReportsFunctionParams->function_id=$id;
							$modelReportsFunctionParams->report_fields_id=$par;
							//$modelReportsFunctionParams->params=$par;
							//$modelReportsFunctionParams->type=$post_data['params_type'][$key];
							$modelReportsFunctionParams->save();
						}
					}
					return 'OK';
				 }else{
					$tableFieldParamsList = ReportsFields::find()->joinWith(['reportsTables','reportsFieldType'])->select(['tbl_reports_fields.id','tbl_reports_fields.report_table_id','tbl_reports_fields.reports_field_type_id','tbl_reports_tables.table_name','tbl_reports_fields.field_name','tbl_reports_field_type.field_type'])->where(['tbl_reports_fields.id'=>$req_params])->orderBy('tbl_reports_tables.table_name ASC, tbl_reports_fields.id ASC')->all();                 
					return $this->renderAjax('nextstep-calculation-function', [
						'model' => $model,	            	
						'tableList'=>$tableList,
						'tableFieldParamsList'=>$tableFieldParamsList,
                                                'model_field_length'=>$model_field_length
					]);
				}
         }catch(Exception $e){
			echo $e->getMessage();
		}
        }       
        return $this->renderAjax('create-calculation-function', [
                'model' => $model,
                'tableList'=>$tableList,
                'model_field_length'=>$model_field_length
            ]);        
    }
    /*Validate Calculation Function next Step*/
    public function actionValidateNextcalfn(){
		$flag=Yii::$app->request->get('flag','');
		if($flag=='')
			$model = new ReportsCalculationFunction(['scenario' => ReportsCalculationFunction::SCENARIO_NEXT]);
		else
			$model = new ReportsCalculationFunction();
    	if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
			Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    		return ActiveForm::validate($model);
    	} else {
    		return [];
    	}
	}
	/*Show Next Calculation Function Step*/
	public function actionNextcalfn(){
		$req_params=Yii::$app->request->post('params',[0]);
		$params=Yii::$app->request->post();
		$model = new ReportsCalculationFunction();
		if(isset($params['ReportsCalculationFunction']['id'])  && $params['ReportsCalculationFunction']['id']!="")
			$model = ReportsCalculationFunction::findOne($params['ReportsCalculationFunction']['id']);
		
		$model->load(Yii::$app->request->post());
		$tableFieldParamsList = ReportsFields::find()->joinWith(['reportsTables','reportsFieldType'])->select(['tbl_reports_fields.id','tbl_reports_fields.report_table_id','tbl_reports_fields.reports_field_type_id','tbl_reports_tables.table_name','tbl_reports_fields.field_name','tbl_reports_field_type.field_type'])->where(['tbl_reports_fields.id'=>$req_params])->orderBy('tbl_reports_tables.table_name ASC, tbl_reports_fields.id ASC')->all();                
		return $this->renderAjax('nextstep-calculation-function', [
                'model' => $model,
                'tableFieldParamsList'=>$tableFieldParamsList,
                'req_params'=>$req_params,                                        
        ]); 
	}
	/*Show Prev Calculation Function Step*/
	public function actionPreviouscalfn(){
		$req_params=Yii::$app->request->post('params','');
		$tableList = ArrayHelper::map(ReportsTables::find()->select(['id','table_name'])->orderBy('table_name')->all(),'id','table_name');
		$params=Yii::$app->request->post();
		$model = new ReportsCalculationFunction();
		if(isset($params['ReportsCalculationFunction']['id']) && $params['ReportsCalculationFunction']['id']!="")
			$model = ReportsCalculationFunction::findOne($params['ReportsCalculationFunction']['id']);
		
		$model->load(Yii::$app->request->post());
		return $this->renderAjax('_form-calculation-function', [
                'model' => $model,
                'tableList'=>$tableList,
                'req_params'=>$req_params
        ]);
	}
	/*Show Function Params Tables*/
	public function actionFunctionparams(){
		$tableList = ArrayHelper::map(ReportsTables::find()->select(['id','table_name'])->orderBy('table_name')->all(),'id','table_name');
		return $this->renderAjax('functionparams', [
                'tableList'=>$tableList
        ]); 
	}
	/*Show Function Params Table Fields */
	public function actionFunctionparamsfield(){
		$table_name=Yii::$app->request->get('table_name','');
		$tableFieldParamsList = ReportsFields::find()->joinWith(['reportsTables','reportsFieldType'])->select(['tbl_reports_fields.id','tbl_reports_fields.report_table_id','tbl_reports_fields.reports_field_type_id','tbl_reports_tables.table_name','tbl_reports_fields.field_name','tbl_reports_field_type.field_type'])->where(['table_name'=>$table_name])->orderBy('tbl_reports_tables.table_name ASC, tbl_reports_fields.id ASC')->all();
		return $this->renderAjax('functionparamsfield', [
                'tableFieldParamsList'=>$tableFieldParamsList,
        ]); 
	}
	
	/**/
	public function actionValidateNextfieldcalc(){
		$flag=Yii::$app->request->get('flag','');
		if($flag=='')
			$model = new ReportsFieldCalculations(['scenario' => ReportsCalculationFunction::SCENARIO_NEXT]);
		else
			$model = new ReportsFieldCalculations();
    	if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
			Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    		return ActiveForm::validate($model);
    	} else {
    		return [];
    	}
	}
    
    /**
     * Creates a new Report Calculation SP model.
     * If creation is successful, the browser will be redirected to the 'Reports SP Calculation list' page.
     * @return mixed
     */
    public function actionCreateCalculationSp(){
        $model = new ReportsCalculationSp();
        $tableList = ArrayHelper::map(ReportsTables::find()->select(['id','table_name'])->orderBy('table_name')->all(),'id','table_name');
        if ($model->load(Yii::$app->request->post())) {
			$post_data = Yii::$app->request->post();
			//echo "<pre>",print_r($post_data),"</pre>";die;
			try{
				Yii::$app->db->createCommand("DROP PROCEDURE IF EXISTS ".trim($post_data['ReportsCalculationSp']['sp_name']).";")->execute();	
				if(Yii::$app->db->driverName == 'mysql'){
					Yii::$app->db->createCommand("{$post_data['ReportsCalculationSp']['mysql_sp_code']}")->execute();
				}else{
					Yii::$app->db->createCommand("{$post_data['ReportsCalculationSp']['mssql_sp_code']}")->execute();
				}
				if($model->save()){ 
					 $id=$model->id;
					 if(isset($post_data['ReportsCalculationSp']['primary_tables']) && $post_data['ReportsCalculationSp']['primary_tables']){
						foreach($post_data['ReportsCalculationSp']['primary_tables'] as $table){
								$modelReportsFieldCalculationTable=new ReportsCalculationSpTable();
								$modelReportsFieldCalculationTable->table_id=$table;
								$modelReportsFieldCalculationTable->sp_id=$id;
								$modelReportsFieldCalculationTable->save();
						}
					 }
					 if(isset($post_data['params']) && $post_data['params']){
						foreach($post_data['params'] as $key=>$par){
							$modelReportsFunctionParams=new ReportsCalculationSpParams();
							$modelReportsFunctionParams->sp_id=$id;
							$modelReportsFunctionParams->params=$par;
							$modelReportsFunctionParams->type=$post_data['params_type'][$key];
							$modelReportsFunctionParams->save();
						}
					}
					 return 'OK';
				 }else {                 
					return $this->renderAjax('_form-calculation-sp', [
						'model' => $model,	            	
						'tableList'=>$tableList
					]);
				}
         }catch(Exception $e){
			echo $e->getMessage();die("sss");
		}
        }       
        return $this->renderAjax('create-calculation-sp', [
                'model' => $model,
                'tableList'=>$tableList
            ]);        
    }
    /**
     * Updates an existing Report Calculation Function model.
     * If update is successful, the browser will be redirected to the 'Function Calculation' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdateCalculationFunction($id){
        $model = $this->findCalculationFunctionModel($id);
        $sql="SELECT table_id FROM tbl_reports_calculation_function_table WHERE function_id= ".$id;
        $model_field_length = (new User)->getTableFieldLimit($model->tableSchema->name); 
        $req_params_init= ArrayHelper::map(ReportsCalculationFunctionParams::find()->select(['report_fields_id'])->where('function_id IN ('.$id.')')->all(),'report_fields_id','report_fields_id');
        $tableList = ArrayHelper::map(ReportsTables::find()->select(['id','table_name'])->orderBy('table_name')->all(),'id','table_name');
        
        if ($model->load(Yii::$app->request->post())) {        
			$post_data = Yii::$app->request->post();
			
			 if($model->save()){ 
				if(Yii::$app->db->driverName == 'mysql'){
					Yii::$app->db->createCommand("DROP FUNCTION IF EXISTS ".trim($post_data['ReportsCalculationFunction']['function_name']).";")->execute();
					Yii::$app->db->createCommand("{$post_data['ReportsCalculationFunction']['mysql_function_code']}")->execute();
				}else{
					Yii::$app->db->createCommand("IF object_id(N'".trim($post_data['ReportsCalculationFunction']['function_name'])."', N'FN') IS NOT NULL
					DROP FUNCTION ".trim($post_data['ReportsCalculationFunction']['function_name']))->execute();	
					Yii::$app->db->createCommand("{$post_data['ReportsCalculationFunction']['mssql_function_code']}")->execute();
				}
				if(isset($post_data['params']) && $post_data['params']){
					foreach($post_data['params'] as $key=>$par){
						if(!in_array($par,$req_params_init)){
							$modelReportsFunctionParams=new ReportsCalculationFunctionParams();
							$modelReportsFunctionParams->function_id=$id;
							$modelReportsFunctionParams->report_fields_id=$par;
							$modelReportsFunctionParams->save();
						}
					}
					if(!empty($post_data['params'])){
						ReportsCalculationFunctionParams::deleteAll('report_fields_id NOT IN ('.implode(",",$post_data['params']).') AND function_id='.$id);
					}
				}else{
					ReportsCalculationFunctionParams::deleteAll('function_id='.$id);
				}
				
			    echo "OK";die;
			} else {
				$req_params=Yii::$app->request->post('params',[0]);
				$tableFieldParamsList = ReportsFields::find()->joinWith(['reportsTables','reportsFieldType'])->select(['tbl_reports_fields.id','tbl_reports_fields.report_table_id','tbl_reports_fields.reports_field_type_id','tbl_reports_tables.table_name','tbl_reports_fields.field_name','tbl_reports_field_type.field_type'])->where(['tbl_reports_fields.id'=>$req_params])->orderBy('tbl_reports_tables.table_name ASC, tbl_reports_fields.id ASC')->all();
        		return $this->renderAjax('nextstep-calculation-function', [
						'model' => $model,	
						'tableList'=>$tableList,
						'tableFieldParamsList'=>$tableFieldParamsList,
                                                'model_field_length'=>$model_field_length
					]);
        	}
        } else {
            return $this->renderAjax('update-calculation-function', [
                'model' => $model,
                'tables'=>$tables,
                'tableList'=>$tableList,
                'req_params'=>$req_params_init,
                'model_field_length'=>$model_field_length
            ]);
        }
    }
    
    /**
     * Updates an existing Report Calculation Function model.
     * If update is successful, the browser will be redirected to the 'Function Calculation' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdateCalculationSp($id){
        $model = $this->findCalculationSpModel($id);
        $sql="SELECT table_id FROM tbl_reports_calculation_sp_table WHERE sp_id= ".$id;
        $tables=ArrayHelper::map(ReportsTables::find()->where('id IN ('.$sql.')')->all(),'id','id');
        $spparams = ReportsCalculationSpParams::find()->where('sp_id IN ('.$id.')')->all();
        $tableList = ArrayHelper::map(ReportsTables::find()->select(['id','table_name'])->orderBy('table_name')->all(),'id','table_name');
                      
        if ($model->load(Yii::$app->request->post())) {        
			$post_data = Yii::$app->request->post();
			 if($model->save()){ 
				/*Yii::$app->db->createCommand("DROP PROCEDURE IF EXISTS ".trim($post_data['ReportsCalculationSp']['sp_name']).";")->execute();	
				if(Yii::$app->db->driverName == 'mysql'){
					Yii::$app->db->createCommand("{$post_data['ReportsCalculationSp']['mysql_sp_code']}")->execute();
				}else{
					Yii::$app->db->createCommand("{$post_data['ReportsCalculationSp']['mssql_sp_code']}")->execute();
				}*/
				if(Yii::$app->db->driverName == 'mysql'){
					Yii::$app->db->createCommand("DROP PROCEDURE IF EXISTS ".trim($post_data['ReportsCalculationSp']['sp_name']).";")->execute();
					Yii::$app->db->createCommand("{$post_data['ReportsCalculationSp']['mysql_sp_code']}")->execute();
				}else{
					Yii::$app->db->createCommand("IF EXISTS (select * from dbo.sysobjects where id = object_id(N'[dbo].[".trim($post_data['ReportsCalculationSp']['sp_name'])."]') and OBJECTPROPERTY(id, N'IsProcedure') = 1)
					DROP PROCEDURE [dbo].[".trim($post_data['ReportsCalculationSp']['sp_name'])."]")->execute();
					Yii::$app->db->createCommand("{$post_data['ReportsCalculationSp']['mssql_sp_code']}")->execute();
				}
				 ReportsCalculationSpTable::deleteAll('sp_id='.$id);
				 ReportsCalculationSpParams::deleteAll('sp_id='.$id);
				 if(isset($post_data['ReportsCalculationSp']['primary_tables']) && $post_data['ReportsCalculationSp']['primary_tables']){
					foreach($post_data['ReportsCalculationSp']['primary_tables'] as $table){
							$modelReportsFieldCalculationTable=new ReportsCalculationSpTable();
							$modelReportsFieldCalculationTable->table_id=$table;
							$modelReportsFieldCalculationTable->sp_id=$id;
							$modelReportsFieldCalculationTable->save();
					}
				}
				if(isset($post_data['params']) && $post_data['params']){
						foreach($post_data['params'] as $key=>$par){
							$modelReportsFunctionParams=new ReportsCalculationSpParams();
							$modelReportsFunctionParams->sp_id=$id;
							$modelReportsFunctionParams->params=$par;
							$modelReportsFunctionParams->type=$post_data['params_type'][$key];
							$modelReportsFunctionParams->save();
						}
					}
			    echo "OK";die;
			} else {
        		return $this->renderAjax('_form-calculation-sp', [
	                'model' => $model,
	                'tables'=>$tables,
	                'tableList'=>$tableList,
	                'spparams'=>$spparams
	            ]);
        	}
        } else {
            return $this->renderAjax('update-calculation-sp', [
                'model' => $model,
                'tables'=>$tables,
                'tableList'=>$tableList,
                'spparams'=>$spparams           	
            ]);
        }
    }
    
    /**
     * Creates a new Report Type model.
     * If creation is successful, the browser will be redirected to the 'Reports Type list' page.
     * @return mixed
     */
    public function actionCreateReportType(){
        $model = new ReportsReportType();
        $model_field_length = (new User)->getTableFieldLimit($model->tableSchema->name); 
        if ($model->load(Yii::$app->request->post())) {
			$post_data = Yii::$app->request->post();
			$transaction = Yii::$app->db->beginTransaction();
			try{
				//echo "<pre>",print_r($post_data),"</pre>";die;
				if($model->save()) {
					// To Save into tbl_reports_report_type_field_calculation
                    $field_calculation = $post_data['field_calculation'];             
                    if(!empty($field_calculation)){
                       foreach($field_calculation as $single){                                                     
						   $mymodel = new ReportsReportTypeFieldCalculation();
						   $mymodel->report_type_id = $model->id;                               
						   $mymodel->field_calculation_id = $single;                                                           
						   $mymodel->save();                                                         
                        }  
                    }
                    
                    // To Save into tbl_reports_report_type_fields
                    if(isset($post_data['field_lists']) && is_array($post_data['field_lists']) && !empty($post_data['field_lists'])){
						foreach($post_data['field_lists'] as $table => $fields){
							foreach($fields as $keyfield=>$field){
								//echo "<pre>$field => {$post_data['field_lists_relationship'][$field]} => ",print_r($post_data['field_lists_relationship']),"</pre>";
								$mymodel = new ReportsReportTypeFields();
								$mymodel->report_type_id = $model->id;                               
								$mymodel->reports_fields_id = $field;
								$mymodel->reports_fields_relationships_id = isset($post_data['field_lists_relationship'][$field])?$post_data['field_lists_relationship'][$field]:0;
								$mymodel->report_condition =isset($post_data['field_filter'][$table][$keyfield])?$post_data['field_filter'][$table][$keyfield]:'';
								$mymodel->is_grp =(isset($post_data['field_grp'][$table][$keyfield]) && $post_data['field_grp'][$table][$keyfield] ==1)?1:0;
								$mymodel->save();            
							}
						}
					}
					$transaction->commit();   
                    return 'OK';
				} else {   
					$transaction->rollBack();              
					return $this->renderAjax('_form-report-type', [
						'model' => $model,
                                                'model_field_length'=>$model_field_length
					]);
				}
			} catch(Exception $e) {
				$transaction->rollBack();
				return $e->getMessage();
			}
		}
         
        return $this->renderAjax('create-report-type', [
			'model' => $model,
                        'model_field_length'=>$model_field_length
        ]);        
    }    
    /*
     *   Creates a new Field Operator model.
     * If creation is successful, the browser will be redirected to the 'Field operators list' page.
     * @return mixed
     */
    public function actionCreateFieldOperator(){
        $model = new ReportsFieldOperators();
        $model_field_length = (new User)->getTableFieldLimit($model->tableSchema->name); 
         if ($model->load(Yii::$app->request->post())) {
             if($model->save()){                 
                $field_types = Yii::$app->request->post('field_type');
                if(count($field_types)>0){
                foreach($field_types as $val){
                        $mymodel = new ReportsFieldTypeOperatorLogic();
                        $mymodel->fieldtype_id =  $val;
                        $mymodel->fieldoperator_id = $model->id;
                        $mymodel->save();
                }
                }
                echo "OK";
            } else {
               return $this->renderAjax('create-field-operator', [
                'model' => $model,
                'model_field_length'=>$model_field_length                          
            ]); 
            }
            die();
         }
        return $this->renderAjax('create-field-operator', [
                'model' => $model,
                'model_field_length'=>$model_field_length
            ]); 
    }
     /*
     *   Creates a new Chart Display by model.
     * If creation is successful, the browser will be redirected to the 'Chart Display by list' page.
     * @return mixed
     */
    public function actionCreateChartDisplayBy(){
        $model = new ReportsChartFormatDisplayBy();
        $model_field_length = (new User)->getTableFieldLimit($model->tableSchema->name); 
         if ($model->load(Yii::$app->request->post())) {
             if($model->save()){                 
                $chart_format = Yii::$app->request->post('chart_format');
                if(count($chart_format)>0){
					foreach($chart_format as $val){
							$mymodel = new ReportsChartFormatDisplayLogic();
							$mymodel->chartformat_id =  $val;
							$mymodel->chartformat_displayby_id = $model->id;
							$mymodel->save();
					}
                }
                echo "OK";
            } else {
               return $this->renderAjax('create-chart-display-by', [
                'model' => $model,
                'model_field_length'=>$model_field_length                   
            ]); 
            }
            die();
         }
        return $this->renderAjax('create-chart-display-by', [
                'model' => $model,
                'model_field_length'=>$model_field_length
            ]); 
    }
    /**
     * Updates an existing Report field Operator model.
     * If update is successful, the browser will be redirected to the 'Operator Type' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdateFieldOperator($id)
    {        
        $model = $this->findOperatorModel($id);
        $model_field_length = (new User)->getTableFieldLimit($model->tableSchema->name); 
        if ($model->load(Yii::$app->request->post())) {        	
        	if($model->save()){
                     $field_types = Yii::$app->request->post('field_type');                    
                     $delete = ReportsFieldTypeOperatorLogic::deleteAll('fieldoperator_id='.$id);
                     if(count($field_types) > 0){
                     foreach($field_types as $val){
                            $mymodel = new ReportsFieldTypeOperatorLogic();
                            $mymodel->fieldtype_id =  $val;
                            $mymodel->fieldoperator_id = $model->id;
                            $mymodel->save();
                        }
                     }
                    return 'OK';
        	} else {
        		return $this->renderAjax('_form-operator', [
	                'model' => $model,
                        'model_field_length'=>$model_field_length
	            ]);
        	}
        } else {            
             $fieldtypes = ReportsFieldType::find()
                     ->select(['fieldtype_id','field_type'])
                     ->innerJoinWith([
                        'reportsFieldTypeOperatorLogic' => function(\yii\db\ActiveQuery $query) use($id){
                            $query->where(['fieldoperator_id'=>$id]);
                        }
                     ])
                     ->asArray()
                     ->all();                                        
            return $this->renderAjax('update-operator-type', [
                'model' => $model ,
                'fieldtypesList'=>$fieldtypes,
                'model_field_length'=>$model_field_length                
            ]);
        }
    }
    /**
     * Updates an existing Chart Display By model.
     * If update is successful, the browser will be redirected to the 'Chart Display By Type' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdateChartDisplayBy($id)
    {        
        $model = $this->findChartDisplayBYModel($id);
        $model_field_length = (new User)->getTableFieldLimit($model->tableSchema->name); 
        if ($model->load(Yii::$app->request->post())) {        	
        	if($model->save()){
                     $chartformats = Yii::$app->request->post('chart_format');
                     $delete = ReportsChartFormatDisplayLogic::deleteAll('chartformat_displayby_id='.$id);
                     if(count($chartformats) > 0){
                    foreach($chartformats as $val){
                            $mymodel = new ReportsChartFormatDisplayLogic();
                            $mymodel->chartformat_id =  $val;
                            $mymodel->chartformat_displayby_id = $model->id;
                            $mymodel->save();
                        }
                     }
                    return 'OK';
        	} else {
        		return $this->renderAjax('_form-chart-display-by', [
	                'model' => $model,
                        'model_field_length'=>$model_field_length
	            ]);
        	}
        } else {
            $chartidList = ReportsChartFormatDisplayLogic::find()->select(['chartformat_id'])->where('chartformat_displayby_id='.$id);                        
            $chartformatList = ReportsChartFormat::find()->select(['tbl_reports_chart_format.id','tbl_reports_chart_format.chart_format'])
                            ->Where(['IN','tbl_reports_chart_format.id',$chartidList])->asArray()->all();            
            $chart_ids = [];
            foreach($chartformatList as $single){
                $chart_ids[] = $single['id'];
            }
            $chart_ids = implode(',',$chart_ids);                        
            return $this->renderAjax('update-chart-display-by', [
                'model' => $model ,
                'chart_ids'=>$chart_ids,
                'chartdisplayList'=>$chartformatList,
                'model_field_length'=>$model_field_length
            ]);
        }
    }
    /**
     * Updates an existing Report Chart Format model.
     * If update is successful, the browser will be redirected to the 'Chart Format Type' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdateChartFormat($id)
    {        
        $model = $this->findChartFormatModel($id);
        $model_field_length = (new User)->getTableFieldLimit($model->tableSchema->name); 
        if(Yii::$app->request->post()){
            $post_data = Yii::$app->request->post();                    
            $post_data['ReportsChartFormat']['chart_axis'] = '';
            if(isset($post_data['axis']) && $post_data['axis'] != ''){
                $post_data['ReportsChartFormat']['chart_axis']= implode(',',$post_data['axis']);        
            }            
        }
        if ($model->load($post_data)) {        	                
        	if($model->save()){                                 
                    return 'OK';
        	} else {
                    return $this->renderAjax('_form-chart-format', [
                        'model' => $model,	            	
                        'model_field_length'=>$model_field_length
                    ]);
        	}
        } else {            
            return $this->renderAjax('update-chart-format', [
                'model' => $model,
                'model_field_length'=>$model_field_length                
            ]);
        }
    }
   /**
     * Updates an existing Report field type model.
     * If update is successful, the browser will be redirected to the 'Field Type' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdateFieldType($id){
        $model = $this->findModel($id);
        $fieldsthemeList = ReportsFieldTypeTheme::find()->select(['id', 'field_type_theme'])->orderBy('id')->asArray()->all();
        $model_field_length = (new User)->getTableFieldLimit($model->tableSchema->name); 
        if ($model->load(Yii::$app->request->post())) {        	
        	if($model->save()){            	
                    echo "OK";
                    die;
        	} else {
        		return $this->renderAjax('_form-field-type', [
	                'model' => $model,
                        'themeList' => $fieldsthemeList,
                        'model_field_length'=>$model_field_length
	            ]);
        	}
        } else {
            return $this->renderAjax('update-field-type', [
                'model' => $model,
                'themeList' => $fieldsthemeList,
                'model_field_length'=>$model_field_length
            ]);
        }
    }
    /**
     * Updates an existing Report Format model.
     * If update is successful, the browser will be redirected to the 'Report Format' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdateReportFormat($id){
        $model = $this->findReportFormatModel($id);
        $model_field_length = (new User)->getTableFieldLimit($model->tableSchema->name); 
        if ($model->load(Yii::$app->request->post())) {        	
        	if($model->save()){            	
                    echo "OK";
                    die;
        	} else {
        		return $this->renderAjax('_form-report-format', [
                            'model' => $model,	            	
                            'model_field_length'=>$model_field_length
	            ]);
        	}
        } else {
            return $this->renderAjax('update-report-format', [
                'model' => $model,
                'model_field_length'=>$model_field_length
            ]);
        }
    }
    /**
     * Updates an existing Report field Calculation model.
     * If update is successful, the browser will be redirected to the 'Field Calculation' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdateFieldCalculation($id){
        $model = $this->findCalculationModel($id);
        $sql="SELECT table_id FROM tbl_reports_field_calculation_table WHERE field_cal_id= ".$id;
        $tables=ArrayHelper::map(ReportsTables::find()->where('id IN ('.$sql.')')->all(),'id','id');
        $tableList = ArrayHelper::map(ReportsTables::find()->select(['id','table_name'])->orderBy('table_name')->all(),'id','table_name');
        $functions = ArrayHelper::map(ReportsCalculationFunction::find()->all(),'id','function_name');              
        $formula="";
        if ($model->load(Yii::$app->request->post())) {        
			$post_data = Yii::$app->request->post();
			if($post_data['ReportsFieldCalculations']['calculation_type']==2){
				$function=ReportsCalculationFunction::findOne($post_data['ReportsFieldCalculations']['calculation_primary']);
				$fn_name = $function->function_name;
				$reportFields=ReportsFields::find()->select(['id','field_name','report_table_id'])->where("id IN (SELECT report_fields_id FROM tbl_reports_calculation_function_params WHERE function_id={$post_data['ReportsFieldCalculations']['calculation_primary']})")->all();
				$params="";
				if(!empty($reportFields)){
					foreach($reportFields as $rfield){
							if($params=="")
								$params=$rfield->reportsTables->table_name.".".$rfield->field_name;
							else
								$params.=",".$rfield->reportsTables->table_name.".".$rfield->field_name;
					}
				}
				$post_data['ReportsFieldCalculations']['select_sql']=$fn_name."(".$params.")";
				
				$model->load($post_data);
			}else{
				$formula=$post_data['formula'];
				$calc_table = array();
				if($post_data['ReportsFieldCalculations']['calculation_type']==1){
					foreach($tableList as $table_id=>$table_name){
						if(strpos($post_data['formula'], $table_name)!==false){
							$calc_table[$table_id]=$table_id;
						}
					}
					if(!empty($calc_table)){
						$post_data['ReportsFieldCalculations']['select_sql']=$post_data['formula'];
					}	
				}
				$model->load($post_data);
			}
			 if($model->save()){ 
				 ReportsFieldCalculationTable::deleteAll('field_cal_id='.$id);
				 if($post_data['ReportsFieldCalculations']['calculation_type']==2){
					$sql="SELECT report_fields_id FROM tbl_reports_calculation_function_params WHERE function_id=".$post_data['ReportsFieldCalculations']['calculation_primary'];
					$calc_table = ArrayHelper::map(ReportsFields::find()->where("id IN ($sql)")->all(),'report_table_id','report_table_id');
					if(!empty($calc_table)){
						foreach($calc_table as $table){
							$modelReportsFieldCalculationTable=new ReportsFieldCalculationTable();
							$modelReportsFieldCalculationTable->table_id=$table;
							$modelReportsFieldCalculationTable->field_cal_id=$id;
							$modelReportsFieldCalculationTable->save();
						}
					}
				}
				if($post_data['ReportsFieldCalculations']['calculation_type']==1){
					foreach($calc_table as $table){
						$modelReportsFieldCalculationTable=new ReportsFieldCalculationTable();
						$modelReportsFieldCalculationTable->table_id=$table;
						$modelReportsFieldCalculationTable->field_cal_id=$id;
						$modelReportsFieldCalculationTable->save();
					}
				}
			    echo "OK";die;
			} else {
				return $this->renderAjax('_form-field-calculation', [
	                'model' => $model,	  
	                'tableList'=>$tableList,
	                'functions'=>$functions,
	                'formula'=>$formula,
	                'flag'=>'next'
	            ]);
        	}
        } else {
            $model_field_length = (new User)->getTableFieldLimit($model->tableSchema->name); 
            return $this->renderAjax('update-field-calculation', [
                'model' => $model,
                'tableList' =>$tableList,
                'tables'=>$tables,
                'functions'=>$functions,
                'model_field_length'=>$model_field_length                
            ]);
        }
    }
    /**
     * Updates an existing Report Type model.
     * If update is successful, the browser will be redirected to the 'Report Type' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdateReportType($id)
    {
        $model = $this->findReportTypeModel($id);                
        $model_field_length = (new User)->getTableFieldLimit($model->tableSchema->name); 
        if ($model->load(Yii::$app->request->post())) {    
			$post_data = Yii::$app->request->post();
			//echo "<pre>",print_r($post_data),"</pre>";die;
			$transaction = Yii::$app->db->beginTransaction();
			try {
				if($model->save()) {
					
					// To Save into tbl_reports_report_type_field_calculation
					$field_calculation = $post_data['field_calculation'];      
					if(!empty($field_calculation)){
						ReportsReportTypeFieldCalculation::deleteAll('report_type_id='.$id.' AND field_calculation_id NOT IN ('.implode(",",$field_calculation).')');
					}else{
						ReportsReportTypeFieldCalculation::deleteAll('report_type_id='.$id);
					}       
					if(!empty($field_calculation)){
					   foreach($field_calculation as $single){  
						   $modelReportsReportTypeFieldCalculation = ReportsReportTypeFieldCalculation::find()->where(['report_type_id'=>$model->id,'field_calculation_id'=>$single]);
							if($modelReportsReportTypeFieldCalculation->count() > 0){
								continue;
							}                                                   
						   $mymodel = new ReportsReportTypeFieldCalculation();
						   $mymodel->report_type_id = $model->id;                               
						   $mymodel->field_calculation_id = $single;                                                           
						   $mymodel->save();                                                         
						}  
					}
					
					// To Save into tbl_reports_report_type_fields
					if(isset($post_data['field_lists']) && is_array($post_data['field_lists']) && !empty($post_data['field_lists'])){
						$fieldsAr = array();
						foreach($post_data['field_lists'] as $table => $fields) {
							foreach($fields as $keyfield=>$field) {
								$fieldsAr[$field] = $field;
								$count = ReportsReportTypeFields::find()->where(['report_type_id' => $model->id, 'reports_fields_id' => $field]);
								if($count->count() == 0) {
									$mymodel = new ReportsReportTypeFields();
									$mymodel->report_type_id = $model->id;                               
									$mymodel->reports_fields_id = $field;
									$mymodel->reports_fields_relationships_id = isset($post_data['field_lists_relationship'][$field])?$post_data['field_lists_relationship'][$field]:0;
									$mymodel->report_condition =isset($post_data['field_filter'][$table][$keyfield])?$post_data['field_filter'][$table][$keyfield]:'';
									$mymodel->is_grp =(isset($post_data['field_grp'][$table][$keyfield]) && $post_data['field_grp'][$table][$keyfield] ==1)?1:0;
									//echo $field." => ".$post_data['field_lists_relationship'][$field].' = '.isset($post_data['field_lists_relationship'][$field])?$post_data['field_lists_relationship'][$field]:0;
									$mymodel->save();
								} else {
									$mymodel = $count->one();
									//$mymodel = new ReportsReportTypeFields();
									$mymodel->reports_fields_relationships_id = isset($post_data['field_lists_relationship'][$field])?$post_data['field_lists_relationship'][$field]:0;
									$mymodel->report_condition =isset($post_data['field_filter'][$table][$keyfield])?$post_data['field_filter'][$table][$keyfield]:'';
									$mymodel->is_grp =(isset($post_data['field_grp'][$table][$keyfield]) && $post_data['field_grp'][$table][$keyfield] ==1)?1:0;
									//echo $field." => ".$post_data['field_lists_relationship'][$field].' = '.isset($post_data['field_lists_relationship'][$field])?$post_data['field_lists_relationship'][$field]:0;
									$mymodel->save();
								}
							}
						}
						ReportsReportTypeFields::deleteAll('reports_fields_id NOT IN ('.implode(",",$fieldsAr).') AND report_type_id = '.$model->id);
					}
					$transaction->commit();
					return 'OK';       
				} else {
					$transaction->rollBack();
					return $this->renderAjax('_form-report-type', [
						'model' => $model,
                                                'model_field_length'=>$model_field_length
					]);
				}
			} catch(Exception $e) {
				$transaction->rollBack();
				return $e->getMessage();
			}
        } else {
			$field_list = array();
			$tables = array();
			
			$reportsReportTypeFields = ReportsReportTypeFields::find()->select(['reports_fields_id'])->where(['report_type_id'=>$id]);
			$field_relationships = ArrayHelper::map(ReportsReportTypeFields::find()->select(['reports_fields_id','reports_fields_relationships_id'])->where(['report_type_id'=>$id])->all(),'reports_fields_id','reports_fields_relationships_id');
			$resultdata = Yii::$app->db->createCommand('SELECT report_table_id,tbl_reports_report_type_fields.id FROM tbl_reports_fields 
			INNER JOIN tbl_reports_report_type_fields ON tbl_reports_report_type_fields.reports_fields_id=tbl_reports_fields.id
			WHERE tbl_reports_report_type_fields.report_type_id = '.$id.' GROUP BY report_table_id,tbl_reports_report_type_fields.id ORDER BY tbl_reports_report_type_fields.id ASC')->queryAll();
			$resultdata = ArrayHelper::map($resultdata, 'report_table_id', 'report_table_id');
			
			if(!empty($resultdata)){
				$orderBy = "(CASE tbl_reports_tables.id";
				$i = 1;
				foreach($resultdata as $table){
					$orderBy .= " WHEN $table THEN $i ";
					$i++;
				}
				$orderBy .= " ELSE ".$i++." END), tbl_reports_fields.id ASC";
			}
			$fieldListAr = ReportsFields::find()
				->joinWith([
					'reportsFieldsRelationships' => function(\yii\db\ActiveQuery $query){
						$query->joinWith('reportsFieldsRelationshipsLookups');
					}
				])
				->innerJoinWith('reportsTables')
				->where(['in','tbl_reports_fields.id',$reportsReportTypeFields]);

			//$report_typefield_data=ReportsReportTypeFields::find()->where(['report_type_id'=>$id])->all();	
			$fieldListAr->orderBy($orderBy);
			$fieldList = $fieldListAr->asArray()->all();
			//echo "<pre>";print_r($report_typefield_data);die;
			foreach($fieldList as $fields){
				$report_typefield_data=ReportsReportTypeFields::find()->where(['reports_fields_id'=>$fields['id'], 'report_type_id'=>$id])->one();
				$fields['filter']=$report_typefield_data->report_condition;
				$fields['grp']=$report_typefield_data->is_grp;
				$table_ids[$fields['reportsTables']['id']]= $fields['reportsTables']['id'];
				$tables[$fields['reportsTables']['table_name']]['id'] = $fields['reportsTables']['id'];
				$tables[$fields['reportsTables']['table_name']]['table_display_name'] = $fields['reportsTables']['table_display_name'];
				$field_list[$fields['reportsTables']['table_name']][] = $fields;
			}
			/* Check has calcution field */
			//$field_list['calcutions'][0]=>array();
			//$final_field=(new ReportsReportType())->checkHasCalFields($table_ids);
			//$final_field=(new ReportsReportType())->checkHasCalFields($table_ids);
			$cals_ids=array();
			//if(!empty($final_field)){
				//$field_list['calcutions']=
				$cals=ArrayHelper::map(ReportsFieldCalculations::find()->select(['id','calculation_name'])->where("id IN (SELECT field_calculation_id FROM tbl_reports_report_type_field_calculation WHERE report_type_id={$id})")->all(),'id','calculation_name');
				foreach($cals as $cid=>$cval){
					$field_list['calcutions'][$cid] = array(
						'id' => $cid,
						'report_table_id' => $cid,
						'field_name' => 'cal',
						'field_display_name' => $cval,
						'reports_field_type_id' => "",
						'reportsFieldsRelationships' => array(),
						'filter'=>'',
						'grp'=>0,
						'reportsTables' => array('id' => 0,'table_name' => 'calculation','table_display_name' => 'Calculation')
					);
				}
			//}
			/*if(!empty($final_field)){
				$cals=ArrayHelper::map(ReportsFieldCalculations::find()->select(['id','calculation_name'])->where("id IN (".implode(',',$final_field).")")->all(),'id','calculation_name');
				foreach($cals as $cid=>$cval){
					if(!in_array($cid,$cals_ids)){
						$field_list['calcutions'][$cid] = array(
							'id' => $cid,
							'report_table_id' => $cid,
							'field_name' => 'cal',
							'field_display_name' => $cval,
							'reports_field_type_id' => "",
							'reportsFieldsRelationships' => array(),
							'reportsTables' => array('id' => 0,'table_name' => 'calculation','table_display_name' => 'Calculation')
						);
					}
				}
			}*/
			if(!isset($field_list['calcutions'])){
				$field_list['calcutions']=array();
			}
			/* end check calculation*/
			//echo "<pre>",print_r($field_list),print_r($table_ids),"</pre>";
			
			// data provider
			$dataProvider = new ArrayDataProvider([
				'allModels' => $field_list,
				'pagination' => [
					'pageSize' => -1,
				]
			]);
			
			$calculationList = ReportsFieldCalculations::find()->select(['field_calculation_id','calculation_name'])
				->innerJoinWith(['reportsReportTypeFieldCalculation' => function(\yii\db\ActiveQuery $query) use($id){
					$query->where(['report_type_id'=>$id]);
				}])->asArray()->all();    
			//echo "<pre>";print_r($dataProvider);die;
			// update report type
                            return $this->renderAjax('update-report-type', [
				'model' => $model,
				'dataProvider' => $dataProvider,
				'tables' => $tables,
				'calculationList'=>$calculationList,
				'field_relationships' => $field_relationships,
                'model_field_length'=>$model_field_length,
				'report_typefield_data'=>$report_typefield_data
			]);
        }
    }
    
    function actionDeleteReportTableName(){
		$table_name = Yii::$app->request->post('tbl_name');
		$primary_table = $session->get('report_type_table_primary');
		unset($primary_table[$table_name]);
		die();
    }
    /*
     * Action to delete the last table 
     * @param table name
     * @response Whole grid loaded with primary and secondary
     */
    public function actionDeleteReportTypeTableSecondary()
    {
        $table_name = Yii::$app->request->post('tbl_name');
        $table_type = Yii::$app->request->post('table_type');
        $session = Yii::$app->session;
        $primary_table = $session->get('report_type_table_primary');
        $secondary_table = $session->get('report_type_table_secondary');            
        if($table_type == 1 ){
            $primary_table = $session->set('report_type_table_primary','');
            $secondary_table = $session->set('report_type_table_secondary','');            
        }else if($table_type == 2){
            unset($secondary_table[$table_name]);
            $secondary_table = $session->set('report_type_table_secondary',$secondary_table);            
            $final_table = array_merge($session->get('report_type_table_primary'),$session->get('report_type_table_secondary'));           
            $result = [];
            foreach($final_table as $single_tbl){
                foreach($single_tbl as $single_fields){                    
                    if($single_fields['table_type'] == 1){  
                        $preprare_data['table_full_name'] = "Primary: ".$single_fields['table_name'];                    
                    }else if($single_fields['table_type'] == 2){                    
                        $preprare_data['table_full_name'] = "Secondary: ".$single_fields['table_name'];                    
                    }                            
                }
                $result[] = $preprare_data;
            }   
            $datProvider = new ArrayDataProvider([
                'allModels' => $result,
                'pagination' => [
                    'pageSize' => -1,
                ]
            ]);        
        }   
         return $this->renderAjax('show-primary-table-grid', [    		
    		'dataProvider' => $datProvider,
    	]);
    }
    /*Action to get the list of field from table
     */
    public function actionGetTableFieldDetails(){
        $reporttype_id = Yii::$app->request->get('reporttype_id',0);
        $expandRowKey=Yii::$app->request->post('expandRowInd',0);
        $id = '2';
        $query = ReportsReportTypeFields::find()
                                            ->select(['reporttype_id','table_name','field_name','field_display_name','relationship','join_type','reports_field_type_id'])
                                            ->where(['reporttype_id'=>$reporttype_id]); 
            $dataProvider = new ActiveDataProvider([
    		'query' => $query,
    		'pagination' =>['pageSize'=>-1],
    	]);
        $models = $dataProvider->getModels();        
        echo '<pre>',print_r($models);
        die('nelson');
    }
    
    /**
     * Deletes an existing Report Field Type.
     * If deletion is successful, the browser will be redirected to the 'Field type index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDeleteFieldType($id){
        /** Start : To remove Report Field Type */
     	$modelfieldtype=ReportsFieldType::findOne($id);        
        $modelfieldtype->delete();
        /** End : To remove Report Field Type */
        return 'OK';
    }
     /**
     * Deletes an existing Report Format.
     * If deletion is successful, the browser will be redirected to the 'Report Format index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDeleteReportFormat($id){
        /** Start : To remove Report Format */
     	$modelfieldtype=ReportsReportFormat::findOne($id);        
        $modelfieldtype->delete();
        /** End : To remove Report Format */
        return 'OK';
    }
    /**
     * Deletes an existing Report Chart Format.
     * If deletion is successful, the browser will be redirected to the 'Chart Format index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDeleteChartFormat($id){
        /** Start : To remove Report Chart Format */
     	$modelfieldtype=ReportsChartFormat::findOne($id);        
        $modelfieldtype->delete();
        /** End : To remove Report Chart Format */
        return 'OK';
    }
     /**
     * Deletes an existing Report Type.
     * If deletion is successful, the browser will be redirected to the 'Report Type index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDeleteReportType($id)
    { 
		$tran = Yii::$app->db->beginTransaction();
		$ret = "OK";
		try
		{    
			if(ReportsUserSaved::find()->where('report_type_id='.$id)->count()==0)
			{   
				ReportsReportTypeFieldCalculation::deleteAll('report_type_id='.$id);
				ReportsReportTypeFields::deleteAll('report_type_id='.$id);
				//ReportsReportTypeSql::deleteAll('reporttype_id='.$id);
				$modelfieldtype=ReportsReportType::findOne($id);        
				$modelfieldtype->delete();
				$tran->commit();
				$ret = "OK";
			}
			else
			{
				$ret = "NOT OK";
			}
		}
		catch(Exception $e)
		{
			$tran->rollBack();	
			$ret = "OK";
		}
        return $ret;
    }
    /**
     * Deletes an existing Report Type Field.
     * If deletion is successful, the browser will be redirected to the 'Report Type index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDeleteReportTypeField($report_type_id,$id,$table_name)
    { 
		$ret = "OK";
		$reports_fields_id = $id;
		try
		{    
			if($table_name != "Calculation")
			{
				$report_type_field_id_ar = ReportsReportTypeFields::find()
				->select(['id'])
				->where('report_type_id='.$report_type_id.' AND reports_fields_id='.$reports_fields_id)
				->asArray()
				->all();
				$report_type_field_id = $report_type_field_id_ar[0]['id'];
				
				if(ReportsUserSavedFields::find()->where('report_type_field_id='.$report_type_field_id)->count()==0)
				{
					$ret = "OK";
				}
				else
				{
					$ret = "NOT OK";
				}
			}
			else
			{
				$field_calculation_id = $reports_fields_id;
				if(ReportsUserSavedFields::find()->joinWith(['reportsUserSaved'])->where('report_type_id='.$report_type_id.' AND field_calculation_id='.$field_calculation_id)->count()==0)
				{ 
					$ret = "OK";
				}
				else
				{
					$ret = "NOT OK";
				}
			}
		}
		catch(Exception $e)
		{	
			$ret = "OK";
		}
        return $ret;
    }
    /**
     * Deletes an existing Report Type Table.
     * If deletion is successful, the browser will be redirected to the 'Report Type index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDeleteReportTypeTable($report_type_id,$table_name)
    { 
		$ret = "OK";
		try
		{    
			if($table_name != "Calculation")
			{
				$report_table_id_ar = ReportsTables::find()
				->select(['id'])
				->where("table_name='".$table_name."'")
				->asArray()
				->all();
				$report_table_id = $report_table_id_ar[0]['id'];
				
				$reportsFields = ReportsFields::find()->select(['id'])->where(['report_table_id'=>$report_table_id]);
				$reportsreportTypeFields = ReportsReportTypeFields::find()->select(['id'])->where(['in','reports_fields_id',$reportsFields])->andWhere(['report_type_id'=>$report_type_id]);
				$reportsuserSavedFieldscount = ReportsUserSavedFields::find()->select(['id'])->where(['in','report_type_field_id',$reportsreportTypeFields])->count();
				if($reportsuserSavedFieldscount == 0)
				{
					$ret = "OK";
				}
				else
				{
					$ret = "NOT OK";
				}
			}
		}
		catch(Exception $e)
		{	
			$ret = "OK";
		}
        return $ret;
    }
     /**
     * Check is calculation field is in use or not.
     */
	public function actionCheckIsdelcalcfield(){
		$id=Yii::$app->request->post('calc_id',0);
		if(ReportsReportTypeFieldCalculation::find()->where('field_calculation_id='.$id)->count()==0){
			return "OK";
		}else{
			return "InUse";
		}
	}
	public function actionGetRelatedTableAndFn(){
		$post_data['primary_table_name']=$post_data['table_name']=$id=Yii::$app->request->post('table_id',0);
		$post_data['flag'] = 'relationship';
		$where = $where_outer = '1=1';	
        if(!empty($post_data)){
			if(isset($post_data['table_name']) && !empty($post_data['table_name'])){
				$table_list = $post_data['primary_table_name'];
				$whereAttach = '1=1';
				if($post_data['flag'] == 'relationship'){
					//$tableNotInclude = implode(",",$post_data['table_name']);
					//$whereAttach .= " AND t1.id NOT IN ($tableNotInclude)";
				}
				$prefix = 'SELECT * FROM (';
				$suffix = ') as t1 WHERE '.$whereAttach;
				$wherePart = '';
				if(isset($post_data['flag']) && $post_data['flag'] == 'relationship'){
					$sqlpart = "SELECT table_name FROM tbl_reports_tables WHERE id IN ({$table_list})";
					$wherePart = "tableslist.table_name IN (SELECT tbl_reports_fields_relationships.rela_table FROM tbl_reports_fields_relationships WHERE rela_base_table IN ($sqlpart) UNION ALL SELECT tbl_reports_fields_relationships.rela_base_table FROM tbl_reports_fields_relationships WHERE rela_table IN ($sqlpart))";
					
				}
				$where .= " AND ($wherePart) OR tableslist.id IN ($id)";
			}
		}
		
		$current_table = (new ReportsReportType)->getTablesFromReports('COUNT(DISTINCT tablefields.id) as COLUMN_COUNT, tableslist.id, tableslist.table_name, tableslist.table_display_name', $where, '', 'GROUP BY tableslist.id, tableslist.table_name, tableslist.table_display_name',$prefix,$suffix); // params = select, where, order, group
		$table_ids=array();
		$table_fields=array();
		$function=array();
		if(!empty($current_table)){
			foreach($current_table as $ctable){
					$table_ids[$ctable['id']]=$ctable['id'];
			}
		}
		if(!empty($table_ids)){
			$sql="SELECT function_id FROM tbl_reports_calculation_function_params where report_fields_id IN (select id from tbl_reports_fields where tbl_reports_fields.report_table_id in (".implode(',',$table_ids).")) ";
			$function=ArrayHelper::map(ReportsCalculationFunction::find()->select(['id','function_name'])->where("id IN ($sql)")->all(),'id','function_name');
			foreach($table_ids as $table_id){
				$table_fields[$table_id]=ArrayHelper::map(ReportsFields::find()->select(['id','field_name'])->where(['report_table_id'=>$table_id])->all(),'id','field_name');
			}
		}
		return json_encode(array('tables'=>$current_table,'table_fields'=>$table_fields,'functions'=>$function));
	}
	
	public function actionGetFnwithparams(){
		$id=Yii::$app->request->post('fn_id',0);
		$function=ReportsCalculationFunction::findOne($id);
		$fn_name = $function->function_name;
		$reportFields=ReportsFields::find()->select(['id','field_name','report_table_id'])->where("id IN (SELECT report_fields_id FROM tbl_reports_calculation_function_params WHERE function_id={$id})")->all();
		$params="";
		if(!empty($reportFields)){
			foreach($reportFields as $rfield){
					if($params=="")
						$params=$rfield->reportsTables->table_name.".".$rfield->field_name;
					else
						$params.=",".$rfield->reportsTables->table_name.".".$rfield->field_name;
			}
		}
		return $fn_name."(".$params.")";
	}
	
     /**
     * Deletes an existing Report Field Calculation.
     * If deletion is successful, the browser will be redirected to the 'Field Calculation index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDeleteFieldCalculation($id){   
		//need to check if calculation field already used in report_type
		     
     	$modelfieldtype=ReportsFieldCalculations::findOne($id);
     	ReportsFieldCalculationTable::deleteAll('field_cal_id='.$id);
        $modelfieldtype->delete();        
        return 'OK';
    }
    
    /**
     * Deletes an existing Report Function Calculation.
     * If deletion is successful, the browser will be redirected to the 'Field Calculation index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDeleteCalculationFunction($id){        
     	$modelfieldtype=ReportsCalculationFunction::findOne($id);        
     	//Yii::$app->db->createCommand("DROP FUNCTION IF EXISTS ".trim($modelfieldtype->function_name).";")->execute();
     	if(Yii::$app->db->driverName == 'mysql')
     	{
			Yii::$app->db->createCommand("DROP FUNCTION IF EXISTS ".trim($modelfieldtype->function_name).";")->execute();
		}
		else
		{
			Yii::$app->db->createCommand("IF object_id(N'".trim($modelfieldtype->function_name)."', N'FN') IS NOT NULL
			DROP FUNCTION ".trim($modelfieldtype->function_name))->execute();	
		} 
     	//ReportsCalculationFunctionTable::deleteAll('function_id='.$id);
     	ReportsCalculationFunctionParams::deleteAll('function_id='.$id);
        $modelfieldtype->delete();        
        return 'OK';
    }
    
    /**
     * Deletes an existing Report Sp Calculation.
     * If deletion is successful, the browser will be redirected to the 'Field Calculation index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDeleteCalculationSp($id){        
     	$modelfieldtype=ReportsCalculationSp::findOne($id);        
     	//Yii::$app->db->createCommand("DROP PROCEDURE IF EXISTS ".trim($modelfieldtype->sp_name).";")->execute();
     	if(Yii::$app->db->driverName == 'mysql')
     	{
			Yii::$app->db->createCommand("DROP PROCEDURE IF EXISTS ".trim($modelfieldtype->sp_name).";")->execute();
		}
		else
		{
			Yii::$app->db->createCommand("IF EXISTS (select * from dbo.sysobjects where id = object_id(N'[dbo].[".trim($modelfieldtype->sp_name)."]') and OBJECTPROPERTY(id, N'IsProcedure') = 1)
			DROP PROCEDURE [dbo].[".trim($modelfieldtype->sp_name)."]")->execute();
		}   
     	ReportsCalculationSpTable::deleteAll('sp_id='.$id);
     	ReportsCalculationSpParams::deleteAll('sp_id='.$id);
        $modelfieldtype->delete();        
        return 'OK';
    }
    
    /**
     * Deletes an existing Report Field Operartor.
     * If deletion is successful, the browser will be redirected to the 'Field type index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDeleteFieldOperator($id){
        /** Start : To remove Report Field Operator*/
        $delete = ReportsFieldTypeOperatorLogic::deleteAll('fieldoperator_id='.$id);
     	$modelfieldtype=ReportsFieldOperators::findOne($id);        
        $modelfieldtype->delete();
        /** End : To remove Report Field Operator */
        return 'OK';
    }
    
    /**
     * Deletes an existing Report Field Type.
     * If deletion is successful, the browser will be redirected to the 'Field type index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDeleteFieldLookup($id){
        /** Start : To remove Report Field Type */
        ReportsLookupValues::deleteAll('reports_lookup_id='.$id);
     	$modelfieldlookup=ReportsLookups::findOne($id);
        $modelfieldlookup->delete();
        /** End : To remove Report Field Type */
        return 'OK';
    }
     /**
     * Deletes an existing Chart Display By.
     * If deletion is successful, the browser will be redirected to the 'chart Display By index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDeleteChartDisplayBy($id){
        /** Start : To remove Report Field Operator*/
        $delete = ReportsChartFormatDisplayLogic::deleteAll('chartformat_displayby_id='.$id);
     	$modelfieldtype=ReportsChartFormatDisplayBy::findOne($id);        
        $modelfieldtype->delete();
        /** End : To remove Report Field Operator */
        return 'OK';
    }
    /*
     * Get all list of Report Field Types
    * @param integer $id
    * @return All List of Field types
    */
    public function actionGetReportFieldtypeLists($id){
        $id = Yii::$app->request->get('id');			
        $post_data = Yii::$app->request->post();                    
        $field_typeids = $theme_ids= [];
        if(isset($post_data['field_typeids']) && $post_data['field_typeids'] != ''){
            $field_typeids = explode(',',$post_data['field_typeids']);            
        }
        $fieldstypeList = ReportsFieldType::find()
			->select(['id', 'field_type','field_type_theme_id'])                            
			->orderBy('field_type_theme_id, field_type ASC')
			->where(['not in','id',$field_typeids])
			->asArray()
			->all();         
        foreach($fieldstypeList as $single_ids){
            if(!in_array($single_ids['field_type_theme_id'],$theme_ids)){
                $theme_ids[] = $single_ids['field_type_theme_id'];
            }
        }       
//        echo '<pre>',print_r($theme_ids);die;
        $themeList = ReportsFieldTypeTheme::find()->select(['id', 'field_type_theme'])
                ->where(['in','id',$theme_ids])->orderBy('id')->asArray()->all();                                                  
        return $this->renderAjax('report-fieldtype-lists', [
    		'model' => $model,
    		'fieldstypeList' => $fieldstypeList,
                'themeList'=>$themeList
    	]);
    }
    /*
     * Get all list of Database Tables
    * @param integer $id
    * @return All List of Database Table
    */
    public function actionGetTableLists(){
	      
        $post_data = Yii::$app->request->post();  
        $filter_data = array();
        $where = $where_outer = '1=1';	
        if(!empty($post_data)){
			if(isset($post_data['table_name']) && !empty($post_data['table_name'])){
				$table_list = $post_data['primary_table_name'];
				/*$whereBeg = "CONCAT(t1.id,'-',t1.COLUMN_COUNT) NOT IN (";
				$whereAr = array();
				foreach($post_data['table_name'] as $table){
					$whereAr[] = "('".$table."-".count($post_data['field_lists'][$table])."')";
				}
				$whereEnd = ")";*/
				$whereAttach = '1=1';
				if($post_data['flag'] == 'relationship'){
					$tableNotInclude = implode(",",$post_data['table_name']);
					$whereAttach .= " AND t1.id NOT IN ($tableNotInclude)";
				}
				
				//if(!empty($whereAr)){
					$prefix = 'SELECT * FROM (';
					//$where_outer = $whereBeg.implode(", ",$whereAr).$whereEnd;
					//$suffix = ') as t1 WHERE '.$where_outer.$whereAttach;
					$suffix = ') as t1 WHERE '.$whereAttach;
				//}
				//$table_list = implode(',',$post_data['table_name']);
				
				$wherePart = '';
				if(isset($post_data['flag']) && $post_data['flag'] == 'relationship'){
					//$wherePart = "(tableslist.table_name IN (SELECT tbl_reports_fields_relationships.rela_table FROM tbl_reports_fields_relationships INNER JOIN tbl_reports_fields ON tbl_reports_fields.id = tbl_reports_fields_relationships.rela_base_field INNER JOIN tbl_reports_tables ON tbl_reports_tables.id = tbl_reports_fields.report_table_id WHERE rela_base_table IN ((SELECT table_name FROM tbl_reports_tables WHERE id IN ({$table_list}) AND tbl_reports_fields_relationships.rela_type = 0) OR (SELECT rela_base_table FROM tbl_reports_fields_relationships WHERE tbl_reports_fields_relationships.rela_table IN (SELECT table_name FROM tbl_reports_tables WHERE id IN ({$table_list}))))))";
					$sqlpart = "SELECT table_name FROM tbl_reports_tables WHERE id IN ({$table_list})";
					$wherePart = "tableslist.table_name IN (SELECT tbl_reports_fields_relationships.rela_table FROM tbl_reports_fields_relationships WHERE rela_base_table IN ($sqlpart) UNION ALL SELECT tbl_reports_fields_relationships.rela_base_table FROM tbl_reports_fields_relationships WHERE rela_table IN ($sqlpart))";
					
				} else if (isset($post_data['flag']) && $post_data['flag'] == 'addfields') {
					$wherePart = "(tablefields.report_table_id IN ({$table_list}))";
				}
				$where .= " AND ($wherePart)";
			}
		}
		
		$current_table = (new ReportsReportType)->getTablesFromReports('COUNT(DISTINCT tablefields.id) as COLUMN_COUNT, tableslist.id, tableslist.table_name, tableslist.table_display_name', $where, '', 'GROUP BY tableslist.id, tableslist.table_name, tableslist.table_display_name',$prefix,$suffix); // params = select, where, order, group /* ORDER BY tableslist.table_name */
		
	    return $this->renderAjax('get-table-lists', [
    		'tables_list' => $current_table,
    		'primary_table_name' => $table_list,
    		'flag' => $post_data['flag']
    	]);
    }
    
    public function actionGetCalculationLists(){
		$post_data = Yii::$app->request->post();  
		$table_names= $post_data['table_name'];
		$field_calculation=$post_data['field_calculation'];
		$calculation=array();
		if(!empty($table_names)){
			//$final_field=(new ReportsReportType())->checkHasCalFields(array_values($table_names));
			$final_field=(new ReportsReportType())->checkHasCalFields($table_names);
			//echo "<pre>",print_r($final_field),print_r($table_names),"</pre>";die;
			if(!empty($final_field)){
    			if(!empty($field_calculation)){
    				$calculation=ArrayHelper::map(ReportsFieldCalculations::find()->select(['id','calculation_name'])->where("id NOT IN (".implode(',',$field_calculation).") AND id IN (".implode(',',$final_field).")")->all(),'id','calculation_name');
    			}else{
    				$calculation=ArrayHelper::map(ReportsFieldCalculations::find()->select(['id','calculation_name'])->where("id IN (".implode(',',$final_field).")")->all(),'id','calculation_name');
    			}
			}
		}
		return $this->renderAjax('get-calculation-lists', [
    		'calculation' => $calculation,
    		'flag' => 'add-calfield'
    	]);
	}
    public function actionGetRelatedTableLists(){
	    $post_data = Yii::$app->request->post();  
        $filter_data = array();
        $where = $where_outer = '1=1';	
        if(!empty($post_data)){
			if(isset($post_data['primary_table_name']) && !empty($post_data['primary_table_name'])){
				$table_list = $post_data['primary_table_name'];
				$whereAttach = '1=1';
				$prefix = 'SELECT * FROM (';
				$suffix = ') as t1 WHERE '.$whereAttach;
				$wherePart = '';
				$sqlpart = "SELECT table_name FROM tbl_reports_tables WHERE id IN ({$table_list})";
				$wherePart = " (tableslist.table_name IN ($sqlpart) OR tableslist.table_name IN (SELECT tbl_reports_fields_relationships.rela_table FROM tbl_reports_fields_relationships WHERE rela_base_table IN ($sqlpart) UNION ALL SELECT tbl_reports_fields_relationships.rela_base_table FROM tbl_reports_fields_relationships WHERE rela_table IN ($sqlpart)))";
				$where .= " AND ($wherePart)";
				$where .=" AND tableslist.id NOT IN ({$post_data['primary_table_name']})";
			}
		}
		
		$current_table = (new ReportsReportType)->getTablesFromReports('COUNT(DISTINCT tablefields.id) as COLUMN_COUNT, tableslist.id, tableslist.table_name, tableslist.table_display_name', $where, '', 'GROUP BY tableslist.id, tableslist.table_name, tableslist.table_display_name',$prefix,$suffix); // params = select, where, order, group
		
		//echo "<pre>",print_r($current_table),"</pre>";
	    return $this->renderAjax('get-related-table-lists', [
    		'tables_list' => $current_table,
    	]);
    }
    
    public function actionGetTableFields()
     {
		$post_data = Yii::$app->request->post();
		$table_name = $post_data['selected_table'];
		
		$table_org_name = ReportsTables::findOne($table_name)->table_name;
		$primary_table = $post_data['primary_table_name'];
		$primary_table_name =  ReportsTables::findOne($primary_table)->table_name;
		$field_relationship_id = ReportsFieldsRelationships::find()->select(['tbl_reports_fields_relationships.id','tbl_reports_fields_relationships.rela_base_field'])
		->where(['rela_base_table'=>$primary_table_name, 'rela_table' => $table_org_name])
		->orWhere(['rela_table'=>$primary_table_name, 'rela_base_table' => $table_org_name])
		->one()->id;
		
		$fieldexist = array();
		if(isset($post_data['field_lists'][$table_name]) && !empty($post_data['field_lists'][$table_name])){
			$fieldexist = $post_data['field_lists'][$table_name];
		}
		$where = "tableslist.id='{$table_name}'";
		if(!empty($fieldexist))
			$where .= " AND tablefields.id NOT IN (".implode(",",$fieldexist).")";
			
         $current_table = (new ReportsReportType)->getTablesFromReports("tableslist.id, tableslist.table_name, tableslist.table_display_name, tablefields.id as field_id, tablefields.field_name, tablefields.field_display_name",$where,"","GROUP BY tableslist.id, tableslist.table_name, tableslist.table_display_name, tablefields.id, tablefields.field_name, tablefields.field_display_name"); 
		 $fieldstypeList = ReportsFieldType::find()->select(['id', 'field_type'])->orderBy('id')->all();                        
         $fieldtypeselectList = ArrayHelper::map($fieldstypeList,'field_type','id'); 
         
		 return $this->renderAjax('get-table-fields', [
			 'table_name' => $table_name,
    		 'current_table'=>$current_table,
             'fieldtypeselectList'=>$fieldtypeselectList,
             'post_data'=>$post_data,
             'primary_table' => $primary_table,
             'field_relationship_id' => $field_relationship_id
        ]);
	 }
    
    /**
     * Get all table field lists from popup (Add Fields from Primary Table)
     * @params interger $id
     * @return All lists of Database Table Fields
     */
     public function actionGetTableFieldLists()
     {
		$post_data = Yii::$app->request->post();
		$table_name = $post_data['selected_table'];
		
		$table_org_name = ReportsTables::findOne($table_name)->table_name;
		$primary_table = $post_data['primary_table_name'];
		$primary_table_name =  ReportsTables::findOne($primary_table)->table_name;
		$field_relationship_id = ReportsFieldsRelationships::find()->select(['tbl_reports_fields_relationships.id','tbl_reports_fields_relationships.rela_base_field'])
		->where(['rela_base_table'=>$primary_table_name, 'rela_table' => $table_org_name])
		->orWhere(['rela_table'=>$primary_table_name, 'rela_base_table' => $table_org_name])
		->one()->id;
		
		$fieldexist = array();
		if(isset($post_data['field_lists'][$table_name]) && !empty($post_data['field_lists'][$table_name])){
			$fieldexist = $post_data['field_lists'][$table_name];
		}
		$where = "tableslist.id='{$table_name}'";
		if(!empty($fieldexist))
			$where .= " AND tablefields.id NOT IN (".implode(",",$fieldexist).")";
			
         $current_table = (new ReportsReportType)->getTablesFromReports("tableslist.id, tableslist.table_name, tableslist.table_display_name, tablefields.id as field_id, tablefields.field_name, tablefields.field_display_name",$where,"","GROUP BY tableslist.id, tableslist.table_name, tableslist.table_display_name, tablefields.id, tablefields.field_name, tablefields.field_display_name"); 
		 $fieldstypeList = ReportsFieldType::find()->select(['id', 'field_type'])->orderBy('id')->all();                        
         $fieldtypeselectList = ArrayHelper::map($fieldstypeList,'field_type','id'); 
         
		 return $this->renderAjax('get-table-field-lists', [
			 'table_name' => $table_name,
    		 'current_table'=>$current_table,
             'fieldtypeselectList'=>$fieldtypeselectList,
             'post_data'=>$post_data,
             'primary_table' => $primary_table,
             'field_relationship_id' => $field_relationship_id
        ]);
	 }
    
    /*
     * Action to load all db table schema in session
     */
    public function actionAddDbtablesReportType(){        
        $session = Yii::$app->session;        
        $current_table = $session->get('report_type_db_table');
        $db_table_count = $session->get('report_type_db_table_count');
        $connection = Yii::$app->db;//get connection        
        $dbSchema = $connection->schema;
        $tables = $dbSchema->getTableSchemas();//returns array of tbl schema's*/                
        if(count($tables) == $db_table_count){
            echo 'loaded';
        }else{            
            $session->set('report_type_db_table',  $tables);
            $session->set('report_type_db_table_count',  count($tables));            
            echo 'success';
        }
        die;
    }
     /*
     * Get first list of Secondary Tables
    * @param integer $id
    * @return All List of Database Table
    */
    public function actionGetSecondTableLists(){
        $session = Yii::$app->session;        
        $current_table = $session->get('report_type_db_table');
        $current_table = array_slice($current_table,0,24);
        $post_data = Yii::$app->request->post();    
        $fieldstypeList = ReportsFieldType::find()->select(['id', 'field_type'])->orderBy('id')->all();                        
        $fieldtypeselectList = ArrayHelper::map($fieldstypeList,'field_type','id');        
        
        return $this->renderAjax('get-second-table-lists', [
    		'tables_list'=>$current_table,
                'post_data'=>$post_data,
                'fieldtypeselectList'=>$fieldtypeselectList
    	]);        
    }
     /*
     * Get more list of Secondary Tables
    * @param integer $id
    * @return All List of Database Table
    */
    public function actionGetMoreSecondTableLists(){
        $session = Yii::$app->session;        
        $current_table = $session->get('report_type_db_table');                     
        $post_data = Yii::$app->request->post();          
        $total_tables = $post_data['total_table_count'];
        //echo "<pre>{$post_data['total_table_count']} - $total_tables = ",print_r($post_data),print_r($current_table),"</pre>";
        $post_data  = json_decode(base64_decode($post_data['table_loaded']),true);
        
        $current_table = array_slice($current_table,$total_tables,24);
        
    	/*foreach ($current_table as $table){
        	echo $table->name."<br/>";
        }*/
        $fieldstypeList = ReportsFieldType::find()->select(['id', 'field_type'])->orderBy('id')->all();                        
        $fieldtypeselectList = ArrayHelper::map($fieldstypeList,'field_type','id');                
        return $this->renderAjax('get-more-second-table-lists', [
    		'tables_list'=>$current_table,
                'post_data'=>$post_data,
                'fieldtypeselectList'=>$fieldtypeselectList,
                'total_tables'=>$total_tables
    	]);        
    }
    /*
     * Get all list of Chart Format List
    * @param integer $id
    * @return All List of all Chart Format List
    */
    public function actionGetChartFormatLists($id){
         $id = Yii::$app->request->get('id');	
         $post_data = Yii::$app->request->post();     
         $chart_ids =  [];
         
        if(isset($post_data['chart_ids']) && $post_data['chart_ids'] != ''){
            $chart_ids = explode(',',$post_data['chart_ids']);            
        }
       
        $chartformatList = ReportsChartFormat::find()->select(['id', 'chart_format'])
                                                    ->where(['not in','id',$chart_ids])->orderBy('id')->all();                                                            
        return $this->renderAjax('get-chart-format-lists', [
    		'model' => $model,
    		'chartformatList' => $chartformatList,
    	]);
    }
    /*
     * Get all list of Field Calculation
     * @param integer $id
     * @return All List of Field Calcultion
     */
    public function actionGetFieldCalculationLists($id){      
        $post_data = Yii::$app->request->post();     
        $fc_ids =  [];
         
        if(isset($post_data['fc_ids']) && $post_data['fc_ids'] != ''){
            $fc_ids = explode(',',$post_data['fc_ids']);            
        }
        $fieldcalculationList = ReportsFieldCalculations::find()->select(['id','calculation_name'])
        ->where(['not in','id',$fc_ids])->orderBy('calculation_name ASC')->all();        
        return $this->renderAjax('get-field-calculation-lists', [    		
    		'fieldcalculationList' => $fieldcalculationList,
    	]);
    }
    
    /*
     * Action to show the grid view from the selected fields fromt he pop up 
     */
    public function actionShowPrimaryTableGrid(){
        $post_data = Yii::$app->request->post();
        $field_relationships = $post_data['field_lists_relationship'];
        $field_list = array();
        $tables = array();
		if(!empty($post_data['column_field_name'])){
			if(isset($post_data['field_lists']) && !empty($post_data['field_lists'])){
				foreach($post_data['field_lists'] as $k => $fields){
					foreach($fields as $field) {
						$post_data['column_field_name'][] = $field;
					}
				}
			}
			$orderBy = "tbl_reports_fields.id ASC";
			if(is_array($post_data['table_name']) && !empty($post_data['table_name'])){
				$orderBy = "(CASE tbl_reports_tables.id";
				$i = 1;
				foreach($post_data['table_name'] as $table){
					$orderBy .= " WHEN $table THEN $i ";
					$i++;
				}
				$orderBy .= " WHEN {$post_data['selected_table_name']} THEN ".$i++." ";
				$orderBy .= " ELSE ".$i++." END), tbl_reports_fields.id ASC";
			}
			
			$fieldListAr = ReportsFields::find()
				->joinWith([
					'reportsFieldsRelationships' => function(\yii\db\ActiveQuery $query){
						$query->joinWith('reportsFieldsRelationshipsLookups');
					}
				])
				->innerJoinWith('reportsTables')
				->where(['in','tbl_reports_fields.id',$post_data['column_field_name']]);
			$fieldListAr->orderBy($orderBy);
			$fieldList = $fieldListAr->asArray()->all();
			
			foreach($fieldList as $fields){
				$tables[$fields['reportsTables']['table_name']]['id'] = $fields['reportsTables']['id'];
				$tables[$fields['reportsTables']['table_name']]['table_display_name'] = $fields['reportsTables']['table_display_name'];
				$field_list[$fields['reportsTables']['table_name']][] = $fields;
			}
		}
		if(!empty($post_data['cal_field_name'])){
			$field_lists=array();
			foreach($post_data['field_lists'] as $fkey=>$fdata){
			$field_lists=array_merge($fdata,$field_lists);	
			}
			//echo "<pre>",print_r($field_lists),"</pre>";
			//die;
			$fieldListAr = ReportsFields::find()
				->joinWith([
					'reportsFieldsRelationships' => function(\yii\db\ActiveQuery $query){
						$query->joinWith('reportsFieldsRelationshipsLookups');
					}
				])
				->innerJoinWith('reportsTables')
				->where(['in','tbl_reports_fields.id',$field_lists]);
			$fieldListAr->orderBy($orderBy);
			$fieldList = $fieldListAr->asArray()->all();
			
			foreach($fieldList as $fields){
				$tables[$fields['reportsTables']['table_name']]['id'] = $fields['reportsTables']['id'];
				$tables[$fields['reportsTables']['table_name']]['table_display_name'] = $fields['reportsTables']['table_display_name'];
				$field_list[$fields['reportsTables']['table_name']][] = $fields;
			}
			$final_field=array(0);
			if(isset($post_data['field_calculation'])){
				$final_field= $post_data['field_calculation'];
			}
			
			foreach($post_data['cal_field_name'] as $cf){
				$final_field[$cf]=$cf;
			}
			$cals=ArrayHelper::map(ReportsFieldCalculations::find()->select(['id','calculation_name'])->where("id IN (".implode(',',$final_field).")")->all(),'id','calculation_name');
			foreach($cals as $cid=>$cval){
				$field_list['calcutions'][$cid] = array(
					'id' => $cid,
					'report_table_id' => $cid,
					'field_name' => 'cal',
					'field_display_name' => $cval,
					'reports_field_type_id' => "",
					'reportsFieldsRelationships' => array(),
					'reportsTables' => array('id' => 0,'table_name' => 'calculation','table_display_name' => 'Calculation')
				);
			}
		}else{
			/* Check has calcution field */
			/*$table_ids=array($post_data['selected_table_name']);
			if(is_array($post_data['table_name']) && !empty($post_data['table_name'])){
				$post_data['table_name'][]=$post_data['selected_table_name'];
				$table_ids=$post_data['table_name'];
				
			}
			$final_field=(new ReportsReportType())->checkHasCalFields($table_ids);*/
			$final_field=array();
			if(isset($post_data['field_calculation']) && !empty($post_data['field_calculation'])){
				$final_field=$post_data['field_calculation'];
			}
			if(!empty($final_field)){
				//$field_list['calcutions']=
				$cals=ArrayHelper::map(ReportsFieldCalculations::find()->select(['id','calculation_name'])->where("id IN (".implode(',',$final_field).")")->all(),'id','calculation_name');
				foreach($cals as $cid=>$cval){
					$field_list['calcutions'][$cid] = array(
						'id' => $cid,
						'report_table_id' => $cid,
						'field_name' => 'cal',
						'field_display_name' => $cval,
						'reports_field_type_id' => "",
						'reportsFieldsRelationships' => array(),
						'reportsTables' => array('id' => 0,'table_name' => 'calculation','table_display_name' => 'Calculation')
					);
				}
			}
		}
		if(!isset($field_list['calcutions'])){
			$field_list['calcutions']=array();
		}
		/* end check calculation*/
		
		// data provider
	    $datProvider = new ArrayDataProvider([
            'allModels' => $field_list,
            'pagination' => [
                'pageSize' => -1,
            ]
        ]);
        //echo "<pre>",print_r($field_list),"</pre>";die;
        
        return $this->renderAjax('show-primary-table-grid', [
    		'dataProvider' => $datProvider,
    		'tables' => $tables,
    		'field_relationships' => $field_relationships
    	]);
    }
    /*
      * Change Display Field by table name
    * @param string Display name 
    * @return success
     */
    public function actionAddSecondaryTableGrid(){
        $session = Yii::$app->session;
        $post_data = Yii::$app->request->post();
        $table_name = $post_data['table_list']['tabledetail'][0]['table_name'];
        $result_data[$table_name] = $post_data['table_list']['tabledetail'];
        
//        $primary_tbl = $session->get('report_type_table_primary');
//        $tbl_name = explode('.',$post_data['relation_field'])[0];        
//        $primary_tbl[$tbl_name][$post_data['table_index']]['relationship']= $post_data['relation_name'];
//        $primary_tbl[$tbl_name][$post_data['table_index']]['join_type']=$post_data['relation_join_type'];        
//        $session->set('report_type_table_primary',$primary_tbl);
//        
        //relation_field
        $secondary_data = $session->get('report_type_table_secondary');                
        if(empty($secondary_data)){
            $session->set('report_type_table_secondary', $result_data);                
        }else {
            $secondary_data = array_merge($secondary_data,$result_data);
            $session->set('report_type_table_secondary', $secondary_data);                
        }        
        $final_table = array_merge($session->get('report_type_table_primary'),$session->get('report_type_table_secondary'));        
        $final_table_names = array_keys($final_table);
        $result = [];
        foreach($final_table as $single_tbl){
            foreach($single_tbl as $single_fields){
                //echo '<pre>';print_r($single_tbl);die;                
                if($single_fields['table_type'] == 1){  
                    $preprare_data['table_full_name'] = "Primary: ".$single_fields['table_name'];                    
                }else if($single_fields['table_type'] == 2){                    
                    $preprare_data['table_full_name'] = "Secondary: ".$single_fields['table_name'];                    
                }                            
            }
            $result[] = $preprare_data;
        }           
        $datProvider = new ArrayDataProvider([
            'allModels' => $result,
            'pagination' => [
                'pageSize' => -1,
            ]
        ]);                
         return $this->renderAjax('show-primary-table-grid', [    		
    		'dataProvider' => $datProvider,
    	]);       
    }
    /*
      * Change Display Field by table name
    * @param string Display name 
    * @return success
     */
    public function actionReportTypeChangeDisplayName(){
        $session = Yii::$app->session;
        $post_data = Yii::$app->request->post();
        $table_primary = $session->get('report_type_table_primary');
        $table_primary[$post_data['table_name']][$post_data['array_index']]['field_display_name'] = $post_data['display_name'];
        $session->set('report_type_table_primary', $table_primary);        
        echo 'success';
        die;        
    }
    
    /**
     * 
     */
     public function actionGetTableRelationFields(){
		$data = Yii::$app->request->post();
		if($data != ''){
			if(!empty($data)){
				$primary_table = Yii::$app->request->post('primary_table');
				$table_relation = Yii::$app->request->post('table_relation');
				$table_fields = array();
				$firstDD_table_list = array();
				
				// table relation
				$relationshipexist = 0;
				if($table_relation!='' && $table_relation!='[]'){
					$firstDD_table_list = array();
					$relation = array();
					foreach($table_relation as $tval){
						$relation = json_decode($tval, true); // primary table
						if(!empty($relation)){
							foreach($relation as $tables){
								$relationshipexist = 1;
								$firstDD_table_list[$tables['primary_table_name']] = $tables['primary_table_name'];
								$firstDD_table_list[$tables['secondary_table_name']] = $tables['secondary_table_name'];
							}
						}	
					}
				}
				
				// sel table
				foreach($data['sel_table'] as $val){
					$res = json_decode($val, true); // primary table
					foreach($res as $dval){
						$table_fields[$dval['table_name']][] = $dval;
						if($relationshipexist == 0){
							$firstDD_table_list[$dval['table_name']] = $dval['table_name'];
						} 
					}
				}
			}
		}
		return $this->renderAjax('get-table-relationships', [    		
    		'data' => $data,
    		'table_fields' => $firstDD_table_list,
    		'filter_data' => json_encode($table_fields),
    		'filter_relation' => json_encode($relation),
    	]);
	 }
	 
	 /**
	  * 
	  */
	 public function actionGetRelationshipsTableFields(){
		 $filter_data = json_decode(Yii::$app->request->post('filter_data'),true);
		 $secondary_table_data_lists = $filter_data;
		 $pr_table = Yii::$app->request->post('pr_table');
		 if(!empty($filter_data)){
			 $pr_table_fields = array();
			 foreach($filter_data[$pr_table] as $val){
				$pr_table_fields[$pr_table][] = $val; 
			 }	
			 unset($secondary_table_data_lists[$pr_table]);
		 }
		 
		 // result 
		 $result['primary_fields'] = $this->renderAjax('get-primary-table-fields-relation',['pr_table_fields' => $pr_table_fields, 'pr_table' => $pr_table]);
		 $result['secondary_fields'] = $this->renderAjax('get-secondary-table-fields-relation',['sr_table_fields' => $secondary_table_data_lists, 'filter_data' => json_encode($filter_data)]);
		 return json_encode($result);
	 }
	 
	 
	 /**
	  * Get Secondary Table lists
	  * @return 
	  */
	  public function actionGetSecondaryTableLists(){
		  $sr_table = Yii::$app->request->post('scr_table');
		  $filter_data = json_decode(Yii::$app->request->post('filter_data'),true);
		  if(!empty($filter_data)){
			$sr_table_fields = array();
			foreach($filter_data[$sr_table] as $cval){
				$sr_table_fields[$sr_table][] = $cval; 
			}
		  }
		  return $this->renderAjax('get-secondary-table-relation',['sr_table_fields' => $sr_table_fields, 'sr_table' => $sr_table]);
	  }
    
    
    /*
     * Get all list of Field by table name
     * @param string table name
     * @return All List of Field Names with the session stored table
     */
   /* public function actionGetPrimaryTableFields(){
        $session = Yii::$app->session;
        $post_data = Yii::$app->request->post();
        if(!empty($session->get('report_type_table_secondary'))){
            $final_table = array_merge($session->get('report_type_table_primary'),$session->get('report_type_table_secondary'));
        }else {
            $final_table = $session->get('report_type_table_primary');
        }
        $final_table_names = array_keys($final_table);        
        $result = [];
        foreach($final_table_names as $single_tbl){
            $preprare_data['table_name'] = $single_tbl;
            $result[]=$preprare_data;
        }
        $table_name = $result[$post_data['expandRowInd']]['table_name'];        
        $primary_fields = $final_table[$table_name];
        
        return $this->renderAjax('get-primary-table-fields', [    		
    		'dataProvider' => $primary_fields,
    	]);
    } */
    /*
    * Delete Field in table name
    * @param string table name
    * @return Delete the raw if last than remove whole table
    */
    public function actionDeleteReportTypeFieldName()
    {
        $session = Yii::$app->session;
        $post_data = Yii::$app->request->post();  
        $primary_table = $post_data['primary_table'];   
        //echo "<pre>",print_r($post_data),"</pre>";die;
        if(!empty($post_data['table_lists']))
        {
			$table_name = $post_data['tbl_name_field'];
			
			// filter data table
			$tb_lists = array(); $field_list = array(); $filter_data = array();
			foreach($post_data['table_lists'] as $tval){
				$tb_lists = json_decode($tval, true); 
				foreach($tb_lists as $val)
				{
					if($table_name!=$val['table_name'])
					{
						$field_list[$val['table_name']][] = $val;
						$filter_data[$val['table_name']][] = $val;
					}
				}
			}
		}
		
		if(count($field_list) == 1 || !isset($field_list[$primary_table])){
			$primary_table = key($field_list);
		}
		
		// table relation
		if(!empty($post_data['table_relation'])){
			/** table relationship **/
			$relation_data = array();
			$rel_val_ar = json_decode($post_data['table_relation'], true);
			foreach($rel_val_ar as $keys => $rel_val){
				if(!isset($field_list[$rel_val['primary_table_name']]) || !isset($field_list[$rel_val['secondary_table_name']])){
					continue;
				}
				if(is_numeric($keys)){
					$relation_data["Table Relationships"][$keys] = $rel_val;
				}
			}
			$response['relation_data'] = json_encode($relation_data);
			$field_list = array_merge($field_list, $relation_data);
		}
		
	    //echo "<pre>A",print_r($field_list),print_r($filter_data),"</pre>";
		
	    // dataprovider 
		$dataProvider = new ArrayDataProvider([
            'allModels' => $field_list,
            'pagination' => [
                'pageSize' => -1,
            ]
        ]);
		
		$response = $this->renderAjax('show-primary-table-grid', [    		
    		'dataProvider' => $dataProvider,
    		'primary_table' =>$primary_table,
    		'filter_data' => json_encode($filter_data),
    		'table_lists' => $field_list,
    		'relations' => isset($relation_data["Table Relationships"])?json_encode($relation_data["Table Relationships"]):''
    	]);
    	/*$response['filter_data'] = $this->renderAjax('manage-tables-list', [    		
    		'filter_data' => $filter_data,
    		'flag' => 'table_list',
    	]);
    	$response['filter_table'] = $this->renderAjax('manage-tables-list', [    		
    		'filter_data' => $filter_data,
    		'flag' => 'table',
    	]);*/
    	
        return $response;
    }
    /**
     * Finds the Report field type model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Report Field type the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ReportsFieldType::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    /**
     * Finds the Report type model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Report type the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findReportTypeModel($id){
        if (($model = ReportsReportType::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    /**
     * Finds the Report Format model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Report format the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findReportFormatModel($id){
         if (($model = ReportsReportFormat::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
     /**
     * Finds the Report Chart Display By model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Report Chart Display By the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findChartDisplayBYModel($id){
        if (($model = ReportsChartFormatDisplayBy::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    /**
     * Finds the Report Chart format model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Report Field type the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findChartFormatModel($id)
    {
        if (($model = ReportsChartFormat::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    /**
     * Finds the Report field Calculation model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Report Field Calculation the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findCalculationModel($id)
    {
        if (($model = ReportsFieldCalculations::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    /**
     * Finds the Report Calculation Function model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Report Field Calculation the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findCalculationFunctionModel($id)
    {
        if (($model = ReportsCalculationFunction::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    /**
     * Finds the Report Calculation Sp model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Report Field Calculation the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findCalculationSpModel($id)
    {
        if (($model = ReportsCalculationSp::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    /**
     * Finds the Report field Operator model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Report Field Operator the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected  function findOperatorModel($id){
        if (($model = ReportsFieldOperators::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    /**
     * To check whether Calculation field is in use or not.
     * If Yes then it will return 1
     * Else 0
     * @param $id int
     * @field_origin  1=Database, 2=Calculation
     */
    public function actionCheckCalculationInuse()
    {
		$field_id = Yii::$app->request->get('id',0);
		$modelData = ReportsUserSavedFields::find()->where(['field_calculation_id'=>$field_id])->count();
		$result = ['inuse'=>0];
		if($modelData > 0)
			$result = ['inuse'=>1];
		return json_encode($result);
	}
	
	/**
     * To check whether Report Type Table/Field is in use or not.
     * If Yes then it will return 1
     * Else 0
     * @param $id int
     * @field_origin  1=Database, 2=Calculation
     */
    public function actionCheckFieldInuse()
    {
		/*$field_id = Yii::$app->request->get('id',0);
		$modelData = ReportsUserSavedFields::find()->where(['field_origin'=>1,'report_type_field_id'=>$field_id])->count();
		*/
		$table_name = Yii::$app->request->post('table_name','');
		$field_name = Yii::$app->request->post('field_name','');
		
		$modelData = ReportsUserSavedFields::find()
		->innerJoinWith([
			'reportsReportTypeFields' => function(\yii\db\ActiveQuery $query) use($table_name,$field_name){
				$query->where(['table_name'=>$table_name]);
				if($field_name!=''){
					$query->andWhere(['field_name'=>$field_name]);
				}
			}
		])
		->count();
		
		$result = ['inuse'=>0];
		if($modelData > 0)
			$result = ['inuse'=>1];
		return json_encode($result);
	}
	
	/**
     * To check whether Relationship is use in Report Type or not.
     * If Yes then it will return 1
     * Else 0
     * @param $table_name varchar
     * @field_origin  1=Database, 2=Calculation
     */
    public function actionCheckRelationshipinuse(){
		$table_name = Yii::$app->request->post('table_name','');
		$joinType = Yii::$app->params['join_type'];
		$primary_table_name= Yii::$app->request->post('primary_table_name','');
		$primary_field_name= Yii::$app->request->post('primary_field_name','');
		$secondary_table_name= Yii::$app->request->post('secondary_table_name','');
		$secondary_field_name= Yii::$app->request->post('secondary_field_name','');
		$type= Yii::$app->request->post('type','');
		$reports_fields_relationships_id= ReportsFieldsRelationships::find()->where(['rela_join_string'=>$joinType[$type],'rela_base_table' => $primary_table_name,'rela_table' => $secondary_table_name,'rela_field' => $secondary_field_name])->andWhere("rela_base_field IN (SELECT id FROM tbl_reports_fields WHERE report_table_id IN (SELECT id FROM tbl_reports_tables WHERE table_name ='".$primary_table_name."') AND field_name = '".$primary_field_name."')")->one()->id;
		$modelData=0;
		if($reports_fields_relationships_id > 0)
			$modelData = ReportsReportTypeFields::find()->where(['reports_fields_relationships_id'=>$reports_fields_relationships_id])->count();
			
		$result = ['inuse'=>0];
		if($modelData > 0)
			$result = ['inuse'=>1];
			
		return json_encode($result);
	}
	
	/**
	 * To Manage Relationships in Tables
	 */ 
	public function actionIndexFieldRelationship()
	{
		$modelReportTables  =  new ReportsTables();
		$modelReportFields  =  new ReportsFields();
		$modelReportFieldsRelationships = new ReportsFieldsRelationships();
		
		$tableList = ArrayHelper::map(ReportsTables::find()->select(['id','table_name'])->all(),'id','table_name');
		
		return $this->renderAjax('index-field-relationship',[
			'modelReportTables' => $modelReportTables,
			'modelReportFields' => $modelReportFields,
			'modelReportFieldsRelationships' => $modelReportFieldsRelationships,
			'tableList' => $tableList
		]);
	}
	
	/**
	 * To Load new form and to save Field Relationships
	 */ 
	public function actionCreateFieldRelationships()
	{
		$modelReportTables  =  new ReportsTables();
		$modelReportFields  =  new ReportsFields();
		$modelReportFieldsRelationships = new ReportsFieldsRelationships();
		
		$tableList = ArrayHelper::map((new ReportsReportType)->getTables('TABLE_NAME','AND TABLE_NAME NOT IN (SELECT table_name FROM tbl_reports_tables)','','GROUP BY TABLE_NAME'),'TABLE_NAME','TABLE_NAME');
		$model_field_length = (new User)->getTableFieldLimit($modelReportTables->tableSchema->name); 
		
		return $this->renderAjax('create-field-relationships',[
			'modelReportTables' => $modelReportTables,
			'modelReportFields' => $modelReportFields,
			'modelReportFieldsRelationships' => $modelReportFieldsRelationships,
			'tableList' => $tableList,
                        'model_field_length'=>$model_field_length
		]);
	}
	
	/**
	 * To Load Field List by Table Name
	 * @param table_name string
	 */ 
	public function actionNextstepFieldRelationship()
	{
		$id = Yii::$app->request->get('id',0);
		
		$modelReportTables  =  new ReportsTables();
		if(isset($id) && $id!=0){
			$modelReportTables  =  ReportsTables::findOne($id);
		}
		$table_name = Yii::$app->request->get('table_name','');
		$table_display_name = Yii::$app->request->get('table_display_name','');
		$post_data = Yii::$app->request->post();
		$relations=array();
		$lookup=array();
		if(!empty($post_data)){
			if(isset($post_data['related_table_name'])){
				foreach($post_data['related_table_name'] as $field=>$vals){
					foreach($vals as $key=>$val){
						if(isset($val) && $val!=""){
							$relations[$field][$key]['primary_table_name']=$table_name;
							$relations[$field][$key]['primary_field_name']=$field;
							$relations[$field][$key]['secondary_table_name']=$val;
							$relations[$field][$key]['secondary_field_name']=$post_data['related_field_name'][$field][$key];
							$relations[$field][$key]['join_type']=$post_data['related_type'][$field][$key];
						}
					}
				}
			}
			//echo "<pre>",print_r($relations),"</pre>";
			if(isset($post_data['lookup_filter_table'])){
				foreach($post_data['lookup_filter_table'] as $field=>$val){
					if(isset($val) && $val!=""){
						$lookup[$field]['lookup_filter_table']=$val;
						$lookup[$field]['lookup_filter_field']=$field;
						$lookup[$field]['lookup_table']=$post_data['lookup_table'][$field];
						$lookup[$field]['lookup_field']=$post_data['lookup_field'][$field];
						$lookup[$field]['lookup_type']=$post_data['lookup_type'][$field];
						$lookup[$field]['lookup_custom']=$post_data['lookup_custom'][$field];
						if(isset($post_data['lookup_custom_field'][$field])){
							$lookup[$field]['lookup_custom_field']=$post_data['lookup_custom_field'][$field];	
						}
						$lookup[$field]['lookup_field_separator']=$post_data['lookup_field_separator'][$field];
						if($post_data['lookup_type'][$field]==1)
						{
							$lookup[$field]['lookup_relationship_table']=$post_data['related_table_name'][$field];
							$lookup[$field]['lookup_relationship_field']=$post_data['related_field_name'][$field];
						}
					}
				}
			}
		}else if(isset($id) && $id!=0){
			$join_type_rev=array('LEFT'=>1, 'INNER'=>2, 'RIGHT'=>3);
			if(!empty($modelReportTables->reportsFields)){
				
				foreach($modelReportTables->reportsFields as $field_data){
					
					$post_data['ReportsFields']['id'][$field_data->field_name] = $field_data->id;
					$post_data['ReportsFields']['field_name'][] = $field_data->field_name;
					$post_data['ReportsFields']['field_display_name'][] = $field_data->field_display_name;
					
					$post_data['related_table_name'][$field_data->field_name] = '';
					$post_data['related_field_name'][$field_data->field_name] = '';
					$post_data['related_type'][$field_data->field_name] = '';
					
					$post_data['lookup_filter_table'][$field_data->field_name] = '';
					$post_data['lookup_filter_field'][$field_data->field_name] = '';
					$post_data['lookup_table'][$field_data->field_name] = '';
					$post_data['lookup_field'][$field_data->field_name] = '';
					$post_data['lookup_type'][$field_data->field_name] = '';
					$post_data['lookup_custom'][$field_data->field_name] = '';
					$post_data['lookup_field_separator'][$field_data->field_name] = '';
					//echo "<pre>",print_r($field_data->reportsFieldsRelationships),print_r($post_data),"</pre>";
					if(!empty($field_data->reportsFieldsRelationships)){
						$i = 0;
						foreach($field_data->reportsFieldsRelationships as $relation_data)
						{
							$post_data['ReportsFieldsRelationships']['relation_id'][$field_data->field_name][$i] = $relation_data->id;
							$post_data['related_table_name'][$field_data->field_name][$i] = $relation_data->rela_table;
							$post_data['related_base_table'][$field_data->field_name][$i] = $relation_data->rela_base_table;
							$post_data['related_type'][$field_data->field_name][$i] = $join_type_rev[$relation_data->rela_join_string];
							$post_data['related_field_name'][$field_data->field_name][$i] = $relation_data->rela_field;
							
							if($relation_data->rela_type==0){
								
								$relations[$field_data->field_name][$i]['primary_table_name']=$table_name;
								$relations[$field_data->field_name][$i]['primary_field_name']=$field_data->field_name;
								$relations[$field_data->field_name][$i]['secondary_table_name']=$relation_data->rela_table;
								$relations[$field_data->field_name][$i]['secondary_field_name']=$relation_data->rela_field;
								$relations[$field_data->field_name][$i]['join_type']=$join_type_rev[$relation_data->rela_join_string];
							
							}
							
							if($relation_data->rela_type==3){
								$lookup[$field_data->field_name]['lookup_filter_table']=$modelReportTables->table_name;
								$lookup[$field_data->field_name]['lookup_filter_field']=$field_data->field_name;;
								$lookup[$field_data->field_name]['lookup_table']='';
								$lookup[$field_data->field_name]['lookup_field']='';
								$lookup[$field_data->field_name]['lookup_field_separator']=$relation_data->lookup_field_separator;
								$lookup[$field_data->field_name]['lookup_type']=3;
								$lookup[$field_data->field_name]['lookup_custom']='';
								$lookup[$field_data->field_name]['lookup_custom_field']=$relation_data->lookup_fields;
								
								$post_data['lookup_filter_table'][$field_data->field_name] = $modelReportTables->table_name;
								$post_data['lookup_filter_field'][$field_data->field_name] = $field_data->field_name;
								$post_data['lookup_table'][$field_data->field_name] = '';
								$post_data['lookup_field'][$field_data->field_name] = '';
								$post_data['lookup_type'][$field_data->field_name] = 3;
								$post_data['lookup_custom'][$field_data->field_name] = '';
								$post_data['lookup_custom_field'][$field_data->field_name]=$relation_data->lookup_fields;
								$post_data['lookup_field_separator'][$field_data->field_name]=$relation_data->lookup_field_separator;
								
							}
							if($relation_data->rela_type==1 || $relation_data->rela_type==2){
								$lookup[$field_data->field_name]['lookup_filter_table']=$modelReportTables->table_name;
								$lookup[$field_data->field_name]['lookup_filter_field']=$field_data->field_name;
								$lookup[$field_data->field_name]['lookup_table']=$relation_data->lookup_table;
								$lookup[$field_data->field_name]['lookup_field']=$relation_data->lookup_fields;
								$lookup[$field_data->field_name]['lookup_field_separator']=$relation_data->lookup_field_separator;
								$lookup[$field_data->field_name]['lookup_type']=1;
								$lookup[$field_data->field_name]['lookup_custom']='';
								$lookup[$field_data->field_name]['lookup_custom_field']='';
								$post_data['ReportsFieldsRelationships']['lookup_id'][$field_data->field_name] = $relation_data->rela_base_field; 
								$post_data['lookup_filter_table'][$field_data->field_name] = $modelReportTables->table_name;
								$post_data['lookup_filter_field'][$field_data->field_name] = $field_data->field_name;
								$post_data['lookup_table'][$field_data->field_name] = $relation_data->lookup_table;
								$post_data['lookup_field'][$field_data->field_name] = $relation_data->lookup_fields;
								$post_data['lookup_type'][$field_data->field_name] = 1;
								$post_data['lookup_custom'][$field_data->field_name] = '';
								$post_data['lookup_field_separator'][$field_data->field_name]=$relation_data->lookup_field_separator;
								/*if($relation_data->rela_type==3){
									$post_data['lookup_relationship_table'][$field_data->field_name]=$relation_data->rela_base_table;
									$post_data['lookup_relationship_field'][$field_data->field_name]=$relation_data->rela_data;
									$lookup[$field_data->field_name]['lookup_relationship_table']=$relation_data->rela_base_table;
									$lookup[$field_data->field_name]['lookup_relationship_field']=$relation_data->rela_data;
								}*/
								if($relation_data->rela_type==2){
									$lookup[$field_data->field_name]['lookup_type']=2;
									$post_data['lookup_type'][$field_data->field_name] = 2;
									$lookup_custom=array();
									if(!empty($relation_data->reportsFieldsRelationshipsLookups)){
										foreach($relation_data->reportsFieldsRelationshipsLookups as $lookup_data){
											//$post_data['ReportsFieldsRelationshipsLookups']['lookup_id'][$field_data->field_name] = $lookup_data->id;
											$lookup_custom[]=array('field_value'=>$lookup_data->field_value,'lookup_value'=>$lookup_data->lookup_value);
										}
									}
								
									if(!empty($lookup_custom)){
										$lookup[$field_data->field_name]['lookup_custom']=json_encode($lookup_custom);
										
										$post_data['lookup_custom'][$field_data->field_name] = json_encode($lookup_custom);
									}
								}
							}
							$i++;
						}
					}
				}
			}
		}
		
		$field_list = array();
		if($table_name!=''){
			$field_list = ArrayHelper::map((new ReportsReportType)->getTables('COLUMN_NAME',"AND TABLE_NAME like '{$table_name}'",'',''),'COLUMN_NAME','COLUMN_NAME');	
		}

			// Data providers
			$dataProvider = new ArrayDataProvider([
				'allModels' => array('fields'=>'Fields','field_relationship'=>'Field Relationships','field_lookup'=>'Field Lookup'),
				'pagination' => [
					'pageSize' => -1,
				]
			]);
                $modelReportFields  =  new ReportsFields();        
                $model_field_length = (new User)->getTableFieldLimit($modelReportFields->tableSchema->name);                
		return $this->renderAjax('nextstep-field-relationship',[
			'modelReportTables' => $modelReportTables,
			'modelReportFields' => $modelReportFields,
			'modelReportFieldsRelationships' => $modelReportFieldsRelationships,
			'field_list' => $field_list,
			'table_name' => $table_name,
			'table_display_name' =>$table_display_name,
			'dataprovider'=>$dataProvider,
			'relations'=>$relations,
			'lookup'=>$lookup,
			'post_data'=>$post_data,
            'model_field_length'=>$model_field_length
		]);
	}
	
	/**
	 * To Manage Relationships
	 * @param Maxied
	 */ 
	public function actionManageRelationships(){
		$table_name = Yii::$app->request->get('table_name','');
		$post_data  = Yii::$app->request->post();
		$field_list = array();
		$OthertableList = array();
		if($table_name!=''){
			$where=" AND TABLE_NAME like '{$table_name}'";
			$whereTables = $table_name;
			if(isset($post_data['related_table_name'])){
				foreach($post_data['related_table_name'] as $field=>$vals){
					foreach($vals as $key => $val){
						if(isset($val) && $val!=""){
							$whereTables.="','".$val;
						}
					}
				}
			}
			//$field_list = ArrayHelper::map((new ReportsReportType)->getTables('COLUMN_NAME',$where,'',' LIMIT 1'),'COLUMN_NAME','COLUMN_NAME');	
			$field_list = ArrayHelper::map((new ReportsReportType)->getTables('COLUMN_NAME',$where,'',' '),'COLUMN_NAME','COLUMN_NAME');	
			$OthertableList = ArrayHelper::map((new ReportsReportType)->getTables('TABLE_NAME',"AND TABLE_NAME NOT IN ('{$whereTables}')",'','GROUP BY TABLE_NAME'),'TABLE_NAME','TABLE_NAME');
		}
		
		return $this->renderAjax('manage-relationships',[
			'field_list' => $field_list,
			'table_name' => $table_name,
			'othertableList'=>$OthertableList
		]);
		
	}
	
	/**
	 * To Manage Relationships
	 * @param Maxied
	 */ 
	public function actionManageSecondaryTableFieldLists(){
		  $sr_table = Yii::$app->request->post('scr_table');
		  $sr_table_fields = array();
		  if($sr_table!=''){
				$sr_table_fields = ArrayHelper::map((new ReportsReportType)->getTables('COLUMN_NAME',"AND TABLE_NAME like '{$sr_table}'",'',''),'COLUMN_NAME','COLUMN_NAME');	
		  }
		  return $this->renderAjax('manage-secondary-table-field-lists',['sr_table_fields' => $sr_table_fields, 'sr_table' => $sr_table]);
	}
	
	/**
	 * To Manage Lookup value for a Table to Field Relationship
	 * @param table_name string
	 * @param mixed array
	 */ 
	public function actionManageLookup(){
		$table_name = Yii::$app->request->get('table_name','');
		$post_data  = Yii::$app->request->post();
		$field_list = array();
		$OthertableList = array();
		
		$model = new ReportsLookups();
		
		if($table_name!=''){
			$where=" AND TABLE_NAME like '{$table_name}'";
			if(isset($post_data['lookup_filter_table'])) {
				foreach($post_data['lookup_filter_table'] as $field=>$val){
					if(isset($val) && $val!="") {
						$where.=" AND COLUMN_NAME !='{$field}'";
					}
				}
			}
			$field_list = ArrayHelper::map((new ReportsReportType)->getTables('COLUMN_NAME',$where,'',''),'COLUMN_NAME','COLUMN_NAME');	
			$OthertableList = ArrayHelper::map((new ReportsReportType)->getTables('TABLE_NAME',"AND TABLE_NAME NOT IN ('{$table_name}')",'','GROUP BY TABLE_NAME'),'TABLE_NAME','TABLE_NAME');
		}
		
		return $this->renderAjax('manage-lookup',[
			'field_list' => $field_list,
			'table_name' => $table_name,
			'othertableList'=>$OthertableList,
			'model' => $model
		]);
		
	}
	
	/**
	 * To save Table, Fields, Relationships, Lookups
	 */ 
	public function actionSaveFieldRelationships()
	{
		$post_data  = Yii::$app->request->post();
		$modelReportTables  =  new ReportsTables();
		
		$joinType = Yii::$app->params['join_type'];
		
		if(!empty($post_data)){
			if($modelReportTables->load($post_data)){
				$transaction = Yii::$app->db->beginTransaction();
				try{
					// To Save into model $modelReportTables
					if(!$modelReportTables->save()){
						return print_r($modelReportTables->getErrors(),true);
					}
					
					// To Save into model $modelReportFields
					$fields_list = array();
					if(isset($post_data['ReportsFields']) && !empty($post_data['ReportsFields'])) {
						$field_type_list = ArrayHelper::map((new ReportsReportType)->getTables('COLUMN_NAME, DATA_TYPE',"AND TABLE_NAME like '{$modelReportTables->table_name}'",'',''),'COLUMN_NAME','DATA_TYPE');	
						foreach($post_data['ReportsFields']['field_name'] as $key => $field) {
							$modelReportFields  =  new ReportsFields();
							$modelReportFields->field_name = $field;
							$modelReportFields->field_display_name = $post_data['ReportsFields']['field_display_name'][$key];
							$modelReportFields->report_table_id = $modelReportTables->id;
							if($field_type_list[$field] == 'longtext') $field_type_list[$field] = 'text';
							$modelReportFields->reports_field_type_id = ReportsFieldType::find()->where(['field_type'=>$field_type_list[$field]])->one()->id;
							if(!$modelReportFields->save()){
								return print_r($modelReportFields->getErrors(),true);
							}
							$fields_list[$field] = $modelReportFields->id;
						}
					}
					// To Save into model $modelReportFieldsRelationships
					/* if(isset($post_data['related_table_name']) && !empty($post_data['related_table_name'])){
						foreach($post_data['related_table_name'] as $field => $related_value){
							$hasLookup = false;
							$modelReportFieldsRelationships = new ReportsFieldsRelationships();	
							$modelReportFieldsRelationships->report_fields_id = $fields_list[$field];
							if($related_value!=''){
								$modelReportFieldsRelationships->rela_type = 0;
								$modelReportFieldsRelationships->rela_join_string = $joinType[$post_data['related_type'][$field]];
								$modelReportFieldsRelationships->rela_base_table = $related_value;
								$modelReportFieldsRelationships->rela_data = $post_data['related_field_name'][$field];
							}
						}
					} */
					
					if(isset($post_data['related_table_name']) && !empty($post_data['related_table_name'])) {
						foreach($post_data['related_table_name'] as $field => $val) {
							foreach($val as $key => $related_value) {
								//Insert only newly added relationship
								if($related_value!='') {
									$modelReportFieldsRelationships = new ReportsFieldsRelationships();	
									$modelReportFieldsRelationships->rela_type = 0;
									$modelReportFieldsRelationships->rela_base_table = $post_data['related_base_table'][$field][$key];
									$modelReportFieldsRelationships->rela_join_string = $joinType[$post_data['related_type'][$field][$key]];
									$modelReportFieldsRelationships->rela_table = $related_value;
									$modelReportFieldsRelationships->rela_base_field = $post_data['ReportsFields']['id'][$field];
									$modelReportFieldsRelationships->rela_field = $post_data['related_field_name'][$field][$key];
									if(!$modelReportFieldsRelationships->save()) {
										return print_r($modelReportFieldsRelationships->getErrors(),true);
									}
								}
							}
						}
					}
					
					if(isset($post_data['lookup_filter_table']) && !empty($post_data['lookup_filter_table'])){
						foreach($post_data['lookup_filter_table'] as $field => $rela_base_table){
							if($rela_base_table != ''){
								// To store table Lookup
								if(isset($post_data['lookup_table'][$field]) && $post_data['lookup_table'][$field]!=''){
									$modelReportFieldsRelationships = new ReportsFieldsRelationships();
									$modelReportFieldsRelationships->rela_type = 1;
									$modelReportFieldsRelationships->rela_base_table = $rela_base_table;
									$modelReportFieldsRelationships->rela_base_field = $post_data['ReportsFields']['id'][$post_data['lookup_filter_field'][$field]];
									
									$modelReportFieldsRelationships->lookup_table = $post_data['lookup_table'][$field];
									$modelReportFieldsRelationships->lookup_fields = $post_data['lookup_field'][$field];
									$modelReportFieldsRelationships->lookup_field_separator = $post_data['lookup_field_separator'][$field]!=''?$post_data['lookup_field_separator'][$field]:NULL;
									if(!$modelReportFieldsRelationships->save()) {
										return print_r($modelReportFieldsRelationships->getErrors(),true);
									}
								}
								
								// To store custom Lookup
								if(isset($post_data['lookup_custom'][$field]) && $post_data['lookup_custom'][$field]!=''){
									$modelReportFieldsRelationships = new ReportsFieldsRelationships();
									$modelReportFieldsRelationships->rela_type = 1;
									$modelReportFieldsRelationships->rela_base_table = $rela_base_table;
									$modelReportFieldsRelationships->rela_base_field = $post_data['ReportsFields']['id'][$post_data['lookup_filter_field'][$field]];
									$modelReportFieldsRelationships->rela_type = 2;
									if(!$modelReportFieldsRelationships->save()){
										return print_r($modelReportFieldsRelationships->getErrors(),true);
									}
									// [lookup_custom]	
									$customLook = json_decode($post_data['lookup_custom'][$field],true);
									if(!empty($customLook)){
										foreach($customLook as $lookup){
											$modelReportsFieldsRelationshipsLookups = new ReportsFieldsRelationshipsLookups();
											$modelReportsFieldsRelationshipsLookups->reports_fields_relationships_id = $modelReportFieldsRelationships->id;
											$modelReportsFieldsRelationshipsLookups->field_value = $lookup['field_value'];
											$modelReportsFieldsRelationshipsLookups->lookup_value = $lookup['lookup_value'];
											if(!$modelReportsFieldsRelationshipsLookups->save()){
												return print_r($modelReportsFieldsRelationshipsLookups->getErrors(),true);
											}
										}
									}
								}
								
								// To store Lookup Custom Field
								if(isset($post_data['lookup_custom_field'][$field]) && $post_data['lookup_custom_field'][$field]!=''){
									$modelReportFieldsRelationshipsFieldLookup = new ReportsFieldsRelationships();
									$modelReportFieldsRelationshipsFieldLookup->rela_type = 3;
									$modelReportFieldsRelationshipsFieldLookup->rela_base_table = $rela_base_table;
									$modelReportFieldsRelationshipsFieldLookup->rela_base_field = $post_data['ReportsFields']['id'][$post_data['lookup_filter_field'][$field]];
									
									//$modelReportFieldsRelationships->lookup_table = $post_data['lookup_table'][$field];
									$modelReportFieldsRelationshipsFieldLookup->lookup_fields = $post_data['lookup_custom_field'][$field];
									$modelReportFieldsRelationshipsFieldLookup->lookup_field_separator = $post_data['lookup_field_separator'][$field]!=''?$post_data['lookup_field_separator'][$field]:NULL;
									if(!$modelReportFieldsRelationshipsFieldLookup->save()){
										return print_r($modelReportFieldsRelationshipsFieldLookup->getErrors(),true);
									}
								}
							}
						}
					}
					
					$transaction->commit();
				} catch (Exception $e) {
					$transaction->rollBack();	
					return $e->getMessage();
				}
			}
		}
		
		return 'OK';
	}
	
	/**
	 * To Load Edit Mode form and to Update Field Relationships
	 */ 
	public function actionUpdateFieldRelationships($id)
	{
		$modelReportTables  =  ReportsTables::findOne($id);
		$modelReportFields  =  new ReportsFields();
		$modelReportFieldsRelationships = new ReportsFieldsRelationships();
		
		$tableList = ArrayHelper::map((new ReportsReportType)->getTables('TABLE_NAME','AND TABLE_NAME NOT IN (SELECT table_name FROM tbl_reports_tables)','','GROUP BY TABLE_NAME'),'TABLE_NAME','TABLE_NAME');
		$model_field_length = (new User)->getTableFieldLimit($modelReportTables->tableSchema->name); 
		return $this->renderAjax('update-field-relationships',[
			'modelReportTables' => $modelReportTables,
			'modelReportFields' => $modelReportFields,
			'modelReportFieldsRelationships' => $modelReportFieldsRelationships,
			'tableList' => $tableList,
			'model_field_length'=>$model_field_length
		]);
	}
	
	public function actionModifyFieldRelationships()
	{
		$id = Yii::$app->request->get('id',0);
		$post_data = Yii::$app->request->post();
		$modelReportTables  =  ReportsTables::findOne($id);
		$joinType = Yii::$app->params['join_type'];
		//echo "<pre>",print_r($post_data),"</pre>";//die;
		$relation_ids = isset($post_data['ReportsFieldsRelationships']['relation_id']) ? $post_data['ReportsFieldsRelationships']['relation_id']:array();
		$lookup_ids = isset($post_data['ReportsFieldsRelationships']['lookup_id']) ? $post_data['ReportsFieldsRelationships']['lookup_id']:array();
		$rela_ids = array();
		if(isset($relation_ids) && !empty($relation_ids)){
			foreach($relation_ids as $k=>$va){ foreach($va as $ke=>$v){if($v!=0){$rela_ids[$v] = $v;}}}
		}
		//echo "<pre>",print_r($rela_ids),"</pre>";die;
		if(isset($lookup_ids) && !empty($lookup_ids)){
			foreach($lookup_ids as $k=>$v){if($v==0){unset($lookup_ids[$k]);}}
		}
		
		if(!empty($post_data)){
			if($modelReportTables->load($post_data)){
				$transaction = Yii::$app->db->beginTransaction();
				try{
					// To Update model $modelReportTables
					if(!$modelReportTables->save()){
						return print_r($modelReportTables->getErrors(),true);
					}
					// To Update into model $modelReportFields
					$fields_list = array();
					$field_type_list = ArrayHelper::map((new ReportsReportType)->getTables('COLUMN_NAME, DATA_TYPE',"AND TABLE_NAME like '{$modelReportTables->table_name}'",'',''),'COLUMN_NAME','DATA_TYPE');	
					if(isset($post_data['ReportsFields']['id']) && !empty($post_data['ReportsFields']['id'])){
						$updatesql = 'UPDATE tbl_reports_fields SET field_display_name = ( CASE id ';
						$idAr =  array();
						$newidAr =  array();
						foreach($post_data['ReportsFields']['field_name'] as $key => $field){
							if($post_data['ReportsFields']['id'][$field] != 0 && $post_data['ReportsFields']['id'][$field] != ''){
								$updatesql.=" WHEN {$post_data['ReportsFields']['id'][$field]} THEN '{$post_data['ReportsFields']['field_display_name'][$key]}' ";
								$idAr[$post_data['ReportsFields']['id'][$field]] = $post_data['ReportsFields']['id'][$field];
							}else{
								$modelReportFields  =  new ReportsFields();
								$modelReportFields->field_name = $field;
								$modelReportFields->field_display_name = $post_data['ReportsFields']['field_display_name'][$key];
								$modelReportFields->report_table_id = $modelReportTables->id;
								$modelReportFields->reports_field_type_id = ReportsFieldType::find()->where(['field_type'=>$field_type_list[$field]])->one()->id;
								
								if(!$modelReportFields->save()){
									return print_r($modelReportFields->getErrors(),true);
								}else{
									$newidAr[$modelReportFields->id]=$modelReportFields->id;
									$post_data['ReportsFields']['id'][$field]=$modelReportFields->id;
								}
							}
						}
						if(!empty($idAr)){
							$ids = implode(',',$idAr);
							$updatesql.="END) WHERE id IN ({$ids})";
							$idAr = array_merge($idAr,$newidAr);
							\Yii::$app->db->createCommand($updatesql)->execute();
							ReportsFields::deleteAll(['and','report_table_id=:report_table_id',['not in','id',$idAr]],[':report_table_id'=>$modelReportTables->id]);
						}
					}
					
					//Delete Relationships data if only lookup or relationship
					if(isset($rela_ids) && !empty($rela_ids)){
						ReportsFieldsRelationships::deleteAll('rela_base_field IN (SELECT id FROM tbl_reports_fields where report_table_id = '.$id.') AND id NOT IN ('.implode(",",$rela_ids).') AND rela_type IN (0)');
					}
						
					if(isset($lookup_ids) && !empty($lookup_ids)){
						ReportsFieldsRelationshipsLookups::deleteAll('reports_fields_relationships_id IN(select id from tbl_reports_fields_relationships WHERE rela_base_field IN (SELECT id FROM tbl_reports_fields where report_table_id = '.$id.') AND rela_base_field NOT IN ('.implode(",",$lookup_ids).') AND rela_type IN (1,2,3) )');
						ReportsFieldsRelationships::deleteAll(' rela_base_field IN (SELECT id FROM tbl_reports_fields where report_table_id = '.$id.') AND rela_base_field NOT IN ('.implode(",",$lookup_ids).') AND rela_type IN (1,2,3)');
					}
					if(empty($lookup_ids)){
						ReportsFieldsRelationshipsLookups::deleteAll('reports_fields_relationships_id IN(select id from tbl_reports_fields_relationships WHERE rela_base_field IN (SELECT id FROM tbl_reports_fields where report_table_id = '.$id.') AND rela_type NOT IN (0) )');
						ReportsFieldsRelationships::deleteAll(' rela_base_field IN (SELECT id FROM tbl_reports_fields where report_table_id = '.$id.') AND rela_type NOT IN (0)');
					}
					if(!isset($post_data['ReportsFieldsRelationships']['relation_id'])){
						ReportsFieldsRelationships::deleteAll(' rela_base_field IN (SELECT id FROM tbl_reports_fields where report_table_id = '.$id.') AND rela_type NOT IN (3,4)');
					}
					/*else{
						ReportsFieldsRelationshipsLookups::deleteAll(' reports_fields_relationships_id IN( select id from tbl_reports_fields_relationships WHERE report_fields_id IN (SELECT id FROM tbl_reports_fields where report_table_id = '.$id.') AND rela_type NOT IN (3,4) )');
						ReportsFieldsRelationships::deleteAll(' report_fields_id IN (SELECT id FROM tbl_reports_fields where report_table_id = '.$id.') AND rela_type NOT IN (3,4)');
						ReportsFieldsRelationships::updateAll(array( 'lookup_table' => null, 'lookup_fields'=>null), 'report_fields_id IN (SELECT id FROM tbl_reports_fields where report_table_id = '.$id.') AND rela_type IN (3,4)' );
					}*/
					
					
					//INSERT NEW RELATIONSHIP AND LOOKUP IN UPDATE MODE
					if(isset($post_data['related_table_name']) && !empty($post_data['related_table_name'])){
						foreach($post_data['related_table_name'] as $field => $val){
							foreach($val as $key => $related_value){
							//Insert only newly added relationship
								if($related_value!=''){
									$countRelation = ReportsFieldsRelationships::find()->where(['rela_base_field' => $post_data['ReportsFields']['id'][$field], 'rela_table' => $post_data['related_table_name'][$field][$key]])->count();
									if($countRelation){
										$modelReportFieldsRelationships = ReportsFieldsRelationships::find()->where(['rela_base_field' => $post_data['ReportsFields']['id'][$field], 'rela_table' => $related_value])->one();	
									} else {
										$modelReportFieldsRelationships = new ReportsFieldsRelationships();	
									}
									$modelReportFieldsRelationships->rela_type = 0;
									$modelReportFieldsRelationships->rela_base_table = $post_data['related_base_table'][$field][$key];
									$modelReportFieldsRelationships->rela_join_string = $joinType[$post_data['related_type'][$field][$key]];
									$modelReportFieldsRelationships->rela_table = $related_value;
									$modelReportFieldsRelationships->rela_base_field = $post_data['ReportsFields']['id'][$field];
									$modelReportFieldsRelationships->rela_field = $post_data['related_field_name'][$field][$key];
									if(!$modelReportFieldsRelationships->save()){
										return print_r($modelReportFieldsRelationships->getErrors(),true);
										
									}
								}
							}
						}
					}
					if(isset($post_data['lookup_filter_table']) && !empty($post_data['lookup_filter_table'])){
						foreach($post_data['lookup_filter_table'] as $field => $rela_base_table){
							if($rela_base_table != ''){
								// To store table Lookup
								if(isset($post_data['lookup_table'][$field]) && $post_data['lookup_table'][$field]!=''){
									$countLookup = ReportsFieldsRelationships::find()->where(['rela_base_field' => $post_data['ReportsFields']['id'][$field]])->andWhere('rela_type != 0')->count();
									if($countLookup){
										$modelReportFieldsRelationships = ReportsFieldsRelationships::find()->where(['rela_base_field' => $post_data['ReportsFields']['id'][$field]])->andWhere('rela_type != 0')->one();	
										ReportsFieldsRelationshipsLookups::deleteAll('reports_fields_relationships_id = '.$modelReportFieldsRelationships->id);
									} else {
										$modelReportFieldsRelationships = new ReportsFieldsRelationships();
									}
									$modelReportFieldsRelationships->rela_type = 1;
									$modelReportFieldsRelationships->rela_base_table = $rela_base_table;
									$modelReportFieldsRelationships->rela_base_field = $post_data['ReportsFields']['id'][$post_data['lookup_filter_field'][$field]];
									
									$modelReportFieldsRelationships->lookup_table = $post_data['lookup_table'][$field];
									$modelReportFieldsRelationships->lookup_fields = $post_data['lookup_field'][$field];
									$modelReportFieldsRelationships->lookup_field_separator = $post_data['lookup_field_separator'][$field]!=''?$post_data['lookup_field_separator'][$field]:NULL;
									if(!$modelReportFieldsRelationships->save()){
										return print_r($modelReportFieldsRelationships->getErrors(),true);
									}
								}
								// To store custom Lookup
								if(isset($post_data['lookup_custom'][$field]) && $post_data['lookup_custom'][$field]!=''){
									//die('custom lookup');
									$countLookup = ReportsFieldsRelationships::find()->where(['rela_base_field' => $post_data['ReportsFields']['id'][$field]])->andWhere('rela_type != 0')->count();
									if($countLookup){
										$modelReportFieldsRelationships = ReportsFieldsRelationships::find()->where(['rela_base_field' => $post_data['ReportsFields']['id'][$field]])->andWhere('rela_type != 0')->one();	
										ReportsFieldsRelationshipsLookups::deleteAll('reports_fields_relationships_id = '.$modelReportFieldsRelationships->id);
									} else {
										$modelReportFieldsRelationships = new ReportsFieldsRelationships();
									}
									$modelReportFieldsRelationships->rela_base_table = $rela_base_table;
									$modelReportFieldsRelationships->rela_base_field = $post_data['ReportsFields']['id'][$post_data['lookup_filter_field'][$field]];
									$modelReportFieldsRelationships->rela_type = 2;
									if(!$modelReportFieldsRelationships->save()){
										return print_r($modelReportFieldsRelationships->getErrors(),true);
									}
									// [lookup_custom] [{"field_value":"0","lookup_value":"Not Active"},{"field_value":"1","lookup_value":"Active"}]	
									$customLook = json_decode($post_data['lookup_custom'][$field],true);
									if(!empty($customLook)){
										foreach($customLook as $lookup){
											if(Yii::$app->db->driverName == 'mysql')
											{
												$countLookupValue = ReportsFieldsRelationshipsLookups::find()->where(['reports_fields_relationships_id' => $modelReportFieldsRelationships->id, 'field_value' => $lookup['field_value'], 'lookup_value' => $lookup['lookup_value']])->count();
											}
											else
											{
												$countLookupValue = ReportsFieldsRelationshipsLookups::find()->where(['reports_fields_relationships_id' => $modelReportFieldsRelationships->id, 'CONVERT(VARCHAR, field_value)' => $lookup['field_value'], 'lookup_value' => $lookup['lookup_value']])->count();
											}
											//$countLookupValue = ReportsFieldsRelationshipsLookups::find()->where(['reports_fields_relationships_id' => $modelReportFieldsRelationships->id, 'field_value' => $lookup['field_value'], 'lookup_value' => $lookup['lookup_value']])->count();
											$modelReportsFieldsRelationshipsLookups = new ReportsFieldsRelationshipsLookups();
											$modelReportsFieldsRelationshipsLookups->reports_fields_relationships_id = $modelReportFieldsRelationships->id;
											$modelReportsFieldsRelationshipsLookups->field_value = $lookup['field_value'];
											$modelReportsFieldsRelationshipsLookups->lookup_value = $lookup['lookup_value'];
											if(!$modelReportsFieldsRelationshipsLookups->save()){
												die('die123456');
												return print_r($modelReportsFieldsRelationshipsLookups->getErrors(),true);
											}
										}
									}
								}
								// To store Lookup Field
								if(isset($post_data['lookup_custom_field'][$field]) && $post_data['lookup_custom_field'][$field]!=''){
									//die('custom  field lookup');
									$countFieldLookup = ReportsFieldsRelationships::find()->where(['rela_base_field' => $post_data['ReportsFields']['id'][$field]])->andWhere('rela_type != 0')->count();
									if($countFieldLookup){
										$modelReportFieldsRelationshipsFieldLookup = ReportsFieldsRelationships::find()->where(['rela_base_field' => $post_data['ReportsFields']['id'][$field]])->andWhere('rela_type != 0')->one();	
										ReportsFieldsRelationshipsLookups::deleteAll('reports_fields_relationships_id = '.$modelReportFieldsRelationshipsFieldLookup->id);
									} else {
										$modelReportFieldsRelationshipsFieldLookup = new ReportsFieldsRelationships();
									}
									$modelReportFieldsRelationshipsFieldLookup->rela_type = 3;
									$modelReportFieldsRelationshipsFieldLookup->rela_base_table = $rela_base_table;
									$modelReportFieldsRelationshipsFieldLookup->rela_base_field = $post_data['ReportsFields']['id'][$post_data['lookup_filter_field'][$field]];
									$modelReportFieldsRelationshipsFieldLookup->lookup_table = $post_data['lookup_filter_table'][$field];
									$modelReportFieldsRelationshipsFieldLookup->lookup_fields = $post_data['lookup_custom_field'][$field];
									$modelReportFieldsRelationshipsFieldLookup->lookup_field_separator = $post_data['lookup_field_separator'][$field]!=''?$post_data['lookup_field_separator'][$field]:NULL;
									if(!$modelReportFieldsRelationshipsFieldLookup->save()){
										return print_r($modelReportFieldsRelationshipsFieldLookup->getErrors(),true);
									}
								}
							}
						}
					}
					$transaction->commit();
				} catch(Exception $e){
					$transaction->rollBack();
					return $e->getErrorMessage();
				}
			}
		}
		return 'OK';	
	}
	
	/**
	 * To remove ReportsTable & it's relations, lookups.
	 * @params id int
	 */ 
	public function actionDeleteReportsTable()
	{
		$id = Yii::$app->request->get('id',0);
		if($id != '' && $id != 0){
			$transaction = Yii::$app->db->beginTransaction();
			try{
				ReportsFieldsRelationshipsLookups::deleteAll("reports_fields_relationships_id IN (SELECT id FROM tbl_reports_fields_relationships WHERE rela_base_field IN (SELECT id FROM tbl_reports_fields WHERE report_table_id={$id}))");
				ReportsFieldsRelationships::deleteAll("rela_base_field IN (SELECT id FROM tbl_reports_fields WHERE report_table_id={$id})");
				ReportsFields::deleteAll("report_table_id={$id}");
				ReportsTables::deleteAll("id = {$id}");
				$transaction->commit();
			} catch (Exception $e){
				$transaction->rollBack();
				return $e->getErrorMessage();
			}
		}
		return 'OK';
	}
	
	/*
	 * This action is used to build exp for calculation field
	 *  */
	 public function actionBuildExpress(){
		 $post_data = Yii::$app->request->post();
		 $table_ids=0;
		 $functions=0;
		 $sps=0;
		 $tables=array();$functions=array();$sp=array();
		 if(isset($post_data['calc_table']) && $post_data['calc_table']){
			$table_ids=implode(",",$post_data['calc_table']);	 
		 }
		 $tables=ReportsTables::find()->where('id IN ('.$table_ids.')')->all();
		 /* Check has calcution Function */
		 $final_field=(new ReportsReportType())->checkHasCalFunctions($table_ids);
		 if(!empty($final_field)){
			 //echo "<pre>",print_r($final_field),"</pre>";die;
	 		$functions=ReportsCalculationFunction::find()->where("id IN (".implode(',',$final_field).")")->all();
		 }
		 /* Check has calcution Function */
		 /* Check has calcution Store Procedure */
		 $final_field_sp=(new ReportsReportType())->checkHasCalSp($table_ids);
		 if(!empty($final_field_sp)){
	 		$sp=ReportsCalculationSp::find()->where("id IN (".implode(',',$final_field_sp).")")->all();
		 }
		 /* Check has calcution Store Procedure */
		 
		 return $this->renderAjax('build-expression',[
			'tables' => $tables,
			'functions'=>$functions,
			'sp'=>$sp,
		]);
		 
	 }
	 /*get function params*/
	 public function actionGetFnparams(){
		 $post_data = Yii::$app->request->post();
		 if(isset($post_data['fn_id']) && $post_data['fn_id']!=0){
			 
			 $function_data = ReportsCalculationFunction::findOne($post_data['fn_id']);
			 $fnparams_data = ReportsCalculationFunctionParams::find()->where('function_id='.$post_data['fn_id'])->all();
			 $fields = ReportsFields::find()->where('report_table_id IN (select table_id FROM tbl_reports_calculation_function_table where function_id='.$post_data['fn_id'].')')->all();
		 }
		 return $this->renderAjax('fn-params',[
			'fnparams_data' => $fnparams_data,
			'function_data'=> $function_data,
			'fields' => $fields
		]);
	 }
	 /*add filter*/
	 public function actionAddReportFieldfilter($id){
		 $post_data = Yii::$app->request->post();
		 $model=new ReportsReportTypeFields();
		 if(isset($post_data['report_type_id']) && $post_data['report_type_id']!=0){
			$model=ReportsReportTypeFields::find()->where(['reports_fields_id'=>$id,'report_type_id'=>$post_data['report_type_id']])->one();
		 }
		 return $this->renderAjax('add-report-field-filter',[
			'post_data' => $post_data,
			'model'=> $model
		]);
	 }
}
