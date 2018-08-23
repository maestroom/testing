<?php
namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use app\models\Options;
use app\models\TaskInstruct;

/**
 * TaskInstructSearch represents the model behind the search form about `app\models\TaskInstruct`.
 */
class TaskInstructSearch extends TaskInstruct
{
	public $service_name = '';
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'sales_user_id', 'task_type', 'task_id', 'task_priority', 'task_projectreqtype', 'saved', 'mediadisplay_by', 'load_prev', 'created_by', 'modified_by'], 'integer'],
            [['task_duedate', 'project_name', 'requestor', 'task_timedue', 'instruct_version', 'isactive', 'created', 'service_name', 'modified'], 'safe'],
        ];
    }
	public function attributes()
    {
        // add related fields to searchable attributes
        return array_merge(parent::attributes(), ['instruct_version']);
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
    public function search($params,$task_id=0)
    {
        $query = TaskInstruct::find()->joinWith(['createdUser created_user','modifiedUser modified_user']);
        if(isset($params['case_id']) && $params['case_id']!="" && $params['case_id']!=0)
        $query->where('client_case_id='.$params['case_id']);
        
        
        // add conditions that should always apply here
        $params['TaskInstructSearch']['id'] = (isset($params['TaskInstructSearch']['id']) && $params['TaskInstructSearch']['id']!="All")?$params['TaskInstructSearch']['id']:'';
        $params['TaskInstructSearch']['modified_by'] = (isset($params['TaskInstructSearch']['modified_by']) && $params['TaskInstructSearch']['modified_by']!="All")?$params['TaskInstructSearch']['modified_by']:'';
        $params['TaskInstructSearch']['created_by'] = (isset($params['TaskInstructSearch']['created_by']) && $params['TaskInstructSearch']['created_by']!="All")?$params['TaskInstructSearch']['created_by']:'';
        $params['TaskInstructSearch']['created'] = (isset($params['TaskInstructSearch']['created']) && $params['TaskInstructSearch']['created']!="All")?$params['TaskInstructSearch']['created']:'';
        $params['TaskInstructSearch']['modified'] = (isset($params['TaskInstructSearch']['modified']) && $params['TaskInstructSearch']['modified']!="All")?$params['TaskInstructSearch']['modified']:'';
        $params['TaskInstructSearch']['service_name'] = (isset($params['TaskInstructSearch']['service_name']) && $params['TaskInstructSearch']['service_name']!="All")?$params['TaskInstructSearch']['service_name']:'';
        $params['TaskInstructSearch']['instruct_version'] = (isset($params['TaskInstructSearch']['instruct_version']) && $params['TaskInstructSearch']['instruct_version']!="All")?$params['TaskInstructSearch']['instruct_version']:'';
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $defaultSort="";
		if(!isset($params['sort'])){
			$defaultSort=['id'=>SORT_DESC];
			//$query->orderBy('id DESC');
		}
		$dataProvider->sort->enableMultiSort=true;
		/*IRT-67*/
		if(isset($params['grid_id']) && $params['grid_id']!=""){
			$grid_id=$params['grid_id'].'_'.Yii::$app->user->identity->id;
			$sql="SELECT * FROM tbl_dynagrid_dtl WHERE id IN (SELECT sort_id FROM tbl_dynagrid WHERE id='$grid_id')";
			$sort_data=Yii::$app->db->createCommand($sql)->queryOne();
			if(!empty($sort_data)){
					$defaultSort=json_decode($sort_data['data'],true);
			}
		}
		/*IRT-67*/
        if(!isset($params['sort']) && $defaultSort!=""){
			$dataProvider->sort->defaultOrder=$defaultSort;
		}
		$dataProvider->sort->attributes['created_by'] = [
    			'asc' => ['created_user.usr_first_name' => SORT_ASC,'created_user.usr_lastname' => SORT_ASC],
    			'desc' => ['created_user.usr_first_name' => SORT_DESC,'created_user.usr_lastname' => SORT_DESC],
    	];
		$dataProvider->sort->attributes['modified_by'] = [
    			'asc' => ['modified_user.usr_first_name' => SORT_ASC,'modified_user.usr_lastname' => SORT_ASC],
    			'desc' => ['modified_user.usr_first_name' => SORT_DESC,'modified_user.usr_lastname' => SORT_DESC],
    	];
		/*multiselect*/
        if ($params['TaskInstructSearch']['id'] != null && is_array($params['TaskInstructSearch']['id'])) {
			if(!empty($params['TaskInstructSearch']['id'])){
				foreach($params['TaskInstructSearch']['id'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['TaskInstructSearch']['id']);
					}
				}
			}
		}
		if ($params['TaskInstructSearch']['created_by'] != null && is_array($params['TaskInstructSearch']['created_by'])) {
			if(!empty($params['TaskInstructSearch']['created_by'])){
				foreach($params['TaskInstructSearch']['created_by'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['TaskInstructSearch']['created_by']);
					}
				}
			}
		}
		if ($params['TaskInstructSearch']['modified_by'] != null && is_array($params['TaskInstructSearch']['modified_by'])) {
			if(!empty($params['TaskInstructSearch']['modified_by'])){
				foreach($params['TaskInstructSearch']['modified_by'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['TaskInstructSearch']['modified_by']);
					}
				}
			}
		}
		if ($params['TaskInstructSearch']['instruct_version'] != null && is_array($params['TaskInstructSearch']['instruct_version'])) {
			if(!empty($params['TaskInstructSearch']['instruct_version'])){
				foreach($params['TaskInstructSearch']['instruct_version'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['TaskInstructSearch']['instruct_version']);
					}
				}
			}
		}
		/*multiselect*/
        $this->load($params);

        if($task_id!=0){
        	$this->task_id=$task_id;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'tbl_task_instruct.id' => $this->id,
            'tbl_task_instruct.sales_user_id' => $this->sales_user_id,
            'tbl_task_instruct.task_type' => $this->task_type,
            'tbl_task_instruct.task_id' => $this->task_id,
            'tbl_task_instruct.task_duedate' => $this->task_duedate,
            'tbl_task_instruct.task_priority' => $this->task_priority,
            'tbl_task_instruct.task_projectreqtype' => $this->task_projectreqtype,
            'tbl_task_instruct.saved' => $this->saved,
            'tbl_task_instruct.mediadisplay_by' => $this->mediadisplay_by,
            'tbl_task_instruct.load_prev' => $this->load_prev,
        ]);
        
        if(isset($params['TaskInstructSearch']['id']) && $params['TaskInstructSearch']['id']!='' && $params['TaskInstructSearch']['id']!="All"){
        	$query->andFilterWhere(['tbl_task_instruct.id' => $params['TaskInstructSearch']['id']]);
        }
        
        if(isset($params['TaskInstructSearch']['instruct_version']) && $params['TaskInstructSearch']['instruct_version']!='' && $params['TaskInstructSearch']['instruct_version']!="All"){            
			if(is_array($params['TaskInstructSearch']['instruct_version'])){
				foreach($params['TaskInstructSearch']['instruct_version'] as $key => $single_version){
					$instruct_version[] = explode("V",$single_version)[1];                    
				}
				$query->andFilterWhere(['in','tbl_task_instruct.instruct_version', $instruct_version]);            
			}else{
				$params['TaskInstructSearch']['instruct_version'] = preg_replace('/[^0-9]/', '', $params['TaskInstructSearch']['instruct_version']);
				$query->andFilterWhere(['like','tbl_task_instruct.instruct_version',$params['TaskInstructSearch']['instruct_version']]);            
			}    
        }
        
        if(isset($params['TaskInstructSearch']['service_name']) && $params['TaskInstructSearch']['service_name']!='' && $params['TaskInstructSearch']['service_name']!="All"){
        	$service_names = $params['TaskInstructSearch']['service_name'];
        	$query->select(['tbl_task_instruct.*','tbl_teamservice.service_name']);
        	$query->join('INNER JOIN','tbl_task_instruct_servicetask','tbl_task_instruct_servicetask.task_instruct_id = tbl_task_instruct.id');
        	$query->join('INNER JOIN','tbl_teamservice','tbl_teamservice.id = tbl_task_instruct_servicetask.teamservice_id');
        	$query->andFilterWhere(['like', 'tbl_teamservice.service_name', $service_names]);
        }
        
       	$UTCtimezoneOffset = (new Options)->getOffsetOfTimeZone();
        $UserSettimezoneOffset = (new Options)->getOffsetOfTimeZone($_SESSION['usrTZ']);  
        $timezoneOffset = (new Options)->getOffsetOfCurrenttimeZone();
        if(isset($params['TaskInstructSearch']['created']) && $params['TaskInstructSearch']['created']!='' && $params['TaskInstructSearch']['created']!="All"){
        	$created_date=explode("-",$params['TaskInstructSearch']['created']);
			$created_date_start=explode("/",trim($created_date[0]));
			$created_date_end=explode("/",trim($created_date[1]));
			$created_date_s=$created_date_start[2]."-".$created_date_start[0]."-".$created_date_start[1];
			$created_date_e=$created_date_end[2]."-".$created_date_end[0]."-".$created_date_end[1];
			if (Yii::$app->db->driverName == 'mysql'){
        		$where_date_query ="DATE_FORMAT( CONVERT_TZ(tbl_task_instruct.`created`,'+00:00','{$timezoneOffset}'), '%Y-%m-%d')";
        	}else{
        		$where_date_query = "CAST(switchoffset(todatetimeoffset(Cast(tbl_task_instruct.created as datetime), '+00:00'), '{$timezoneOffset}') as date) ";
    		} 
    		$query->andWhere(" $where_date_query >= '$created_date_s' AND $where_date_query  <= '$created_date_e' ");
        	/*$start_date=date('Y-m-d',strtotime($params['TaskInstructSearch']['created']));
			if (Yii::$app->db->driverName == 'mysql') {
       			$datesql = "DATE_FORMAT(CONVERT_TZ(tbl_task_instruct.created,'{$UTCtimezoneOffset}','{$UserSettimezoneOffset}'),'%Y-%m-%d')";
       		} else {
       			$datesql = "Cast(switchoffset(todatetimeoffset(Cast(tbl_task_instruct.created as datetime), '+00:00'), '{$UserSettimezoneOffset}') as date)";
       		}
       		$query->andFilterWhere(["$datesql" => $start_date]);*/
        }
        
        if(isset($params['TaskInstructSearch']['modified']) && $params['TaskInstructSearch']['modified']!='' && $params['TaskInstructSearch']['modified']!="All"){
        	$modified_date=explode("-",$params['TaskInstructSearch']['modified']);
			$modified_date_start=explode("/",trim($modified_date[0]));
			$modified_date_end=explode("/",trim($modified_date[1]));
			$modified_date_s=$modified_date_start[2]."-".$modified_date_start[0]."-".$modified_date_start[1];
			$modified_date_e=$modified_date_end[2]."-".$modified_date_end[0]."-".$modified_date_end[1];
			if (Yii::$app->db->driverName == 'mysql'){
        		$where_date_query ="DATE_FORMAT( CONVERT_TZ(tbl_task_instruct.`modified`,'+00:00','{$timezoneOffset}'), '%Y-%m-%d')";
        	}else{
        		$where_date_query = "CAST(switchoffset(todatetimeoffset(Cast(tbl_task_instruct.modified as datetime), '+00:00'), '{$timezoneOffset}') as date) ";
    		} 
    		$query->andWhere(" $where_date_query >= '$modified_date_s' AND $where_date_query  <= '$modified_date_e' ");
        	/*$start_date=date('Y-m-d',strtotime($params['TaskInstructSearch']['modified']));
        	if (Yii::$app->db->driverName == 'mysql') {
        		$datesql = "DATE_FORMAT(CONVERT_TZ(tbl_task_instruct.modified,'{$UTCtimezoneOffset}','{$UserSettimezoneOffset}'),'%Y-%m-%d')";
        	} else {
        		$datesql = "Cast(switchoffset(todatetimeoffset(Cast(tbl_task_instruct.modified as datetime), '+00:00'), '{$UserSettimezoneOffset}') as date)";
        	}
        	$query->andFilterWhere(["$datesql" => $start_date]);*/
        }
        
        if(isset($params['TaskInstructSearch']['created_by']) && $params['TaskInstructSearch']['created_by']!='' && $params['TaskInstructSearch']['created_by']!="All"){
        	$para = $params['TaskInstructSearch']['created_by'];
        	$query->joinWith([
        		'createdUser' => function(\yii\db\ActiveQuery $query) use($para){
        			$query->andFilterWhere(['or like',"CONCAT(tbl_user.usr_first_name,' ',tbl_user.usr_lastname)", $para]);
        		}
        	]);
        }
        
        if(isset($params['TaskInstructSearch']['modified_by']) && $params['TaskInstructSearch']['modified_by']!='' && $params['TaskInstructSearch']['modified_by']!="All"){
        	$para = $params['TaskInstructSearch']['modified_by'];
        	$query->joinWith([
        		'createdUser' => function(\yii\db\ActiveQuery $query) use($para){
        			$query->andFilterWhere(['like',"CONCAT(tbl_user.usr_first_name,' ',tbl_user.usr_lastname)", $para]);
        		}
        	]);
        }
        
        $query->andFilterWhere(['like', 'project_name', $this->project_name])
            ->andFilterWhere(['like', 'requestor', $this->requestor])
            ->andFilterWhere(['like', 'task_timedue', $this->task_timedue])
            ->andFilterWhere(['like', 'isactive', $this->isactive]);

        return $dataProvider;
    }
    
    /**
     * SearchFilter 
     * 
     * @param array $params
     */
    public function searchFilter($params, $task_id=0)
    {
		
    	$query = TaskInstruct::find();
    	if(isset($params['case_id']) && $params['case_id']!="" && $params['case_id']!=0){
			$query->where('client_case_id='.$params['case_id']);
		}
    	//->select(['tbl_task_instruct.id','tbl_task_instruct.created_by','tbl_task_instruct.modified_by','tbl_task_instruct.created','tbl_task_instruct.modified','tbl_teamservice.service_name',"CONCAT('V ',tbl_task_instruct.instruct_version) as instruct_version"])
    	/*->with(['createdUser' => function(\yii\db\ActiveQuery $query){
    			$query->select(['tbl_user.id','tbl_user.usr_first_name','tbl_user.usr_lastname']);
    	}])*/
    	//->join('INNER JOIN','tbl_task_instruct_servicetask','tbl_task_instruct_servicetask.task_instruct_id = tbl_task_instruct.id')
    	//
    	if(isset($params['TaskInstructSearch']['saved'])){
    		$where = 'tbl_task_instruct.saved='.$params['TaskInstructSearch']['saved'];
    		$query->andWhere($where);
    	}
    	
    	
    	$dataProvider = new ActiveDataProvider([
    		'query' => $query,
    	]);
    	 
    	$this->load($params);
    	if($task_id!=0){
    		//$this->task_id=$task_id;
    		$query->andFilterWhere(['tbl_task_instruct.task_id' => $task_id]);
    	}
    	if($params['field']=='id'){
    		$query->select('tbl_task_instruct.id');
    		if(isset($params['q']) && $params['q']!="" && $params['q']!="All"){
				$query->andFilterWhere(['like','tbl_task_instruct.id',$params['q']]);
    		}
    		$dataProvider = ArrayHelper::map($query->all(),'id','id');
    	}
    	if($params['field']=='service_name'){$query->select('service_name');
    		$query->join('LEFT JOIN','tbl_task_instruct_servicetask','tbl_task_instruct_servicetask.task_instruct_id = tbl_task_instruct.id');
    		$query->join('LEFT JOIN','tbl_teamservice','tbl_teamservice.id = tbl_task_instruct_servicetask.teamservice_id');
    		if(isset($params['q']) && $params['q']!="" && $params['q']!="All"){
    			$query->andFilterWhere(['like','tbl_teamservice.service_name', $params['q']]);
    		}
    		$query->groupBy('service_name');
    		$dataProvider = ArrayHelper::map($query->all(),'service_name','service_name');
    	}
    	if($params['field']=='instruct_version'){
    		if(isset($params['q']) && $params['q']!="" && $params['q']!="All"){
				$query->andFilterWhere(['like','tbl_task_instruct.instruct_version',$params['q']]);
    		}
    		$dataProvider = ArrayHelper::map($query->all(),function($model, $defaultValue) {
			  	return 'V'.$model['instruct_version'];
		    }, function($model, $defaultValue) {
		      	return 'V'.$model['instruct_version'];
		    });
    	}
    	
    	
     	if($params['field']=='created_by'){
     		if(isset($params['q']) && $params['q']!="" && $params['q']!="All") {
				$para = $params['q'];
				$query->with(['createdUser' => function(\yii\db\ActiveQuery $query) use ($para){
					$query->select(['tbl_user.id','tbl_user.usr_first_name','tbl_user.usr_lastname']);
					$query->andFilterWhere(['like',"CONCAT(tbl_user.usr_first_name,' ',tbl_user.usr_lastname)", $para]);
				}]);
			}
			$dataProvider = ArrayHelper::map($query->all(), function($model, $defaultValue) {
		      	return $model['createdUser']['usr_first_name'].' '.$model['createdUser']['usr_lastname'];
		    }, function($model, $defaultValue) {
		      	return $model['createdUser']['usr_first_name'].' '.$model['createdUser']['usr_lastname'];
		    });
    	}
    	
    	if($params['field']=='modified_by'){
    		if(isset($params['q']) && $params['q']!="" && $params['q']!="All") {
				$para = $params['q'];
    			$query->with(['modifiedUser' => function(\yii\db\ActiveQuery $query) use ($para){
					$query->select(['tbl_user.id','tbl_user.usr_first_name','tbl_user.usr_lastname']);
					$query->andFilterWhere(['like',"CONCAT(tbl_user.usr_first_name,' ',tbl_user.usr_lastname)", $para]);
				}]);
    		}
    		$dataProvider = ArrayHelper::map($query->all(), function($model, $defaultValue) {
		      	return $model['createdUser']['usr_first_name'].' '.$model['createdUser']['usr_lastname'];
		    },function($model, $defaultValue) {
		      	return $model['createdUser']['usr_first_name'].' '.$model['createdUser']['usr_lastname'];
		    });
    	}
    	//echo "<pre>"; print_r($dataProvider); exit;
    	return array('All'=>'All') +  $dataProvider;
    }
}
