<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\EvidenceTo;
use yii\helpers\ArrayHelper;
/**
 * EvidenceToSearch represents the model behind the search form about `app\models\EvidenceTo`.
 */
class EvidenceToSearch extends EvidenceTo
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'remove'], 'integer'],
            [['to_name'], 'safe'],
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
        $query = EvidenceTo::find()->where(['remove'=>0])->orderBy(['to_name'=>SORT_ASC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        	'pagination'=>['pageSize'=>25]
        ]);

		if ($params['EvidenceToSearch']['to_name'] != null && is_array($params['EvidenceToSearch']['to_name'])) {
			if(!empty($params['EvidenceToSearch']['to_name'])){
				foreach($params['EvidenceToSearch']['to_name'] as $k=>$v){
					if($v=='All'){ // || strpos($v,",") !== false
						unset($params['EvidenceToSearch']['to_name']);break;
					}
				}
			}
			$query->andFilterWhere(['or like', 'to_name', $params['EvidenceToSearch']['to_name']]);
		}else{
            if ($params['EvidenceToSearch']['to_name'] != null)
                $query->andFilterWhere(['like', 'to_name', $params['EvidenceToSearch']['to_name']]);
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

        //$query->andFilterWhere(['like', 'to_name', $this->to_name]);

        return $dataProvider;
    }
    
    public function searchFilter($params)
    {
		$dataProvider = array();
		$query = EvidenceTo::find()->where(['remove'=>0])->orderBy(['tbl_evidence_to.to_name'=>SORT_ASC])->limit(100);
		if($params['field']=='to_name'){
    		$query->select(['to_name']);
    		if(isset($params['q']) && $params['q']!=""){$query->andFilterWhere(['or like','to_name',$params['q']]);}
    		$query->groupBy('to_name');
    		$query->orderBy('to_name');
    		$dataProvider = ArrayHelper::map($query->all(),'to_name','to_name');
    	}
    	return array_merge(array(''=>'All'), $dataProvider);
    }
}
