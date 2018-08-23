<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\UnitPrice;
use yii\helpers\ArrayHelper;

/**
 * UnitPriceSearch represents the model behind the search form about `app\models\UnitPrice`.
 */
class UnitPriceSearch extends UnitPrice
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'remove'], 'integer'],
            [['unit_price_name'], 'safe'],
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
        $query = UnitPrice::find()->where(['remove'=>0])->orderBy(['unit_price_name'=>SORT_ASC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        	'pagination'=>['pageSize'=>25]
        ]);
		
		if ($params['UnitPriceSearch']['unit_price_name'] != null && is_array($params['UnitPriceSearch']['unit_price_name'])) {
			if(!empty($params['UnitPriceSearch']['unit_price_name'])){
				foreach($params['UnitPriceSearch']['unit_price_name'] as $k=>$v){
					if($v=='All' || strpos($v,",") !== false){
						unset($params['UnitPriceSearch']['unit_price_name']);break;
					}
				}
			}
			$query->andFilterWhere(['or like', 'unit_price_name', $params['UnitPriceSearch']['unit_price_name']]);
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

        //$query->andFilterWhere(['like', 'unit_price_name', $this->unit_price_name]);

        return $dataProvider;
    }
    
    public function searchFilter($params)
    {
		$dataProvider = array();
		$query = UnitPrice::find()->where(['remove'=>0])->orderBy(['unit_price_name'=>SORT_ASC])->limit(100);
		if($params['field']=='unit_price_name'){
    		$query->select(['unit_price_name']);
    		if(isset($params['q']) && $params['q']!=""){$query->andFilterWhere(['or like','unit_price_name',$params['q']]);}
    		$query->groupBy('unit_price_name');
    		$query->orderBy('unit_price_name');
    		$dataProvider = ArrayHelper::map($query->all(),'unit_price_name','unit_price_name');
    	}
    	
    	//echo "<pre>",print_r($dataProvider),"</pre>";
    	return array_merge(array('0'=>'All'), $dataProvider);
    }
}
