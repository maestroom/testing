<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Pricing;

/**
 * SearchPricing represents the model behind the search form about `app\models\Pricing`.
 */
class SearchPricing extends Pricing
{
	public $pricing_rate='';
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'team_id', 'pricing_type', 'utbms_code_id', 'unit_price_id', 'is_custom', 'created_by', 'modified_by', 'accum_cost', 'remove'], 'integer'],
            [['price_point', 'pricing_range', 'description', 'cust_desc_template', 'created', 'modified','pricing_rate'], 'safe'],
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
    	$query = Pricing::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);
        
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'team_id' => $this->team_id,
            'pricing_type' => $this->pricing_type,
            'utbms_code_id' => $this->utbms_code_id,
            'unit_price_id' => $this->unit_price_id,
            'is_custom' => $this->is_custom,
            'created' => $this->created,
            'created_by' => $this->created_by,
            'modified' => $this->modified,
            'modified_by' => $this->modified_by,
            'accum_cost' => $this->accum_cost,
            'remove' => $this->remove,
        ]);

        $query->andFilterWhere(['like', 'price_point', $this->price_point])
            ->andFilterWhere(['like', 'pricing_range', $this->pricing_range])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'cust_desc_template', $this->cust_desc_template]);

        return $dataProvider;
    }
}
