<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\CaseType;
use yii\data\Pagination;

/**
 * CaseTypeSearch represents the model behind the search form about `app\models\CaseType`.
 */
class CaseTypeSearch extends CaseType
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'remove'], 'integer'],
            [['case_type_name'], 'safe'],
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
    	$query = CaseType::find()->where(['remove'=>0])->orderBy(['case_type_name'=>SORT_ASC]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        	'pagination' =>['pageSize'=>25],
        ]);
        
        if ($params['CaseTypeSearch']['case_type_name'] != null && is_array($params['CaseTypeSearch']['case_type_name'])) {
			if(!empty($params['CaseTypeSearch']['case_type_name'])){
				foreach($params['CaseTypeSearch']['case_type_name'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['CaseTypeSearch']['case_type_name']);break;
					}
				}
			}
			$query->andFilterWhere(['or like', 'case_type_name', $params['CaseTypeSearch']['case_type_name']]);
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
        //$query->andFilterWhere(['or like', 'case_type_name', $this->case_type_name]);
        return $dataProvider; 
    }
}
