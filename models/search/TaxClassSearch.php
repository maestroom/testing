<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use app\models\TaxClass;

/**
 * TaxClassSearch represents the model behind the search form about `app\models\TaxClass`.
 */
class TaxClassSearch extends TaxClass
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'pricepoint', 'created_by', 'modified_by'], 'integer'],
            [['class_name', 'tax_class_desc', 'created', 'modified'], 'safe'],
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
        $query = TaxClass::find();
		
		// add conditions that should always apply here
        $params['TaxClassSearch']['class_name'] = (isset($params['TaxClassSearch']['class_name']) && $params['TaxClassSearch']['class_name']!="All")?$params['TaxClassSearch']['class_name']:'';
        $params['TaxClassSearch']['tax_class_desc'] = (isset($params['TaxClassSearch']['tax_class_desc']) && $params['TaxClassSearch']['tax_class_desc']!="All")?$params['TaxClassSearch']['tax_class_desc']:'';
        $params['TaxClassSearch']['pricepoint'] = (isset($params['TaxClassSearch']['pricepoint']) && $params['TaxClassSearch']['pricepoint']!="All")?$params['TaxClassSearch']['pricepoint']:'';
		/*multiselect*/
        if ($params['TaxClassSearch']['class_name'] != null && is_array($params['TaxClassSearch']['class_name'])) {
			if(!empty($params['TaxClassSearch']['class_name'])){
				foreach($params['TaxClassSearch']['class_name'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['TaxClassSearch']['class_name']);
					}
				}
			}
		}
		if ($params['TaxClassSearch']['pricepoint'] != null && is_array($params['TaxClassSearch']['pricepoint'])) {
			if(!empty($params['TaxClassSearch']['pricepoint'])){
				foreach($params['TaxClassSearch']['pricepoint'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['TaxClassSearch']['pricepoint']);
					}
				}
			}
		}
		/*multiselect*/
        // add conditions that should always apply here
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
				$dataProvider->sort->defaultOrder=json_decode($sort_data['data'],true);
			}
		}
		/*IRT-67*/
		$this->load($params);

//         if (!$this->validate()) {
//             // uncomment the following line if you do not want to return any records when validation fails
//             // $query->where('0=1');
//             return $dataProvider;
//         }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
       //     'pricepoint' => $this->pricepoint,
            'created' => $this->created,
            'created_by' => $this->created_by,
            'modified' => $this->modified,
            'modified_by' => $this->modified_by,
        ]);
        
        if(isset($params['TaxClassSearch']['class_name']) && $params['TaxClassSearch']['class_name']!='' && $params['TaxClassSearch']['class_name']!="All"){
        	$query->andFilterWhere(['or like','class_name',$params['TaxClassSearch']['class_name']]);
        }
        
        if(isset($params['TaxClassSearch']['pricepoint']) && $params['TaxClassSearch']['pricepoint']!='' && $params['TaxClassSearch']['pricepoint']!="All"){
        	$query->andFilterWhere(['or like','pricepoint',$params['TaxClassSearch']['pricepoint']]);
        }
        
        if(isset($params['TaxClassSearch']['tax_class_desc']) && $params['TaxClassSearch']['tax_class_desc']!='blank' && $params['TaxClassSearch']['tax_class_desc']!="All"){
        	$query->andFilterWhere(['like','tax_class_desc',$params['TaxClassSearch']['tax_class_desc']]);
        }
        
        //if($params['TaxClassSearch']['tax_class_desc'] == 'blank'){
		//	$query->andWhere(['tax_class_desc' => '']);
		//}
        //$query->andFilterWhere(['or like', 'class_name', $this->class_name]);
     //   $query->andFilterWhere(['like', 'tax_class_desc', $this->tax_class_desc]);
        
        return $dataProvider;
    }
    
    /**
     * Search the filter
     * @return Active Data Provider
     */
    public function searchFilter($params)
    {
    	$query = TaxClass::find();
    	
        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        
        $this->load($params);

        if($params['field']=='class_name'){
        	if(isset($params['q']) && $params['q']!="" && $params['q']!="All") {
        		$query->andFilterWhere(['like', 'class_name', $params['q']]);
        	}
        	$dataProvider = ArrayHelper::map($query->all(),'class_name','class_name');
        }
        
        if($params['field']=='tax_class_desc'){
        	if(isset($params['q']) && $params['q']!="" && $params['q']!="All") {
        		$query->andFilterWhere(['like', 'tax_class_desc', $params['q']]);
        	}
        	$dataProvider = ArrayHelper::map($query->all(),'tax_class_desc','tax_class_desc');
        }
        
        if($params['field']=='price_point'){
        	if(isset($params['q']) && $params['q']!="" && $params['q']!="All") {
        		$query->andFilterWhere(['like', 'pricepoint', $params['q']]);
        	}
        	$dataProvider = ArrayHelper::map($query->all(),'pricepoint','pricepoint');
        }
        
        return array('All'=>'All') + $dataProvider;
    }
}
