<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Tasks;

/**
 * ProjectSearch represents the model behind the search form about `app\models\Tasks`.
 */
class ProjectSearch extends Tasks
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'client_id', 'client_case_id', 'sales_user_id', 'task_status', 'task_closed', 'task_cancel', 'team_priority', 'created_by', 'modified_by'], 'integer'],
            [['task_complete_date', 'task_cancel_reason', 'created', 'modified'], 'safe'],
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
        $query = Tasks::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->sort->enableMultiSort = true;
        /*IRT-67*/
		if(isset($params['grid_id']) && $params['grid_id']!=""){
			$grid_id=$params['grid_id'].'_'.Yii::$app->user->identity->id;
			$sql="SELECT * FROM tbl_dynagrid_dtl WHERE id IN (SELECT sort_id FROM tbl_dynagrid WHERE id='$grid_id')";
			$sort_data=Yii::$app->db->createCommand($sql)->queryOne();
			if(!empty($sort_data)){
					$dataProvider->sort->defaultOrder=json_decode($sort_data['data'],true);
			}
		}
		/*IRT-67*/
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'client_id' => $this->client_id,
            'client_case_id' => $this->client_case_id,
            'sales_user_id' => $this->sales_user_id,
            'task_status' => $this->task_status,
            'task_complete_date' => $this->task_complete_date,
            'task_closed' => $this->task_closed,
            'task_cancel' => $this->task_cancel,
            'team_priority' => $this->team_priority,
            'created' => $this->created,
            'created_by' => $this->created_by,
            'modified' => $this->modified,
            'modified_by' => $this->modified_by,
        ]);

        $query->andFilterWhere(['like', 'task_cancel_reason', $this->task_cancel_reason]);

        return $dataProvider;
    }
}
