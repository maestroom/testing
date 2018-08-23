<?php

namespace app\models;
use kotchuprik\sortable\behaviors;


use Yii;
use app\models\TeamLocs;
use app\models\Tasks;
use app\models\ActivityLog;
use app\models\ProjectSecurity;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\db\Query;
/**
 * This is the model class for table "{{%team}}".
 *
 * @property integer $id
 * @property string $team_name
 * @property string $team_description
 * @property integer $team_type
 * @property integer $team_status
 * @property integer $sort_order
 * @property string $created
 * @property integer $created_by
 * @property string $modified
 * @property integer $modified_by
 *
 * @property CaseXteam $caseXteam
 * @property CommentTeams[] $commentTeams
 * @property InvoiceBatchTeams[] $invoiceBatchTeams
 * @property PricingDisplayTeams[] $pricingDisplayTeams
 * @property Servicetask[] $servicetasks
 * @property TaskInstructServicetask[] $taskInstructServicetasks
 * @property TasksTeams[] $tasksTeams
 * @property TeamLocs[] $teamLocs
 * @property Teamservice[] $teamservices
 */
class Team extends \yii\db\ActiveRecord
{
	public $team_location;
	public $orderAttribute ='sort_order';
	public $team_loc;
	public $team_location_name;
	public $team_priority;
	public $client_case_id;
	public $client_id;
	public $per_complete = "";
	public $team_per_complete = "";
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%team}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['team_name', 'team_type','team_location'], 'required'],
        	[['team_name'], 'unique', 'filter' => function($value) {
        		return 'LOWER(team_name) ='.strtolower($this->team_name).' AND id NOT IN ('.$this->id.')';
        	}],
        	[['team_name', 'team_description'], 'string'],
            [['team_type', 'team_status', 'sort_order', 'created_by', 'modified_by'], 'integer'],

            [['created', 'modified','client_id','client_case_id','per_complete','team_per_complete'], 'safe']
        ];
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'team_name' => 'Team Name',
            'team_description' => 'Team Description',
            'team_type' => 'Team Type',
            'team_status' => 'Team Status',
            'sort_order' => 'Sort Order',
        	'team_location'=>'Team Location',
            'created' => 'Created',
            'created_by' => 'Created By',
            'modified' => 'Modified',
            'modified_by' => 'Modified By',
            'client_case_id' => 'Case',
            'client_id' => 'Client',
            'per_complete' => '% Complete',
			'team_per_complete' => 'Team % Complete'
        ];
    }
    /**
     * @sortable behaviors
     */
    public function behaviors()
    {
    	return [
    			'sortable' => [
    					'class' => \kotchuprik\sortable\behaviors\Sortable::className(),
    					'query' => self::find(),
    					'orderAttribute'=>'sort_order',
    			],
    	];
    }
    /**
     * @inheritdoc
     */
    public function beforeInsert()
    {
    	$last = $this->find()->orderBy([$this->orderAttribute => SORT_DESC])->limit(1)->one();
    	if ($last === null) {
    		$this->{$this->orderAttribute} = 1;
    	} else {
    		$this->{$this->orderAttribute} = $last->{$this->orderAttribute} + 1;
    	}
    }
    /**
     * @inheritdoc
     */
    public function beforeSave($insert){
    	if (parent::beforeSave($insert)) {
    
    		if($this->isNewRecord){
    			$this->created = date('Y-m-d H:i:s');
    			$this->created_by =Yii::$app->user->identity->id;
    			$this->modified =date('Y-m-d H:i:s');
    			$this->modified_by =Yii::$app->user->identity->id;
    			$this->team_status = 1;
    		}else{
    			$this->modified =date('Y-m-d H:i:s');
    			$this->modified_by =Yii::$app->user->identity->id;
    		}
    		// Place your custom code here
    		return true;
    	} else {
    		return false;
    	}
    }
    public function logAndSecurity($team_id,$team_name,$mod='add'){
    		$activityLog = new ActivityLog();
    		$projectSecurity = new ProjectSecurity();
			//ProjectSecurity::deleteAll('team_id = '.$team_id.' AND client_id=0 AND client_case_id=0 AND user_id='.Yii::$app->user->identity->id);
    		if($mod == 'add'){
	    		$projectSecurity->addUserSecurityAllTeamLocations(Yii::$app->user->identity->id, 0, 0, $team_id, 0);
	    		$activityLog->generateLog('Team','Added', $team_id, $team_name);
    		}else{
				TeamLocs::deleteAll('team_id = '.$team_id);
    			$activityLog->generateLog('Team','Updated', $team_id, $team_name);
    		}
			if(!empty($this->team_location)){
    			foreach ($this->team_location as $loc){
					$projectSecurity->addUserSecurityAllTeamLocations(Yii::$app->user->identity->id, 0, 0, $team_id, $loc);
					$projectSecurity->addUserSecurityLocationswithinTeam(Yii::$app->user->identity->id, 0, 0, $team_id, $loc);
    				$teamlocModel=new TeamLocs();
    				$teamlocModel->team_id=$team_id;
    				$teamlocModel->team_loc=$loc;
    				$teamlocModel->save();
    			}
    		}
			 $deleteSql="DELETE FROM tbl_project_security WHERE id NOT IN (SELECT minid FROM (SELECT min(id) as minid FROM tbl_project_security GROUP BY user_id, client_id, client_case_id,team_id,team_loc) as newtable)";
       		 \Yii::$app->db->createCommand($deleteSql)->execute();
    		return true;
   }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCaseXteam()
    {
        return $this->hasOne(CaseXteam::className(), ['id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCommentTeams()
    {
        return $this->hasMany(CommentTeams::className(), ['team_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoiceBatchTeams()
    {
        return $this->hasMany(InvoiceBatchTeams::className(), ['team_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPricingDisplayTeams()
    {
        return $this->hasMany(PricingDisplayTeams::className(), ['team_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServicetasks()
    {
        return $this->hasMany(Servicetask::className(), ['teamId' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTaskInstructServicetasks()
    {
        return $this->hasMany(TaskInstructServicetask::className(), ['team_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTasksTeams()
    {
        return $this->hasMany(TasksTeams::className(), ['team_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeamLocs()
    {
        return $this->hasMany(TeamLocs::className(), ['team_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeamservices()
    {
        return $this->hasMany(Teamservice::className(), ['teamid' => 'id']);
    }
    
    /**
     * get All Team Location for User Access Manage User module
     * @return
     */
    public function getTeamLocationdetails(){
    	$teamList = Team::find()->select(['id','team_name'])->where('id!=1')->orderBy('team_name ASC')->asArray()->all();
    	$myteams = array();
    	foreach($teamList as $team){
    		$myteams[$team['team_name']][$team['id']] = TeamLocs::find()->select(['tbl_teamlocation_master.id','tbl_teamlocation_master.team_location_name'])->join('LEFT JOIN', 'tbl_teamlocation_master','tbl_teamlocation_master.id = tbl_team_locs.team_loc')->where('tbl_team_locs.team_id='.$team['id'].' AND remove=0')->asArray()->all();
    	}
    	return $myteams;
    }

    /*
     * IRT-434
     * Get All Teams 
     */
    public static function getTeamList(){ //$q
//        $query = new Query;
//    	$query->select('id, name')->from('tbl_team')->where('id != 1');
//    	/*if (!is_null($q)) {
//            $query->andWhere(['like', 'id', $q]);
//    	}*/
//    	$query->limit(100);
//    	$command = $query->createCommand();
//    	$data = $command->queryAll();
//    	$teamList = $data;
//        $condition = '';
//        if($q != ''){
//            $condition = " AND team_name LIKE '%$q%'";            
//        }
//        $teamList = ArrayHelper::map(Team::find()->select(['id','team_name as text'])->where('id!=1'.$condition)->orderBy('team_name ASC')->asArray()->all(), 'id', 'text');
    	//return $teamList;
    }
    /* IRT-434
    *  date modified:27-2-2017     
    */
    public function getTeamnames(){
    	$myteams = array();
        
    	$sql="SELECT tbl_team.id,team_name,tbl_team_locs.team_loc,tbl_teamlocation_master.team_location_name FROM tbl_team INNER JOIN tbl_team_locs on tbl_team_locs.team_id=tbl_team.id INNER JOIN tbl_teamlocation_master on tbl_teamlocation_master.id=tbl_team_locs.team_loc WHERE tbl_team.id!=1 and tbl_teamlocation_master.remove=0 order by team_name ASC ";
    	$only_team = [];
        $teamList =Yii::$app->db->createCommand($sql)->queryAll();
    	foreach($teamList as $team_data){            
            $only_team[$team_data['id']] = $team_data['team_name'];
    	}                
    	return $only_team;
    }
    public function getTeamsWithPrjectSec($userID){
        $notTeams = [];
        $TeamsWithLocation_SQL = "SELECT tbl_team_locs.team_id,team_name,COUNT(tbl_team_locs.team_id) AS total_teams FROM tbl_team LEFT JOIN tbl_team_locs
ON tbl_team.id=tbl_team_locs.team_id WHERE tbl_team_locs.team_id != 1 GROUP BY tbl_team_locs.team_id,team_name";
        $Qu_res = Yii::$app->db->createCommand($TeamsWithLocation_SQL)->queryAll();
        $allTeams = ArrayHelper::map($Qu_res,'team_id','team_name');
        $TeamsWithLocations = ArrayHelper::map($Qu_res,'team_id','total_teams');
        
        $SQLonlysecured = "SELECT team_id,COUNT(team_id) AS total FROM tbl_project_security WHERE user_id = $userID and team_id <> 0 GROUP BY team_id";
        $allprojectteams = ArrayHelper::map(Yii::$app->db->createCommand($SQLonlysecured)->queryAll(),'team_id','total');        
        if(!empty($TeamsWithLocations) && !empty($allprojectteams)){            
            foreach($TeamsWithLocations as $team_id => $locationTotal){
                if(isset($allprojectteams[$team_id]) && $allprojectteams[$team_id] != ''){
                    if($allprojectteams[$team_id] == $locationTotal ){
                        if(isset($allTeams[$team_id]) && $allTeams[$team_id] != '')
                            unset($allTeams[$team_id]);
                    }                        
                }                
            }                
        }
//        $condition = '';
//        if(!empty($notTeams))
//            $condition = "AND tbl_team.id NOT IN (".implode(',',$notTeams).")";
//         
//        $sql="SELECT tbl_team.id,team_name FROM tbl_team INNER JOIN tbl_team_locs on tbl_team_locs.team_id=tbl_team.id INNER JOIN tbl_teamlocation_master on tbl_teamlocation_master.id=tbl_team_locs.team_loc WHERE tbl_team.id!=1 and tbl_teamlocation_master.remove=0 $condition order by team_name ASC ";    	
//        $teamList = ArrayHelper::map(Yii::$app->db->createCommand($sql)->queryAll(),'id','team_name');    	
        return $allTeams;
    }
    public function getTeamLocationdetailsByIds($teamIds,$addedTeamsLocation)
    {
	    	$myteams = array();
	        $con_teamids = $condtion_teams= 'AND 1=1';
	        if(!empty($addedTeamsLocation)){
	            $condtion_teams = $inner_condtion_teams= '';
	            foreach($addedTeamsLocation as $single){                
	                if($inner_condtion_teams == ''){
	                    $inner_condtion_teams = "'".$single."'";
	                }else{
	                    $inner_condtion_teams .= ",'".$single."'"; 
	                }                                               
	            }
	            $condtion_teams =  "AND (concat(tbl_team.id,',',tbl_team_locs.team_loc) NOT IN (".$inner_condtion_teams."))";
	        }
	        if(!empty($teamIds)){
	            if(!in_array('All',$teamIds)){
	               $con_teamids = 'AND tbl_team.id IN ('.implode(',',$teamIds).')';                  
	            }            
	            $sql="SELECT tbl_team.id,team_name,tbl_team_locs.team_loc,tbl_teamlocation_master.team_location_name FROM tbl_team INNER JOIN tbl_team_locs on tbl_team_locs.team_id=tbl_team.id INNER JOIN tbl_teamlocation_master on tbl_teamlocation_master.id=tbl_team_locs.team_loc WHERE tbl_team.id!=1 and tbl_teamlocation_master.remove=0 $con_teamids $condtion_teams order by team_name ASC ";       	            
	            $teamList =Yii::$app->db->createCommand($sql)->queryAll();        
	    	foreach($teamList as $team_data){
	            $myteams[$team_data['team_name']][$team_data['id']][$team_data['team_loc']] = $team_data['team_location_name']; 
	    	}
        }    	                
    	return $myteams;
    }
    /* IRT-434
    *  date modified:27-2-2017     
    */
    public function getTeamLocationdetailsArray(){
    	$myteams = array();
    	$sql="SELECT tbl_team.id,team_name,tbl_team_locs.team_loc,tbl_teamlocation_master.team_location_name FROM tbl_team INNER JOIN tbl_team_locs on tbl_team_locs.team_id=tbl_team.id INNER JOIN tbl_teamlocation_master on tbl_teamlocation_master.id=tbl_team_locs.team_loc WHERE tbl_team.id!=1 and tbl_teamlocation_master.remove=0 order by team_name ASC ";
    	$teamList =Yii::$app->db->createCommand($sql)->queryAll();        
    	foreach($teamList as $team_data){
            $myteams[$team_data['team_name']][$team_data['id']][$team_data['team_loc']] = $team_data['team_location_name']; 
    	}                
    	return $myteams;
    }
    public static function getTeamLocData(){
		if(!isset($_SESSION['HeaderTeamLocData'])) {
			$userId = Yii::$app->user->identity->id; 
			$roleId = Yii::$app->user->identity->role_id;
			$sql_query = "SELECT team.id as team_id,team.team_name,tbl_team_locs.team_loc,master.team_location_name  FROM tbl_team as team LEFT JOIN tbl_team_locs on tbl_team_locs.team_id=team.id LEFT JOIN tbl_teamlocation_master as master ON master.id = tbl_team_locs.team_loc AND master.remove=0 WHERE  team.id != 1 AND tbl_team_locs.team_loc IS NOT NULL order by team.team_name,master.team_location_name ";
			if($roleId!=0){
				$sql_query = "SELECT security.team_id,security.team_loc,team.team_name,master.team_location_name  FROM tbl_project_security security INNER JOIN tbl_team as team ON team.id = security.team_id INNER JOIN tbl_teamlocation_master as master ON master.id = security.team_loc AND master.remove=0 WHERE security.user_id = ".$userId." AND security.team_id != 0 AND security.team_loc != 0 order by team.team_name,master.team_location_name";
			}
			$dropdown_data = \Yii::$app->db->createCommand($sql_query)->queryAll();
			if(!empty($dropdown_data)) {
				foreach($dropdown_data as $drop => $value) {
					if(isset($value['team_loc'])  && $value['team_loc']!="") {
						$dropdown_widget[$value['team_id'].'_'.$value['team_loc']] = Html::decode($value['team_name'].' - '.$value['team_location_name']);
					}
				}
			}
			$_SESSION['HeaderTeamLocData']=$dropdown_widget;
		}
		return $_SESSION['HeaderTeamLocData'];
	}
    
    
    public function getTeamSerachGoogled($term, $teamId)
    {
			$pos = strrpos($teamId, '_');
        	$varloc = "";
        	$te_id=0;
        	$te_loc_id=0;
        	if ($pos) {
        		$team_loc_id = explode("_", $teamId);
        		$te_id = $team_loc_id[0];
        		$te_loc_id = $team_loc_id[1];
        	}else{
        		$te_id=$teamId;
        	}
        	$comment_arr = (new Tasks)->getUnreadCommentsTeam($te_id, $te_loc_id,'task_ids');
        	$user_id = Yii::$app->user->identity->id;
        	
			// echo "<pre>",print_r($comment_arr); die;
		    if(!empty($comment_arr))
            	$comment_arr = implode(",", $comment_arr);
            else
            	$comment_arr = 0;
            	
           $comment_data = Comments::find()->select(['tbl_comments.Id','tbl_comments.comment','tbl_comments.task_id','tbl_comments.created_by','tbl_tasks.client_case_id','usr_first_name' , 'usr_lastname', 'tbl_comments.comment_origination','tbl_comments.comment_origination','(select COUNT(comment_id) from tbl_comments_read where tbl_comments_read.comment_id = tbl_comments.Id and tbl_comments_read.user_id='.$user_id.') as readcount'])
			->innerJoinWith([
				'tasks' => function (\yii\db\ActiveQuery $query) use($te_id,$te_loc_id) {
					$query->innerJoinWith(['clientCase' => function(\yii\db\ActiveQuery $query){ 
							$query->select(['tbl_client_case.client_id','tbl_client_case.id','tbl_client_case.case_name']);
							$query->with(['client']);
						},'createdUser'])
						->innerJoinWith(['tasksUnits' => function(\yii\db\ActiveQuery $query) use($te_id,$te_loc_id){
							$query->select(['tbl_tasks_units.id'])->where('tbl_tasks_units.team_id = '.$te_id.' AND tbl_tasks_units.team_loc = '.$te_loc_id); 
						}]);
						/*->innerJoinWith([
							'taskInstruct'=>function(\yii\db\ActiveQuery $query) use($te_id,$te_loc_id){
							$query->select(['tbl_task_instruct.id'])->where('tbl_task_instruct.isactive = 1')->innerJoinWith([
								'taskInstructServicetasks'=>function(\yii\db\ActiveQuery $query) use($te_id,$te_loc_id){ 
									$query->select(['tbl_task_instruct_servicetask.id','tbl_task_instruct_servicetask.task_instruct_id','tbl_task_instruct_servicetask.team_id','tbl_task_instruct_servicetask.team_loc'])->where('tbl_task_instruct_servicetask.team_id = '.$te_id.' AND tbl_task_instruct_servicetask.team_loc = '.$te_loc_id);  
								}
							]);
						}
					]); */
				}]
			)->where("tbl_tasks.task_closed = 0 AND tbl_tasks.task_cancel = 0 AND tbl_client_case.is_close=0 AND tbl_comments.task_id IN (".$comment_arr.")")->all(); 
		  
		 	if(!empty($comment_data)){
				$team_info=Team::findOne($te_id);
				$teamloc_info=TeamlocationMaster::findOne($te_loc_id);
				foreach($comment_data as $comments){
					if($comments->readcount == 0){
						if($comments->comment_origination == 1){
							$case_detail=strtoupper($comments->tasks->clientCase->client->client_name.' - '. $comments->tasks->clientCase->case_name);
							$green_link=$case_detail." submitted By ".$comments->tasks->createdUser->usr_first_name." ".$comments->tasks->createdUser->usr_lastname;
							$searchArray[]=array('title'=>"Project #".$comments->task_id,'value'=>$comments->comment,'task_id'=>$comments->task_id,'caseId'=>$comments->tasks->client_case_id,'green_link'=>$green_link,'origination'=>'comment','id'=>$comments->Id);
						}else{
							$case_detail=strtoupper($team_info->team_name.' - '. $teamloc_info->team_location_name);
							$green_link=$case_detail." submitted By ".$comments->tasks->createdUser->usr_first_name." ".$comments->tasks->createdUser->usr_lastname;
							$searchArray[]=array('title'=>"Project #".$comments->task_id,'value'=>$comments->comment,'task_id'=>$comments->task_id,'team_id'=>$te_id,'team_loc'=>$te_loc_id,'green_link'=>$green_link,'origination'=>'comment','id'=>$comments->Id);
						}
					}
				}
			}
		 	//echo "<pre>",print_r($searchArray),"</pre>";
			//die();
		 	/*
		  	if(!empty($comment_data))
			{
				foreach ($comment_data as $comments)
				{
					$has_access = false;
					
					$has_access = (new Comments)->checkAccess($comments);
					
					if($has_access){
						
						$is_read = "N";
						if($user_id == $comments->created_by) 
						{ 
							$is_read = "Y";
						} 
						else 
						{ 
							if (!empty($comments->commentsRead))
							{
								//echo "<pre>"; print_r($comments->commentsRead); 
								foreach($comments->commentsRead as $val)
								{
									//echo "<pre>",$user_id,":",print_r($val->user_id),"</pre>";
									if ($val->user_id != "") {
										$already_readuser = $val->user_id;
										if($user_id==$already_readuser) {
											$is_read = "Y";
											break;
										} else {
											$is_read = "N";
										}
									} 
								}
							} else {
									$is_read = "N";
							}	
						}
						if($is_read=="N")
						{
							$case_detail=strtoupper($comments->tasks->clientCase->client->client_name.' - '. $comments->tasks->clientCase->case_name);
							$green_link=$case_detail." submitted By ".$comments->tasks->createdUser->usr_first_name." ".$comments->tasks->createdUser->usr_lastname;
							$searchArray[]=array('title'=>"Project #".$comments->task_id,'value'=>$comments->comment,'task_id'=>$comments->task_id,'caseId'=>$comments->tasks->client_case_id,'green_link'=>$green_link,'origination'=>'comment','id'=>$comments->Id);
						}
					}
			}
		}
		//echo "<pre>",print_r($case_detail);
		echo "<pre>"; print_r($searchArray); exit;*/
		return $searchArray; 	
	}

	public function getTeamLocdetailsArray(){
		$sql="SELECT tbl_team.id,team_name,tbl_team_locs.team_loc,tbl_teamlocation_master.team_location_name FROM tbl_team INNER JOIN tbl_team_locs on tbl_team_locs.team_id=tbl_team.id INNER JOIN tbl_teamlocation_master on tbl_teamlocation_master.id=tbl_team_locs.team_loc WHERE tbl_team.id!=1 and tbl_teamlocation_master.remove=0 order by team_name ASC ";       	            
	    $teamList =Yii::$app->db->createCommand($sql)->queryAll();        
		foreach($teamList as $team_data){
			$myteams[$team_data['team_name']][$team_data['id']][$team_data['team_loc']] = $team_data['team_location_name']; 
		}
		return $myteams;
	}
	public function getTeamLocWithPermissiondetailsArray(){
		$roleId = Yii::$app->user->identity->role_id;
		$sql="SELECT tbl_team.id,team_name,tbl_team_locs.team_loc,tbl_teamlocation_master.team_location_name FROM tbl_team INNER JOIN tbl_team_locs on tbl_team_locs.team_id=tbl_team.id INNER JOIN tbl_teamlocation_master on tbl_teamlocation_master.id=tbl_team_locs.team_loc WHERE tbl_team.id!=1 and tbl_teamlocation_master.remove=0 order by team_name ASC ";       	            
		if ($roleId != 0) {
            $uid = Yii::$app->user->identity->id;
			$sql="SELECT tbl_team.id,team_name,tbl_team_locs.team_loc,tbl_teamlocation_master.team_location_name FROM tbl_team INNER JOIN tbl_team_locs on tbl_team_locs.team_id=tbl_team.id INNER JOIN tbl_teamlocation_master on tbl_teamlocation_master.id=tbl_team_locs.team_loc WHERE tbl_team.id!=1 AND tbl_team.id IN (SELECT team_id FROM tbl_project_security WHERE user_id=$uid AND client_case_id=0 AND team_id!=0 group by team_id) and tbl_teamlocation_master.remove=0 order by team_name ASC ";       	            
        }
	    $teamList =Yii::$app->db->createCommand($sql)->queryAll();        
		foreach($teamList as $team_data){
			$myteams[$team_data['team_name']][$team_data['id']][$team_data['team_loc']] = $team_data['team_location_name']; 
		}
		return $myteams;
	}


}
