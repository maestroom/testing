<?php
namespace app\controllers;
use Yii;
use app\models\Mydocument;
//use app\models\search\EvidenceProductionSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
//use yii\helpers\ArrayHelper;
use app\models\TeamlocationMaster;
use app\models\MydocumentsBlob;
use yii\data\ActiveDataProvider;
use app\models\TasksUnits;
use app\models\Servicetask;
use app\models\User;
use app\models\TeamLocs;
use yii\helpers\ArrayHelper;
use app\models\Todocats;
use app\models\Options;

/**
 * CaseDocumentController implements the CRUD actions for MyDocument model.
 */
class TeamOverviewController extends Controller
{
    /**
     * @inheritdoc
     */

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all MyDocument models.
     * @return mixed
     */
    public function actionIndex($team_id)
    {
        $node_id= Yii::$app->request->get('node_id');
        $team_loc= Yii::$app->request->get('team_loc');
        $this->layout = "myteam";
        $uid = Yii::$app->user->identity->id;

        $teamLocation = TeamlocationMaster::findOne($team_loc);
        $data = (new Mydocument)->fecthDataRec($team_id,"Team",0,$team_loc);

        $sql="SELECT tbl_task_instruct_servicetask.servicetask_id, COUNT(tbl_tasks_units.id) as  totalunits FROM tbl_tasks_units LEFT JOIN  tbl_task_instruct_servicetask ON  tbl_tasks_units.task_instruct_servicetask_id =  tbl_task_instruct_servicetask.id LEFT JOIN tbl_servicetask ON  tbl_servicetask.id =  tbl_task_instruct_servicetask.servicetask_id WHERE  (((unit_assigned_to <> 0) AND (unit_status <> 4)) AND  ((tbl_task_instruct_servicetask.team_id='".$team_id."') AND  (tbl_task_instruct_servicetask.team_loc='".$team_loc."'))) AND  (tbl_tasks_units.task_id IN (SELECT task.id FROM tbl_tasks as task WHERE  task.task_cancel = 0 AND task.task_closed = 0 AND task.client_case_id  IN (select clientCase.id FROM tbl_client_case as clientCase WHERE  clientCase.is_close=0))) GROUP BY servicetask_id";
        $data=\Yii::$app->db->createCommand($sql)->queryAll();

        //echo "<pre>"; print_r($data); die;
        return $this->render('index', [
            //'caseInfo' => $caseInfo,
            'data' => $data,
            'team_id'=>$team_id,'node_id'=>$node_id,'team_loc'=>$team_loc
        ]);
    }
    /**
     * Shows chart for Task Assignments for current team and location.
     * @return mixed
     */
    public function actionTaskassignments($team_id)
    {
        $team_id= Yii::$app->request->get('team_id');
        $team_loc= Yii::$app->request->get('team_loc');
        $date = Yii::$app->request->get('search_date');
        $pendingDateCondition = '';
        if($date!=''){
			$exploded = explode("/",$date);
			$date = $exploded[2]."-".$exploded[0]."-".$exploded[1];
		}
        $post_data= Yii::$app->request->post();
        $this->layout = "myteam";
        $drivername = Yii::$app->db->driverName;
        $unitdatecondition=""; $tododatecondition="";

        if($date != '')
        {
            $UTCtimezoneOffset = (new Options)->getOffsetOfTimeZone();
            $UserSettimezoneOffset = (new Options)->getOffsetOfTimeZone($_SESSION['usrTZ']);
            if ($drivername == 'mysql'){
                /*$unitdatecondition=" AND DATE_FORMAT( CONVERT_TZ(tbl_tasks_units.created,'{$UTCtimezoneOffset}','{$UserSettimezoneOffset}'), '%Y-%m-%d') = '".$date."'";
                $tododatecondition=" AND DATE_FORMAT(CONVERT_TZ(t.modified,'{$UTCtimezoneOffset}','{$UserSettimezoneOffset}'), '%Y-%m-%d') = '".$date."'";*/
                $unitdatecondition=" AND tbl_tasks_units.id IN (SELECT tasks_unit_id FROM tbl_tasks_units_transaction_log WHERE transaction_type IN (5,6) AND tbl_tasks_units.id = tasks_unit_id AND DATE_FORMAT( CONVERT_TZ(tbl_tasks_units_transaction_log.transaction_date,'{$UTCtimezoneOffset}','{$UserSettimezoneOffset}'), '%Y-%m-%d') = '".$date."' GROUP BY tbl_tasks_units_transaction_log.transaction_date HAVING tbl_tasks_units_transaction_log.transaction_date = MAX(tbl_tasks_units_transaction_log.transaction_date))";

                $tododatecondition=" AND t.id IN (SELECT todo_id FROM tbl_tasks_units_todo_transaction_log WHERE transaction_type IN (7,8) AND t.id = todo_id AND DATE_FORMAT( CONVERT_TZ(tbl_tasks_units_todo_transaction_log.transaction_date,'{$UTCtimezoneOffset}','{$UserSettimezoneOffset}'), '%Y-%m-%d') = '".$date."' GROUP BY tbl_tasks_units_todo_transaction_log.transaction_date HAVING tbl_tasks_units_todo_transaction_log.transaction_date = MAX(tbl_tasks_units_todo_transaction_log.transaction_date))";

                $dateCommonQuery = " AND DATE_FORMAT( CONVERT_TZ(tbl_tasks_units_transaction_log.transaction_date,'{$UTCtimezoneOffset}','{$UserSettimezoneOffset}'), '%Y-%m-%d') = '".$date."' GROUP BY tbl_tasks_units_transaction_log.transaction_date HAVING tbl_tasks_units_transaction_log.transaction_date = MAX(tbl_tasks_units_transaction_log.transaction_date))";

                $pendingDateCondition = " AND B.task_unit_id IN (SELECT tasks_unit_id FROM tbl_tasks_units_transaction_log WHERE transaction_type IN (5,6) AND B.task_unit_id = tasks_unit_id".$dateCommonQuery;

                $notStartedDateCondition = " AND t.id IN (SELECT tasks_unit_id FROM tbl_tasks_units_transaction_log WHERE transaction_type IN (5,6) AND t.id = tasks_unit_id".$dateCommonQuery;

            } else {
                /* $unitdatecondition=" AND Cast(switchoffset(todatetimeoffset(Cast(tbl_tasks_units.created as datetime), '+00:00'), '{$UTCtimezoneOffset}') as date) >= '".$date."'";
                $tododatecondition=" AND Cast(switchoffset(todatetimeoffset(Cast(t.modified as datetime), '+00:00'), '{$UTCtimezoneOffset}') as date) >= '".$date."'"; */
                $unitdatecondition=" AND tbl_tasks_units.id IN (SELECT tasks_unit_id FROM tbl_tasks_units_transaction_log WHERE transaction_type IN (5,6) AND tbl_tasks_units.id = tasks_unit_id AND Cast(switchoffset(todatetimeoffset(Cast(tbl_tasks_units_transaction_log.transaction_date as datetime), '+00:00'), '{$UTCtimezoneOffset}') as date) >= '".$date."' GROUP BY tbl_tasks_units_transaction_log.transaction_date, tbl_tasks_units_transaction_log.tasks_unit_id HAVING tbl_tasks_units_transaction_log.transaction_date = MAX(tbl_tasks_units_transaction_log.transaction_date))";

                $tododatecondition=" AND t.id IN (SELECT todo_id FROM tbl_tasks_units_todo_transaction_log WHERE transaction_type IN (7,8) AND t.id = todo_id AND Cast(switchoffset(todatetimeoffset(Cast(tbl_tasks_units_todo_transaction_log.transaction_date as datetime), '+00:00'), '{$UTCtimezoneOffset}') as date) >= '".$date."' GROUP BY tbl_tasks_units_todo_transaction_log.transaction_date, tbl_tasks_units_todo_transaction_log.todo_id HAVING tbl_tasks_units_todo_transaction_log.transaction_date = MAX(tbl_tasks_units_todo_transaction_log.transaction_date))";

                $pendingDateCondition=" AND B.task_unit_id IN (SELECT tasks_unit_id FROM tbl_tasks_units_transaction_log WHERE transaction_type IN (5,6) AND B.task_unit_id = tasks_unit_id AND Cast(switchoffset(todatetimeoffset(Cast(tbl_tasks_units_transaction_log.transaction_date as datetime), '+00:00'), '{$UTCtimezoneOffset}') as date) >= '".$date."' GROUP BY tbl_tasks_units_transaction_log.transaction_date, tbl_tasks_units_transaction_log.tasks_unit_id HAVING tbl_tasks_units_transaction_log.transaction_date = MAX(tbl_tasks_units_transaction_log.transaction_date))";
                
                $dateCommonQuery = " AND Cast(switchoffset(todatetimeoffset(Cast(tbl_tasks_units_transaction_log.transaction_date as datetime), '+00:00'), '{$UTCtimezoneOffset}') as date)= '".$date."' GROUP BY tbl_tasks_units_transaction_log.transaction_date,tbl_tasks_units_transaction_log.tasks_unit_id HAVING tbl_tasks_units_transaction_log.transaction_date = MAX(tbl_tasks_units_transaction_log.transaction_date))";
				
                $notStartedDateCondition = " AND t.id IN (SELECT tasks_unit_id FROM tbl_tasks_units_transaction_log WHERE transaction_type IN (5,6) AND t.id = tasks_unit_id".$dateCommonQuery;
            }
        }
        //echo $tododatecondition;die;
        //$sql="SELECT tbl_tasks_units.unit_assigned_to, COUNT(tbl_tasks_units.id) as totalunits FROM tbl_tasks_units LEFT JOIN  tbl_task_instruct_servicetask ON  tbl_tasks_units.task_instruct_servicetask_id =  tbl_task_instruct_servicetask.id LEFT JOIN tbl_servicetask ON  tbl_servicetask.id =  tbl_task_instruct_servicetask.servicetask_id WHERE  (((unit_assigned_to <> 0) AND (unit_status <> 4)) AND  ((tbl_task_instruct_servicetask.team_id='".$team_id."') AND  (tbl_task_instruct_servicetask.team_loc='".$team_loc."'))) AND  (tbl_tasks_units.task_id IN (SELECT task.id FROM tbl_tasks as task WHERE  task.task_cancel = 0 AND task.task_closed = 0 AND task.client_case_id  IN (select clientCase.id FROM tbl_client_case as clientCase WHERE  clientCase.is_close=0)))  ".$unitdatecondition." GROUP BY unit_assigned_to";
        /**************************************/
        $sql = "SELECT tbl_tasks_units.unit_assigned_to, COUNT(tbl_tasks_units.id) as totalunits FROM tbl_tasks_units  LEFT JOIN tbl_servicetask ON tbl_servicetask.id = tbl_tasks_units.servicetask_id WHERE (((unit_assigned_to <> 0) AND (unit_status <> 4)) AND ((tbl_tasks_units.team_id='".$team_id."') AND (tbl_tasks_units.team_loc='".$team_loc."'))) AND (tbl_tasks_units.task_id IN (SELECT task.id FROM tbl_tasks as task WHERE task.task_cancel = 0 AND task.task_closed = 0 AND task.client_case_id IN (select clientCase.id FROM tbl_client_case as clientCase WHERE clientCase.is_close=0))) ".$unitdatecondition." GROUP BY unit_assigned_to";
        //$data = \Yii::$app->db->createCommand($sql)->queryAll();

        //$sql_pending = "SELECT tbl_tasks_units.unit_assigned_to, COUNT(tbl_tasks_units.id) as totalunits FROM tbl_tasks_units  LEFT JOIN tbl_servicetask ON tbl_servicetask.id = tbl_tasks_units.servicetask_id WHERE (((unit_assigned_to <> 0) AND (unit_status = 0)) AND ((tbl_tasks_units.team_id='".$team_id."') AND (tbl_tasks_units.team_loc='".$team_loc."'))) AND (tbl_tasks_units.task_id IN (SELECT task.id FROM tbl_tasks as task WHERE task.task_cancel = 0 AND task.task_closed = 0 AND task.client_case_id IN (select clientCase.id FROM tbl_client_case as clientCase WHERE clientCase.is_close=0))) ".$unitdatecondition." GROUP BY unit_assigned_to";
        /* IRT 199 */
        $data_pending = (new TasksUnits)->getTaskPendingTaskTeamOverview($team_id, $team_loc,$pendingDateCondition);

        $sql_working = "SELECT tbl_tasks_units.unit_assigned_to, COUNT(tbl_tasks_units.id) as totalunits FROM tbl_tasks_units LEFT JOIN tbl_servicetask ON tbl_servicetask.id = tbl_tasks_units.servicetask_id WHERE (((unit_assigned_to <> 0) AND (unit_status <> 0) AND (unit_status <> 4)) AND ((tbl_tasks_units.team_id='".$team_id."') AND (tbl_tasks_units.team_loc='".$team_loc."'))) AND (tbl_tasks_units.task_id IN (SELECT task.id FROM tbl_tasks as task WHERE task.task_cancel = 0 AND task.task_closed = 0 AND task.client_case_id IN (select clientCase.id FROM tbl_client_case as clientCase WHERE clientCase.is_close=0))) ".$unitdatecondition." GROUP BY unit_assigned_to";

        $data_working = \Yii::$app->db->createCommand($sql_working)->queryAll();

        $data_notstarted = (new TasksUnits)->getTaskNotstartedTaskTeamOverview($team_id, $team_loc,$notStartedDateCondition);

        // $sql_notstarted = "SELECT tbl_tasks_units.unit_assigned_to, COUNT(tbl_tasks_units.id) as totalunits FROM tbl_tasks_units  LEFT JOIN tbl_servicetask ON tbl_servicetask.id = tbl_tasks_units.servicetask_id WHERE (((unit_assigned_to = 0) AND (unit_status = 0) AND (unit_status <> 4)) AND ((tbl_tasks_units.team_id='".$team_id."') AND (tbl_tasks_units.team_loc='".$team_loc."'))) AND (tbl_tasks_units.task_id IN (SELECT task.id FROM tbl_tasks as task WHERE task.task_cancel = 0 AND task.task_closed = 0 AND task.client_case_id IN (select clientCase.id FROM tbl_client_case as clientCase WHERE clientCase.is_close=0))) ".$unitdatecondition." GROUP BY unit_assigned_to";
        // $data_notstarted = \Yii::$app->db->createCommand($sql_notstarted)->queryAll();

        /*********************************/
        // echo "<pre>"; print_r($data); print_r($data_notstarted);	die;
    	// $sql_todo = "SELECT assigned, count( * ) as totaltodos FROM tbl_tasks_units_todos as t INNER JOIN tbl_tasks_units on tbl_tasks_units.id=t.tasks_unit_id INNER JOIN tbl_task_instruct on tbl_task_instruct.id=tbl_tasks_units.task_instruct_id WHERE tbl_task_instruct.task_id IN (SELECT task.id FROM tbl_tasks as task WHERE task.task_cancel = 0 AND task.task_closed = 0 AND task.client_case_id IN (select clientCase.id FROM tbl_client_case as clientCase WHERE clientCase.is_close=0) ) AND t.complete = 0 AND t.tasks_unit_id IN (SELECT taskunit.id FROM tbl_tasks_units as taskunit INNER JOIN  tbl_task_instruct_servicetask ON  taskunit.task_instruct_servicetask_id =  tbl_task_instruct_servicetask.id WHERE taskunit.unit_status!=4 AND taskunit.unit_assigned_to != 0 AND  tbl_task_instruct_servicetask.team_id = ".$team_id." AND tbl_task_instruct_servicetask.team_loc = ".$team_loc.")  ".$tododatecondition." GROUP BY t.assigned";

        /*$sql_todo = "SELECT tbl_tasks_units.unit_assigned_to as assigned, count( * ) as totaltodos FROM tbl_tasks_units_todos as t
        	INNER JOIN tbl_tasks_units on tbl_tasks_units.id=t.tasks_unit_id
        	INNER JOIN tbl_tasks on tbl_tasks.id=tbl_tasks_units.task_id
        	INNER JOIN tbl_client_case on tbl_client_case.id=tbl_tasks.client_case_id
        	WHERE tbl_tasks.task_cancel = 0 AND tbl_tasks.task_closed = 0 AND tbl_client_case.is_close=0 AND t.complete = 0 and tbl_tasks_units.unit_status!=4 AND tbl_tasks_units.unit_assigned_to != 0 and t.assigned!=0 and t.assigned=tbl_tasks_units.unit_assigned_to and tbl_tasks_units.team_id = ".$team_id." and tbl_tasks_units.team_loc = ".$team_loc." ".$tododatecondition." GROUP BY tbl_tasks_units.unit_assigned_to";
        */$sql_todo = "
        SELECT t.assigned, count( * ) as totaltodos
FROM tbl_tasks_units_todos as t
INNER JOIN tbl_tasks_units on tbl_tasks_units.id=t.tasks_unit_id
INNER JOIN tbl_tasks on tbl_tasks.id=tbl_tasks_units.task_id
INNER JOIN tbl_client_case on tbl_client_case.id=tbl_tasks.client_case_id
WHERE tbl_tasks.task_cancel = 0 AND tbl_tasks.task_closed = 0 AND tbl_client_case.is_close=0 AND t.complete = 0
and tbl_tasks_units.unit_status!=4 and t.assigned!=0
and tbl_tasks_units.team_id = ".$team_id."
and tbl_tasks_units.team_loc = ".$team_loc." ".$tododatecondition."
GROUP BY t.assigned";
        //echo $sql_todo;die;
        $data_todo=\Yii::$app->db->createCommand($sql_todo)->queryAll();
        $userList = User::find()->select(['id', 'usr_first_name','usr_lastname'])->asArray()->all();
        foreach($userList as $user) {
            $user_arr[$user['id']] = $user['usr_first_name'].' '.$user['usr_lastname'];
        }

        //$data=  array_merge($data_pending,$data_working);
        //echo "<pre>",print_r($data_notstarted),"</pre>"; die();
        $graph_arr=array();
        foreach($data_pending as $task)
        {
			$graph_arr[$task['unit_assigned_to']]['task_pending'] = $task['totalunits'];
			if((new User)->checkAccess(5.014)) {
			$dateavail = '';
			if($date!='')
					$dateavail = "&dates=".$date;
			$graph_arr[$task['unit_assigned_to']]['user'] = "<a href='index.php?r=team-tasks/index&team_id=".$team_id."&team_loc=".$team_loc."&unit_assigned_to=".$task['unit_assigned_to'].$dateavail."'>".$user_arr[$task['unit_assigned_to']]."</a>";
			} else
			    $graph_arr[$task['unit_assigned_to']]['user'] = $user_arr[$task['unit_assigned_to']];
        }

        foreach($data_working as $task)
        {
            $graph_arr[$task['unit_assigned_to']]['task_working'] = $task['totalunits'];
            if(!isset($graph_arr[$task['unit_assigned_to']]['user']))
            {
                if((new User)->checkAccess(5.014)) {
                    $dateavail = '';
                    if($date!='')
                    $dateavail = "&dates=".$date;
                    $graph_arr[$task['unit_assigned_to']]['user'] = "<a href='index.php?r=team-tasks/index&team_id=".$team_id."&team_loc=".$team_loc."&notin_unit_status=0&unit_assigned_to=".$task['unit_assigned_to'].$dateavail."'>".$user_arr[$task['unit_assigned_to']]."</a>";
                } else
                    $graph_arr[$task['unit_assigned_to']]['user'] = $user_arr[$task['unit_assigned_to']];
            }
        }

        foreach($data_notstarted as $task)
        {
        	//echo "<pre>",print_r($task),"</pre>";
            $graph_arr[$task['unit_assigned_to']]['task_notstarted'] = $task['totalunits'];
            if(!isset($graph_arr[$task['unit_assigned_to']]['user']))
            {
                if((new User)->checkAccess(5.014)) {
                    $dateavail='';
                    if($date!='')
                    	$dateavail = "&dates=".$date;

                	$graph_arr[$task['unit_assigned_to']]['user'] = "<a href='index.php?r=team-tasks/index&team_id=".$team_id."&team_loc=".$team_loc."&unit_assigned_to=".$task['unit_assigned_to'].$dateavail."&statusFilter=0'>".$user_arr[$task['unit_assigned_to']]."</a>";
                } else
                	$graph_arr[$task['unit_assigned_to']]['user'] = $user_arr[$task['unit_assigned_to']];
            }
        }

        //echo "<pre>",print_r($graph_arr);die;
        foreach($data_todo as $todo)
        {
			if($graph_arr[$todo['assigned']]['user']!='') {
				$graph_arr[$todo['assigned']]['todo']=$todo['totaltodos'];
				if(!isset($graph_arr[$todo['assigned']]['user']))
				{
					if((new User)->checkAccess(5.014)) {
						$dateavail = '';
						if($date!='')
						    $dateavail = "&dates=".$date;

						$graph_arr[$todo['assigned']]['user'] = "<a href='index.php?r=team-tasks/index&team_id=".$team_id."&team_loc=".$team_loc."&unit_assigned_to=".$todo['assigned']."&statusFilter=8".$dateavail."'>".$user_arr[$todo['assigned']]."</a>";
					} else
						$graph_arr[$todo['assigned']]['user'] = "<a href='javascript:void(0);'>".$user_arr[$todo['assigned']]."</a>";
				}
			}else{
                $graph_arr[$todo['assigned']]['todo']=$todo['totaltodos'];
				if(!isset($graph_arr[$todo['assigned']]['user']))
				{
					if((new User)->checkAccess(5.014)) {
						$dateavail = '';
						if($date!='')
						    $dateavail = "&dates=".$date;

						$graph_arr[$todo['assigned']]['user'] = "<a href='index.php?r=team-tasks/index&team_id=".$team_id."&team_loc=".$team_loc."&unit_assigned_to=".$todo['assigned']."&statusFilter=8".$dateavail."'>".$user_arr[$todo['assigned']]."</a>";
					} else
						$graph_arr[$todo['assigned']]['user'] = "<a href='javascript:void(0);'>".$user_arr[$todo['assigned']]."</a>";
				}
            }
        }

        $graph_data=array();
        $k=0;
        foreach($graph_arr as $gdata)
        {
           if(!isset($gdata['task_working']) || $gdata['task_working']=='')
               $gdata['task_working']=0;
           if(!isset($gdata['task_pending']) || $gdata['task_pending']=='')
               $gdata['task_pending']=0;
           if(!isset($gdata['task_notstarted']) || $gdata['task_notstarted']=='')
               $gdata['task_notstarted']=0;

           if(!isset($gdata['todo']) || $gdata['todo']=='')
               $gdata['todo']=0;

           $gdata['total'] = $gdata['task_working'] + $gdata['task_pending'] + $gdata['task_notstarted'] + $gdata['todo'];
           $graph_data[$k]= $gdata;
           $k++;
        }
        usort($graph_data, function($a, $b) {
			if($a['total']==$b['total']) return 0;
			return $a['total'] < $b['total']?1:-1;
		});

	    $graph_data = json_encode($graph_data);

      //  echo "<pre>",print_r($graph_data),"</pre>";die;
        if(isset($post_data['type']) && $post_data['type']=='filter')
        {
			$this->layout = false;
            echo $graph_data; exit;
        }
        else
        {
            $teaminfo = "";
            //TeamLocs::find()->where(['team_id'=>$team_id,'team_loc'=>$team_loc])->joinWith(['team','teamlocationMaster'])->one();
		    return $this->render('taskassignments', [
                'data' => $graph_data,
                'team_id'=>$team_id,'team_loc'=>$team_loc,'teaminfo'=>$teaminfo,'search_date'=>$date
            ]);
        }
    }


    /**
     * Shows chart for Task Assignments for current team and location.
     * @return mixed
     */
    public function actionTaskassigncompleted($team_id)
    {
        $team_id= Yii::$app->request->get('team_id');
        $team_loc= Yii::$app->request->get('team_loc');
        $date = Yii::$app->request->get('search_date');

        if($date!=''){
			$exploded = explode("/",$date);
			$date = $exploded[2]."-".$exploded[0]."-".$exploded[1];
		}

        $post_data= Yii::$app->request->post();
        $this->layout = "myteam";
        $drivername = Yii::$app->db->driverName;
        $unitdatecondition="";$tododatecondition="";
        if($date != '')
        {
            $UTCtimezoneOffset = (new Options)->getOffsetOfTimeZone();
            $UserSettimezoneOffset = (new Options)->getOffsetOfTimeZone($_SESSION['usrTZ']);
            if ($drivername == 'mysql') {
                $unitdatecondition=" AND tbl_tasks_units.id IN (SELECT tasks_unit_id FROM tbl_tasks_units_transaction_log WHERE transaction_type IN (4) AND tbl_tasks_units.id = tasks_unit_id AND DATE_FORMAT( CONVERT_TZ(tbl_tasks_units_transaction_log.transaction_date,'{$UTCtimezoneOffset}','{$UserSettimezoneOffset}'), '%Y-%m-%d') = '".$date."' GROUP BY tbl_tasks_units_transaction_log.transaction_date HAVING tbl_tasks_units_transaction_log.transaction_date = MAX(tbl_tasks_units_transaction_log.transaction_date))";

                $tododatecondition=" AND t.id IN (SELECT todo_id FROM tbl_tasks_units_todo_transaction_log WHERE transaction_type IN (9) AND t.id = todo_id AND DATE_FORMAT( CONVERT_TZ(tbl_tasks_units_todo_transaction_log.transaction_date,'{$UTCtimezoneOffset}','{$UserSettimezoneOffset}'), '%Y-%m-%d') = '".$date."' GROUP BY tbl_tasks_units_todo_transaction_log.transaction_date HAVING tbl_tasks_units_todo_transaction_log.transaction_date = MAX(tbl_tasks_units_todo_transaction_log.transaction_date))";
            } else {
                $unitdatecondition=" AND tbl_tasks_units.id IN (SELECT tasks_unit_id FROM tbl_tasks_units_transaction_log WHERE transaction_type IN (4) AND tbl_tasks_units.id = tasks_unit_id AND Cast(switchoffset(todatetimeoffset(Cast(tbl_tasks_units_transaction_log.transaction_date as datetime), '+00:00'), '{$UTCtimezoneOffset}') as date) = '".$date."' GROUP BY tbl_tasks_units_transaction_log.transaction_date, tbl_tasks_units_transaction_log.tasks_unit_id HAVING tbl_tasks_units_transaction_log.transaction_date = MAX(tbl_tasks_units_transaction_log.transaction_date))";
                $tododatecondition=" AND t.id IN (SELECT todo_id FROM tbl_tasks_units_todo_transaction_log WHERE transaction_type IN (9) AND t.id = todo_id AND Cast(switchoffset(todatetimeoffset(Cast(tbl_tasks_units_todo_transaction_log.transaction_date as datetime), '+00:00'), '{$UTCtimezoneOffset}') as date) = '".$date."' GROUP BY tbl_tasks_units_todo_transaction_log.transaction_date, tbl_tasks_units_todo_transaction_log.todo_id HAVING tbl_tasks_units_todo_transaction_log.transaction_date = MAX(tbl_tasks_units_todo_transaction_log.transaction_date))";
            }
        }
        // $sql="SELECT tbl_tasks_units.unit_assigned_to, COUNT(tbl_tasks_units.id) as  totalunits FROM tbl_tasks_units LEFT JOIN  tbl_task_instruct_servicetask ON  tbl_tasks_units.task_instruct_servicetask_id =  tbl_task_instruct_servicetask.id LEFT JOIN tbl_servicetask ON  tbl_servicetask.id =  tbl_task_instruct_servicetask.servicetask_id WHERE  (((unit_assigned_to <> 0) AND (unit_status <> 4)) AND  ((tbl_task_instruct_servicetask.team_id='".$team_id."') AND  (tbl_task_instruct_servicetask.team_loc='".$team_loc."'))) AND  (tbl_tasks_units.task_id IN (SELECT task.id FROM tbl_tasks as task WHERE  task.task_cancel = 0 AND task.task_closed = 0 AND task.client_case_id  IN (select clientCase.id FROM tbl_client_case as clientCase WHERE  clientCase.is_close=0)))  ".$unitdatecondition." GROUP BY unit_assigned_to";
        /**************************************/
        $sql = "SELECT tbl_tasks_units.unit_assigned_to, COUNT(tbl_tasks_units.id) as totalunits, tbl_tasks_units.unit_status FROM tbl_tasks_units  LEFT JOIN tbl_servicetask ON tbl_servicetask.id = tbl_tasks_units.servicetask_id WHERE (((unit_assigned_to <> 0) AND (unit_status = 4)) AND ((tbl_tasks_units.team_id='".$team_id."') AND (tbl_tasks_units.team_loc='".$team_loc."'))) AND (tbl_tasks_units.task_id IN (SELECT task.id FROM tbl_tasks as task WHERE task.task_cancel = 0 AND task.task_closed = 0 AND task.client_case_id IN (select clientCase.id FROM tbl_client_case as clientCase WHERE clientCase.is_close=0))) ".$unitdatecondition." GROUP BY unit_assigned_to, unit_status";
        $data = \Yii::$app->db->createCommand($sql)->queryAll();
        /*********************************/

      //  echo $sql;die;
      //echo "<pre>"; print_r($data); print_r($data_notstarted); die;
        //$sql_todo = "SELECT assigned, count( * ) as totaltodos FROM tbl_tasks_units_todos as t  INNER JOIN tbl_tasks_units on tbl_tasks_units.id=t.tasks_unit_id INNER JOIN tbl_task_instruct on tbl_task_instruct.id=tbl_tasks_units.task_instruct_id WHERE tbl_task_instruct.task_id IN (SELECT task.id FROM tbl_tasks as task WHERE task.task_cancel = 0 AND task.task_closed = 0 AND task.client_case_id IN (select clientCase.id FROM tbl_client_case as clientCase WHERE clientCase.is_close=0) ) AND t.complete = 0 AND t.tasks_unit_id IN (SELECT taskunit.id FROM tbl_tasks_units as taskunit INNER JOIN  tbl_task_instruct_servicetask ON  taskunit.task_instruct_servicetask_id =  tbl_task_instruct_servicetask.id WHERE taskunit.unit_status!=4 AND taskunit.unit_assigned_to != 0 AND  tbl_task_instruct_servicetask.team_id = ".$team_id." AND tbl_task_instruct_servicetask.team_loc = ".$team_loc.")  ".$tododatecondition." GROUP BY t.assigned";
        /*$sql_todo = "SELECT tbl_tasks_units.unit_assigned_to as assigned, count( * ) as totaltodos FROM tbl_tasks_units_todos as t
        	INNER JOIN tbl_tasks_units on tbl_tasks_units.id=t.tasks_unit_id
        	INNER JOIN tbl_tasks on tbl_tasks.id=tbl_tasks_units.task_id
        	INNER JOIN tbl_client_case on tbl_client_case.id=tbl_tasks.client_case_id
        	WHERE tbl_tasks.task_cancel = 0 AND tbl_tasks.task_closed = 0 AND tbl_client_case.is_close=0 AND t.complete = 1 and tbl_tasks_units.unit_status=4 AND tbl_tasks_units.unit_assigned_to != 0 and tbl_tasks_units.team_id = ".$team_id." and tbl_tasks_units.team_loc = ".$team_loc." ".$tododatecondition." GROUP BY tbl_tasks_units.unit_assigned_to";
        */
        $sql_todo = "
        SELECT t.assigned, count( * ) as totaltodos
        FROM tbl_tasks_units_todos as t
        INNER JOIN tbl_tasks_units on tbl_tasks_units.id=t.tasks_unit_id
        INNER JOIN tbl_tasks on tbl_tasks.id=tbl_tasks_units.task_id
        INNER JOIN tbl_client_case on tbl_client_case.id=tbl_tasks.client_case_id
        WHERE tbl_tasks.task_cancel = 0 AND tbl_tasks.task_closed = 0 AND tbl_client_case.is_close=0 AND t.complete = 1
        and t.assigned!=0
        and tbl_tasks_units.team_id = ".$team_id."
        and tbl_tasks_units.team_loc = ".$team_loc." ".$tododatecondition."
        GROUP BY t.assigned";
        $data_todo=\Yii::$app->db->createCommand($sql_todo)->queryAll();
        $userList = User::find()->select(['id', 'usr_first_name','usr_lastname'])->asArray()->all();

        foreach($userList as $user)
        {
            $user_arr[$user['id']]=$user['usr_first_name'].' '.$user['usr_lastname'];
        }

        // $data=  array_merge($data_pending, $data_working);
        $graph_arr=array();
        foreach($data as $task)
        {
			$graph_arr[$task['unit_assigned_to']]['task'] = $task['totalunits'];
			if((new User)->checkAccess(5.014)) {
				$dateavail = '';
				if($date!='')
                    $dateavail = "&dates=".$date;

    				$graph_arr[$task['unit_assigned_to']]['user'] = "<a href='index.php?r=team-tasks/index&team_id=".$team_id."&team_loc=".$team_loc."&unit_assigned_to=".$task['unit_assigned_to'].$dateavail."'>".$user_arr[$task['unit_assigned_to']]."</a>";
			} else
				$graph_arr[$task['unit_assigned_to']]['user'] = $user_arr[$task['unit_assigned_to']];
        }

        foreach($data_todo as $todo)
        {
			if($graph_arr[$todo['assigned']]['user']!=''){
				$graph_arr[$todo['assigned']]['todo']=$todo['totaltodos'];
				if(!isset($graph_arr[$todo['assigned']]['user']))
				{
					if((new User)->checkAccess(5.014)){
						$dateavail = '';
						if($date!='')
							$dateavail = "&dates=".$date;

						    $graph_arr[$todo['assigned']]['user'] = "<a href='index.php?r=team-tasks/index&team_id=".$team_id."&team_loc=".$team_loc."&unit_assigned_to=".$todo['assigned']."&statusFilter=8".$dateavail."'>".$user_arr[$todo['assigned']]."</a>";

					} else
						$graph_arr[$todo['assigned']]['user'] = "<a href='javascript:void(0);'>".$user_arr[$todo['assigned']]."</a>";
				}
			}
        }

        $graph_data=array();
        $k=0;
        foreach($graph_arr as $gdata)
        {
           if(!isset($gdata['task']) || $gdata['task']=='')
               $gdata['task'] = 0;


           if(!isset($gdata['todo']) || $gdata['todo']=='')
               $gdata['todo'] = 0;

           $gdata['total'] = $gdata['task_working'] + $gdata['task_pending'] + $gdata['task_notstarted'] + $gdata['todo'];
           $graph_data[$k] = $gdata;
           $k++;
        }
        usort($graph_data, function($a, $b) {
			if($a['total']==$b['total']) return 0;
			return $a['total'] < $b['total']?1:-1;
		});
//        echo '<pre>',print_r($graph_arr);die;
        $graph_data = json_encode($graph_data);
        $teaminfo = TeamLocs::find()->where(['team_id'=>$team_id, 'team_loc'=>$team_loc])->joinWith(['team', 'teamlocationMaster'])->one();
        if(isset($post_data['type']) && $post_data['type']=='filter')
        {
			$this->layout = false;
            echo $graph_data; exit;
        }
        else
        {
		    return $this->render('taskassigncompleted', [
                'data' => $graph_data,
                'team_id' => $teaminfoam_id,'team_loc'=>$team_loc,'teaminfo'=>$teaminfo,'search_date'=>$date
            ]);
        }
    }
     /**
     * graph of task distribution by user
     * @return mixed
     */
    public function actionTaskdistribute() {
        $this->layout = "myteam";
        $team_id= Yii::$app->request->get('team_id');
        $team_loc= Yii::$app->request->get('team_loc');
        $user_model=new User();
        //echo "SELECT t.servicetask_id, count(t.id) as totalunits FROM tbl_tasks_units t WHERE t.task_id IN ( SELECT task.id FROM tbl_tasks as task WHERE task.task_cancel = 0 AND task.task_closed = 0 AND task.client_case_id IN (select clientCase.id FROM tbl_client_case as clientCase WHERE clientCase.is_close=0) ) AND t.unit_status!=4 AND t.unit_assigned_to!=0 AND t.team_id = ".$team_id." AND t.team_loc = ".$team_loc." group by t.servicetask_id ";die;
        //$sql="SELECT tbl_task_instruct_servicetask.servicetask_id, COUNT(tbl_tasks_units.id) as  totalunits FROM tbl_tasks_units LEFT JOIN  tbl_task_instruct_servicetask ON  tbl_tasks_units.task_instruct_servicetask_id =  tbl_task_instruct_servicetask.id LEFT JOIN tbl_servicetask ON  tbl_servicetask.id =  tbl_task_instruct_servicetask.servicetask_id WHERE  (((unit_assigned_to <> 0) AND (unit_status <> 4)) AND  ((tbl_task_instruct_servicetask.team_id='".$team_id."') AND  (tbl_task_instruct_servicetask.team_loc='".$team_loc."'))) AND  (tbl_tasks_units.task_id IN (SELECT task.id FROM tbl_tasks as task WHERE  task.task_cancel = 0 AND task.task_closed = 0 AND task.client_case_id  IN (select clientCase.id FROM tbl_client_case as clientCase WHERE  clientCase.is_close=0))) GROUP BY servicetask_id";

        $sql= "SELECT tbl_tasks_units.servicetask_id, COUNT(tbl_tasks_units.id) as totalunits FROM tbl_tasks_units
        LEFT JOIN tbl_servicetask ON tbl_servicetask.id = tbl_tasks_units.servicetask_id
        INNER JOIN tbl_tasks on tbl_tasks.id=tbl_tasks_units.task_id
        INNER JOIN tbl_client_case on tbl_client_case.id=tbl_tasks.client_case_id WHERE (unit_assigned_to <> 0) AND (unit_status <> 4) AND (tbl_tasks_units.team_id='".$team_id."') AND (tbl_tasks_units.team_loc='".$team_loc."') AND tbl_tasks.task_cancel = 0 and tbl_tasks.task_closed = 0 AND tbl_client_case.is_close=0 GROUP BY servicetask_id";

        //$sql="SELECT tbl_servicetask.service_task,COUNT(tbl_tasks_units.id) as  totalunits FROM tbl_servicetask LEFT JOIN tbl_task_instruct_servicetask ON  tbl_servicetask.id =  tbl_task_instruct_servicetask.servicetask_id LEFT JOIN  tbl_tasks_units ON  tbl_tasks_units.task_instruct_servicetask_id =  tbl_task_instruct_servicetask.id  WHERE  (((unit_assigned_to <> 0) AND (unit_status <> 4)) AND  ((tbl_task_instruct_servicetask.team_id='".$team_id."') AND  (tbl_task_instruct_servicetask.team_loc='".$team_loc."'))) AND  (tbl_tasks_units.task_id IN (SELECT task.id FROM tbl_tasks as task WHERE  task.task_cancel = 0 AND task.task_closed = 0 AND task.client_case_id  IN (select clientCase.id FROM tbl_client_case as clientCase WHERE  clientCase.is_close=0))) GROUP BY servicetask_id";
        $data=\Yii::$app->db->createCommand($sql)->queryAll();
        $serviceaskList = ArrayHelper::map(Servicetask::find()->where([])->asArray()->all(), 'id', 'service_task');
        $graph_data=array();
        $graph_drill_data=array();
        $i=0;
        $userList = User::find()->select(['id', 'usr_first_name','usr_lastname'])->asArray()->all();
        $user_arr=array();
        foreach($userList as $user)
        {
            $user_arr[$user['id']]=$user['usr_first_name'].' '.$user['usr_lastname'];
        }
        foreach($data as $row)
        {
            $graph_data[$i]=array('name'=>html_entity_decode($serviceaskList[$row['servicetask_id']]),'y'=> intval($row['totalunits']),'drilldown'=>html_entity_decode($serviceaskList[$row['servicetask_id']]));
            $sql1= "SELECT count(t.id) as totalunits,t.unit_assigned_to FROM tbl_tasks_units t
            WHERE t.task_id IN (SELECT task.id FROM tbl_tasks as task WHERE task.task_cancel = 0 AND task.task_closed = 0 AND task.client_case_id IN (select clientCase.id FROM tbl_client_case as clientCase WHERE clientCase.is_close=0)) AND t.unit_status!=4 AND t.unit_assigned_to!=0 AND t.team_id = ".$team_id." AND t.team_loc = ".$team_loc."  AND t.servicetask_id=".$row['servicetask_id']." group by t.unit_assigned_to";

            $drill_data=\Yii::$app->db->createCommand($sql1)->queryAll();
            $k=0;
            $graph_drill_data[$i]['name']=html_entity_decode($serviceaskList[$row['servicetask_id']]);
            $graph_drill_data[$i]['id']=html_entity_decode($serviceaskList[$row['servicetask_id']]);
            $graph_drill_data[$i]['colorByPoint']=false;
            foreach($drill_data as $entry)
            {
                if ($user_model->checkAccess(5.014) || Yii::$app->user->identity->role_id == '0') {
                	$fullname_link = '<a href="index.php?r=team-tasks/index&team_id='.$team_id.'&team_loc='.$team_loc.'&TasksUnitsSearch[unit_assigned_to][]='.$entry['unit_assigned_to'].'&TasksUnitsSearch[workflow_task]='.$row['servicetask_id'].'" style="text-decoration:none;font-weight:normal;font-size:11px;color:#167fac;">'.$user_arr[$entry['unit_assigned_to']].'</a>';
                } else {
                	$fullname_link = '<a  style="text-decoration:none;font-weight:normal;font-size:11px;color:#666;text-decoration:none;" onclick="javascript:void(0);">'.$user_arr[$entry['unit_assigned_to']].'</a>';
                }
                $graph_drill_data[$i]['data'][$k] = array($fullname_link,intval($entry['totalunits']));
                usort($graph_drill_data[$i]['data'], function($a, $b) {
					if($a['1']==$b['1']) return 0;
					return $a['1'] < $b['1']?1:-1;
				});
                $k++;
            }
            $i++;
        }
        $teaminfo = TeamLocs::find()->where(['team_id'=>$team_id,'team_loc'=>$team_loc])->joinWith(['team', 'teamlocationMaster'])->one();
        usort($graph_data, function($a, $b) {
			if($a['y']==$b['y']) return 0;
			return $a['y'] < $b['y']?1:-1;
		});

        $graph_data=json_encode($graph_data);
//        echo '<pre>';print_r($graph_drill_data);die;
        $graph_drill_data=json_encode($graph_drill_data);

        return $this->render('taskdistribute', [
            'drill_data' => $graph_drill_data,
            'data' => $graph_data,
            'team_id'=>$team_id,'team_loc'=>$team_loc,'teaminfo'=>$teaminfo
        ]);
    }

    /**
     * IRT 197
     */
    public function actionAssignbyprojectsize()
    {
    	$this->layout = "myteam";
    	$team_id= Yii::$app->request->get('team_id');
    	$team_loc= Yii::$app->request->get('team_loc');
    	$date = Yii::$app->request->get('search_date');

        $drivername = Yii::$app->db->driverName;

        $fnname="dbo.totalUnitSizeConversion1";
        $fnname1 = "dbo.unitSizeConversion";
    	$drivername = Yii::$app->db->driverName;
        $castSum = "CAST(SUM(mediaunit) as DECIMAL(32))";
        $unitTypeField = "CAST(unit_type as VARCHAR(500))";
		if($drivername == 'mysql'){
			$fnname="totalUnitSizeConversion1";
            $fnname1 = "unitSizeConversion";
            $castSum = "SUM(mediaunit)";
            $unitTypeField = "unit_type";
        }


		if($date!=''){
			$exploded = explode("/",$date);
			$date = $exploded[2]."-".$exploded[0]."-".$exploded[1];
		}

		/** get the media from project **/
    	/*$sql_evidence = "SELECT ".$fnname."1(total_size,unit,unit_type) as mediaunit, unit_assigned_to FROM (
    	SELECT * from (SELECT tbl_task_instruct_evidence.evidence_id, tbl_tasks_units.task_id, tbl_tasks_units.unit_assigned_to,
            (CASE WHEN unitmaster.unit_type IS NULL AND unitmastercomp.unit_type IS NULL THEN 0 ELSE (CASE WHEN tbl_evidence.contents_total_size <> '' THEN unitmaster.unit_type ELSE unitmastercomp.unit_type END) END) as unit_type,
            (CASE WHEN tbl_evidence.contents_total_size <> '' THEN tbl_evidence.contents_total_size ELSE tbl_evidence.contents_total_size_comp END) as total_size,
            (CASE WHEN tbl_evidence.contents_total_size <> '' THEN (CASE WHEN unit.unit_name IS NOT NULL THEN unit.unit_name ELSE (SELECT unit_name FROM tbl_unit WHERE id=tbl_evidence.unit) END) ELSE (CASE WHEN unitcomp.unit_name IS NOT NULL THEN unitcomp.unit_name ELSE (SELECT unit_name FROM tbl_unit WHERE id=tbl_evidence.comp_unit) END) END) as unit
            FROM tbl_tasks
            LEFT JOIN tbl_tasks_units ON tbl_tasks_units.task_id = tbl_tasks.id
            LEFT JOIN tbl_task_instruct ON tbl_tasks.id = tbl_task_instruct.task_id AND isactive = 1
            LEFT JOIN tbl_task_instruct_evidence ON tbl_task_instruct.id = tbl_task_instruct_evidence.task_instruct_id
            LEFT JOIN tbl_evidence ON tbl_evidence.id = tbl_task_instruct_evidence.evidence_id
            LEFT JOIN tbl_unit as unit ON tbl_evidence.unit = unit.id AND unit.remove = 0
            LEFT JOIN tbl_unit as unitcomp ON tbl_evidence.comp_unit = unitcomp.id AND unit.remove = 0
            LEFT JOIN tbl_unit_master as unitmaster ON unitmaster.unit_id = unit.id
            LEFT JOIN tbl_unit_master as unitmastercomp ON unitmastercomp.unit_id = unitcomp.id
    		WHERE tbl_tasks_units.team_id = ".$team_id." AND tbl_tasks_units.team_loc = ".$team_loc." AND unit_assigned_to!=0 AND unit_status<>4 AND (CASE WHEN tbl_evidence.contents_total_size <> '' THEN tbl_evidence.contents_total_size ELSE tbl_evidence.contents_total_size_comp END) IS NOT NULL
    	) as c GROUP BY c.task_id,c.evidence_id, c.unit_assigned_to, c.total_size, c.unit, c.unit_type) as A WHERE A.total_size IS NOT NULL";*/

    	$sql_evidence = "SELECT (CASE WHEN unit_type IN ('1','2','3') THEN $fnname1(total_unit,unit_type,0) ELSE CONCAT(total_unit,' ',unit_type) END) as mediaunit, unit_assigned_to FROM (
            SELECT $castSum as total_unit, (CASE WHEN unit_type NOT IN (1,2,3) THEN unit ELSE $unitTypeField END) as unit_type, unit_assigned_to FROM (
                SELECT (CASE WHEN unit_type IN (1,2,3) THEN $fnname(total_size,unit,unit_type) ELSE total_size END) as mediaunit, unit_type, unit, unit_assigned_to FROM (
                    SELECT * from (
                        SELECT tbl_task_instruct_evidence.evidence_id, tbl_tasks_units.task_id, tbl_tasks_units.unit_assigned_to,
                        (CASE WHEN unitmaster.unit_type IS NULL AND unitmastercomp.unit_type IS NULL THEN 0 ELSE (CASE WHEN tbl_evidence.contents_total_size <> '' THEN unitmaster.unit_type ELSE unitmastercomp.unit_type END) END) as unit_type,
                        (CASE WHEN tbl_evidence.contents_total_size <> '' THEN tbl_evidence.contents_total_size ELSE tbl_evidence.contents_total_size_comp END) as total_size,
                        (CASE WHEN tbl_evidence.contents_total_size <> '' THEN (CASE WHEN unit.unit_name IS NOT NULL THEN unit.unit_name ELSE (SELECT unit_name FROM tbl_unit WHERE id=tbl_evidence.unit) END) ELSE (CASE WHEN unitcomp.unit_name IS NOT NULL THEN unitcomp.unit_name ELSE (SELECT unit_name FROM tbl_unit WHERE id=tbl_evidence.comp_unit) END) END) as unit
                        FROM tbl_tasks
                        LEFT JOIN tbl_client_case ON tbl_tasks.client_case_id = tbl_client_case.id
                        LEFT JOIN tbl_tasks_units ON tbl_tasks_units.task_id = tbl_tasks.id
                        LEFT JOIN tbl_task_instruct ON tbl_tasks.id = tbl_task_instruct.task_id AND isactive = 1
                        LEFT JOIN tbl_task_instruct_evidence ON tbl_task_instruct.id = tbl_task_instruct_evidence.task_instruct_id
                        LEFT JOIN tbl_evidence ON tbl_evidence.id = tbl_task_instruct_evidence.evidence_id
                        LEFT JOIN tbl_unit as unit ON tbl_evidence.unit = unit.id AND unit.remove = 0
                        LEFT JOIN tbl_unit as unitcomp ON tbl_evidence.comp_unit = unitcomp.id AND unitcomp.remove = 0
                        LEFT JOIN tbl_unit_master as unitmaster ON unitmaster.unit_id = unit.id
                        LEFT JOIN tbl_unit_master as unitmastercomp ON unitmastercomp.unit_id = unitcomp.id
                        WHERE tbl_client_case.is_close=0 AND tbl_tasks.task_cancel = 0 AND tbl_tasks.task_closed = 0 AND tbl_tasks_units.team_id = ".$team_id." AND tbl_tasks_units.team_loc = ".$team_loc." AND unit_assigned_to!=0 AND unit_status<>4 AND (CASE WHEN tbl_evidence.contents_total_size <> '' THEN tbl_evidence.contents_total_size ELSE tbl_evidence.contents_total_size_comp END) IS NOT NULL

                    ) as c
                    GROUP BY c.task_id,c.evidence_id, c.unit_assigned_to, c.total_size, c.unit, c.unit_type
                ) as A
                WHERE A.total_size IS NOT NULL
            ) as B
            GROUP BY B.unit_assigned_to, CASE WHEN unit_type NOT IN (1,2,3) THEN unit ELSE $unitTypeField END
        ) as D";
        //echo "<pre>",$sql_evidence;die;
    	$data_evidence = \Yii::$app->db->createCommand($sql_evidence)->queryAll();

        $userList = User::find()->select(['id', 'usr_first_name', 'usr_lastname'])->asArray()->all();
   		$user_arr = array();
        foreach($userList as $user)
        {
            $user_arr[$user['id']] = $user['usr_first_name'].' '.$user['usr_lastname'];
        }

        $graph_arr=array();
        $unit_arr=array();
        //echo "<pre>",print_r($data_evidence);
        foreach($data_evidence as $task)
        {
        	$convert = explode(" ",$task['mediaunit']);
            $values = $convert[0];
            if(isset($convert[1]) && $convert[1]!=""){
                unset($convert[0]);
                $value1 = implode(" ",$convert);
                $graph_arr[$task['unit_assigned_to']][$value1][] = $values;

                $unit_arr[$value1] = $value1;
            }
        }

        $unitStr = implode("','",$unit_arr);
        $dataUnitSql = "SELECT unit_name, (CASE WHEN tbl_unit.remove=0 THEN unit_type ELSE 0 END) as unit_type, tbl_unit.id as unit_id FROM tbl_unit LEFT JOIN tbl_unit_master ON tbl_unit_master.unit_id = tbl_unit.id WHERE unit_name IN ('{$unitStr}')";
        $dataUnitAr = \Yii::$app->db->createCommand($dataUnitSql)->queryAll();
        $dataUnit = [];
        if(!empty($dataUnitAr)){
            foreach($dataUnitAr as $dataUnits){
                $dataUnit[$dataUnits['unit_name']]= ($dataUnits['unit_type']==1 || $dataUnits['unit_type']==2 || $dataUnits['unit_type']==3) ? 'unit='.$dataUnits['unit_type'] : 'unit=0&unit_id='.$dataUnits['unit_id'];
            }
        }
        //echo "<pre>",print_r($dataUnitAr);die;
        ksort($graph_arr);
        $graph_unit_arr = array();
        foreach($graph_arr as $key => $value){
        	foreach($value as $key1 => $val){
                //$key1 = urlencode($key1);
        		$graph_unit_arr[$key]["data"][$key1] = array_sum($val);
        	}

        	foreach ($unit_arr as $unit_type) {
                //$unit_type = urlencode($unit_type);
        		if(!isset($graph_unit_arr[$key]["data"][$unit_type])){
        			$graph_unit_arr[$key]["data"][$unit_type]=0;
        		}
        	}

        	if(!isset($graph_unit_arr[$key]['user']))
        	{
        		if((new User)->checkAccess(5.014)){
        			$dateavail = '';
        			if($date!='')
        				$dateavail = "&dates=".$date;
        			$graph_unit_arr[$key]['user'] = "<a href='index.php?r=team-tasks/index&team_id=".$team_id."&team_loc=".$team_loc."&unit_assigned_to=".$key."&onlyEvidTasks=true'>".$user_arr[$key]."</a>";

        		} else
        			$graph_unit_arr[$key]['user'] = "<a href='javascript:void(0);'>".$user_arr[$key]."</a>";
        	}
        }

        $graph_unit_array =array();
        foreach($graph_unit_arr as $key => $value) {
           krsort($value['data']);
           $graph_unit_arr[$key]['data'] = $value['data'];
           foreach($value['data'] as $keys => $value1) {
        		$graph_unit_array[$keys][]=floatval($value1);
           }
        }

        $arr=[];
        foreach ($graph_unit_array as $name => $data) {
        	$arr[]=array('name'=>$name, 'data'=>$data);
        }
        if(!empty($arr)) {
        	$graph_unit_array = $arr;
        }
        $teaminfo = TeamLocs::find()->where(['team_id' => $team_id, 'team_loc' => $team_loc])->joinWith(['team', 'teamlocationMaster'],false)->one();

        //echo "<pre>",print_r($graph_unit_arr),print_r($graph_unit_array);die;

        $graph_unit_arr = json_encode($graph_unit_arr,true);
        $graph_unit_array = json_encode($graph_unit_array,true);

        $graph_data = array();

    	return $this->render('assignbyprojectsize', [
    		'data' => $graph_unit_arr,
    		'series' => $graph_unit_array,
            'dataUnit' => json_encode($dataUnit),
    		'search_date' => $date,
    		'team_id' => $team_id, 'team_loc' => $team_loc, 'teaminfo' => $teaminfo
    	]);
    }

    public function actionFollowupdistribute()
    {
        $this->layout = "myteam";
        $team_id = Yii::$app->request->get('team_id');
        $team_loc = Yii::$app->request->get('team_loc');
        $user_model=new User();
        //echo "SELECT t.servicetask_id, count(t.id) as totalunits FROM tbl_tasks_units t WHERE t.task_id IN ( SELECT task.id FROM tbl_tasks as task WHERE task.task_cancel = 0 AND task.task_closed = 0 AND task.client_case_id IN (select clientCase.id FROM tbl_client_case as clientCase WHERE clientCase.is_close=0) ) AND t.unit_status!=4 AND t.unit_assigned_to!=0 AND t.team_id = ".$team_id." AND t.team_loc = ".$team_loc." group by t.servicetask_id ";die;
        //$sql="SELECT tbl_task_instruct_servicetask.servicetask_id, COUNT(tbl_tasks_units.id) as  totalunits FROM tbl_tasks_units LEFT JOIN  tbl_task_instruct_servicetask ON  tbl_tasks_units.task_instruct_servicetask_id =  tbl_task_instruct_servicetask.id LEFT JOIN tbl_servicetask ON  tbl_servicetask.id =  tbl_task_instruct_servicetask.servicetask_id WHERE  (((unit_assigned_to <> 0) AND (unit_status <> 4)) AND  ((tbl_task_instruct_servicetask.team_id='".$team_id."') AND  (tbl_task_instruct_servicetask.team_loc='".$team_loc."'))) AND  (tbl_tasks_units.task_id IN (SELECT task.id FROM tbl_tasks as task WHERE  task.task_cancel = 0 AND task.task_closed = 0 AND task.client_case_id  IN (select clientCase.id FROM tbl_client_case as clientCase WHERE  clientCase.is_close=0))) GROUP BY servicetask_id";
        //$sql = "SELECT todo_cat_id , count( * ) as totaltodos FROM tbl_tasks_units_todos as t WHERE t.task_id IN ( SELECT task.id FROM tbl_tasks as task WHERE task.task_cancel = 0 AND task.task_closed = 0 AND task.client_case_id IN (select clientCase.id FROM tbl_client_case as clientCase WHERE clientCase.is_close=0) ) AND t.todo_cat_id !=0 AND t.complete =0 AND t.tasks_unit_id IN (SELECT taskunit.id FROM tbl_tasks_units as  taskunit INNER JOIN  tbl_task_instruct_servicetask ON  taskunit.task_instruct_servicetask_id =  tbl_task_instruct_servicetask.id WHERE taskunit.unit_status!=4 AND taskunit.unit_assigned_to!=0 AND tbl_task_instruct_servicetask.team_id = ".$team_id." AND tbl_task_instruct_servicetask.team_loc = ".$team_loc.") GROUP BY t.todo_cat_id ";
        $sql = "SELECT todo_cat_id , count( * ) as totaltodos FROM tbl_tasks_units_todos as t
	    	INNER JOIN tbl_tasks_units on tbl_tasks_units.id=t.tasks_unit_id
	    	INNER JOIN tbl_tasks on tbl_tasks.id=tbl_tasks_units.task_id
	    	INNER JOIN tbl_client_case on tbl_client_case.id=tbl_tasks.client_case_id WHERE tbl_tasks.task_cancel = 0 AND tbl_tasks.task_closed = 0 AND tbl_client_case.is_close=0 AND t.complete = 0 and t.todo_cat_id !=0 AND tbl_tasks_units.unit_status!=4 and tbl_tasks_units.unit_assigned_to != 0 and tbl_tasks_units.team_id = ".$team_id." and tbl_tasks_units.team_loc = ".$team_loc." GROUP BY t.todo_cat_id ";
        $data=\Yii::$app->db->createCommand($sql)->queryAll();

        $graph_data=array();
        $graph_drill_data=array();
        $i=0;
        $TodocatList = Todocats::find()->select(['id', 'todo_cat','todo_desc'])->asArray()->all();

        $userList = User::find()->select(['id', 'usr_first_name','usr_lastname'])->asArray()->all();
        $user_arr=array();
        $todo_arr=array();
        foreach($userList as $user)
        {
            $user_arr[$user['id']]=$user['usr_first_name'].' '.$user['usr_lastname'];
        }
        foreach($TodocatList as $todocat)
        {
            $todo_arr[$todocat['id']]=$todocat['todo_cat'].' - '.$todocat['todo_desc'];
        }

        foreach($data as $row)
        {
             //echo "<pre>";  print_r($todo_arr);print_r($row);die;
            $graph_data[$i]=array('name'=>$todo_arr[$row['todo_cat_id']],'y'=> intval($row['totaltodos']),'drilldown'=>$todo_arr[$row['todo_cat_id']]);

            $sql1= "SELECT assigned , count(*) as totaltodos FROM tbl_tasks_units_todos as t
            INNER JOIN tbl_tasks_units ON t.tasks_unit_id = tbl_tasks_units.id
            WHERE tbl_tasks_units.task_id IN ( SELECT task.id FROM tbl_tasks as task WHERE task.task_cancel = 0 AND task.task_closed = 0 AND task.client_case_id IN (select clientCase.id FROM tbl_client_case as clientCase WHERE clientCase.is_close=0) ) AND t.todo_cat_id !=0 AND t.complete =0 AND t.tasks_unit_id IN (SELECT taskunit.id FROM tbl_tasks_units as  taskunit WHERE taskunit.unit_status!=4 AND taskunit.unit_assigned_to!=0 AND taskunit.team_id = ".$team_id." AND taskunit.team_loc = ".$team_loc.") AND t.todo_cat_id=".$row['todo_cat_id']." GROUP BY t.assigned";

            $drill_data=\Yii::$app->db->createCommand($sql1)->queryAll();
            $k=0;
            $graph_drill_data[$i]['name']=$todo_arr[$row['todo_cat_id']];
            $graph_drill_data[$i]['id']=$todo_arr[$row['todo_cat_id']];
            $graph_drill_data[$i]['colorByPoint']=false;
            foreach($drill_data as $entry)
            {
                if ($user_model->checkAccess(5.014) || Yii::$app->user->identity->role_id == '0') {
                     $fullname_link = '<a href="index.php?r=team-tasks/index&team_id='.$team_id.'&team_loc='.$team_loc.'&unit_assigned_to='.$entry['assigned'].'&statusFilter=8&followcat_id='.$row['todo_cat_id'].'&source=todofollowup" style="font-size:11px;color:#167fac;">'.$user_arr[$entry['assigned']].'</a>';
                } else {
                     $fullname_link = '<a style="font-size:11px;color:#666;">'.$user_arr[$entry['assigned']].'</a>';
                }
                $graph_drill_data[$i]['data'][$k] = array($fullname_link, intval($entry['totaltodos']));
				usort($graph_drill_data[$i]['data'], function($a, $b) {
					if($a['1']==$b['1']) return 0;
						return $a['1'] < $b['1']?1:-1;
					});
                $k++;
            }
            $i++;
        }
        $teaminfo = TeamLocs::find()->where(['team_id' => $team_id,'team_loc' => $team_loc])->joinWith(['team','teamlocationMaster'])->one();
        usort($graph_data, function($a, $b) {
			if($a['y']==$b['y']) return 0;
			return $a['y'] < $b['y']?1:-1;
		});

		$graph_data=json_encode($graph_data, true);
        $graph_drill_data=json_encode($graph_drill_data, true);

        return $this->render('followupdistribute', [
            'drill_data' => $graph_drill_data,
            'data' => $graph_data,
            'team_id'=>$team_id,'team_loc'=>$team_loc,'teaminfo'=>$teaminfo
        ]);
        //echo "<pre>"; print_r($graph_data); print_r($graph_drill_data);die;
     }
    /**
     * Finds the MyDocument model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return MyDocument the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Mydocument::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
