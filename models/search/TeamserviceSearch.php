<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Teamservice;
use yii\helpers\ArrayHelper;

/**
 * DataTypeSearch represents the model behind the search form about `app\models\DataType`.
 */
class TeamserviceSearch extends Teamservice
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['teamid', 'sort_order', 'created_by', 'modified_by',], 'integer'],
        	[['service_name', 'service_description', 'hastasks'], 'string'],
            [['created', 'modified'], 'safe'],
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
        $query = Teamservice::find()->where(['teamid'=>$params['team_id']]);// ->orderBy(['sort_order'=>SORT_ASC]);

	    $dataProvider = new ActiveDataProvider([
            'query' => $query,
        	'pagination'=>['pageSize'=>8],
        	'sort'=> ['defaultOrder' => ['sort_order'=>SORT_ASC]]
        ]);

		if ($params['TeamserviceSearch']['service_name'] != null && is_array($params['TeamserviceSearch']['service_name'])) {
			if(!empty($params['TeamserviceSearch']['service_name'])){
				foreach($params['TeamserviceSearch']['service_name'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['TeamserviceSearch']['service_name']);break;
					}
				}
			}
			$query->andFilterWhere(['or like', 'service_name', $params['TeamserviceSearch']['service_name']]);
		}

		if ($params['TeamserviceSearch']['hastasks'] != null && is_array($params['TeamserviceSearch']['hastasks'])) {
			if(!empty($params['TeamserviceSearch']['hastasks'])) {
				foreach($params['TeamserviceSearch']['hastasks'] as $k=>$v) {
					if($v=='All') { // || strpos($v,",") !== false
						unset($params['TeamserviceSearch']['hastasks']);break;
					}
				}
			}
			$query->andFilterWhere(['hastasks' => $params['TeamserviceSearch']['hastasks']]);
		}

	    $this->load($params);
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'teamid' => $this->id,
        ]);

		if(isset($this->hastasks) && $this->hastasks==0 && $this->hastasks!=""){
			$has_tasks = 0;
			$query->andWhere("hastasks='".$has_tasks."'");
		}
		if(isset($this->hastasks) && $this->hastasks==1 && $this->hastasks!=""){
			$has_tasks = 1;
			$query->andWhere("hastasks='".$has_tasks."'");
		}

		//$query->andFilterWhere(['like', 'service_name', $this->service_name]);
        $query->andFilterWhere(['like', 'service_description', $this->service_description]);

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
		$query = Teamservice::find()->where(['teamid'=>$params['teamId']]);

        if($params['field']=='service_name'){
    		if(isset($params['q']) && $params['q']!=""){$query->andFilterWhere(['like', 'service_name', $params['q']]);}
    		$dataProvider = array('All'=>'All') + ArrayHelper::map($query->all(),'service_name','service_name');
    	}

    	if($params['field']=='service_description' ){
    		if(isset($params['q']) && $params['q']!=""){$query->andFilterWhere(['like', 'service_description', $params['q']]);}
    		$dataProvider = ArrayHelper::map($query->all(),'service_description','service_description');
    	}

        return $dataProvider;
    }
}
