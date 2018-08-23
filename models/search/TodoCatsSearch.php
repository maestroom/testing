<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Todocats;
use yii\helpers\ArrayHelper;

/**
 * TodoCatsSearch represents the model behind the search form about `app\models\TodoCats`.
 */
class TodoCatsSearch extends Todocats
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'stop', 'remove'], 'integer'],
            [['todo_cat', 'todo_desc', 'notes', 'stop'], 'safe'],
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
        $query = TodoCats::find()->where(['remove'=>0])->orderBy(['todo_cat'=>SORT_ASC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        	'pagination'=>['pageSize'=>25]
        ]);

		$dataProvider->sort->attributes['stop'] = [
			'asc' => ['stop' => SORT_ASC],
			'desc' => ['stop' => SORT_DESC], 
    	];

		if ($params['TodoCatsSearch']['todo_cat'] != null && is_array($params['TodoCatsSearch']['todo_cat'])) {
			if(!empty($params['TodoCatsSearch']['todo_cat'])){
				foreach($params['TodoCatsSearch']['todo_cat'] as $k=>$v){
					if($v=='All') { //  || strpos($v,",") !== false
						unset($params['TodoCatsSearch']['todo_cat']);break;
					}
				}
			}
			$query->andFilterWhere(['or like', 'todo_cat', $params['TodoCatsSearch']['todo_cat']]);
		}else{
            if ($params['TodoCatsSearch']['todo_cat'] != null)
                $query->andFilterWhere(['like', 'todo_cat', $params['TodoCatsSearch']['todo_cat']]);
        }
		
		if ($params['TodoCatsSearch']['stop'] != null && is_array($params['TodoCatsSearch']['stop'])) {
			if(!empty($params['TodoCatsSearch']['stop'])){
				foreach($params['TodoCatsSearch']['stop'] as $k=>$v){
					if($v=='All') { // || strpos($v,",") !== false
						unset($params['TodoCatsSearch']['stop']); break;
					}
				}
			}
			$query->andFilterWhere(['or like', 'stop', $params['TodoCatsSearch']['stop']]);
		} else {
			$query->andFilterWhere(['or like', 'stop', $params['TodoCatsSearch']['stop']]);
		}

        $this->load($params);

        /*if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }*/

        $query->andFilterWhere([
            'id' => $this->id,
            //'stop' => $this->stop,
            'remove' => $this->remove,
        ]);

        //$query->andFilterWhere(['like', 'todo_cat', $this->todo_cat])
        $query->andFilterWhere(['like', 'todo_desc', $this->todo_desc])
            ->andFilterWhere(['like', 'notes', $this->notes]);

        return $dataProvider;
    }
    
    public function searchFilter($params)
    {
		$dataProvider = array();
		$query = TodoCats::find()->where(['remove'=>0])->orderBy(['todo_cat'=>SORT_ASC])->limit(100);
		if($params['field']=='todo_cat'){
    		$query->select(['todo_cat']);
    		if(isset($params['q']) && $params['q']!=""){$query->andFilterWhere(['or like','todo_cat',$params['q']]);}
    		$query->groupBy('todo_cat');
    		$query->orderBy('todo_cat');
    		$dataProvider = ArrayHelper::map($query->all(),'todo_cat','todo_cat');
    	}
    	
    	//echo "<pre>",print_r($dataProvider),"</pre>";
    	return array_merge(array('0'=>'All'), $dataProvider);
    }
}
