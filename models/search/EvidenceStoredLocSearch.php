<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\EvidenceStoredLoc;
use yii\helpers\ArrayHelper;
/**
 * EvidenceStoredLocSearch represents the model behind the search form about `app\models\EvidenceStoredLoc`.
 */
class EvidenceStoredLocSearch extends EvidenceStoredLoc
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'remove'], 'integer'],
            [['stored_loc'], 'safe'],
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
        $query = EvidenceStoredLoc::find()->where(['remove'=>0])->orderBy(['stored_loc'=>SORT_ASC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        	'pagination'=>['pageSize'=>25]
        ]);

		if ($params['EvidenceStoredLocSearch']['stored_loc'] != null && is_array($params['EvidenceStoredLocSearch']['stored_loc'])) {
			if(!empty($params['EvidenceStoredLocSearch']['stored_loc'])){
				foreach($params['EvidenceStoredLocSearch']['stored_loc'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['EvidenceStoredLocSearch']['stored_loc']);break;
					}
				}
			}
			$query->andFilterWhere(['or like', 'stored_loc', $params['EvidenceStoredLocSearch']['stored_loc']]);
		}else{
            if ($params['EvidenceStoredLocSearch']['stored_loc'] != null)
                $query->andFilterWhere(['like', 'stored_loc', $params['EvidenceStoredLocSearch']['stored_loc']]);
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

        //$query->andFilterWhere(['like', 'stored_loc', $this->stored_loc]);

        return $dataProvider;
    }
    
    public function searchFilter($params)
    {
		$dataProvider = array();
		$query = EvidenceStoredLoc::find()->where(['tbl_evidence_stored_loc.remove'=>0])->orderBy(['tbl_evidence_stored_loc.stored_loc'=>SORT_ASC])->limit(100);
		if($params['field']=='stored_loc'){
    		$query->select(['stored_loc']);
    		if(isset($params['q']) && $params['q']!=""){$query->andFilterWhere(['or like','stored_loc',$params['q']]);}
    		$query->groupBy('stored_loc');
    		$query->orderBy('stored_loc');
    		$dataProvider = ArrayHelper::map($query->all(),'stored_loc','stored_loc');
    	}
    	
    	//echo "<pre>",print_r($dataProvider),"</pre>";
    	return array_merge(array('0'=>'All'), $dataProvider);
    }
}
