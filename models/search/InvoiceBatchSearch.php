<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use app\models\Options;
use yii\data\ActiveDataProvider;
use app\models\InvoiceBatch as InvoiceBatchModel;

/**
 * InvoiceBatch represents the model behind the search form about `app\models\InvoiceBatch`.
 */
class InvoiceBatchSearch extends InvoiceBatchModel
{
	public $modified_user='';
    public $created_user='';	
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'display_by', 'created_by', 'modified_by'], 'integer'],
            [['datefrom', 'dateto', 'display_invoice', 'created', 'modified','modified_user','created_user'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
		$timezoneOffset = (new Options)->getOffsetOfCurrenttimeZone();
        $query = InvoiceBatchModel::find();
		$query->select(['tbl_invoice_batch.*',"CONCAT(tbl_user.usr_first_name,' ',tbl_user.usr_lastname) as created_user","CONCAT(modifieduser.usr_first_name,' ',modifieduser.usr_lastname) as modified_user"]);
		$query->joinWith(['createUser','modifiedUser'],false);

        // add conditions that should always apply here
        $params['InvoiceBatchSearch']['id'] = (isset($params['InvoiceBatchSearch']['id']) && $params['InvoiceBatchSearch']['id']!="All")?$params['InvoiceBatchSearch']['id']:'';
        $params['InvoiceBatchSearch']['display_by'] = (isset($params['InvoiceBatchSearch']['display_by']) && $params['InvoiceBatchSearch']['display_by']!="All")?$params['InvoiceBatchSearch']['display_by']:'';
        $params['InvoiceBatchSearch']['created'] = (isset($params['InvoiceBatchSearch']['created']) && $params['InvoiceBatchSearch']['created']!="All")?$params['InvoiceBatchSearch']['created']:'';
        $params['InvoiceBatchSearch']['modified'] = (isset($params['InvoiceBatchSearch']['modified']) && $params['InvoiceBatchSearch']['modified']!="All")?$params['InvoiceBatchSearch']['modified']:'';
        $params['InvoiceBatchSearch']['created_by'] = (isset($params['InvoiceBatchSearch']['created_by']) && $params['InvoiceBatchSearch']['created_by']!="All")?$params['InvoiceBatchSearch']['created_by']:'';
        $params['InvoiceBatchSearch']['modified_by'] = (isset($params['InvoiceBatchSearch']['modified_by']) && $params['InvoiceBatchSearch']['modified_by']!="All")?$params['InvoiceBatchSearch']['modified_by']:'';
        $params['InvoiceBatchSearch']['display_invoice'] = (isset($params['InvoiceBatchSearch']['display_invoice']) && $params['InvoiceBatchSearch']['display_invoice']!="All")?$params['InvoiceBatchSearch']['display_invoice']:'';
        
     	$dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['enableMultiSort'=>true,'defaultOrder' => ['id'=>SORT_DESC]]
        ]);
        $dataProvider->sort->enableMultiSort=true;
		/*IRT-67*/
        if(isset($params['grid_id']) && $params['grid_id']!=""){
            $grid_id=$params['grid_id'].'_'.Yii::$app->user->identity->id;
            $sql="SELECT * FROM tbl_dynagrid_dtl WHERE id IN (SELECT sort_id FROM tbl_dynagrid WHERE id='$grid_id')";
            $sort_data=Yii::$app->db->createCommand($sql)->queryOne();
            if(!empty($sort_data)){
                    $dataProvider->sort->defaultSort=json_decode($sort_data['data'],true);
            }
        }
        /*IRT-67*/
		/*multiselect*/
        if ($params['InvoiceBatchSearch']['id'] != null && is_array($params['InvoiceBatchSearch']['id'])) {
			if(!empty($params['InvoiceBatchSearch']['id'])){
				foreach($params['InvoiceBatchSearch']['id'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['InvoiceBatchSearch']['id']);
					}
				}
			}
		}
		if ($params['InvoiceBatchSearch']['display_invoice'] != null && is_array($params['InvoiceBatchSearch']['display_invoice'])) {
			if(!empty($params['InvoiceBatchSearch']['display_invoice'])){
				foreach($params['InvoiceBatchSearch']['display_invoice'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['InvoiceBatchSearch']['display_invoice']);
					}
				}
			}
		}
		if ($params['InvoiceBatchSearch']['display_by'] != null && is_array($params['InvoiceBatchSearch']['display_by'])) {
			if(!empty($params['InvoiceBatchSearch']['display_by'])){
				foreach($params['InvoiceBatchSearch']['display_by'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['InvoiceBatchSearch']['display_by']);
					}
				}
			}
		}
		if ($params['InvoiceBatchSearch']['created_by'] != null && is_array($params['InvoiceBatchSearch']['created_by'])) {
			if(!empty($params['InvoiceBatchSearch']['created_by'])){
				foreach($params['InvoiceBatchSearch']['created_by'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['InvoiceBatchSearch']['created_by']);
					}
				}
			}
		}
		if ($params['InvoiceBatchSearch']['modified_by'] != null && is_array($params['InvoiceBatchSearch']['modified_by'])) {
			if(!empty($params['InvoiceBatchSearch']['modified_by'])){
				foreach($params['InvoiceBatchSearch']['modified_by'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['InvoiceBatchSearch']['modified_by']);
					}
				}
			}
		}
		/*multiselect*/
        $this->load($params);
		
	    // grid filtering conditions
        //$query->andFilterWhere([
           // 'datefrom' => $this->datefrom,
           // 'dateto' => $this->dateto,
		//]);
        
        $UTCtimezoneOffset = (new Options)->getOffsetOfTimeZone();
        $UserSettimezoneOffset = (new Options)->getOffsetOfTimeZone($_SESSION['usrTZ']);  
		 
        if(isset($params['InvoiceBatchSearch']['id']) && $params['InvoiceBatchSearch']['id']!='' && $params['InvoiceBatchSearch']['id']!="All"){
        	$query->andFilterWhere(['tbl_invoice_batch.id' => $params['InvoiceBatchSearch']['id']]);
        }
        
        if(isset($params['InvoiceBatchSearch']['display_by']) && $params['InvoiceBatchSearch']['display_by']!='' && $params['InvoiceBatchSearch']['display_by']!="All"){
			$query_display_by="";
			
		 	if(!empty($params['InvoiceBatchSearch']['display_by'])){
				foreach($params['InvoiceBatchSearch']['display_by'] as $k=>$v){
					$display_by = $v=="Itemized"?'1':'2';
					if($query_display_by==""){
						$query_display_by=" tbl_invoice_batch.display_by = $display_by";
					}else{
						$query_display_by.=" OR tbl_invoice_batch.display_by = $display_by";
					}
				}
			}
			if($query_display_by!=""){
				$query->andWhere("(".$query_display_by.")");
			}
		}
        
        if(isset($params['InvoiceBatchSearch']['display_invoice']) && $params['InvoiceBatchSearch']['display_invoice']!='' && $params['InvoiceBatchSearch']['display_invoice']!="All"){
        	$query->andFilterWhere(['or like', 'tbl_invoice_batch.display_invoice', $params['InvoiceBatchSearch']['display_invoice']]);
        }
        if(isset($params['InvoiceBatchSearch']['datefrom']) && $params['InvoiceBatchSearch']['datefrom']!=""){       
			$task_duedate=explode("-",$params['InvoiceBatchSearch']['datefrom']);
			$task_duedate_start=explode("/",trim($task_duedate[0]));
			$task_duedate_end=explode("/",trim($task_duedate[1]));
			$task_duedate_s=$task_duedate_start[2]."-".$task_duedate_start[0]."-".$task_duedate_start[1];
			$task_duedate_e=$task_duedate_end[2]."-".$task_duedate_end[0]."-".$task_duedate_end[1];
        	$where_date_query_s ="datefrom";
        	$where_date_query_e ="dateto";
			$query->andWhere("($where_date_query_s >= '$task_duedate_s' AND $where_date_query_s <= '$task_duedate_e') AND ($where_date_query_e >= '$task_duedate_s' AND $where_date_query_e <= '$task_duedate_e')");
    		//$query->andWhere(" $where_date_query_s <= '$task_duedate_s' AND $where_date_query_e  >= '$task_duedate_e' ");
        }
        /*if(isset($params['InvoiceBatchSearch']['created']) && $params['InvoiceBatchSearch']['created']!='' && $params['InvoiceBatchSearch']['created']!="All"){
        	$start_date=date('Y-m-d',strtotime($params['InvoiceBatchSearch']['created']));
       		if (DB_TYPE == 'sqlsrv') {
       			$datesql = "Cast(switchoffset(todatetimeoffset(Cast(tbl_invoice_batch.created as datetime), '+00:00'), '{$UserSettimezoneOffset}') as date)";
       		} else {
       			$datesql = "DATE_FORMAT(CONVERT_TZ(tbl_invoice_batch.created,'{$UTCtimezoneOffset}','{$UserSettimezoneOffset}'),'%Y-%m-%d')";
       		}
       		$query->andFilterWhere(["$datesql" => $start_date]);
        }*/
        if(isset($params['InvoiceBatchSearch']['created']) && $params['InvoiceBatchSearch']['created']!=""){       
			$created_duedate=explode("-",$params['InvoiceBatchSearch']['created']);
			$created_duedate_start=explode("/",trim($created_duedate[0]));
			$created_duedate_end=explode("/",trim($created_duedate[1]));
			$created_duedate_duedate_s=$created_duedate_start[2]."-".$created_duedate_start[0]."-".$created_duedate_start[1];
			$created_duedate_duedate_e=$created_duedate_end[2]."-".$created_duedate_end[0]."-".$created_duedate_end[1];
			if (Yii::$app->db->driverName == 'mysql'){
        		$where_date_query ="DATE_FORMAT( CONVERT_TZ(tbl_invoice_batch.created,'+00:00','{$timezoneOffset}'), '%Y-%m-%d')";
        	}else{
        		$where_date_query = "CAST(switchoffset(todatetimeoffset(Cast(tbl_invoice_batch.created as datetime), '+00:00'), '{$timezoneOffset}') as date) ";
    		} 
    		$query->andWhere(" $where_date_query >= '$created_duedate_duedate_s' AND $where_date_query  <= '$created_duedate_duedate_e' ");
        }
        
        /*if(isset($params['InvoiceBatchSearch']['modified']) && $params['InvoiceBatchSearch']['modified']!='' && $params['InvoiceBatchSearch']['modified']!="All"){
        	$start_date=date('Y-m-d',strtotime($params['InvoiceBatchSearch']['modified']));
       		if (DB_TYPE == 'sqlsrv') {
       			$datesql = "Cast(switchoffset(todatetimeoffset(Cast(tbl_invoice_batch.modified as datetime), '+00:00'), '{$UserSettimezoneOffset}') as date)";
       		} else {
       			$datesql = "DATE_FORMAT(CONVERT_TZ(tbl_invoice_batch.modified,'{$UTCtimezoneOffset}','{$UserSettimezoneOffset}'),'%Y-%m-%d')";
       		}
       		$query->andFilterWhere(["$datesql" => $start_date]);
        }*/
        if(isset($params['InvoiceBatchSearch']['modified']) && $params['InvoiceBatchSearch']['modified']!=""){       
			$modified_duedate=explode("-",$params['InvoiceBatchSearch']['modified']);
			$modified_duedate_start=explode("/",trim($modified_duedate[0]));
			$modified_duedate_end=explode("/",trim($modified_duedate[1]));
			$modified_duedate_duedate_s=$modified_duedate_start[2]."-".$modified_duedate_start[0]."-".$modified_duedate_start[1];
			$modified_duedate_duedate_e=$modified_duedate_end[2]."-".$modified_duedate_end[0]."-".$modified_duedate_end[1];
			if (Yii::$app->db->driverName == 'mysql'){
        		$where_date_query ="DATE_FORMAT( CONVERT_TZ(tbl_invoice_batch.modified,'+00:00','{$timezoneOffset}'), '%Y-%m-%d')";
        	}else{
        		$where_date_query = "CAST(switchoffset(todatetimeoffset(Cast(tbl_invoice_batch.modified as datetime), '+00:00'), '{$timezoneOffset}') as date) ";
    		} 
    		$query->andWhere(" $where_date_query >= '$modified_duedate_duedate_s' AND $where_date_query  <= '$modified_duedate_duedate_e' ");
        }
        if(isset($params['InvoiceBatchSearch']['created_by']) && $params['InvoiceBatchSearch']['created_by']!='' && $params['InvoiceBatchSearch']['created_by']!="All"){
			$query_created_by="";
			if(!empty($params['InvoiceBatchSearch']['created_by'])){
				foreach($params['InvoiceBatchSearch']['created_by'] as $k=>$v){
						if($query_created_by==''){
							$query_created_by=" CONCAT(tbl_user.usr_first_name,' ',tbl_user.usr_lastname) like '%".$v."%'";
						}else{
							$query_created_by.=" OR CONCAT(tbl_user.usr_first_name,' ',tbl_user.usr_lastname) like '%".$v."%'";
						}
				}
			}
			if($query_created_by!=""){
				//$query->joinWith(['createUser' => function(\yii\db\ActiveQuery $query) use($created_by,$query_created_by){
				//$query->select(['tbl_user.id','tbl_user.usr_first_name','tbl_user.usr_lastname']);
				$query->andWhere("(".$query_created_by.")");
				//}]);
			}
			/*$created_by = $params['InvoiceBatchSearch']['created_by'];
			$query->joinWith(['createUser' => function(\yii\db\ActiveQuery $query) use($created_by){
				$query->select(['tbl_user.id','tbl_user.usr_first_name','tbl_user.usr_lastname']);
				$query->andFilterWhere(['or like',"CONCAT(tbl_user.usr_first_name,' ',tbl_user.usr_lastname)",$created_by]);
			}]);*/
		}
        
        if(isset($params['InvoiceBatchSearch']['modified_by']) && $params['InvoiceBatchSearch']['modified_by']!='' && $params['InvoiceBatchSearch']['modified_by']!="All"){
			$query_modified_by="";
			if(!empty($params['InvoiceBatchSearch']['modified_by'])){
				foreach($params['InvoiceBatchSearch']['modified_by'] as $k=>$v){
						if($query_modified_by==''){
							$query_modified_by=" CONCAT(modifieduser.usr_first_name,' ',modifieduser.usr_lastname) like '%".$v."%'";
						}else{
							$query_modified_by.=" OR CONCAT(modifieduser.usr_first_name,' ',modifieduser.usr_lastname) like '%".$v."%'";
						}
				}
			}
			if($query_modified_by!=""){
				//$query->joinWith(['modifiedUser' => function(\yii\db\ActiveQuery $query) use($created_by,$query_modified_by){
				//$query->select(['tbl_user.id','tbl_user.usr_first_name','tbl_user.usr_lastname']);
				$query->andWhere("(".$query_modified_by.")");
				//}]);
			}
			/*$modified_by = $params['InvoiceBatchSearch']['modified_by'];
			$query->joinWith(['createUser' => function(\yii\db\ActiveQuery $query) use($modified_by){
				$query->select(['tbl_user.id','tbl_user.usr_first_name','tbl_user.usr_lastname']);
				$query->andFilterWhere(['like',"CONCAT(tbl_user.usr_first_name,' ',tbl_user.usr_lastname)",$modified_by]);
			}]);*/
		}
		
		$query->all();
    	return $dataProvider;
    }
    
    /**
     * Creates data provider instance with searchFilter query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchFilter($params)
    {
		$query = InvoiceBatchSearch::find();
	
		$dataProvider = new ActiveDataProvider([
    		'query' => $query,
    		'sort'=> ['defaultOrder' => ['id'=>SORT_DESC]]
    	]);
    	
    	$this->load($params);
    	
    	if($params['field']=='batch'){
			if(isset($params['q']) && $params['q']!=""){
				//$query->andFilterWhere(['like','id' => $params['q']]);
				$query->where(['like','id', $params['q'].'%',false]);
    			$query->orderBy('id');
    		}
    		$dataProvider = ArrayHelper::map($query->all(),'id','id');
    	}
    	
    	if($params['field']=='display_invoice'){
			if(isset($params['q']) && $params['q']!=""){
                $query->andFilterWhere(['like','display_invoice',$params['q']]);
    		}
    		$dataProvider = ArrayHelper::map($query->all(),'display_invoice','display_invoice');
    	}
    	
    	if($params['field']=='display_by'){
			if(isset($params['q']) && $params['q']!=""){
			    $query->andFilterWhere(['display_by' => $params['q']]);
    		}
    		$dataProvider = ArrayHelper::map($query->all(),function($model, $defaultValue) {
				return $model->display_by==1?"Itemized":"Consolidated";
		    },function($model, $defaultValue) {
				return $model->display_by==1?"Itemized":"Consolidated";
		    });
    	}
    	
    	if($params['field']=='created_by'){
			if(isset($params['q']) && $params['q']!=""){
				$fullname = $params['q'];
				$query->With(['createUser' => function(\yii\db\ActiveQuery $query) use($fullname){
					$query->select(['tbl_user.id','tbl_user.usr_first_name','tbl_user.usr_lastname']);
					$query->andFilterWhere(['like',"CONCAT(tbl_user.usr_first_name,' ',tbl_user.usr_lastname)",$fullname]);
				}]);
			}
			$dataProvider = ArrayHelper::map($query->all(), function($model, $defaultValue) {
		      	return $model['createUser']['usr_first_name'].' '.$model['createUser']['usr_lastname'];
		    }, function($model, $defaultValue) {
		      	return $model['createUser']['usr_first_name'].' '.$model['createUser']['usr_lastname'];
		    });
		}
    	
    	if($params['field']=='modified_by'){
			if(isset($params['q']) && $params['q']!=""){
				$fullname = $params['q'];
				$query->With(['modifiedUser' => function(\yii\db\ActiveQuery $query) use($fullname){
					$query->select(['tbl_user.id','tbl_user.usr_first_name','tbl_user.usr_lastname']);
					$query->andFilterWhere(['like',"CONCAT(tbl_user.usr_first_name,' ',tbl_user.usr_lastname)",$fullname]);
				}]);
			}
    		$dataProvider = ArrayHelper::map($query->all(), function($model, $defaultValue) {
		      	return $model['modifiedUser']['usr_first_name'].' '.$model['modifiedUser']['usr_lastname'];
		    }, function($model, $defaultValue) {
		      	return $model['modifiedUser']['usr_first_name'].' '.$model['modifiedUser']['usr_lastname'];
		    });
    	}
    	
    	return array('All'=>'All') + $dataProvider;
    }
}
