<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\PriorityTeamLoc;
use yii\helpers\ArrayHelper;

/**
 * PriorityTeamSearch represents the model behind the search form about `app\models\PriorityTeam`.
 */
class PriorityTeamLocSearch extends PriorityTeamLoc
{
	public $priority_team_location;
	public $team_location;
	
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'priority_team_id', 'team_id', 'team_loc_id'], 'required'],
            [['id', 'priority_team_id', 'team_id', 'team_loc_id', 'priority_order', 'team_location'], 'integer'],
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
		$query = PriorityTeamLoc::find()
		->select(["CONCAT(tbl_priority_team_loc.team_id,' - ',tbl_priority_team_loc.team_loc_id) team_location",'tbl_priority_team_loc.team_loc_id','tbl_priority_team_loc.team_id'])
		->groupBy(['tbl_priority_team_loc.team_loc_id','tbl_priority_team_loc.team_id']);
	    
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        	'pagination' => ['pageSize' => 25]
        ]);
        
        if ($params['PriorityTeamLocSearch']['team_location'] != null && is_array($params['PriorityTeamLocSearch']['team_location'])) {
			$query->select(["CONCAT(tbl_priority_team_loc.team_id,' - ',tbl_priority_team_loc.team_loc_id) team_location",'tbl_priority_team_loc.team_id','tbl_priority_team_loc.team_loc_id',"CONCAT(tbl_team.team_name,' - ',tbl_teamlocation_master.team_location_name) as priority_team_location"]);
			$query->join('INNER JOIN','tbl_team','tbl_team.id = tbl_priority_team_loc.team_id');
    		$query->join('INNER JOIN','tbl_teamlocation_master','tbl_teamlocation_master.id = tbl_priority_team_loc.team_loc_id');
			if(!empty($params['PriorityTeamLocSearch']['team_location'])){
				foreach($params['PriorityTeamLocSearch']['team_location'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['PriorityTeamLocSearch']['team_location']); break;
					}
				}
			}
			$query->andFilterWhere(['or like', "CONCAT(tbl_team.team_name,' - ',tbl_teamlocation_master.team_location_name)", $params['PriorityTeamLocSearch']['team_location']]);
			$query->groupBy(["CONCAT(tbl_priority_team_loc.team_id,' - ',tbl_priority_team_loc.team_loc_id)", "CONCAT(tbl_team.team_name,' - ',tbl_teamlocation_master.team_location_name)",'tbl_priority_team_loc.team_id','tbl_priority_team_loc.team_loc_id']);
		}
		
		$this->load($params);
		// if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
			// return $dataProvider;
       // }

		$dataProvider->sort->attributes['team_location'] = [
            'asc' => ['team_location' => SORT_ASC],
            'desc' => ['team_location' => SORT_DESC],
        ];
        
        $query->andFilterWhere([
       //   'id' => $this->id,
			'priority_order' => $this->priority_order,
        ]);

		return $dataProvider;
    }
    
    public function searchFilter($params)
    {
		$dataProvider = array();
		$query = PriorityTeamLoc::find()
		->select(["CONCAT(tbl_priority_team_loc.team_id,' - ',tbl_priority_team_loc.team_loc_id) team_loc_id", 'tbl_priority_team_loc.team_loc_id','tbl_priority_team_loc.team_id'])
		->groupBy(['tbl_priority_team_loc.team_id','tbl_priority_team_loc.team_loc_id']);
		
		if($params['field'] == 'team_loc_id')
		{
			$query->select(["CONCAT(tbl_team.team_name,' - ',tbl_teamlocation_master.team_location_name) as priority_team_location"]);
			$query->join('INNER JOIN','tbl_team','tbl_team.id = tbl_priority_team_loc.team_id');
    		$query->join('INNER JOIN','tbl_teamlocation_master','tbl_teamlocation_master.id = tbl_priority_team_loc.team_loc_id');
    		if(isset($params['q']) && $params['q']!="") { 
				$query->andFilterWhere(['or like', "CONCAT(tbl_team.team_name,' - ',tbl_teamlocation_master.team_location_name)", $params['q']]);
			}
			$query->groupBy(['tbl_priority_team_loc.team_id','tbl_priority_team_loc.team_loc_id',"CONCAT(tbl_team.team_name,' - ',tbl_teamlocation_master.team_location_name)"]);
			$dataProvider = ArrayHelper::map($query->all(), 'priority_team_location', 'priority_team_location');
    	}
    	return array_merge(array('0'=>'All'), $dataProvider);
    }
}
