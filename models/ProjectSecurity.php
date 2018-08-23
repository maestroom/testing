<?php

namespace app\models;

use Yii;
use app\models\ClientCase;
use app\models\Role;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%project_security}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $client_id
 * @property integer $client_case_id
 * @property integer $team_id
 * @property integer $team_loc
 *
 * @property User $user
 */
class ProjectSecurity extends \yii\db\ActiveRecord
{
	public $team_name;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%project_security}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'client_id', 'client_case_id', 'team_id', 'team_loc'], 'required'],
            [['user_id', 'client_id', 'client_case_id', 'team_id', 'team_loc'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'client_id' => 'Client ID',
            'client_case_id' => 'Client Case ID',
            'team_id' => 'Team ID',
            'team_loc' => 'Team Loc',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeam()
    {
    	return $this->hasOne(Team::className(), ['id' => 'team_id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClient()
    {
        return $this->hasMany(Client::className(), ['id' => 'client_id']);
    }
    
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClientCase()
    {
        return $this->hasMany(ClientCase::className(), ['id' => 'client_case_id']);
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
        $query = ProjectSecurity::find()->orderBy(['id'=>SORT_ASC]);
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        	'pagination'=>['pageSize'=>8]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere(['id' => $this->id]);
        $query->andFilterWhere(['user_id' => $this->user_id]);
        $query->andFilterWhere(['client_id' => $this->client_id]);
        $query->andFilterWhere(['client_case_id' => $this->client_case_id]);
        $query->andFilterWhere(['team_id' => $this->team_id]);
        $query->andFilterWhere(['team_loc' => $this->team_loc]);
        
        return $dataProvider;
    }
    
    /**
    *@to store Add new user security  Auto-Inherit All Client Cases
    * */
    public static function addUserSecurityAllClientCase($user_id, $client_id=0, $client_case_id=0, $team_id=0, $team_loc=0)
    {
		$where = "tu.usr_inherent_cases=2";
		$sql = 'INSERT INTO tbl_project_security(user_id,client_id,client_case_id,team_id,team_loc) SELECT tu.id, :client_id, :case_id, :team_id, :team_loc from tbl_user as tu WHERE '.$where.' AND tu.id<>:current_user_id;';
		\Yii::$app->db->createCommand($sql,[ ':client_id' => $client_id, ':case_id' => $client_case_id, ':team_id' => $team_id, ':team_loc' => $team_loc, ':current_user_id'=>$user_id ] )->execute();
		
		$sql = 'INSERT INTO tbl_project_security(user_id,client_id,client_case_id,team_id,team_loc) VALUES(:current_user_id, :client_id, :case_id, :team_id, :team_loc);';
		\Yii::$app->db->createCommand($sql, [ ':current_user_id' => $user_id, ':client_id' => $client_id, ':case_id' => $client_case_id, ':team_id' => $team_id, ':team_loc' => $team_loc ])->execute();
    }
    /**
    *@to store Add new user security  Auto-Inherit All New Cases within Client(s)
    * */
    public static function addUserSecurityCaseswithinClient($user_id, $client_id=0, $client_case_id=0, $team_id=0, $team_loc=0)
    {
		$sql = 'INSERT INTO tbl_project_security(user_id,client_id,client_case_id,team_id,team_loc) SELECT tu.id, :client_id, :case_id, :team_id, :team_loc from tbl_user as tu INNER JOIN tbl_project_security on tbl_project_security.user_id=tu.id and tbl_project_security.client_id='.$client_id.' AND client_case_id NOT IN ('.$client_case_id.') WHERE tu.usr_inherent_cases=1 AND tu.id<>:current_user_id group by tu.id;';
		\Yii::$app->db->createCommand($sql,[ ':client_id' => $client_id, ':case_id' => $client_case_id, ':team_id' => $team_id, ':team_loc' => $team_loc, ':current_user_id'=>$user_id ] )->execute();
		
		$sql = 'INSERT INTO tbl_project_security(user_id,client_id,client_case_id,team_id,team_loc) VALUES(:current_user_id, :client_id, :case_id, :team_id, :team_loc);';
		\Yii::$app->db->createCommand($sql, [ ':current_user_id' => $user_id, ':client_id' => $client_id, ':case_id' => $client_case_id, ':team_id' => $team_id, ':team_loc' => $team_loc ])->execute();
    }


    /**
     * @to store Auto-Inherit Auto-Inherit All Team Locations
     * */
    public static function addUserSecurityAllTeamLocations($user_id, $client_id=0, $client_case_id=0, $team_id=0, $team_loc=0)
    {
		
		$where = "tu.usr_inherent_teams=2";
        $ignore="";
		if(Yii::$app->db->driverName == 'mysql'){
			$ignore="IGNORE";
		}
		$sql = 'INSERT '.$ignore.'  INTO tbl_project_security(user_id,client_id,client_case_id,team_id,team_loc) SELECT tu.id, :client_id, :case_id, :team_id, :team_loc from tbl_user as tu WHERE '.$where.' AND tu.id<>:current_user_id;';
		\Yii::$app->db->createCommand($sql,[ ':client_id' => $client_id, ':case_id' => $client_case_id, ':team_id' => $team_id, ':team_loc' => $team_loc, ':current_user_id'=>$user_id ] )->execute();
		
		$sql = 'INSERT '.$ignore.' INTO tbl_project_security(user_id,client_id,client_case_id,team_id,team_loc) VALUES(:current_user_id, :client_id, :case_id, :team_id, :team_loc);';
		\Yii::$app->db->createCommand($sql, [ ':current_user_id' => $user_id, ':client_id' => $client_id, ':case_id' => $client_case_id, ':team_id' => $team_id, ':team_loc' => $team_loc ])->execute();
    }

    /**
    *@to store Add new user security  Auto-Inherit All New Cases within Client(s)
    * */
    public static function addUserSecurityLocationswithinTeam($user_id, $client_id=0, $client_case_id=0, $team_id=0, $team_loc=0)
    {
        $ignore="";
		if(Yii::$app->db->driverName == 'mysql'){
			$ignore="IGNORE";
		}
        $sql = 'INSERT '.$ignore.'  INTO tbl_project_security(user_id,client_id,client_case_id,team_id,team_loc) SELECT tu.id, :client_id, :case_id, :team_id, :team_loc from tbl_user as tu INNER JOIN tbl_project_security on tbl_project_security.user_id=tu.id and tbl_project_security.team_id='.$team_id.' AND team_loc NOT IN ('.$team_loc.') WHERE tu.usr_inherent_teams=1 AND tu.id<>:current_user_id group by tu.id;';
		\Yii::$app->db->createCommand($sql,[ ':client_id' => $client_id, ':case_id' => $client_case_id, ':team_id' => $team_id, ':team_loc' => $team_loc, ':current_user_id'=>$user_id ] )->execute();
		
		$sql = 'INSERT '.$ignore.'  INTO tbl_project_security(user_id,client_id,client_case_id,team_id,team_loc) VALUES(:current_user_id, :client_id, :case_id, :team_id, :team_loc);';
		\Yii::$app->db->createCommand($sql, [ ':current_user_id' => $user_id, ':client_id' => $client_id, ':case_id' => $client_case_id, ':team_id' => $team_id, ':team_loc' => $team_loc ])->execute();
        
    }

    /**
     * @to store Add new user security
     * */
    public static function addUserSecurity($user_id, $client_id=0, $client_case_id=0, $team_id=0, $team_loc=0)
    {
		$where = "tu.usr_inherent_cases=1";
		if($team_id !=0 && $team_loc != 0){
			$where = "tu.usr_inherent_teams=1";
		}
    	$sql = 'INSERT INTO tbl_project_security(user_id,client_id,client_case_id,team_id,team_loc) SELECT tu.id, :client_id, :case_id, :team_id, :team_loc from tbl_user as tu WHERE '.$where.' AND tu.id<>:current_user_id;';
		\Yii::$app->db->createCommand($sql,[ ':client_id' => $client_id, ':case_id' => $client_case_id, ':team_id' => $team_id, ':team_loc' => $team_loc, ':current_user_id'=>$user_id ] )->execute();
		
		$sql = 'INSERT INTO tbl_project_security(user_id,client_id,client_case_id,team_id,team_loc) VALUES(:current_user_id, :client_id, :case_id, :team_id, :team_loc);';
		\Yii::$app->db->createCommand($sql, [ ':current_user_id' => $user_id, ':client_id' => $client_id, ':case_id' => $client_case_id, ':team_id' => $team_id, ':team_loc' => $team_loc ])->execute();
    }
    
    /**
     * Case Security Update
     */
    public function updateCaseSecurity($caseIds, $userId, $clients=array())
    {
    	$cases_rows=array();
		$already_added_client=array();
		$rows=array();
		foreach ($caseIds as $cases){
			if(is_array($cases)){
				foreach ($cases as $case){
					$caseAttr=array();
					$clientId=(new ClientCase)->getClientId($case);
					$caseAttr['user_id']=$userId;
					$caseAttr['client_id']=$clientId;
					$caseAttr['client_case_id']=$case;
					$already_added_client[$clientId]=$clientId;
					$caseAttr['team_id']=0;
					$caseAttr['team_loc']=0;
					$rows[] = $caseAttr;
				}
			}
		}
		if(!empty($clients)){
			foreach ($clients as $c_id){
				if(!in_array($c_id,$already_added_client)){
					$caseAttr=array();
					$caseAttr['user_id']=$userId;
					$caseAttr['client_id']=$c_id;
					$caseAttr['client_case_id']=0;
					$caseAttr['team_id']=0;
					$caseAttr['team_loc']=0;
					$rows[]=$caseAttr;
				}
			}
		}
		if(!empty($rows)){
			$columns = (new ProjectSecurity)->attributes();
			unset($columns[array_search('id',$columns)]);
			Yii::$app->db->createCommand()->batchInsert(ProjectSecurity::tableName(), $columns, $rows)->execute();
		}
		return true;
    }
    
    /**
     * Project Security by id
     */
    public function getProjectSecurity($userId){
    	$project_security = $this->find()->where('user_id='.$userId)->asArray()->all();
    	return $project_security; 
    }
    /* IRT-434 */
    public function getProjectSecurityWithDetailsClients($userId){
        $ifCondf = 'IFNULL';        
        if(Yii::$app->db->driverName == 'sqlsrv')
            $ifCondf = 'ISNULL';        
       
        $sqlQuery = "SELECT tps.id,tps.client_id,tps.client_case_id,tps.team_id,tps.team_loc,$ifCondf(tc.client_name,0) AS ClientName,$ifCondf(tcc.case_name,0) AS CaseName 
        FROM tbl_project_security AS tps 
        LEFT JOIN tbl_client AS tc ON tc.id = tps.client_id
        LEFT JOIN tbl_client_case AS tcc ON tcc.id = tps.client_case_id
        WHERE tps.user_id = $userId  AND tps.team_id = 0 AND tps.team_loc = 0";
        $allSecurityDetails = ArrayHelper::map(Yii::$app->db->createCommand($sqlQuery)->queryAll(),'client_case_id',function($data){
            return $data['client_id'].','.$data['client_case_id'];
        });
 
        return $allSecurityDetails;
    }

    /* IRT-434 */
    public function getProjectSecurityWithDetailsTeams($userId){
        $ifCondf = 'IFNULL';        
        if(Yii::$app->db->driverName == 'sqlsrv')
            $ifCondf = 'ISNULL'; 
       $sqlQuery = "SELECT tps.id,tps.client_id,tps.client_case_id,tps.team_id,tps.team_loc,$ifCondf(tc.client_name,0) AS ClientName,$ifCondf(tcc.case_name,0) AS CaseName, $ifCondf(tt.team_name,0) AS TeamName,$ifCondf(ttlm.team_location_name,0) AS TeamLocationName FROM tbl_project_security AS tps 
LEFT JOIN tbl_client AS tc
ON tc.id = tps.client_id
LEFT JOIN tbl_client_case AS tcc
ON tcc.id = tps.client_case_id
LEFT JOIN tbl_team as tt
ON tt.id = tps.team_id
LEFT JOIN tbl_teamlocation_master AS ttlm
ON ttlm.id = tps.team_loc where tps.user_id = $userId AND tps.client_id = 0 AND tps.client_case_id = 0";
        $allSecurityDetails = Yii::$app->db->createCommand($sqlQuery)->queryAll();
        $list_data=[];
        foreach($allSecurityDetails as $data){
            $list_data[$data['team_id'].','.$data['team_loc']]=$data['team_id'].','.$data['team_loc'];
        }
//        echo '<pre>';
//        print_r($allSecurityDetails);
//        die;  
        return $list_data;
        //$allSecurityDetails;
    }
    
    /**
     * Delete Project Security by user_id
     */
    public function deleteuserprojectsecurity($user_id)
    {
    	foreach (ProjectSecurity::find()->where('user_id='.$user_id)->all() as $project_security) {
    		$project_security->delete();
    	}
    	return true;
    }
    
    /**
     * Team Security update
     */
    public function updateTeamSecuritywithLocations($teamLocs,$userId) {
        $teams_rows = array();
        $already_added_team = array();
        if (!empty($teamLocs)) {
            $i=0;
            $columns = (new ProjectSecurity)->attributes();
            unset($columns[array_search('id', $columns)]);
            foreach ($teamLocs as $teamswithloc) {
                $teamDetail = explode(',',$teamswithloc);
                $teamAttr = array();
                $teamAttr['user_id'] = $userId;
                $teamAttr['client_id'] = 0;
                $teamAttr['client_case_id'] = 0;
                $teamAttr['team_id'] = $teamDetail[0];
                $teamAttr['team_loc'] = $teamDetail[1];
                $teams_rows[] = $teamAttr;  
                 $i++;
                if (!empty($teams_rows) && $i>900) {
                    Yii::$app->db->createCommand()->batchInsert(ProjectSecurity::tableName(), $columns, $teams_rows)->execute();
                    $i=0;
                    $teams_rows = [];
                }              
            }
            if($i <= 900) {
//            echo '<pre>';
//            print_r($teams_rows);
//            die;
            //$columns = (new ProjectSecurity)->attributes();
           // unset($columns[array_search('id', $columns)]);
            Yii::$app->db->createCommand()->batchInsert(ProjectSecurity::tableName(), $columns, $teams_rows)->execute();
            }
        }
        
        return true;
    }
    /**
     * Team Security update
     */
    public function updateCaseSecuritywithClients($casesToinsert,$userId) {
        $cases_rows = array();
        $already_added_cases = array();
        if (!empty($casesToinsert)) {
            $i=0;
            $columns = (new ProjectSecurity)->attributes();
            unset($columns[array_search('id', $columns)]);
            foreach ($casesToinsert as $casesWithCleint) {
                $caseDetail = explode(',',$casesWithCleint);
                $caseAttr = array();
                $caseAttr['user_id'] = $userId;
                $caseAttr['client_id'] = $caseDetail[0];
                $caseAttr['client_case_id'] = $caseDetail[1];
                $caseAttr['team_id'] = 0;
                $caseAttr['team_loc'] = 0;
                $cases_rows[] = $caseAttr;                
                $i++;
                if (!empty($cases_rows) && $i>900) {
                    Yii::$app->db->createCommand()->batchInsert(ProjectSecurity::tableName(), $columns, $cases_rows)->execute();
                    $i=0;
                    $cases_rows = [];
                }
                
            }
            if($i <= 900){
                Yii::$app->db->createCommand()->batchInsert(ProjectSecurity::tableName(), $columns, $cases_rows)->execute();                
            }
        }
        
        return true;
    }
    /*
     * IRt-434
     * Delete Team Locations with userID and team_id with team_loc
     */
    public function deleteTeamSecuritywithLocations($teamsTodelete, $id){
                ProjectSecurity::deleteAll(["CONCAT(team_id,',',team_loc)"=>$teamsTodelete,'user_id'=>$id]);
        //ProjectSecurity::deleteAll(['id'=>$teamsTodelete,'user_id'=>$id]);
//        foreach (ProjectSecurity::find()->where('user_id='.$user_id)->all() as $project_security) {
//    		$project_security->delete();
//    	}
    	return true;
    }
    /*
     * IRt-434
     * Delete Cases with userID and Client ID with Client_case_id
     */
    public function deleteCaseSecuritywithClients($casesTodelete, $id){
        if(Yii::$app->db->driverName == 'sqlsrv'){
            $insert_sql ="";
            foreach($casesTodelete as $cases_to_delete){
                if($insert_sql=="")
                    $insert_sql = $insert_sql." INSERT INTO #TempTableDeleteCaseSecurity (ID) VALUES ('".$cases_to_delete."')";
                else
                    $insert_sql =$insert_sql. ", ('".$cases_to_delete."')";
                    
            }
            if($insert_sql!="") {
                $insert_sql=$insert_sql.';';
                $smt="IF OBJECT_ID('tempdb..#TempTableDeleteCaseSecurity') IS NOT NULL DROP Table #TempTableDeleteCaseSecurity;
                CREATE TABLE #TempTableDeleteCaseSecurity(
                ID VARCHAR(255));
                $insert_sql
                Delete FROM tbl_project_security WHERE user_id=$id AND CONCAT(client_id,',',client_case_id) IN (SELECT ID FROM #TempTableDeleteCaseSecurity);
                DROP Table #TempTableDeleteCaseSecurity;";
                Yii::$app->db->createCommand($smt)->execute();
            }
        }else{
            $insert_sql ="";
            foreach($casesTodelete as $cases_to_delete){
                if($insert_sql=="")
                    $insert_sql = " INSERT INTO TempTableDeleteCaseSecurity (ID) VALUES ('".$cases_to_delete."')";
                else
                    $insert_sql =$insert_sql. ", ('".$cases_to_delete."')";
            }
            if($insert_sql!="") {
                $insert_sql=$insert_sql.';';
                $smt="DROP TABLE IF EXISTS TempTableDeleteCaseSecurity;
                CREATE TEMPORARY TABLE TempTableDeleteCaseSecurity (ID VARCHAR(255));
                $insert_sql
                Delete FROM tbl_project_security WHERE user_id=$id AND CONCAT(client_id,',',client_case_id) IN (SELECT ID FROM TempTableDeleteCaseSecurity);
                DROP TABLE TempTableDeleteCaseSecurity;
                ";
                Yii::$app->db->createCommand($smt)->execute();
            }
        }
        //////ProjectSecurity::deleteAll(["CONCAT(client_id,',',client_case_id)"=>$casesTodelete,'user_id'=>$id]);
        //ProjectSecurity::deleteAll('client_case_id =0 and team_id=0 and team_loc=0 and client_id!=0 and user_id='.$id);
    	return true;
    }
    /*
     * IRt-434
     * Delete Cases with userID and Client ID with Client_case_id
     */
    public function deleteBulkTeamwithLocations($teamsTodelete){
        ProjectSecurity::deleteAll(['id'=>$teamsTodelete]);
    	return true;
    }
    /*
     * IRt-434
     * Delete Bulk Cases with userID and Client ID with Client_case_id
     */
    public function deleteBulkCommon($casesTodelete){
        ProjectSecurity::deleteAll(['id'=>$casesTodelete]);
    	return true;
    }
    /*
     * IRt-434
     * Insert Bulk Cases with userID and Client ID with Client_case_id
     */
    public function insertBulkCommon($dataToInsert){
        if(!empty($dataToInsert)){
            $columns = (new ProjectSecurity)->attributes();
            unset($columns[array_search('id',$columns)]);
            Yii::$app->db->createCommand()->batchInsert(ProjectSecurity::tableName(), $columns, $dataToInsert)->execute();
        }
    	return true;
    }
    
    /**
     * Team Security update
     */
    public function updateTeamSecuritywithLoc($teamLocIds,$userId)
    {
		$teams_rows=array();
		$already_added_team=array();
		if(!empty($teamLocIds['team_loc'])){
			foreach ($teamLocIds['team_loc'] as $team_id => $teamswithloc){
				if(is_array($teamswithloc)){
					foreach ($teamswithloc as $team_loc){
						$already_added_team[$team_id]=$team_id;
						$teamAttr = array();
						$teamAttr['user_id']=$userId;
						$teamAttr['client_id']=0;
						$teamAttr['client_case_id']=0;
						$teamAttr['team_id']=$team_id;
						$teamAttr['team_loc']=$team_loc;
						$teams_rows[] = $teamAttr;
					}
				}
			}
		}
		if(!empty($teamLocIds['team'])){
			foreach ($teamLocIds['team'] as $team_id){
						if(in_array($team_id,$already_added_team)){ continue;}
						$teamAttr = array();
						$teamAttr['user_id']=$userId;
						$teamAttr['client_id']=0;
						$teamAttr['client_case_id']=0;
						$teamAttr['team_id']=$team_id;
						$teamAttr['team_loc']=0;
						$teams_rows[] = $teamAttr;
			}
		}
		
			
		
		/* foreach($teamLocIds as $teamId => $locId){
			$teamAttr['team_id'] = $teamId;
			foreach($locId as $keys => $teamLoc){
				$res = $teamId==$teamLoc?'0':$teamLoc;
				if($res == 0 && $teamId!=1) continue;
				$teamAttr['team_loc'] = $res;
				$teams_rows[]=$teamAttr;
			}
		} */
		if(!empty($teams_rows)){
			$columns = (new ProjectSecurity)->attributes();
			unset($columns[array_search('id',$columns)]);
			Yii::$app->db->createCommand()->batchInsert(ProjectSecurity::tableName(), $columns, $teams_rows)->execute();
		}
		return true;
    }

    /* get data for user teams in array format */	    
    public function getUserTeamsArr($userId)
    {
	$teams = ArrayHelper::map($this->find()->select(['team_id'])->where(['user_id' => $userId])->andWhere('team_id!=0')->orderby(['team_id' => 'ASC'])->groupBy(['team_id'])->asArray()->all(), 'team_id', 'team_id');
	return array_unique($teams);
    }	
    /* get data for user teams in array format */
    public function getUserTeamsLocArr($userId){
    	$team_locs = ArrayHelper::map($this->find()->select(['team_loc'])->where(['user_id' => $userId])->andWhere('team_id!=0')->groupBy(['team_loc'])->asArray()->all(), 'team_loc', 'team_loc');
    	return array_unique($team_locs);
    }
    public function checkTeamAccess($team_id,$team_loc=0,$case_id=0){
        //Yii::$app->user->identity->role_id;
        if($_SESSION['role']) {
            $role_info =$_SESSION['role'];
            $roleId = $role_info->id;
        } else {
            if(isset($_SESSION['identity_data'])) {
                $user_info=$_SESSION['identity_data'];
                $roleId = $user_info->role_id;    
            } else {
                $roleId = Yii::$app->user->identity->role_id;    
            }
            $role_info = Role::findOne($roleId);    
        }
        $role_type = explode(',', $role_info->role_type);
    	if ($team_id != 1) {
    		$sql = "SELECT user_id FROM tbl_project_security WHERE tbl_project_security.team_id=$team_id  AND tbl_project_security.team_loc=$team_loc AND tbl_project_security.user_id =".Yii::$app->user->identity->id;
    	} else {
    		$sql = "SELECT user_id FROM tbl_project_security 
    			INNER JOIN tbl_user ON tbl_user.id = tbl_project_security.user_id 
    			INNER JOIN tbl_role ON tbl_role.id = tbl_user.role_id
    			WHERE tbl_role.role_type LIKE '%1%'AND tbl_project_security.user_id=".Yii::$app->user->identity->id;
    	}
    	$security_data=ProjectSecurity::findBySql($sql)->count();
    	if($security_data){
    		return true;
    	}
    	if($case_id!=0){
	    	if (in_array(1, $role_type)) {
	    		if ($team_id == 1)
	    			return true;
	    	}
	    	if ($role_type == 1) {
	    		if ($teamId != 1)
	    			return true;
	    	}
    	}
    	if($roleId == 0){
			return true;
		}
    	return false;
    }
    /* get Assign Transition Users list */
    public function getUsersAssignTransit($servicetask_id,$task_id,$team_loc,$team_id){
    	$data = array();
    	if($team_id == 1){
    		$case_id = Tasks::findOne($task_id)->client_case_id;
    		$innerSql="SELECT user_id FROM tbl_project_security Where user_id != 1 AND client_case_id =".$case_id;
    		//$innerBothSql="SELECT user_id FROM tbl_project_security Where team_id =".$team_id;
    		//if(isset($team_loc) && $team_loc!=0){
    			//$innerBothSql.=" AND team_loc = ".$team_loc;
    		//}
    		//$innerSql .= " AND user_id IN ($innerBothSql)";
    		$sql     ="	SELECT tbl_user.id,tbl_user.usr_first_name,tbl_user.usr_lastname,tbl_user.usr_username FROM tbl_user INNER JOIN tbl_role ON tbl_role.id = tbl_user.role_id WHERE tbl_user.id IN ($innerSql) AND tbl_role.role_type IN ('1') ORDER BY tbl_user.usr_lastname,tbl_role.role_name";
    		$bothSql =" SELECT tbl_user.id,tbl_user.usr_first_name,tbl_user.usr_lastname,tbl_user.usr_username FROM tbl_user INNER JOIN tbl_role ON tbl_role.id = tbl_user.role_id WHERE tbl_user.id IN ($innerSql) AND (tbl_role.role_type IN ('1,2') OR tbl_role.role_type IN ('2,1'))  ORDER BY tbl_user.usr_lastname,tbl_role.role_name";
    		$data['case_members'] = User::findBySql($sql)->all();
    		$data['both_members'] = User::findBySql($bothSql)->all();
    	}else{
    		$innerSql="SELECT user_id FROM tbl_project_security Where user_id != 1 AND team_id =".$team_id;
    		if(isset($team_loc) && $team_loc!=0){
    			$innerSql.=" AND team_loc = ".$team_loc;
    		}
    		$sql     ="	SELECT tbl_user.id,tbl_user.usr_first_name,tbl_user.usr_lastname,tbl_user.usr_username FROM tbl_user INNER JOIN tbl_role ON tbl_role.id = tbl_user.role_id WHERE tbl_user.id IN ($innerSql) AND tbl_role.role_type IN ('2') ORDER BY tbl_user.usr_lastname,tbl_role.role_name";
    		$bothSql =" SELECT tbl_user.id,tbl_user.usr_first_name,tbl_user.usr_lastname,tbl_user.usr_username FROM tbl_user INNER JOIN tbl_role ON tbl_role.id = tbl_user.role_id WHERE tbl_user.id IN ($innerSql) AND (tbl_role.role_type IN ('1,2') OR tbl_role.role_type IN ('2,1'))  ORDER BY tbl_user.usr_lastname,tbl_role.role_name";
    		$data['team_members'] = User::findBySql($sql)->all();
    		$data['both_members'] = User::findBySql($bothSql)->all();
    	}	
    	return $data;
    }
    public function getUserCases($userId){
		$cases = ArrayHelper::map($this->find()->select(['client_case_id'])->where(['user_id' => $userId])->andWhere('team_id=0')->orderby(['id' => 'ASC'])->groupBy(['client_case_id','id'])->asArray()->all(), 'client_case_id', 'client_case_id');
		return array_unique($cases);
	}
	
	/**
	 * Get Case Security Data From Generate Invoice Billing
	 * @return mixed
	 */
	public function getCaseSecurityData()
	{
		$userId = Yii::$app->user->identity->id;
		$roleId = Yii::$app->user->identity->role_id;
		if($roleId!=0){
			//$result = $this->find()->select(['t.client_i','t.client_case_id','tbl_client.client_name','tbl_client_case.id','tbl_client_case.case_name','tbl_client_case.is_close'])->From('tbl_project_security as t')->joinWith(['clientCase','client'])->where('t.user_id='.$userId.' AND t.client_case_id!=0 AND t.team_id=0')->distinct()->orderBy('tbl_client.client_name, tbl_client_case.case_name')->asArray()->all();
            $result = Yii::$app->db->createCommand("SELECT DISTINCT t.client_id, t.client_case_id, tbl_client.client_name, tbl_client_case.id AS client_case_id, tbl_client_case.id, tbl_client_case.case_name, tbl_client_case.is_close FROM tbl_project_security t LEFT JOIN tbl_client_case ON t.client_case_id = tbl_client_case.id LEFT JOIN tbl_client ON t.client_id = tbl_client.id WHERE t.user_id=$userId AND t.client_case_id!=0 AND t.team_id=0 ORDER BY tbl_client.client_name, tbl_client_case.case_name")->queryAll();
            //echo "<pre>",print_r($result),"</pre>";die;
		}else{
			//$result = ClientCase::find()->select(['tbl_client_case.client_id','tbl_client_case.id as client_case_id','tbl_client.client_name','tbl_client_case.id','tbl_client_case.case_name','tbl_client_case.is_close'])->From('tbl_client_case')->joinWith(['client'])->distinct()->orderBy('tbl_client.client_name, tbl_client_case.case_name')->asArray()->all();
            $result = Yii::$app->db->createCommand("SELECT DISTINCT tbl_client_case.client_id, tbl_client_case.id AS client_case_id, tbl_client.client_name, tbl_client_case.id, tbl_client_case.case_name, tbl_client_case.is_close FROM tbl_client_case LEFT JOIN tbl_client ON tbl_client_case.client_id = tbl_client.id ORDER BY tbl_client.client_name, tbl_client_case.case_name")->queryAll();
            
		}
		return $result;
	}

    /**
	 * Get Case Security Data From Generate Invoice Billing
	 * @return mixed
	 */
	public function getCaseSecurityDataNew($page)
	{
		$userId = Yii::$app->user->identity->id;
		$roleId = Yii::$app->user->identity->role_id;
        $limit=100;
        $mssql="OFFSET 0 ROWS FETCH NEXT $limit ROWS ONLY;";
        $mysql="LIMIT $limit OFFSET 0";
        
            $offset=( ( $page - 1 ) * $limit );
            if(Yii::$app->db->driverName == 'mysql'){
                $mysql="LIMIT $limit OFFSET $offset";
                $limit_sql=$mysql;
            }else{
                $mssql="OFFSET $offset ROWS FETCH NEXT $limit ROWS ONLY;";
                $limit_sql=$mssql;
            }
        if($roleId!=0) {
			//$result = $this->find()->select(['t.client_i','t.client_case_id','tbl_client.client_name','tbl_client_case.id','tbl_client_case.case_name','tbl_client_case.is_close'])->From('tbl_project_security as t')->joinWith(['clientCase','client'])->where('t.user_id='.$userId.' AND t.client_case_id!=0 AND t.team_id=0')->distinct()->orderBy('tbl_client.client_name, tbl_client_case.case_name')->asArray()->all();
            $result = Yii::$app->db->createCommand("SELECT DISTINCT t.client_id, t.client_case_id, tbl_client.client_name, tbl_client_case.id AS client_case_id, tbl_client_case.id, tbl_client_case.case_name, tbl_client_case.is_close FROM tbl_project_security t LEFT JOIN tbl_client_case ON t.client_case_id = tbl_client_case.id LEFT JOIN tbl_client ON t.client_id = tbl_client.id WHERE t.user_id=$userId AND t.client_case_id!=0 AND t.team_id=0 ORDER BY tbl_client.client_name, tbl_client_case.case_name $limit_sql")->queryAll();
            //echo "<pre>",print_r($result),"</pre>";die;
		} else {
			//$result = ClientCase::find()->select(['tbl_client_case.client_id','tbl_client_case.id as client_case_id','tbl_client.client_name','tbl_client_case.id','tbl_client_case.case_name','tbl_client_case.is_close'])->From('tbl_client_case')->joinWith(['client'])->distinct()->orderBy('tbl_client.client_name, tbl_client_case.case_name')->asArray()->all();
            $result = Yii::$app->db->createCommand("SELECT DISTINCT tbl_client_case.client_id, tbl_client_case.id AS client_case_id, tbl_client.client_name, tbl_client_case.id, tbl_client_case.case_name, tbl_client_case.is_close FROM tbl_client_case LEFT JOIN tbl_client ON tbl_client_case.client_id = tbl_client.id ORDER BY tbl_client.client_name, tbl_client_case.case_name $limit_sql")->queryAll();
            
		}
		return $result;
	}
}
