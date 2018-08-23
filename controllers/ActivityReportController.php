<?php

namespace app\controllers;

use Yii;
use app\models\Unit;
use app\models\Tasks;
use app\models\search\UnitSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;


/**
 * ActivityReportController implements the CRUD actions for  model.
 */
class ActivityReportController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['get'],
                ],
            ],
        ];
    }

    /**
     * Activity Report Layout
     */
    public function actionIndex()
    {
        $this->layout = 'report';
    	return $this->render('index');
    }
    /**
     * Check Project Exist or Not.
     * 
     * */
    public function actionTaskexist(){
	
		$task_id = Yii::$app->request->post('task_id');
		$data = Tasks::find()->select(['id'])->where('id = '.$task_id)->one();
		if(empty($data)){
			die('Please Adjust Your Process:-The Project # does not exist in the application.  Please enter an existing Project # to generate the report.');
		}die('OK');
	}
	/*
	 * Get the data of Selected Project.
	 * */
	public function actionRunprojecttransactiviyreport(){
		
		$task_id = Yii::$app->request->post('task_id');
		
		$parameters = Yii::$app->params['activities'];
	
		$sql1 = "SELECT t.service_name,ser.service_task,s.id as servicetask_id,trans.duration,trans.transaction_type,trans.transaction_date,trans.created as trans_created,CONCAT(usr.usr_first_name,' ',usr.usr_lastname) as transaction_to,task.task_status,task.task_complete_date,task.id,task.task_cancel,task.created,task.modified,CONCAT(us.usr_first_name,' ',us.usr_lastname) as project_by,CONCAT(t_by.usr_first_name,' ',t_by.usr_lastname) as transaction_by from tbl_tasks_units as u 
		RIGHT JOIN tbl_task_instruct_servicetask as s ON u.task_instruct_servicetask_id = s.id 
		LEFT JOIN tbl_tasks_units_transaction_log as trans ON trans.tasks_unit_id = u.id 
		LEFT JOIN tbl_user as usr ON trans.user_assigned = usr.id 
		INNER JOIN tbl_teamservice as t ON s.teamservice_id = t.id 
		INNER JOIN tbl_servicetask as ser ON s.servicetask_id = ser.id 
		INNER JOIN tbl_task_instruct as ins ON s.task_instruct_id = ins.id 
		INNER JOIN tbl_tasks as task ON u.task_id = task.id 
		INNER JOIN tbl_user as us ON task.created_by = us.id 
		LEFT JOIN tbl_user as t_by ON trans.created_by = t_by.id
		where u.task_id = :tasks_id AND ins.isactive = 1 order by s.sort_order ASC";
		
		$activity_report = \Yii::$app->db->createCommand($sql1,[ ':tasks_id' => $task_id ] )->queryAll();
		
		$this->renderPartial('runprojtransactivityreport',['task_id' => $task_id,'parameters' => $parameters,'activity_report'=>$activity_report]);
	}
   
}
