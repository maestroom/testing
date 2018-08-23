<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\EvidenceEncryptType;
use yii\helpers\ArrayHelper;

/**
 * EvidenceEncryptTypeSearch represents the model behind the search form about `app\models\EvidenceEncryptType`.
 */
class EvidenceEncryptTypeSearch extends EvidenceEncryptType
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'remove'], 'integer'],
            [['encrypt'], 'safe'],
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
        $query = EvidenceEncryptType::find()->where(['remove'=>0]);
        
        if(!isset($params['sort'])){
        	$query->orderBy(['encrypt'=>SORT_ASC]);
        }

		if ($params['EvidenceEncryptTypeSearch']['encrypt'] != null && is_array($params['EvidenceEncryptTypeSearch']['encrypt'])) {
			if(!empty($params['EvidenceEncryptTypeSearch']['encrypt'])){
				foreach($params['EvidenceEncryptTypeSearch']['encrypt'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['EvidenceEncryptTypeSearch']['encrypt']);break;
					}
				}
			}
			$query->andFilterWhere(['or like', 'encrypt', $params['EvidenceEncryptTypeSearch']['encrypt']]);
		}else{
            if ($params['EvidenceEncryptTypeSearch']['encrypt'] != null)
                $query->andFilterWhere(['like', 'encrypt', $params['EvidenceEncryptTypeSearch']['encrypt']]);
        }

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

        $query->andFilterWhere([
            'id' => $this->id,
            'remove' => $this->remove,
        ]);
		
		return $dataProvider;
    }
    
    public function searchFilter($params)
    {
		$dataProvider = array();
		$query = EvidenceEncryptType::find()->where(['remove'=>0])->orderBy(['tbl_evidence_encrypt_type.encrypt'=>SORT_ASC])->limit(100);
		if($params['field']=='encrypt'){
    		$query->select(['encrypt']);
    		if(isset($params['q']) && $params['q']!=""){$query->andFilterWhere(['or like','encrypt',$params['q']]);}
    		$query->groupBy('encrypt');
    		$query->orderBy('encrypt');
    		$dataProvider = ArrayHelper::map($query->all(),'encrypt','encrypt');
    	}
    	return array_merge(array(''=>'All'), $dataProvider);
    }
}
