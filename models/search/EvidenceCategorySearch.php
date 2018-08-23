<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\EvidenceCategory;

/**
 * EvidenceCategorySearch represents the model behind the search form about `app\models\EvidenceCategory`.
 */
class EvidenceCategorySearch extends EvidenceCategory
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'remove'], 'integer'],
            [['category'], 'safe'],
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
        $query = EvidenceCategory::find()->where(['remove'=>0])->orderBy(['category'=>SORT_ASC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        	'pagination'=>['pageSize'=>25]
        ]);
        
        if ($params['EvidenceCategorySearch']['category'] != null && is_array($params['EvidenceCategorySearch']['category'])) {
			if(!empty($params['EvidenceCategorySearch']['category'])){
				foreach($params['EvidenceCategorySearch']['category'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['EvidenceCategorySearch']['category']);break;
					}
				}
			}
			$query->andFilterWhere(['or like', 'category', $params['EvidenceCategorySearch']['category']]);
		}else{
            if ($params['EvidenceCategorySearch']['category'] != null)
                $query->andFilterWhere(['like', 'category', $params['EvidenceCategorySearch']['category']]);
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

        //$query->andFilterWhere(['or like', 'category', $this->category]);

        return $dataProvider;
    }
}
