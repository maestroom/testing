<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Pricing;

use yii\helpers\ArrayHelper;

/**
 * PricingSearch represents the model behind the search form about `app\models\Pricing`.
 */
class PricingSearch extends Pricing
{
	public $pricing_rate='';
	public $service_task = '';
	public $display_teams = '';
	public $display_teams_type = '';
	public $utbmscode = '';
	public $unit_name = '';
	public $pricing_rates = '';
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'team_id', 'pricing_type', 'utbms_code_id', 'unit_price_id', 'is_custom', 'created_by', 'modified_by', 'accum_cost', 'remove', 'display_teams_type'], 'integer'],
            [['price_point', 'pricing_range', 'description', 'cust_desc_template', 'created', 'modified','pricing_rate','utbmscode','unit_name','pricing_rates'], 'safe'],
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
	//	echo "<pre>",print_r($params),"</pre>"; die;
		$query = Pricing::find();
		$query->distinct();

     //   echo "<pre>",print_r($params),"</pre>";die;
		//if($query->count())
		// $query->distinct();
    	$params['PricingSearch']['price_point'] = (isset($params['PricingSearch']['price_point']) && $params['PricingSearch']['price_point']!="All")?$params['PricingSearch']['price_point']:'';
        $params['PricingSearch']['accum_cost'] = (isset($params['PricingSearch']['accum_cost']) && $params['PricingSearch']['accum_cost']!="All")?$params['PricingSearch']['accum_cost']:'';
        $params['PricingSearch']['description'] = (isset($params['PricingSearch']['description']) && $params['PricingSearch']['description']!="All")?html_entity_decode(htmlentities($params['PricingSearch']['description'])):'';
        $params['PricingSearch']['utbms_code_id'] = (isset($params['PricingSearch']['utbms_code_id']) && $params['PricingSearch']['utbms_code_id']!="All")?$params['PricingSearch']['utbms_code_id']:'';

    	/*multiselect*/
        if ($params['PricingSearch']['price_point'] != null && is_array($params['PricingSearch']['price_point'])) {
			if(!empty($params['PricingSearch']['price_point'])){
				foreach($params['PricingSearch']['price_point'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['PricingSearch']['price_point']);
					}
				}
			}
		}
		if ($params['PricingSearch']['accum_cost'] != null && is_array($params['PricingSearch']['accum_cost'])) {
			if(!empty($params['PricingSearch']['accum_cost'])){
				foreach($params['PricingSearch']['accum_cost'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['PricingSearch']['accum_cost']);
					}
				}
			}
       }
		if ($params['PricingSearch']['utbms_code_id'] != null && is_array($params['PricingSearch']['utbms_code_id'])) {
			if(!empty($params['PricingSearch']['utbms_code_id'])){
				foreach($params['PricingSearch']['utbms_code_id'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['PricingSearch']['utbms_code_id']);
					}
				}
			}
		}
		/*multiselect*/
        // add conditions that should always apply here
    	if($params['PricingSearch']['utbms_code_id']!=''){
			/*$query->innerJoinWith([
                'pricingUtbmsCodes' => function(\yii\db\ActiveQuery $query) use($params){
                	$query->where(['or like','code',$params['PricingSearch']['utbms_code_id']]);
                }
			]);*/
			$query->andWhere(['or like','tbl_pricing_utbms_codes.code',$params['PricingSearch']['utbms_code_id']]);
			$query->andWhere("utbms_code_id!=''");
    	}

       /*$dataProvider->sort->attributes['pricing_rate'] = [
            'asc' => ['tbl_pricing_rates.rate_amount' => SORT_ASC],
            'desc' => ['tbl_pricing_rates.rate_amount' => SORT_DESC],
        ];*/

        $this->load($params);

        /*if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }*/

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'team_id' => $this->team_id,
            'pricing_type' => $this->pricing_type,
            'unit_price_id' => $this->unit_price_id,
        	'accum_cost'=>$this->accum_cost,
        	'tbl_pricing.remove' => $this->remove,
        ]);

        $query->andFilterWhere(['or like', 'price_point', $this->price_point]);
        if(isset($this->description) && $this->description!=""){
            $str = str_replace(PHP_EOL, '', $this->description);
            $str = str_replace("\r", '', $str);
            $query->andWhere(['or like', 'description', $str]);
    	}

    	$query->select(['tbl_pricing.id','price_point','description','accum_cost','utbms_code_id','pricing_type','unit_price_id','tbl_pricing_utbms_codes.code as utbmscode','tbl_unit.unit_name  as unit_name']);
		if(isset($params['PricingSearch']['pricing_rate']) && $params['PricingSearch']['pricing_rate']!=0 && $params['PricingSearch']['pricing_rate']!=""){
			$params['PricingSearch']['pricing_rate'] = str_replace( array('%','!','@','#','$','&','*','(',')','-','_','+','=','{','}','[',']',',','<','>','/','?',':',';','"','\''), '', $params['PricingSearch']['pricing_rate']);
			$remove_sql=" ";
			if(isset($this->remove)){
				$remove_sql =' AND tbl_pricing.remove ='.$this->remove;
			}
			if(isset($this->team_id))
				$query->where("tbl_pricing.id IN (SELECT pricing_id FROM tbl_pricing_rates WHERE tbl_pricing.id= tbl_pricing_rates.pricing_id AND tbl_pricing.team_id =".$this->team_id." AND tbl_pricing_rates.rate_amount = ".$params['PricingSearch']['pricing_rate'].$remove_sql.")");
			else if(isset($this->pricing_type))
				$query->where("tbl_pricing.id IN (SELECT pricing_id FROM tbl_pricing_rates WHERE tbl_pricing.id= tbl_pricing_rates.pricing_id AND tbl_pricing.pricing_type=".$this->pricing_type." AND tbl_pricing_rates.rate_amount = ".$params['PricingSearch']['pricing_rate'].$remove_sql.")");
			else
				$query->where("tbl_pricing.id IN (SELECT pricing_id FROM tbl_pricing_rates WHERE tbl_pricing.id= tbl_pricing_rates.pricing_id AND tbl_pricing_rates.rate_amount = ".$params['PricingSearch']['pricing_rate'].$remove_sql.")");
		}
		$query->joinWith(['pricingUtbmsCodes','unit'],false);
    	/*$query->joinWith(['pricingRates' => function(\yii\db\ActiveQuery $query) use ($params){
            $params['PricingSearch']['pricing_rate'] = str_replace( array('%','!','@','#','$','&','*','(',')','-','_','+','=','{','}','[',']',',','<','>','/','?',':',';','"','\''), '', $params['PricingSearch']['pricing_rate']);
            $query->select(['tbl_pricing_rates.pricing_id','tbl_pricing_rates.rate_amount']);
            if(isset($params['PricingSearch']['pricing_rate']) && $params['PricingSearch']['pricing_rate']!=0 && $params['PricingSearch']['pricing_rate']!="")
            {
                $query->andWhere('tbl_pricing_rates.rate_amount='.$params['PricingSearch']['pricing_rate']);
            }
        },'pricingUtbmsCodes','unit'],false);*/


		$countquery = $query;
        $counttest = $countquery->count();
    	$dataProvider = new ActiveDataProvider([
            'query' => $query,
            'totalCount' => $counttest,
            'sort' => [
				 'enableMultiSort'=>true,
				 'defaultOrder' => [
						'tbl_pricing.id' => SORT_ASC,
					],
				'attributes' => [
					'tbl_pricing.id','price_point','description','accum_cost','utbms_code_id',
					/*'pricing_rate' => [
						'asc' => ['tbl_pricing_rates.rate_amount' => SORT_ASC],
						'desc' => ['tbl_pricing_rates.rate_amount' => SORT_DESC]
					] */
				],
			],
        ]);

        return $dataProvider;
    }

	public function searchFilter($params)
    {
    	$dataProvider = array();

        $query = Pricing::find()->select(['id','price_point','description','accum_cost','utbms_code_id'])->where(['remove' => $params['remove']]);

        if(isset($params['team_id']) && $params['team_id']!='' && $params['team_id'] != 0)
        	$query->andWhere(['team_id'=>$params['team_id']]);

        if(isset($params['pricing_type']) && $params['pricing_type']!='')
        	$query->andWhere(['pricing_type'=>$params['pricing_type']]);

    	if($params['field']=='price_point'){
    		if(isset($params['q']) && $params['q']!=""){
                $query->andFilterWhere(['like','price_point', $params['q']]);
    		}
    		$dataProvider = ArrayHelper::map($query->all(),'id','price_point');
    	}
    	if($params['field']=='description'){
    		$query->andWhere("description!=''");
    		if(isset($params['q']) && $params['q']!=""){
                $query->andFilterWhere(['like', 'description', $params['q']]);
            }
    		$dataProvider = ArrayHelper::map($query->all(),'description','description');
    	}

    	if($params['field']=='utbms_code_id'){
    		$query->with([
                'pricingUtbmsCodes' => function(\yii\db\ActiveQuery $query) use($params){
                	$query->select(['id','code']);
                	if(isset($params['q']) && $params['q']!='')
                		$query->where(['like', 'code', $params['q']]);
                }
			]);
			$query->andWhere("utbms_code_id!=''");
    		$dataProvider = ArrayHelper::map($query->all(),'utbms_code_id',function($model, $defaultValue) {
		        return $model['pricingUtbmsCodes']['code'];
		    });
    	}

    	return array_merge(array(''=>'All'),$dataProvider);
    }
}
