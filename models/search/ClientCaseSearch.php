<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ClientContacts;
use app\models\CaseContacts;

/**
 * ClientCaseSearch represents the model behind the search form about `app\models\ClientCase`.
 */
class ClientCaseSearch extends ClientCase
{
	/**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['client_id', 'case_name'], 'required'],
            [['client_id', 'case_type_id', 'case_close_id', 'is_close', 'sales_user_id', 'budget_value', 'budget_alert', 'created_by', 'modified_by'], 'integer'],
            [['created', 'modified'], 'safe'],
            [['case_name', 'counsel_name'], 'string'],
            [['description', 'close_reason'], 'string'],
            [['case_matter_no', 'internal_ref_no'], 'string'],
            [['case_manager'], 'string']
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
        $query = ClientCase::find()->select(['id','client_name','description'])->orderBy(['id'=>SORT_ASC]);
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        	'pagination'=>['pageSize'=>25]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        
        $query->andFilterWhere(['id' => $this->id]);
        $query->andFilterWhere(['client_id' => $this->client_id]);
        $query->andFilterWhere(['like', 'case_name', $this->case_name]);
        $query->andFilterWhere(['like', 'description', $this->description]);
        $query->andFilterWhere(['case_type_id', $this->case_type_id]);
        $query->andFilterWhere(['case_close_id', $this->case_close_id]);
        $query->andFilterWhere(['like', 'case_matter_no', $this->case_matter_no]);
        $query->andFilterWhere(['like', 'internal_ref_no', $this->internal_ref_no]);
        $query->andFilterWhere(['like', 'counsel_name', $this->counsel_name]);
        $query->andFilterWhere(['is_close', $this->is_close]);
        $query->andFilterWhere(['like', 'close_reason', $this->close_reason]);
        $query->andFilterWhere(['sales_user_id', $this->sales_user_id]);
        $query->andFilterWhere(['like', 'case_manager', $this->case_manager]);

        return $dataProvider;
    }
}
