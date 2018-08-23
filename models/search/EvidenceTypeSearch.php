<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\EvidenceType;
use yii\helpers\ArrayHelper;
/**
 * EvidenceTypeSearch represents the model behind the search form about `app\models\EvidenceType`.
 */
class EvidenceTypeSearch extends EvidenceType
{
	public $unit;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'est_size', 'media_unit_id', 'remove'], 'integer'],
            [['evidence_name'], 'safe'],
        	[['unit'], 'safe'],
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
        $query = EvidenceType::find()->where(['tbl_evidence_type.remove'=>0]);
        $query->joinWith(['unit']);
        if(!isset($params['sort'])){
        	$query->orderBy(['evidence_name'=>SORT_ASC]);
        }
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        	'pagination'=>['pageSize'=>25]
        ]);
        
        if ($params['EvidenceTypeSearch']['evidence_name'] != null && is_array($params['EvidenceTypeSearch']['evidence_name'])) {
			if(!empty($params['EvidenceTypeSearch']['evidence_name'])){
				foreach($params['EvidenceTypeSearch']['evidence_name'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['EvidenceTypeSearch']['evidence_name']);break;
					}
				}
			}
			$query->andFilterWhere(['or like', 'evidence_name', $params['EvidenceTypeSearch']['evidence_name']]);
		}else{
			if ($params['EvidenceTypeSearch']['evidence_name'] != null)
				$query->andFilterWhere(['like', 'evidence_name', $params['EvidenceTypeSearch']['evidence_name']]);
		}
		
		if ($params['EvidenceTypeSearch']['unit'] != null && is_array($params['EvidenceTypeSearch']['unit'])) {
			if(!empty($params['EvidenceTypeSearch']['unit'])){
				foreach($params['EvidenceTypeSearch']['unit'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['EvidenceTypeSearch']['unit']);break;
					}
				}
			}
			$query->andFilterWhere(['or like', 'tbl_unit.unit_name', $params['EvidenceTypeSearch']['unit']]);
		}
        
        if ($params['EvidenceTypeSearch']['est_size'] != null && is_array($params['EvidenceTypeSearch']['est_size'])) {
			if(!empty($params['EvidenceTypeSearch']['est_size'])){
				foreach($params['EvidenceTypeSearch']['est_size'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['EvidenceTypeSearch']['est_size']);break;
					}
				}
			}
			$query->andFilterWhere(['or like', 'tbl_evidence_type.est_size', $params['EvidenceTypeSearch']['est_size']]);
		}
        
        // Important: here is how we set up the sorting
        // The key is the attribute name on our "Search" instance
        $dataProvider->sort->attributes['unit'] = [
        		// The tables are the ones our relation are configured to
        		// in my case they are prefixed with "tbl_"
        		'asc' => ['tbl_unit.unit_name' => SORT_ASC],
        		'desc' => ['tbl_unit.unit_name' => SORT_DESC],
        ];
        $this->load($params);

        /*if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }*/

        $query->andFilterWhere([
            'id' => $this->id,
            //'tbl_evidence_type.est_size' => $this->est_size,
            'media_unit_id' => $this->media_unit_id,
            'tbl_evidence_type.remove' => $this->remove,
        ]);
        //$query->andFilterWhere(['like', 'tbl_unit.unit_name', $this->unit]);

        //$query->andFilterWhere(['like', 'evidence_name', $this->evidence_name]);

        return $dataProvider;
    }
    
    public function searchFilter($params)
    {
		$dataProvider = array();
		$query = EvidenceType::find()->where(['tbl_evidence_type.remove'=>0])->orderBy(['tbl_evidence_type.evidence_name'=>SORT_ASC])->limit(100);
		if($params['field']=='evidence_name'){
    		$query->select(['evidence_name']);
    		if(isset($params['q']) && $params['q']!=""){$query->andFilterWhere(['or like','evidence_name',$params['q']]);}
    		$query->groupBy('evidence_name');
    		$query->orderBy('evidence_name');
    		$dataProvider = ArrayHelper::map($query->all(),'evidence_name','evidence_name');
    	}
    	
    	if($params['field']=='est_size'){
    		$query->select(['est_size']);
    		if(isset($params['q']) && $params['q']!=""){$query->andFilterWhere(['or like','est_size',$params['q']]);}
    		$query->groupBy('est_size');
    		$query->orderBy('est_size');
    		$dataProvider = ArrayHelper::map($query->all(),'est_size','est_size');
    	}
    	
    	if($params['field']=='unit_name'){
			$query->select(['media_unit_id','tbl_unit.unit_name']);
    		$query->joinWith(['unit']);
    		$query->where('tbl_unit.unit_name IS NOT NULL');
    		if(isset($params['q']) && $params['q']!=""){$query->andFilterWhere(['or like','tbl_unit.unit_name',$params['q']]);}
    		$query->groupBy('tbl_unit.unit_name,media_unit_id');
    		$query->orderBy('tbl_unit.unit_name,media_unit_id');
    		$dataProvider = ArrayHelper::map($query->all(),function($model){return $model->unit->unit_name;},function($model){return $model->unit->unit_name;});
    	}
    	//echo "<pre>",print_r($dataProvider),"</pre>";
    	return array_merge(array('0'=>'All'), $dataProvider);
    }
}
