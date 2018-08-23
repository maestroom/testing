<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Industry;

/**
 * IndustrySearch represents the model behind the search form about `app\models\Industry`.
 */
class IndustrySearch extends Industry
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'remove'], 'integer'],
            [['industry_name'], 'safe'],
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
        $query = Industry::find()->where(['remove'=>0])->orderBy(['industry_name'=>SORT_ASC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
			'pagination'=>['pageSize'=>25]
        ]);

		if ($params['IndustrySearch']['industry_name'] != null && is_array($params['IndustrySearch']['industry_name'])) {
			if(!empty($params['IndustrySearch']['industry_name'])){
				foreach($params['IndustrySearch']['industry_name'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['IndustrySearch']['industry_name']);break;
					}
				}
			}
			$query->andFilterWhere(['or like', 'industry_name', $params['IndustrySearch']['industry_name']]);
		}else{
            if ($params['IndustrySearch']['industry_name'] != null)
                $query->andFilterWhere(['like', 'industry_name', $params['IndustrySearch']['industry_name']]);
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

        //$query->andFilterWhere(['or like', 'industry_name', $this->industry_name]);

        return $dataProvider;
    }
}
