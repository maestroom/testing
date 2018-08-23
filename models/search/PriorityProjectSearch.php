<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\PriorityProject;
use yii\helpers\ArrayHelper;

/**
 * PriorityProjectSearch represents the model behind the search form about `app\models\PriorityProject`.
 */
class PriorityProjectSearch extends PriorityProject
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'priority_order', 'remove'], 'integer'],
            [['priority','project_priority_order'], 'safe'],
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
        $query = PriorityProject::find()->where(['remove'=>0]);
        if(!isset($params['sort'])){
			$query->orderBy(['priority_order'=>SORT_ASC]);
		}

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        	'pagination'=>['pageSize'=>25]
        ]);
        
        if ($params['PriorityProjectSearch']['priority'] != null && is_array($params['PriorityProjectSearch']['priority'])) {
                if(!empty($params['PriorityProjectSearch']['priority'])){
                        foreach($params['PriorityProjectSearch']['priority'] as $k=>$v){
                                if($v=='All'){ //  || strpos($v,",") !== false
                                        unset($params['PriorityProjectSearch']['priority']);break;
                                }
                        }
                }
                $query->andFilterWhere(['or like', 'priority', $params['PriorityProjectSearch']['priority']]);
        }else{
            if ($params['PriorityProjectSearch']['priority'] != null)
                $query->andFilterWhere(['like', 'priority', $params['PriorityProjectSearch']['priority']]);
        }
        if ($params['PriorityProjectSearch']['project_priority_order'] != null && is_array($params['PriorityProjectSearch']['project_priority_order'])) {
                if(!empty($params['PriorityProjectSearch']['project_priority_order'])){
                        foreach($params['PriorityProjectSearch']['project_priority_order'] as $k=>$v){
                                if($v=='All'){ //  || strpos($v,",") !== false
                                        unset($params['PriorityProjectSearch']['project_priority_order']);break;
                                }
                        }
                }
                $query->andFilterWhere(['or like', 'project_priority_order', $params['PriorityProjectSearch']['project_priority_order']]);
        }else{
            if ($params['PriorityProjectSearch']['project_priority_order'] != null)
                $query->andFilterWhere(['like', 'project_priority_order', $params['PriorityProjectSearch']['project_priority_order']]);
        }
        
        $dataProvider->sort->attributes['priority'] = [	
            'asc' => ['tbl_priority_project.priority_order' => SORT_ASC],
            'desc' => ['tbl_priority_project.priority_order' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['project_priority_order'] = [	
            'asc' => ['tbl_priority_project.project_priority_order' => SORT_ASC],
            'desc' => ['tbl_priority_project.project_priority_order' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'priority_order' => $this->priority_order,
            //'project_priority_order' => $this->project_priority_order,
            'remove' => $this->remove,
        ]);

        //$query->andFilterWhere(['like', 'priority', $this->priority]);

        return $dataProvider;
    }
    
    public function searchFilter($params)
    {
        $dataProvider = array();
        $query = PriorityProject::find()->where(['remove'=>0])->orderBy(['priority_order'=>SORT_ASC])->limit(100);
        if($params['field']=='priority'){
            $query->select(['priority']);
            if(isset($params['q']) && $params['q']!=""){$query->andFilterWhere(['or like','priority',$params['q']]);}
            $query->groupBy('priority');
            $query->orderBy('priority');
            $dataProvider = ArrayHelper::map($query->all(),'priority','priority');
    	}
        if($params['field']=='project_priority_order'){
            $query->select(['project_priority_order']);
            if(isset($params['q']) && $params['q']!=""){$query->andFilterWhere(['or like','project_priority_order',$params['q']]);}
            $query->groupBy('project_priority_order');
            $query->orderBy('project_priority_order');
            $dataProvider = ArrayHelper::map($query->all(),'project_priority_order','project_priority_order');
    	}
    	
    	//echo "<pre>",print_r($dataProvider),"</pre>";
    	return array_merge(array('0'=>'All'), $dataProvider);
    }
}
