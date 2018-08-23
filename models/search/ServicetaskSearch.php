<?php

namespace app\models\search;

use Yii;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Servicetask;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%servicetask}}".
 *
 * @property integer $id
 * @property integer $teamId
 * @property integer $teamservice_id
 * @property string $service_task
 * @property string $description
 * @property integer $task_hide
 * @property integer $publish
 * @property integer $billable_item
 * @property integer $sampling
 * @property string $hasform
 * @property string $haspricing
 * @property integer $service_order
 * @property integer $force_entry
 * @property string $created
 * @property integer $created_by
 * @property string $modified
 * @property integer $modified_by
 *
 * @property PricingServiceTask[] $pricingServiceTasks
 * @property Team $team
 * @property Teamservice $teamservice
 * @property ServicetaskTeamLocs[] $servicetaskTeamLocs
 * @property TaskInstructNotes[] $taskInstructNotes
 * @property TaskInstructServicetask[] $taskInstructServicetasks
 * @property TasksTemplatesServiceTasks[] $tasksTemplatesServiceTasks
 */
class ServicetaskSearch extends Servicetask
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%servicetask}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
    	return [
    			[['service_task', 'description', 'billable_item'], 'string'],
    			[['billable_item','task_hide','hasform', 'haspricing','data_hasform', 'data_publish'], 'integer'],
    			
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
    	
        $query = Servicetask::find()->where(['teamservice_id'=>$params['teamservice_id']]); //->orderBy(['service_order'=>SORT_ASC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        	'pagination'=>['pageSize'=>8],
        	'sort'=> ['defaultOrder' => ['service_order' => SORT_ASC]]
        ]);


		if ($params['ServicetaskSearch']['service_task'] != null && is_array($params['ServicetaskSearch']['service_task'])) {
			if(!empty($params['ServicetaskSearch']['service_task'])){
				foreach($params['ServicetaskSearch']['service_task'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['ServicetaskSearch']['service_task']);break;
					}
				}
			}
			$query->andFilterWhere(['or like', 'service_task', $params['ServicetaskSearch']['service_task']]);
		}
		
		if ($params['ServicetaskSearch']['hasform'] != null && is_array($params['ServicetaskSearch']['hasform'])) {
			if(!empty($params['ServicetaskSearch']['hasform'])){
				foreach($params['ServicetaskSearch']['hasform'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['ServicetaskSearch']['hasform']);break;
					}
				}
			}
			$query->andFilterWhere(['hasform' => $params['ServicetaskSearch']['hasform']]);
		}
		
		if ($params['ServicetaskSearch']['data_hasform'] != null && is_array($params['ServicetaskSearch']['data_hasform'])) {
			if(!empty($params['ServicetaskSearch']['data_hasform'])){
				foreach($params['ServicetaskSearch']['data_hasform'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['ServicetaskSearch']['data_hasform']);break;
					}
				}
			}
			$query->andFilterWhere(['data_hasform' => $params['ServicetaskSearch']['data_hasform']]);
		}
		
		if ($params['ServicetaskSearch']['billable_item'] != null && is_array($params['ServicetaskSearch']['billable_item'])) {
			if(!empty($params['ServicetaskSearch']['billable_item'])){
				foreach($params['ServicetaskSearch']['billable_item'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['ServicetaskSearch']['billable_item']); break;
					}
				}
			}
			if($params['ServicetaskSearch']['billable_item'][0] == "1" && !isset($params['ServicetaskSearch']['billable_item'][1]))
				$query->andWhere('billable_item IN (1,2)');
			if(isset($params['ServicetaskSearch']['billable_item'][0]) && isset($params['ServicetaskSearch']['billable_item'][1]))
				$query->andWhere('billable_item IN (0,1,2)');
			if($params['ServicetaskSearch']['billable_item'][0] == "2" && !isset($params['ServicetaskSearch']['billable_item'][1]))
				$query->andWhere('billable_item IN (0)');	
		}
		
	    $this->load($params);
	    
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

	    $query->andFilterWhere(['teamservice_id' => $this->teamservice_id]);
        $query->andFilterWhere(['task_hide'=> $params['task_hide']]);
        //$query->andFilterWhere(['hasform'=> $this->hasform]);
		//$query->andFilterWhere(['data_hasform' => $this->data_hasform]);
		//$query->andFilterWhere(['like', 'service_task', $this->service_task]);
		
		//if(isset($this->billable_item) && $this->billable_item==1){
		//	$query->andFilterWhere(['billable_item' => $this->billable_item]);
		//	$query->andWhere('billable_item IN (1,2)');
		//} else if(isset($this->billable_item) && $this->billable_item==2){
		//	$query->andFilterWhere(['billable_item' => $this->billable_item]);
		//	$query->andWhere('billable_item IN (0)');
		//} else {
		//	$query->andFilterWhere(['billable_item' => $this->billable_item]);
		//	$query->andWhere('billable_item IN (0,1,2)');
		//}
		//echo "<pre>",print_r($dataProvider->getModels()),"</pre>";
		
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
        $query = Servicetask::find()->where(['teamservice_id'=>$params['teamservice_id']]); //->orderBy(['service_order'=>SORT_ASC]);

	 	if($params['field']=='service_task'){
			if(isset($params['q']) && $params['q']!=""){$query->andFilterWhere(['like', 'service_task', $params['q']]);}
    		$dataProvider = ArrayHelper::map($query->all(),'service_task','service_task');
    	}
    	
    	return array('All'=>'All') +$dataProvider;
	}	
}
