<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\TeamlocationMaster;
use yii\helpers\ArrayHelper;
/**
 * TeamlocationMasterSearch represents the model behind the search form about `app\models\TeamlocationMaster`.
 */
class TeamlocationMasterSearch extends TeamlocationMaster
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'remove'], 'integer'],
            [['team_location_name'], 'safe'],
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
        $query = TeamlocationMaster::find()->where(['remove'=>0])->andWhere('id NOT IN (0)')->orderBy(['team_location_name'=>SORT_ASC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        	'pagination'=>['pageSize'=>25]
        ]);

		if ($params['TeamlocationMasterSearch']['team_location_name'] != null && is_array($params['TeamlocationMasterSearch']['team_location_name'])) {
			if(!empty($params['TeamlocationMasterSearch']['team_location_name'])){
				foreach($params['TeamlocationMasterSearch']['team_location_name'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['TeamlocationMasterSearch']['team_location_name']);break;
					}
				}
			}
			$query->andFilterWhere(['or like', 'team_location_name', $params['TeamlocationMasterSearch']['team_location_name']]);
		}else{
            if ($params['TeamlocationMasterSearch']['team_location_name'] != null)
                $query->andFilterWhere(['like', 'team_location_name', $params['TeamlocationMasterSearch']['team_location_name']]);

        }

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'remove' => $this->remove,
        ]);

        //$query->andFilterWhere(['like', 'team_location_name', $this->team_location_name]);

        return $dataProvider;
    }
    
    public function searchFilter($params)
    {
		$dataProvider = array();
		$query = TeamlocationMaster::find()->where(['remove'=>0])->orderBy(['team_location_name'=>SORT_ASC])->limit(100);
		if($params['field']=='team_location_name'){
    		$query->select(['team_location_name']);
    		if(isset($params['q']) && $params['q']!=""){$query->andFilterWhere(['or like','team_location_name',$params['q']]);}
    		$query->groupBy('team_location_name');
    		$query->orderBy('team_location_name');
    		$dataProvider = ArrayHelper::map($query->all(),'team_location_name','team_location_name');
    	}
    	
    	//echo "<pre>",print_r($dataProvider),"</pre>";
    	return array_merge(array('0'=>'All'), $dataProvider);
    }
}
