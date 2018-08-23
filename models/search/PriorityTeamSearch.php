<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\PriorityTeam;
use yii\helpers\ArrayHelper;

/**
 * PriorityTeamSearch represents the model behind the search form about `app\models\PriorityTeam`.
 */
class PriorityTeamSearch extends PriorityTeam
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'priority_order', 'remove'], 'integer'],
            [['tasks_priority_name', 'priority_desc'], 'safe'],
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
        $query = PriorityTeam::find()->where(['remove'=>0]);

		 if(!isset($params['sort'])){
			$query->orderBy(['priority_order'=>SORT_ASC]);
		 }
		
		$dataProvider->sort->attributes['tasks_priority_name'] = [	
            'asc' => ['tbl_priority_team.priority_order' => SORT_ASC],
            'desc' => ['tbl_priority_team.priority_order' => SORT_DESC],
        ];
		
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        	'pagination'=>['pageSize'=>25]
        ]);

		if ($params['PriorityTeamSearch']['tasks_priority_name'] != null && is_array($params['PriorityTeamSearch']['tasks_priority_name'])) {
			if(!empty($params['PriorityTeamSearch']['tasks_priority_name'])){
				foreach($params['PriorityTeamSearch']['tasks_priority_name'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['PriorityTeamSearch']['tasks_priority_name']);break;
					}
				}
			}
			$query->andFilterWhere(['or like', 'tasks_priority_name', $params['PriorityTeamSearch']['tasks_priority_name']]);
		}

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'priority_order' => $this->priority_order,
            'remove' => $this->remove,
        ]);

        //$query->andFilterWhere(['like', 'tasks_priority_name', $this->tasks_priority_name]);
        $query->andFilterWhere(['like', 'priority_desc', $this->priority_desc]);

        return $dataProvider;
    }
    
    public function searchFilter($params)
    {
		$dataProvider = array();
		$query = PriorityTeam::find()->where(['remove'=>0])->orderBy(['priority_order'=>SORT_ASC])->limit(100);
		if($params['field']=='tasks_priority_name'){
    		$query->select(['tasks_priority_name']);
    		if(isset($params['q']) && $params['q']!=""){$query->andFilterWhere(['or like','tasks_priority_name',$params['q']]);}
    		$query->groupBy('tasks_priority_name');
    		$query->orderBy('tasks_priority_name');
    		$dataProvider = ArrayHelper::map($query->all(),'tasks_priority_name','tasks_priority_name');
    	}
    	
    	if($params['field']=='priority_desc'){
    		$query->select(['priority_desc']);
    		if(isset($params['q']) && $params['q']!=""){$query->andFilterWhere(['or like','priority_desc',$params['q']]);}
    		$query->groupBy('priority_desc');
    		$query->orderBy('priority_desc');
    		$dataProvider = ArrayHelper::map($query->all(),'priority_desc','priority_desc');
    	}
    	
    	//echo "<pre>",print_r($dataProvider),"</pre>";
    	return array_merge(array('0'=>'All'), $dataProvider);
    }
}
