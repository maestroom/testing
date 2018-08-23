<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\DataType;

/**
 * DataTypeSearch represents the model behind the search form about `app\models\DataType`.
 */
class DataTypeSearch extends DataType
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'remove'], 'integer'],
            [['data_type'], 'safe'],
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
        $query = DataType::find()->where(['remove'=>0])->orderBy(['data_type'=>SORT_ASC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        	'pagination'=>['pageSize'=>25]
        ]);

		if ($params['DataTypeSearch']['data_type'] != null && is_array($params['DataTypeSearch']['data_type'])) {
			if(!empty($params['DataTypeSearch']['data_type'])){
				foreach($params['DataTypeSearch']['data_type'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['DataTypeSearch']['data_type']);break;
					}
				}
			}
			$query->andFilterWhere(['or like', 'data_type', $params['DataTypeSearch']['data_type']]);
		}else{
            if ($params['DataTypeSearch']['data_type'] != null)
                $query->andFilterWhere(['like', 'data_type', $params['DataTypeSearch']['data_type']]);
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

        //$query->andFilterWhere(['or like', 'data_type', $this->data_type]);

        return $dataProvider;
    }
}
