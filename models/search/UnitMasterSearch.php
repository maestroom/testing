<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\UnitMaster;
use yii\helpers\ArrayHelper;

/**
 * UnitMasterSearch represents the model behind the search form about `app\models\UnitMaster`.
 */
class UnitMasterSearch extends UnitMaster
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
		return [
            [['unit_id', 'unit_size', 'unit_type'], 'required'],
            [['unit_id', 'unit_size', 'unit_type'], 'integer'],
            [['unit_id'], 'exist', 'skipOnError' => true, 'targetClass' => Unit::className(), 'targetAttribute' => ['unit_id' => 'id']],
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
        $query = UnitMaster::find()->joinWith('unit')->where(['tbl_unit.remove'=>0]);
        if(isset($params['sort']) && $params['sort']!=""){}else{
			$query->orderBy(['sort_order'=>SORT_ASC]);
        }
        //echo "<pre>",print_r($params['UnitSearch']),"</pre>";die;
        if ($params['UnitMasterSearch']['unit_name'] != null && is_array($params['UnitMasterSearch']['unit_name'])) {
			if(!empty($params['UnitMasterSearch']['unit_name'])){
				foreach($params['UnitMasterSearch']['unit_name'] as $k=>$v){
					if($v=='All'){ // || strpos($v,",") !== false
						unset($params['UnitMasterSearch']['unit_name']);break;
					}
				}
			}
			$query->andFilterWhere(['or like', 'unit_name', $params['UnitMasterSearch']['unit_name']]);
		}

		$dataProvider = new ActiveDataProvider([
            'query' => $query,
        	'pagination'=>['pageSize'=>25]
        ]);

        $this->load($params);

        /*$query->andFilterWhere([
            //'id' => $this->id,
            'remove' => $this->remove,
        ]);*/
		
        return $dataProvider;
    }
}
