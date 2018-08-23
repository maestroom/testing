<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\TasksUnitsTodos;
use yii\helpers\ArrayHelper;

/**
 * TasksUnitsTodosSearch represents the model behind the search form about `app\models\TasksUnitsTodos`.
 */
class TasksUnitsTodosSearch extends TasksUnitsTodos
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[ 'tasks_unit_id', 'todo_cat_id', 'assigned', 'complete', 'created_by', 'modified_by'], 'integer'],
            [['created', 'modified'], 'safe'],
            [['todo'], 'string'],
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
        $userId = Yii::$app->user->identity->id;
        $sqlteams = "SELECT tbl_project_security.team_id FROM tbl_project_security WHERE tbl_project_security.user_id=$userId and tbl_project_security.team_id!=0 group by tbl_project_security.team_id";
        $timezoneOffset = (new Options)->getOffsetOfCurrenttimeZone();
        if (Yii::$app->db->driverName == 'mysql') {
            $data_query = "getDueDateTimeByUsersTimezone('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%m/%d/%Y %h:%i %p')";
        } else {
            //$data_query = "[dbo].getDueDateTimeByUsersTimezone('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%m/%d/%Y %h:%i %p')";
            $data_query = "(SELECT duedatetime FROM [dbo].getDueDateTimeByUsersTimezoneTV('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%m/%d/%Y %h:%i %p'))";
        }
        $query = TasksUnitsTodos::find();

        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        	'pagination'=>['pageSize'=>25]
        ]);

        $this->load($params);
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
