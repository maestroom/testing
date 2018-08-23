<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use app\models\ReportsUserSaved;

/**
 * ReportsUserSavedSSearch represents the model behind the search form about `app\models\ReportsUserSaved`.
 */
class ReportsUserSavedSearch extends ReportsUserSaved
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'report_save_to', 'share_report_by', 'report_type_id', 'report_format_id', 'chart_format_id', 'date_type_field_id', 'created_by', 'modified_by'], 'integer'],
            [['custom_report_name', 'custom_report_description', 'date_range', 'created', 'modified'], 'safe'],
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
		$defaultSort="";
        $query = ReportsUserSaved::find()->joinWith(['reportType','reportFormat','createdUser'],false);
		//->orderBy('id desc');
		$userId=Yii::$app->user->identity->id;
		$roleId=Yii::$app->user->identity->role_id;
		// add conditions that should always apply here
        $params['ReportsUserSavedSearch']['id'] = (isset($params['ReportsUserSavedSearch']['id']) && $params['ReportsUserSavedSearch']['id']!="All")?$params['ReportsUserSavedSearch']['id']:'';
        $params['ReportsUserSavedSearch']['custom_report_name'] = (isset($params['ReportsUserSavedSearch']['custom_report_name']) && $params['ReportsUserSavedSearch']['custom_report_name']!="All")?$params['ReportsUserSavedSearch']['custom_report_name']:'';
        $params['ReportsUserSavedSearch']['custom_report_description'] = (isset($params['ReportsUserSavedSearch']['custom_report_description']) && $params['ReportsUserSavedSearch']['custom_report_description']!="All")?$params['ReportsUserSavedSearch']['custom_report_description']:'';
        $params['ReportsUserSavedSearch']['created_by'] = (isset($params['ReportsUserSavedSearch']['created_by']) && $params['ReportsUserSavedSearch']['created_by']!="All")?$params['ReportsUserSavedSearch']['created_by']:'';
        $params['ReportsUserSavedSearch']['report_type_id'] = (isset($params['ReportsUserSavedSearch']['report_type_id']) && $params['ReportsUserSavedSearch']['report_type_id']!="All")?$params['ReportsUserSavedSearch']['report_type_id']:'';
		$params['ReportsUserSavedSearch']['report_format_id'] = (isset($params['ReportsUserSavedSearch']['report_format_id']) && $params['ReportsUserSavedSearch']['report_format_id']!="All")?$params['ReportsUserSavedSearch']['report_format_id']:'';
        $params['ReportsUserSavedSearch']['chart_format_id'] = (isset($params['ReportsUserSavedSearch']['chart_format_id']) && $params['ReportsUserSavedSearch']['chart_format_id']!="All")?$params['ReportsUserSavedSearch']['chart_format_id']:'';
        $params['ReportsUserSavedSearch']['share_report_by'] = (isset($params['ReportsUserSavedSearch']['share_report_by']) && $params['ReportsUserSavedSearch']['share_report_by']!="All")?$params['ReportsUserSavedSearch']['share_report_by']:'';
        if($roleId!=0) {
			$rpaccess="SELECT DISTINCT saved_report_id FROM ( 
			(
				SELECT saved_report_id FROM tbl_reports_user_saved_shared_with 
				INNER JOIN tbl_project_security on tbl_project_security.client_case_id=tbl_reports_user_saved_shared_with.client_case_id 
				AND tbl_project_security.user_id=$userId AND tbl_reports_user_saved_shared_with.client_case_id !=0
			) 
			UNION ALL 
			(
				SELECT saved_report_id FROM tbl_reports_user_saved_shared_with WHERE user_id IN ($userId)
			)
			UNION ALL 
			(
				SELECT saved_report_id FROM tbl_reports_user_saved_shared_with WHERE role_id IN ({$roleId})
			) 
			UNION ALL 
			(
				SELECT saved_report_id FROM tbl_reports_user_saved_shared_with 
				INNER JOIN tbl_project_security on tbl_project_security.team_id=tbl_reports_user_saved_shared_with.team_id 
				AND tbl_project_security.team_loc = tbl_reports_user_saved_shared_with.team_loc 
				AND tbl_project_security.user_id=$userId where tbl_reports_user_saved_shared_with.team_id !=0
			)
			) as RPACCESS";
			//$where=" ((created_by=$userId) OR (report_save_to=3) OR id IN ($rpaccess)) ";
			//$where=" CASE WHEN report_save_to = 1 THEN (created_by=$userId) WHEN report_save_to=3 THEN 1=1  WHEN report_save_to=2 THEN id IN ($rpaccess) OR (created_by=$userId) END ";
			$where="( (report_save_to = 1 AND (tbl_reports_user_saved.created_by=$userId)) OR (report_save_to = 3 AND 1=1) OR (report_save_to=2 AND tbl_reports_user_saved.id IN($rpaccess)) OR (tbl_reports_user_saved.created_by=$userId) )";
			$query->where($where);
		}
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
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
        if(!isset($params['sort']) && $defaultSort!="") {
			$dataProvider->sort->defaultOrder = $defaultSort;
		}
		$dataProvider->sort->attributes['report_type_id'] = [
            'asc' => ['tbl_reports_report_type.report_type' => SORT_ASC],
            'desc' => ['tbl_reports_report_type.report_type' => SORT_DESC],
        ];

		$dataProvider->sort->attributes['report_format_id'] = [
            'asc' => ['tbl_reports_report_format.report_format' => SORT_ASC],
            'desc' => ['tbl_reports_report_format.report_format' => SORT_DESC],
        ];		
		$dataProvider->sort->attributes['created_by'] = [
            'asc' => ["CONCAT(tbl_user.usr_first_name,' ',tbl_user.usr_lastname)" => SORT_ASC],
    		'desc' => ["CONCAT(tbl_user.usr_first_name,' ',tbl_user.usr_lastname)" => SORT_DESC],
        ];		
		
        
		/*multiselect*/
		if ($params['ReportsUserSavedSearch']['id'] != null && is_array($params['ReportsUserSavedSearch']['id'])) {
			if(!empty($params['ReportsUserSavedSearch']['id'])){
				foreach($params['ReportsUserSavedSearch']['id'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['ReportsUserSavedSearch']['id']);
					}
				}
			}
		}
		if ($params['ReportsUserSavedSearch']['report_type_id'] != null && is_array($params['ReportsUserSavedSearch']['report_type_id'])) {
			if(!empty($params['ReportsUserSavedSearch']['report_type_id'])){
				foreach($params['ReportsUserSavedSearch']['report_type_id'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['ReportsUserSavedSearch']['report_type_id']);
					}
				}
			}
		}
		if ($params['ReportsUserSavedSearch']['report_format_id'] != null && is_array($params['ReportsUserSavedSearch']['report_format_id'])) {
			if(!empty($params['ReportsUserSavedSearch']['report_format_id'])){
				foreach($params['ReportsUserSavedSearch']['report_format_id'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['ReportsUserSavedSearch']['report_format_id']);
					}
				}
			}
		}
		if ($params['ReportsUserSavedSearch']['custom_report_name'] != null && is_array($params['ReportsUserSavedSearch']['custom_report_name'])) {
			if(!empty($params['ReportsUserSavedSearch']['custom_report_name'])){
				foreach($params['ReportsUserSavedSearch']['custom_report_name'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['ReportsUserSavedSearch']['custom_report_name']);
					}
				}
			}
		}
		if ($params['ReportsUserSavedSearch']['created_by'] != null && is_array($params['ReportsUserSavedSearch']['created_by'])) {
			if(!empty($params['ReportsUserSavedSearch']['created_by'])){
				foreach($params['ReportsUserSavedSearch']['created_by'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['ReportsUserSavedSearch']['created_by']);
					}
				}
			}
		}
		
		
		/*multiselect*/
		$this->load($params);
		//echo "<pre>",print_r($params);die;
       /* if (!$this->validate()) {
            return $dataProvider;
        } */

        // grid filtering conditions
        $query->andFilterWhere([
			//    'id' => $this->id,
			'report_save_to' => $this->report_save_to,
			//   'share_report_by' => $this->share_report_by,
			//   'report_type_id' => $this->report_type_id,
			//	 'report_format_id' => $this->report_format_id,
			//   'chart_format_id' => $this->chart_format_id,
			//   'date_type_field_id' => $this->date_type_field_id,
			//   'created' => $this->created,
			//   'created_by' => $this->created_by,
			//   'modified' => $this->modified,
			//   'modified_by' => $this->modified_by,
        ]);
        
        if(isset($params['ReportsUserSavedSearch']['id']) && $params['ReportsUserSavedSearch']['id']!='' && $params['ReportsUserSavedSearch']['id']!="All"){
			$query->andFilterWhere(['tbl_reports_user_saved.id' => $params['ReportsUserSavedSearch']['id']]);
        }
        
        if(isset($params['ReportsUserSavedSearch']['share_report_by']) && $params['ReportsUserSavedSearch']['share_report_by']!='' && $params['ReportsUserSavedSearch']['share_report_by']!="All"){
			$access = '';
			if($params['ReportsUserSavedSearch']['share_report_by']=='Private')
				$access = 0;
			else if($params['ReportsUserSavedSearch']['share_report_by']=='By Role')
				$access = 1;	
			else if($params['ReportsUserSavedSearch']['share_report_by']=='By Client/Case')
				$access = 2;
			else if($params['ReportsUserSavedSearch']['share_report_by']=='By Team/Location')
				$access = 3;
				
			$query->andFilterWhere(['share_report_by' => $access]);
        }
        
        if(isset($params['ReportsUserSavedSearch']['custom_report_name']) && $params['ReportsUserSavedSearch']['custom_report_name']!='' && $params['ReportsUserSavedSearch']['custom_report_name']!="All") {
        	$query->andFilterWhere(['or like' ,'custom_report_name' , $params['ReportsUserSavedSearch']['custom_report_name']]);
        }
        
        if(isset($params['ReportsUserSavedSearch']['report_type_id']) && $params['ReportsUserSavedSearch']['report_type_id']!='' && $params['ReportsUserSavedSearch']['report_type_id']!="All") {
			$parameter = $params['ReportsUserSavedSearch']['report_type_id'];
			$query -> joinWith([
				'reportType' => function(\yii\db\ActiveQuery $query) use ($parameter) {
					$query->andFilterWhere(["or like", "tbl_reports_report_type.report_type", $parameter]);
				}
			]);
		}

		if(isset($params['ReportsUserSavedSearch']['report_format_id']) && $params['ReportsUserSavedSearch']['report_format_id']!='' && $params['ReportsUserSavedSearch']['report_format_id']!="All") {
			$parameter = $params['ReportsUserSavedSearch']['report_format_id'];
			$query -> joinWith([
				'reportFormat' => function(\yii\db\ActiveQuery $query) use ($parameter) {
					$query->andFilterWhere(["or like", "tbl_reports_report_format.report_format", $parameter]);
				}
			]);
		}
        
        if(isset($params['ReportsUserSavedSearch']['custom_report_description']) && $params['ReportsUserSavedSearch']['custom_report_description']!='' && $params['ReportsUserSavedSearch']['custom_report_description']!="All"){
			$query->andFilterWhere(['like' ,'custom_report_description' , $params['ReportsUserSavedSearch']['custom_report_description']]);
        }
        
        if(isset($params['ReportsUserSavedSearch']['chart_format_id']) && $params['ReportsUserSavedSearch']['chart_format_id']!='' && $params['ReportsUserSavedSearch']['chart_format_id']!="All"){
	    	if($params['ReportsUserSavedSearch']['chart_format_id']=="Tabular"){
				$query->andFilterWhere(["chart_format_id" => 0]);
			} else {
				$para = $params['ReportsUserSavedSearch']['chart_format_id'];
				$query->joinWith([
					'chartFormat' => function(\yii\db\ActiveQuery $query) use($para){
						$query->andFilterWhere(["like", "tbl_reports_chart_format.chart_format", $para]);
					}
				]);
			}
	    }
        
        if(isset($params['ReportsUserSavedSearch']['created_by']) && $params['ReportsUserSavedSearch']['created_by']!='' && $params['ReportsUserSavedSearch']['created_by']!="All"){
        	$para = $params['ReportsUserSavedSearch']['created_by'];
        	$query->joinWith([
        		'createdUser' => function(\yii\db\ActiveQuery $query) use($para){
        			$query->andFilterWhere(['or like',"CONCAT(tbl_user.usr_first_name,' ',tbl_user.usr_lastname)", $para]);
        		}
        	]);
        }
        
       // die;
        return $dataProvider;
    }
    
     /**
     * SearchFilter 
     * 
     * @param array $params
     */
    public function searchFilter($params)
    {
		$query = ReportsUserSaved::find();

        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

       /* if (!$this->validate()) {
            return $dataProvider;
        } */
        
        if($params['field']=='id'){
			if(isset($params['q']) && $params['q']!="" && $params['q']!="All"){
				//$query->andFilterWhere(['id' => $params['q']]); // 
				$query->where(['like','id', $params['q'].'%',false]);
    			$query->orderBy('id');
			}
    		$dataProvider = ArrayHelper::map($query->all(),'id','id');
    	}
    	
    	if($params['field']=='custom_report_name'){
    		if(isset($params['q']) && $params['q']!="" && $params['q']!="All"){
				$query->andFilterWhere(['like', 'custom_report_name', $params['q']]);
    		}
    		$dataProvider = ArrayHelper::map($query->all(),'custom_report_name','custom_report_name');
    	}
    	
    	if($params['field']=='share_report_by'){
    		if(isset($params['q']) && $params['q']!="" && $params['q']!="All"){
				$query->andFilterWhere(['share_report_by' =>  $params['q']]);
    		}
			$dataProvider = ArrayHelper::map($query->all(), 'share_report_by' , function($model, $defaultValue) {
				$access = '';
				if($model->report_save_to==1){
					$access = 'Private';
				} else if($model->report_save_to==2){
					if($model->share_report_by==1)
						$access = 'By Role';
					else if($model->share_report_by==2)
						$access = 'By Client/Case';
					else if($model->share_report_by==3)
						$access = 'By Team/Location';
				}
				return $access;	
		    });
    	}
    	
    	if($params['field']=='custom_report_description'){
			if(isset($params['q']) && $params['q']!="" && $params['q']!="All"){
				$query->andFilterWhere(['like', 'custom_report_description', $params['q']]);
			}
    		$dataProvider = ArrayHelper::map($query->all(),'custom_report_description','custom_report_description');
    	}
    	
    	if($params['field']=='report_type_id'){
			if(isset($params['q']) && $params['q']!="" && $params['q']!="All"){
				$para = $params['q'];
				$query->with(['reportType' => function(\yii\db\ActiveQuery $query) use ($para){
					$query->select(['tbl_reports_report_type.id','tbl_reports_report_type.report_type','tbl_reports_report_type.report_type_description']);
					$query->andFilterWhere(["like",'tbl_reports_report_type.report_type',$para]);
				}]);
			}
			$dataProvider = ArrayHelper::map($query->all(), function($model, $defaultValue) {
		      	return $model['reportType']['report_type'];
		    }, function($model, $defaultValue) {
		      	return $model['reportType']['report_type'];
		    });
    	}

		if($params['field']=='report_format_id'){
			if(isset($params['q']) && $params['q']!="" && $params['q']!="All"){
				$para = $params['q'];
				$query->with(['reportFormat' => function(\yii\db\ActiveQuery $query) use ($para){
					$query->select(['tbl_reports_report_format.id','tbl_reports_report_format.report_format']);
					$query->andFilterWhere(["like",'tbl_reports_report_format.report_format',$para]);
				}]);
			}
			$dataProvider = ArrayHelper::map($query->all(), function($model, $defaultValue) {
		      	return $model['reportFormat']['report_format'];
		    }, function($model, $defaultValue) {
		      	return $model['reportFormat']['report_format'];
		    });
    	}
    	
    	if($params['field']=='chart_format_id'){
			if(isset($params['q']) && $params['q']!="" && $params['q']!="All"){
				$para = $params['q'];
				$query->with(['chartFormat' => function(\yii\db\ActiveQuery $query) use ($para){
					$query->select(['tbl_reports_chart_format.id','tbl_reports_chart_format.chart_format']);
					$query->Where(["tbl_reports_chart_format.id" => $para]);
				}]);
			}
			$dataProvider = ArrayHelper::map($query->all(), 'chart_format_id', function($model, $defaultValue) {
			  	return ($model->report_format_id==2)?$model['chartFormat']['chart_format']:"Tabular";
			});
		}
    	
    	if($params['field']=='created_by'){
			if(isset($params['q']) && $params['q']!="" && $params['q']!="All"){
				$para = $params['q'];
				$query->with(['CreatedUser' => function(\yii\db\ActiveQuery $query) use ($para){
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

        // grid filtering conditions
        return array('All'=>'All') + $dataProvider;
	}
}
