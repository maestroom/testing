<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Client;


/**
 * ClientSearch represents the model behind the search form about `app\models\Client`.
 */
class ClientSearch extends Client
{
	/**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'client_name'], 'required'],
            [['created', 'modified'], 'safe']
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
       $roleId = Yii::$app->user->identity->role_id;
       $query = Client::find()->select(['tbl_client.id', 'tbl_client.client_name', 'tbl_client.description'])
       ->innerJoinWith(['clientCases' => function (\yii\db\ActiveQuery $query) use ($userId,$roleId){ 
		$query->select(['tbl_client_case.id as client_case_id','tbl_client_case.client_id'])
        ->where(['tbl_client_case.is_close' => 0]);
		if($roleId!=0){
			$query->innerJoinWith(['projectSecurity' => function (\yii\db\ActiveQuery $query) use ($userId,$roleId){  $query->select(['client_case_id'])->where(['tbl_project_security.user_id' => $userId, 'tbl_project_security.team_id' => 0]); }]); 
		}
		$query->innerJoinWith(['tasks'=> function (\yii\db\ActiveQuery $query) use ($userId,$roleId){ 
            $query->select(['tbl_tasks.id'])->where('tbl_tasks.task_status !=4 AND tbl_tasks.task_closed =0 AND tbl_tasks.task_cancel =0'); 
        }]);			   
		}])->groupBy(['tbl_client.id', 'tbl_client.client_name', 'tbl_client.description'])->orderBy('client_name');
	   
       /*$query = Client::find()->select(['tbl_client.id', 'tbl_client.client_name', 'tbl_client.description'])->innerJoinWith(['clientCases'=>function (\yii\db\ActiveQuery $query){
		   $query->select(['tbl_client_case.id'])
			    ->innerJoinWith(['tasks' => function (\yii\db\ActiveQuery $query) use ($userId,$roleId){ $query->select(['tbl_tasks.id'])->where('tbl_tasks.task_status !=4 AND tbl_tasks.task_closed =0 AND tbl_tasks.task_cancel =0')
												      ->innerJoinWith(['clientCase' => function (\yii\db\ActiveQuery $query) use ($userId,$roleId){ 
														  $query->select(['tbl_client_case.id as client_case_id','tbl_client_case.client_id'])->where(['tbl_client_case.is_close' => 0]);
														  if($roleId!=0){
															$query->innerJoinWith(['projectSecurity' => function (\yii\db\ActiveQuery $query) use ($userId,$roleId){  $query->select(['client_case_id'])->where(['tbl_project_security.user_id' => $userId, 'tbl_project_security.team_id' => 0]); }]); 
														   }
									}]); 
				}]);}])->groupBy(['tbl_client.id', 'tbl_client.client_name', 'tbl_client.description'])->orderBy('client_name');	*/		    

        //echo $query->prepare(Yii::$app->db->queryBuilder)->createCommand()->rawSql;die;       
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'totalCount'=>0,
        	'pagination'=>['pageSize'=>25]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        
        return $dataProvider;
    }
}
