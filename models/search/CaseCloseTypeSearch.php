<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\CaseCloseType;

/**
 * CaseCloseTypeSearch represents the model behind the search form about `app\models\CaseCloseType`.
 */
class CaseCloseTypeSearch extends CaseCloseType
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'remove'], 'integer'],
            [['close_type'], 'safe'],
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
        $query = CaseCloseType::find()->where(['remove'=>0])->orderBy(['close_type'=>SORT_ASC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        	'pagination'=>['pageSize'=>25]
        ]);

		if ($params['CaseCloseTypeSearch']['close_type'] != null && is_array($params['CaseCloseTypeSearch']['close_type'])) {
			if(!empty($params['CaseCloseTypeSearch']['close_type'])){
				foreach($params['CaseCloseTypeSearch']['close_type'] as $k=>$v){
					if($v=='All'){ /* || strpos($v,",") !== false */
						unset($params['CaseCloseTypeSearch']['close_type']);break;
					}
				}
			}
			$query->andFilterWhere(['or like', 'close_type', $params['CaseCloseTypeSearch']['close_type']]);
		}else{
            if ($params['CaseCloseTypeSearch']['close_type'] != null)
                $query->andFilterWhere(['like', 'close_type', $params['CaseCloseTypeSearch']['close_type']]);
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

        //$query->andFilterWhere(['or like', 'close_type', $this->close_type]);

        return $dataProvider;
    }
}
