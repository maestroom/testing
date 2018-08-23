<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Unit;
use yii\helpers\ArrayHelper;

/**
 * UnitSearch represents the model behind the search form about `app\models\Unit`.
 */
class UnitSearch extends Unit
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['unit_name', 'default_unit', 'sort_order','is_hidden'], 'safe'],
            //[['est_size'], 'number'],
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
        $query = Unit::find()->where(['remove'=>0]);
        if(isset($params['sort']) && $params['sort']!=""){}else{
			$query->orderBy(['sort_order'=>SORT_ASC]);
        }
        //echo "<pre>",print_r($params['UnitSearch']),"</pre>";die;
        if ($params['UnitSearch']['unit_name'] != null && is_array($params['UnitSearch']['unit_name'])) {
			if(!empty($params['UnitSearch']['unit_name'])) {
				foreach($params['UnitSearch']['unit_name'] as $k=>$v) {
					if($v=='All') { // || strpos($v,",") !== false
						unset($params['UnitSearch']['unit_name']); break;
					}
				}
			}
			$query->andFilterWhere(['or like', 'unit_name', $params['UnitSearch']['unit_name']]);
		}else{
            if ($params['UnitSearch']['unit_name'] != null)
                $query->andFilterWhere(['like', 'unit_name', $params['UnitSearch']['unit_name']]);
        }
        if ($params['UnitSearch']['default_unit'] != null && is_array($params['UnitSearch']['default_unit'])) {
            if(!empty($params['UnitSearch']['default_unit'])) {
				foreach($params['UnitSearch']['default_unit'] as $k=>$v) {
					if($v=='All') { // || strpos($v,",") !== false
						unset($params['UnitSearch']['default_unit']); break;
					}
				}
			}
			$query->andFilterWhere(['or like', 'default_unit', $params['UnitSearch']['default_unit']]);
        }else{
            if ($params['UnitSearch']['default_unit'] != null)
                $query->andFilterWhere(['like', 'default_unit', $params['UnitSearch']['default_unit']]);
        }

		/*if ($params['UnitSearch']['est_size'] != null && is_array($params['UnitSearch']['est_size'])) {
			if(!empty($params['UnitSearch']['est_size'])){
				foreach($params['UnitSearch']['est_size'] as $k=>$v){
					if($v=='All' || strpos($v,",") !== false){
						unset($params['UnitSearch']['est_size']);break;
					}
				}
			}
			$query->andFilterWhere(['est_size' => $params['UnitSearch']['est_size']]);
		}*/

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        	'pagination'=>['pageSize'=>25]
        ]);

        $this->load($params);

        /*if (!$this->validate()) {
			echo "<pre>",print_r($this->getErrors()),"</pre>";die;
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }*/

        $query->andFilterWhere([
            //'id' => $this->id,
            'remove' => $this->remove,
        ]);
		//$query->andFilterWhere(['or like', 'unit_name', $this->unit_name]);
		//$query->andFilterWhere(['est_size' => $this->est_size]);

        return $dataProvider;
    }
    
    public function searchFilter($params)
    {
		$dataProvider = array();
		$query = Unit::find()->where(['remove'=>0])->orderBy(['tbl_unit.sort_order'=>SORT_ASC])->limit(100);
	
    	if($params['field']=='unit_name'){
    		$query->select(['unit_name']);
    		if(isset($params['q']) && $params['q']!=""){$query->andFilterWhere(['or like','unit_name',$params['q']]);}
    		$query->groupBy('unit_name');
    		$query->orderBy('unit_name');
    		$dataProvider = ArrayHelper::map($query->all(),'unit_name','unit_name');
    	}
    	/*if($params['field']=='est_size'){
    		$query->select(['est_size']);
    		if(isset($params['q']) && $params['q']!=""){$query->andFilterWhere(['or like','est_size',$params['q']]);}
    		$query->groupBy('est_size');
    		$query->orderBy('est_size');
    		$dataProvider = ArrayHelper::map($query->all(),'est_size','est_size');
    	}*/
    	return array_merge(array(''=>'All'), $dataProvider);
    }
}
