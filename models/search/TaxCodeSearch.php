<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use app\models\TaxCode;

/**
 * TaxCodeSearch represents the model behind the search form about `app\models\TaxCode`.
 */
class TaxCodeSearch extends TaxCode
{
    public $class_name = '';
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'tax_class_id', 'tax_rate', 'client'], 'integer'],
            [['tax_code', 'tax_code_desc', 'created', 'modified', 'created_by', 'modified_by','class_name'], 'safe'],
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
        $query = TaxCode::find();
        $query->select(['tbl_tax_code.*','tbl_tax_class.class_name as class_name']);
        $query->joinWith(['taxClass'],false);
        // add conditions that should always apply here
        $params['TaxCodeSearch']['tax_code'] = (isset($params['TaxCodeSearch']['tax_code']) && $params['TaxCodeSearch']['tax_code']!="All")?$params['TaxCodeSearch']['tax_code']:'';
        $params['TaxCodeSearch']['tax_class_id'] = (isset($params['TaxCodeSearch']['tax_class_id']) && $params['TaxCodeSearch']['tax_class_id']!="All")?$params['TaxCodeSearch']['tax_class_id']:'';
        $params['TaxCodeSearch']['tax_rate'] = (isset($params['TaxCodeSearch']['tax_rate']) && $params['TaxCodeSearch']['tax_rate']!="All")?$params['TaxCodeSearch']['tax_rate']:'';
        $params['TaxCodeSearch']['tax_code_desc'] = (isset($params['TaxCodeSearch']['tax_code_desc']) && $params['TaxCodeSearch']['tax_code_desc']!="All")?$params['TaxCodeSearch']['tax_code_desc']:'';
        $params['TaxCodeSearch']['client'] = (isset($params['TaxCodeSearch']['client']) && $params['TaxCodeSearch']['client']!="All")?$params['TaxCodeSearch']['client']:'';
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->sort->enableMultiSort=true;
        /*IRT-67*/
        if(isset($params['grid_id']) && $params['grid_id']!=""){
            $grid_id=$params['grid_id'].'_'.Yii::$app->user->identity->id;
            $sql="SELECT * FROM tbl_dynagrid_dtl WHERE id IN (SELECT sort_id FROM tbl_dynagrid WHERE id='$grid_id')";
            $sort_data=Yii::$app->db->createCommand($sql)->queryOne();
            if(!empty($sort_data)){
                    $dataProvider->sort->defaultSort=json_decode($sort_data['data'],true);
            }
        }
        /*IRT-67*/
		/*multiselect*/
        if ($params['TaxCodeSearch']['tax_code'] != null && is_array($params['TaxCodeSearch']['tax_code'])) {
			if(!empty($params['TaxCodeSearch']['tax_code'])){
				foreach($params['TaxCodeSearch']['tax_code'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['TaxCodeSearch']['tax_code']);
					}
				}
			}
		}
		if ($params['TaxCodeSearch']['tax_class_id'] != null && is_array($params['TaxCodeSearch']['tax_class_id'])) {
			if(!empty($params['TaxCodeSearch']['tax_class_id'])){
				foreach($params['TaxCodeSearch']['tax_class_id'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['TaxCodeSearch']['tax_class_id']);
					}
				}
			}
		}
		if ($params['TaxCodeSearch']['tax_rate'] != null && is_array($params['TaxCodeSearch']['tax_rate'])) {
			if(!empty($params['TaxCodeSearch']['tax_rate'])){
				foreach($params['TaxCodeSearch']['tax_rate'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['TaxCodeSearch']['tax_rate']);
					}
				}
			}
		}
		if ($params['TaxCodeSearch']['client'] != null && is_array($params['TaxCodeSearch']['client'])) {
			if(!empty($params['TaxCodeSearch']['client'])){
				foreach($params['TaxCodeSearch']['client'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['TaxCodeSearch']['client']);
					}
				}
			}
		}
		/*multiselect*/
        $this->load($params);

//         if (!$this->validate()) {
//             // uncomment the following line if you do not want to return any records when validation fails
//             // $query->where('0=1');
//             return $dataProvider;
//         }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
         //   'tax_class_id' => $this->tax_class_id,
         //   'tax_rate' => $this->tax_rate,
            'created' => $this->created,
            //'client' => $this->client,
            'created_by' => $this->created_by,
            'modified' => $this->modified,
            'modified_by' => $this->modified_by,
        ]);

        if(isset($params['TaxCodeSearch']['tax_code']) && $params['TaxCodeSearch']['tax_code']!='' && $params['TaxCodeSearch']['tax_code']!="All"){
        	$query->andFilterWhere(['or like','tax_code' ,$params['TaxCodeSearch']['tax_code']]);
        }
        
        if(isset($params['TaxCodeSearch']['tax_class_id']) && $params['TaxCodeSearch']['tax_class_id']!='' && $params['TaxCodeSearch']['tax_class_id']!="All"){
        	$tax_class_id = $params['TaxCodeSearch']['tax_class_id'];
        	//$query->select(['tbl_tax_code.*','t.class_name']);
        	//$query->join('INNER JOIN','tbl_tax_class as t','t.id=tbl_tax_code.tax_class_id');
        	$query->andFilterWhere(['or like','tbl_tax_class.class_name',$tax_class_id]);
        }

        if(isset($params['TaxCodeSearch']['tax_rate']) && $params['TaxCodeSearch']['tax_rate']!='' && $params['TaxCodeSearch']['tax_rate']!="All"){
            $params['TaxCodeSearch']['tax_rate'] = str_replace( array('%','!','@','#','$','&','*','(',')','-','_','+','=','{','}','[',']',',','<','>','.','/','?',':',';','"','\''), '', $params['TaxCodeSearch']['tax_rate']); 
            $tax_rate_query="";
            if(!empty($params['TaxCodeSearch']['tax_rate'])){
                    foreach($params['TaxCodeSearch']['tax_rate'] as $k=>$v){
                                    $tax_res = rtrim($v,'%');
                                    if($tax_rate_query==""){
                                            $tax_rate_query="tax_rate LIKE '%".$tax_res."%'";
                                    }else{
                                            $tax_rate_query.=" OR tax_rate LIKE '%".$tax_res."%'";
                                    }
                    }
            }
            if($tax_rate_query!=""){
                    $query->andWhere("(".$tax_rate_query.")");
            }
            //$tax_res = rtrim($params['TaxCodeSearch']['tax_rate'],'%');
            //$query->andFilterWhere(['like',"tax_rate",$tax_res]);
        }
        
        if(isset($params['TaxCodeSearch']['tax_code_desc']) && $params['TaxCodeSearch']['tax_code_desc']!='blank' && $params['TaxCodeSearch']['tax_code_desc']!="All"){
        	$query->andFilterWhere(['like','tax_code_desc',$params['TaxCodeSearch']['tax_code_desc']]);
        }
        
        if($params['TaxCodeSearch']['tax_code_desc']=='blank'){
			$query->andWhere(['tax_code_desc' => '']); // Blank value condition
		}
        
        if(isset($params['TaxCodeSearch']['client']) && $params['TaxCodeSearch']['client']!='' && $params['TaxCodeSearch']['client']!="All"){
        	$query->andFilterWhere(['or like', 'client' , $params['TaxCodeSearch']['client']]);
        }
        
        //$query->andFilterWhere(['like', 'tax_code', $this->tax_code]);
       // $query->andFilterWhere(['like', 'tax_code_desc', $this->tax_code_desc]);

        return $dataProvider;
    }
    
    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchFilter($params)
    {
    	$query = TaxCode::find()->select(['tbl_tax_code.*','t.class_name'])->join('INNER JOIN','tbl_tax_class as t','t.id=tbl_tax_code.tax_class_id');
    
    	// add conditions that should always apply here
    	$dataProvider = new ActiveDataProvider([
    		'query' => $query,
    	]);
    
    	$this->load($params);
    	
    	if($params['field']=='tax_code'){
    		if(isset($params['q']) && $params['q']!="" && $params['q']!="All") {
    			$query->andFilterWhere(['like', 'tax_code', $params['q']]);
    		}
    		$dataProvider = ArrayHelper::map($query->all(),'tax_code','tax_code');
    	}
    	
    	if($params['field']=='class_name'){
    		if(isset($params['q']) && $params['q']!="" && $params['q']!="All") {
    			$query->andFilterWhere(['like','t.class_name',$params['q']]);
        	}
        	$dataProvider = ArrayHelper::map($query->all(),'class_name','class_name');
       }
    	
    	if($params['field']=='tax_rate'){
    		if(isset($params['q']) && $params['q']!="" && $params['q']!="All") {
				$query->andFilterWhere(['like',"CONCAT(tax_rate,'%')",$params['q']]);
			}
    		$dataProvider = ArrayHelper::map($query->all(),function($model, $defaultValue){
				return $model->tax_rate.'%';
			},function($model, $defaultValue){
				return $model->tax_rate.'%';
			});
    	}
 
    	if($params['field']=='tax_code_desc'){
    		if(isset($params['q']) && $params['q']!="" && $params['q']!="All") {
    			$query->andFilterWhere(['like', 'tax_code_desc', $params['q']]);
    		}
    		$dataProvider = ArrayHelper::map($query->all(),'tax_code_desc','tax_code_desc');
    	}

    	if($params['field']=='client'){
    		if(isset($params['q']) && $params['q']!="" && $params['q']!="All") {
    			$query->andFilterWhere(['client' => $params['q']]);
    		}
    		$dataProvider = ArrayHelper::map($query->all(),'client','client');
    	}
    	
    	return array('All'=>'All') + $dataProvider;
    }
}
