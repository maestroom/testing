<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ProjectRequestType;
use yii\helpers\ArrayHelper;

/**
 * ProjectRequestTypeSearch represents the model behind the search form about `app\models\ProjectRequestType`.
 */
class ProjectRequestTypeSearch extends ProjectRequestType
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'remove'], 'integer'],
            [['request_type'], 'safe'],
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
        $query = ProjectRequestType::find()->where(['remove'=>0])->orderBy(['request_type'=>SORT_ASC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        	'pagination'=>['pageSize'=>25]
        ]);

		if ($params['ProjectRequestTypeSearch']['request_type'] != null && is_array($params['ProjectRequestTypeSearch']['request_type'])) {
			if(!empty($params['ProjectRequestTypeSearch']['request_type'])){
				foreach($params['ProjectRequestTypeSearch']['request_type'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['ProjectRequestTypeSearch']['request_type']);break;
					}
				}
			}
			$query->andFilterWhere(['or like', 'request_type', $params['ProjectRequestTypeSearch']['request_type']]);
		}else{
            if ($params['ProjectRequestTypeSearch']['request_type'] != null)
                $query->andFilterWhere(['like', 'request_type', $params['ProjectRequestTypeSearch']['request_type']]);
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

        //$query->andFilterWhere(['like', 'request_type', $this->request_type]);

        return $dataProvider;
    }
    
    public function searchFilter($params)
    {
		$dataProvider = array();
		$query = ProjectRequestType::find()->where(['remove'=>0])->orderBy(['request_type'=>SORT_ASC])->limit(100);
		if($params['field']=='request_type'){
    		$query->select(['request_type']);
    		if(isset($params['q']) && $params['q']!=""){$query->andFilterWhere(['or like','request_type',$params['q']]);}
    		$query->groupBy('request_type');
    		$query->orderBy('request_type');
    		$dataProvider = ArrayHelper::map($query->all(),'request_type','request_type');
    	}
    	
    	//echo "<pre>",print_r($dataProvider),"</pre>";
    	return array_merge(array('0'=>'All'), $dataProvider);
    }
}
