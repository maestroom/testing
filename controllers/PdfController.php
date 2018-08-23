<?php
namespace app\controllers;
use Yii; 
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\Session;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
 
use kartik\mpdf\Pdf;
use app\models\EvidenceCustodians;
use app\models\EvidenceCustodiansForms;
use app\models\FormBuilder;
use app\models\FormCustodianValues;
use app\models\ClientCase;
use app\models\EvidenceTransaction;
use app\models\TaskInstructServicetask;
use app\models\Tasks;
use app\models\TaskInstruct;
use app\models\InvoiceFinal;
use app\models\Settings;
use app\models\ProjectSecurity;
use app\models\Role;
use app\models\Servicetask;
use app\models\ClientCaseEvidence;
use yii\data\ArrayDataProvider;
use app\models\search\EvidenceProductionSearch;
use app\models\EvidenceProductionMedia;
use app\models\EvidenceProduction;
use app\models\ProjectRequestType;
use app\models\TasksUnits;
use app\models\TasksUnitsTransactionLog;
use app\models\TasksUnitsTodos;
use app\models\TasksUnitsTodoTransactionLog;
use app\models\TeamlocationMaster;
use app\models\User;
use app\models\Team;
use app\models\Options;
use app\models\TaskInstructEvidence;
use app\models\InvoiceFinalBilling;
use app\models\Client;
use app\models\ClientContacts;
use app\models\Unit;
use app\models\ReportsChartFormat;
use app\models\ReportsReportType;
use app\models\ReportsReportTypeFields;

use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Border;
use PHPExcel_Style_Alignment;
use PHPExcel_Shared_Font;
use PHPExcel_Worksheet_Drawing;

class PdfController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['get'],
                ],
            ],
        ];
    }
    
    public function actionExcelWithChart(){
	
		$table_data = Yii::$app->request->post('table_data');
		$image_data = Yii::$app->request->post('image_data');
		$filename = "ProjectsbyClientCase_" . date('m_d_Y', time()) . ".xls";
		//echo $data = $image_data;die;

		$tempDir = '/temp';
		$img = $image_data;
		$img = str_replace('data:image/jpeg;base64,', '', $img);
		$img = str_replace(' ', '+', $img);
		$data = base64_decode($img);
		$file = sys_get_temp_dir() .'/'. uniqid() . '.png';
		file_put_contents($file, $data);
		$objPHPExcel     = new \PHPExcel();
		$activeSheet = $objPHPExcel->getActiveSheet();
		// Add an image to the worksheet
		$objDrawing = new PHPExcel_Worksheet_Drawing();
		$objDrawing->setName('Image');
		$objDrawing->setDescription('Image');
		$objDrawing->setPath($file);
		$objDrawing->setCoordinates('B'.($i+2));
		$objDrawing->setWorksheet($activeSheet);
/**/
		// Save the workbook
		$objPHPExcelWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007');
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); // header for .xlxs file
		header('Content-Disposition: attachment;filename='.$filename); // specify the download file name
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
		//$objPHPExcelWriter->save($fileName);

	
	}
    public function actionExportwithchart()
    {
		  $post_data  = Yii::$app->request->post();
		  $datatable_location = Yii::$app->request->post('datatable_location');
		  $table_data = Yii::$app->request->post('table_data');
		  $image_data = Yii::$app->request->post('image_data');
		  $title_location = Yii::$app->request->post('title_location');
		  $legend_location = Yii::$app->request->post('legend_location');
		  $title = Yii::$app->request->post('title');
		  $pdf = Yii::$app->pdf;
		  $mpdf = $pdf->api;
		  $html=$this->renderPartial('exportchartwithtable', [
				'table_data'=>json_decode($table_data,true),
				'image_data'=>$image_data,
				'datatable_location'=>$datatable_location,
				'title_location'=>$title_location,
				'legend_location' => $legend_location,
				'title'=>$title,
			]);
			//echo $html;die;
		 	$pdf->destination = 'D';
			$pdf->filename = 'ChartReport_'.date("m_d_Y").'.pdf';
			$mpdf->WriteHTML($html);
			return $pdf->render();
	}
	public function actionCaseSummary(){
		$case_id = Yii::$app->request->get('case_id');
		$caseModel = ClientCase::findOne($case_id);
		$model = \app\models\ClientCaseSummary::find()->where(['client_case_id'=>$case_id])->one();
		$pdf = Yii::$app->pdf;
		$mpdf = $pdf->api;
		$html=Yii::$app->view->renderFile('@app/views/case/case-summary.php', [
		'model'=>$model,
		'caseModel'=>$caseModel,
		'case_id'=>$case_id,
		'flag'=>'pdf'
		]);
		$pdf->destination = 'D';
		$pdf->filename = 'CaseSummary_'.date("m_d_Y").'.pdf';
		$mpdf->WriteHTML($html);
		return $pdf->render();
	}

    /**
     * Use to download PDF of selected Custodian with Client and Inetrview Information
     * */
    public function actionCustodiantIneterview($id,$case_id) {
    	$client_data = ClientCase::findOne($case_id);
    	$custodiants['cust_data'] = EvidenceCustodians::find()->where('cust_id IN ('.$id.')')->all();
    	foreach ($custodiants['cust_data'] as $cust) {
    		$getCustomFromId=FormBuilder::find()->joinWith('formCustodianValues formCustodianValues')->select('formref_id')->where(['form_type'=>3,'formCustodianValues.cust_id'=>$cust->cust_id])->one();
			$from_id=(isset($getCustomFromId->formref_id)?$getCustomFromId->formref_id:0);
			$custodiants['formbuilder_data'][$cust->cust_id] = (new FormBuilder)->getFromData($from_id,3,'ASC','formbuilder',$cust_id,'front');
			//$formbuilder_data = $formbuilder_data->getFromData($id,3,'DESC','formvalues',$cust_id,'front');
			//$custodiants['formValues'][$cust->cust_id]=ArrayHelper::map(FormCustodianValues::find()->select(['form_builder_id','element_value'])->where(['cust_id'=>$cust->cust_id])->all(),'form_builder_id','element_value');
			$formcustval = FormCustodianValues::find()->select(['form_builder_id','element_value','element_unit'])->where(['cust_id'=>$cust->cust_id])->all();
			//$formValues = array();
			//$unitValues = array();
			if(!empty($formcustval)) {
				foreach($formcustval as $custval) {
					$custodiants['formValues'][$cust->cust_id][$custval['form_builder_id']] = $custval['element_value'];
					if($custval['element_unit']!=0) {
						$custodiants['unitValues'][$cust->cust_id][$custval['form_builder_id']] = Unit::findOne($custval['element_unit'])->unit_name;
					} else {
						$custodiants['unitValues'][$cust->cust_id][$custval['form_builder_id']] = '';
					}
				}
			}
    	}
    	//echo "<pre>"; print_r($custodiants['formbuilder_data']); exit;
    	$pdf = Yii::$app->pdf;
    	$mpdf = $pdf->api;
    	$html=$this->renderPartial('CustodiantIneterview', [
    			'custodiants'=>$custodiants,
    			'client_data'=>$client_data,
    			'id'=>$from_id,
    			'cust_id'=>$id
    	]);
    	//echo $html;
    	$mpdf->WriteHTML($html);
    	echo $mpdf->Output("CustodianInterviewForms.pdf","D");
    }
    /**
     * Use to download PDF of selected Custodian with Client and Inetrview Information
     * */
    public function actionCaseBudget($case_id)
    {
     	$this->enableCsrfValidation = false;

    	$case_info = ClientCase::findOne($case_id);
    	$caseSpendPerProject = array();
    	$task_data = Tasks::find()->where('client_case_id In (' . $case_id . ')')->select('id')->orderBy('created desc')->all();
    	$total = 0;	$invoiced_total=0; $pending_total=0; $main_total=0;
    	foreach ($task_data as $tdata) {
    		$invoiced = (new InvoiceFinal)->invoicedBillInvoice($tdata->id);
    		$pending  = (new InvoiceFinal)->pendingBillInvoice($tdata->id);
    		if ($invoiced != 0 || $pending != 0) {
    			$task_ids[$tdata->id] = $tdata->id;
    			$caseSpendPerProject[] = array(
    					'project_id' => $tdata->id,
    					'project_name' => $tdata->activeTaskInstruct->project_name,
    					'invoiced' => $invoiced,
    					'pending' => $pending,
    					'total_spent'=> $pending+$invoiced,
    			);
    			$invoiced_total+=$invoiced;
    			$pending_total+=$pending;
    			$total = $total + ($invoiced + $pending);
    		}
    	}
    	if(!empty($caseSpendPerProject)){
    		$caseSpendPerProject['total'] = array(
    				'project_id' => 'Spend Totals',
    				'project_name' => '',
    				'invoiced' => $invoiced_total,
    				'pending' => $pending_total,
    				'total_spent'=> $total,
    		);
    	}
    	$dataProvider = new ArrayDataProvider([
    			'allModels' => $caseSpendPerProject,
    			'pagination' => [
    					'pageSize' => '-1',
    			],
    			'sort' => [
    					'attributes' => ['project_id', 'project_name','invoiced','pending'],
    			],
    	]);
    	
    	// get the rows in the currently requested page
        $pdf = Yii::$app->pdf; 
		$mpdf = $pdf->api; 
		
		
		# Load a stylesheet
	//	$cssfile = '@basedir/css/pdf-bootstrap.css';
		$inline_css = file_get_contents(Yii::getAlias('@basedir').'/css/bootstrap-style.css');
		
	//	$pdf->cssFile = $cssfile;
		$pdf->cssInline = $inline_css.".row div{  font-family: Arial;font-size: 12px;line-height: 1.42857;}.row { margin-left: -7px; margin-right: -7px;}label {display: inline-block;font-weight: bold;margin-bottom: 5px;max-width: 100%;font-family: Arial;font-size: 12px;line-height: 1.42857;}";
		
		
		$pdf->destination = 'D';
		$pdf->filename = 'CaseBudget.pdf';
		$pdf->content = $this->renderPartial('CaseBudget',['case_info'=>$case_info,'pdfimage'=>$_POST['pdfimage'],'caseSpendPerProject'=>$caseSpendPerProject,'dataProvider'=>$dataProvider, 'total' => $total]);
		//die();
		return $pdf->render();
    }
    
	public function actionRunmyreport($task_id){
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
		$pdf = Yii::$app->pdf; 
		$mpdf = $pdf->api; 
		$mpdf->WriteHTML($this->renderPartial('runmyreport',['task_id' => $task_id,'parameters' => $parameters,'activity_report'=>$activity_report]));
	    echo $mpdf->Output("TaskReport.pdf","D");
	}
	public function actionChainofcustody($id){
		$parameters = Yii::$app->params;
		
		$model = new EvidenceTransaction();
        //$params= Yii::$app->request->post();
        
        $model->evid_num_id = $id;
        $evidtrans = EvidenceTransaction::find()->where(['evid_num_id'=>$id])->joinWith(['transby','transRequstedby','storedLoc','evidenceTo','evidence'])
                  ->orderBy(['trans_date'=>SORT_DESC])->all();
        
        
        $clientCaseEvidences_data = ClientCaseEvidence::find()->joinWith(['clientcase' => function(\yii\db\ActiveQuery $query) {
			$query->joinWith('client');
		}])->where(["evid_num_id"=>$id])->orderBy(['tbl_client.client_name'=>SORT_ASC,'tbl_client_case.case_name'=>SORT_ASC])->select(['tbl_client_case.client_id', 'client_case_id'])->all();
	    $casename_arr=array();
		foreach($clientCaseEvidences_data as $clientcase){ 
           $case_name= $clientcase->clientcase->case_name;
           //$clientcase->'case_name'=$case_name;
           $clientCaseEvidences[$clientcase->clientcase->client_id]['client_name']= $clientcase->clientcase->client->client_name;

		   if(!in_array($clientcase->clientcase->case_name,$casename_arr)){
				if(isset($clientCaseEvidences[$clientcase->clientcase->client_id]['case_name']))
					$clientCaseEvidences[$clientcase->clientcase->client_id]['case_name']=$clientCaseEvidences[$clientcase->clientcase->client_id]['case_name'].','. $clientcase->clientcase->case_name;
				else
					$clientCaseEvidences[$clientcase->clientcase->client_id]['case_name']= $clientcase->clientcase->case_name; 
				
				$casename_arr[$clientcase->clientcase->case_name]=$clientcase->clientcase->case_name;  
		   }
        }
		$pdf = Yii::$app->pdf; 
                //$pdf->orientation = 'P';
		$mpdf = $pdf->api; 
		$html = $this->renderPartial('chainofcustody',['id' => $id,'parameters' => $parameters,'evidtrans'=>$evidtrans,'model'=>$model,'clientCaseEvidences'=>$clientCaseEvidences]);
		//echo $html;die;
		$mpdf->WriteHTML($html,'P');
	    echo $mpdf->Output("ChainOfCustody.pdf","D");
	}

    /**
     * Instruction PDF by instruction ID
     * */
	public function actionInstructionpdf($id){
		$model = TaskInstruct::findOne($id);
		$old_instruction = TaskInstruct::find()->where('task_id = '.$model->task_id.' AND instruct_version = '.($model->instruct_version-1))->one();
		$old_instruction_id = $old_instruction->id;
		$oldversion = $old_instruction->instruct_version;
		$changeFBIds=array();
		if(isset($old_instruction_id) && $old_instruction_id!=""){
			$changeFBIds = (new Tasks)->getChangedFBID($model->task_id,$oldversion);
		}
		$project_request_type = ArrayHelper::map(ProjectRequestType::find()->orderBy('request_type ASC')->all(),'id','request_type');
		$project_track_data = (new TaskInstructServicetask)->getTrackProjectDataByInstructionId($id);
		$task_instructions_data = $project_track_data->getModels();
		$task_id = $model->task_id;
		$task_data = Tasks::findOne($task_id);
		$settings_data_footer = Settings::find()->where("field IN ('instruction_footer')")->one();
		$settings_data_header = Settings::find()->where("field IN ('instruction_header')")->one();
		
		foreach($task_instructions_data as $key => $val)
		{
			$servicetask_id = $val['servicetask_id'];
			$taskunit_id   = $val['taskunit_id'];
			$sort_order    = $val['sort_order'];
			$teamId        = $val['teamId'];
			$team_loc      = $val['team_loc'];
			$processTrackData[$key]['media'] 	= (new TaskInstructServicetask)->processTrackMedia($servicetask_id,$sort_order,$task_id,$case_id,$team_id,$taskunit_id,$id,$options);
			$processTrackData[$key]['task_instructions'] 	= (new TaskInstructServicetask)->processTrackInstruction($servicetask_id,$sort_order,$task_id,$case_id,$team_id,$taskunit_id,$id,$options);
		}
		//echo "<pre>",print_r($processTrackData);
		$acces_team_arr 	= (new ProjectSecurity)->getUserTeamsArr(Yii::$app->user->identity->id);
		$acces_team_loc_arr = (new ProjectSecurity)->getUserTeamsLocArr(Yii::$app->user->identity->id);
		
		$roleId             = Yii::$app->user->identity->role_id;
		$roleInfo=Role::findOne($roleId);
		$User_Role=explode(',',$roleInfo->role_type);
		if($roleId=='0'){ //if super user all access
			$acces_team_arr[1] = 1;
		}if(in_array(1,$User_Role)){
			$acces_team_arr[1] = 1;
		}
		$belongtocurr_team_serarr = (new Servicetask)->getBelongto(Yii::$app->user->identity->id);
		$stlocaccess = (new Servicetask)->getBelongtoLoc(Yii::$app->user->identity->id);
		$html = $this->renderPartial('view-instructions',[
				'task_instructions_data'=>$task_instructions_data,
				'task_id' => $task_id,
				'settings_data_footer' => $settings_data_footer,
				'settings_data_header' => $settings_data_header,
				'task_data' => $task_data,
				'stlocaccess'=>$stlocaccess,
				'processTrackData'=>$processTrackData,
				'servicetask_id'=>$servicetask_id,
				'teamId'=>$teamId,
				'instruct_id'=>$id,
				'team_loc'=>$team_loc,
				'belongtocurr_team_serarr'=>$belongtocurr_team_serarr,
				'model'=>$model,
				'project_request_type'=>$project_request_type,
				'old_instruction_id'=>$old_instruction_id,
				'changeFBIds'=>$changeFBIds
		]);
		$pdf = Yii::$app->pdf;
		$mpdf = $pdf->api;
		# Load a stylesheet
		$inline_css = file_get_contents(Yii::getAlias('@basedir').'/css/bootstrap-style.css');
		$pdf->cssInline = $inline_css.".row div{  font-family: Arial;font-size: 12px;line-height: 1.42857;}.row { margin-left: -7px; margin-right: -7px;}label {display: inline-block;font-weight: bold;margin-bottom: 5px;max-width: 100%;font-family: Arial;font-size: 12px;line-height: 1.42857;}";
		$pdf->destination = 'D';
		$pdf->filename = 'taskInstructions.pdf';
		$pdf->content = $html;
		//echo $html;die;
		return $pdf->render();
	}
	public function actionTaskInstructions($task_id)
	{
		$model = TaskInstruct::find()->where('task_id = '.$task_id.' AND isactive = 1')->one();
		$duedate = Yii::$app->request->get('duedate','');
		$project_track_data = (new TaskInstructServicetask)->getTrackProjectDataWithMedia($task_id);
		$task_instructions_data = $project_track_data->getModels();
		$project_request_type = ArrayHelper::map(ProjectRequestType::find()->select(['id','request_type'])->orderBy('request_type ASC')->all(),'id','request_type');
		/*$task_data=Tasks::find()->joinWith(['taskInstruct' => function (\yii\db\ActiveQuery $query) {
		
		$query->select(['tbl_task_instruct.id','tbl_task_instruct.instruct_version','tbl_task_instruct.task_duedate','tbl_task_instruct.task_timedue','tbl_task_instruct.project_name','tbl_task_instruct.requestor','tbl_task_instruct.task_projectreqtype','tbl_task_instruct.task_priority'])
		->where('isactive=1')
		->joinWith(['taskPriority'=>function(\yii\db\ActiveQuery $query){
			 $query->select(['tbl_priority_project.priority']);
		}])
		->joinWith(['taskInstructEvidences'=> function(\yii\db\ActiveQuery $query){
				$query->select(['tbl_task_instruct_evidence.id','tbl_task_instruct_evidence.prod_id','tbl_task_instruct_evidence.task_instruct_id'])->joinWith('evidenceProduction');
			}]);
		}, 'createdUser'=>function(\yii\db\ActiveQuery $query){
			 $query->select(['createdUser.usr_first_name','createdUser.usr_lastname']);
		}, 
		 'clientCase' => function (\yii\db\ActiveQuery $query) { 
			 $query->select(['tbl_client_case.id','tbl_client_case.case_name','tbl_client_case.client_id','tbl_client_case.case_manager','tbl_client_case.sales_user_id','tbl_client_case.internal_ref_no'])->joinWith('salesRepo'); 
		 }])
		 ->where(['tbl_task_instruct.task_id' => $task_id])->one();*/
		 if($duedate == '') {
			$timezoneOffset = (new Options)->getOffsetOfCurrenttimeZone();
			if (Yii::$app->db->driverName == 'mysql') {
				$data_query_sql = "SELECT getDueDateTimeByUsersTimezone('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%m/%d/%Y %h:%i %p')";
			} else {
				$data_query_sql = "SELECT [dbo].getDueDateTimeByUsersTimezone('{$timezoneOffset}',tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,'%m/%d/%Y %h:%i %p')";
			}
		 } else {
			 $data_query_sql = "CONCAT(tbl_task_instruct.task_duedate,'',tbl_task_instruct.task_timedue)";
		 }
		 $taskdata_sql="SELECT 
		 tbl_tasks.id,tbl_tasks.created as submitted_date,(".$data_query_sql.") as task_date_time,tbl_task_instruct.id as instruction_id,tbl_task_instruct.instruct_version,tbl_task_instruct.task_duedate,tbl_task_instruct.task_timedue,tbl_task_instruct.project_name,tbl_task_instruct.requestor,tbl_task_instruct.task_projectreqtype,tbl_task_instruct.task_priority,
		 tbl_priority_project.priority,
		 createdUser.usr_first_name as taskcreate_fn,createdUser.usr_lastname as taskcreate_ln,
		 tbl_user.usr_first_name as salserepofn,tbl_user.usr_lastname as salserepoln,
		 tbl_client.client_name,
		 tbl_client_case.id as client_case_id,tbl_client_case.case_name,tbl_client_case.client_id,tbl_client_case.case_manager,tbl_client_case.sales_user_id,tbl_client_case.internal_ref_no
		 FROM tbl_tasks 
		 LEFT JOIN tbl_task_instruct ON tbl_tasks.id = tbl_task_instruct.task_id 
		 LEFT JOIN tbl_priority_project ON tbl_task_instruct.task_priority = tbl_priority_project.id 
		 LEFT JOIN tbl_task_instruct_evidence ON tbl_task_instruct.id = tbl_task_instruct_evidence.task_instruct_id 
		 LEFT JOIN tbl_evidence_production ON tbl_task_instruct_evidence.prod_id = tbl_evidence_production.id 
		 LEFT JOIN tbl_user createdUser ON tbl_tasks.created_by = createdUser.id 
		 LEFT JOIN tbl_client_case ON tbl_tasks.client_case_id = tbl_client_case.id 
		 LEFT JOIN tbl_client ON tbl_client.id = tbl_client_case.client_id 
		 LEFT JOIN tbl_user ON tbl_client_case.sales_user_id = tbl_user.id 
		 WHERE (tbl_task_instruct.task_id='".$task_id."') AND (isactive=1)";
		$taskdata = Yii::$app->db->createCommand($taskdata_sql)->queryOne();
		$old_instruction_id = TaskInstruct::find()->select('id')->where('task_id = '.$task_id.' AND instruct_version = '.($taskdata['instruct_version']-1))->one()->id;
		$settings_data_footer = Settings::find()->where("field IN ('instruction_footer')")->one();
		$settings_data_header = Settings::find()->where("field IN ('instruction_header')")->one();
		foreach($task_instructions_data as $key => $val){
			// echo "<pre>",print_r($val),"</pre>";die;
		    $servicetask_id = $val['servicetask_id'];
		    $taskunit_id   = $val['taskunit_id'];
		    $sort_order    = $val['sort_order'];
		    $teamId        = $val['teamId'];
		    $team_loc      = $val['team_loc'];
		    $processTrackData[$key] 	= (new TaskInstructServicetask)->processTrackDataInstruction($servicetask_id,$sort_order,$task_id,0,$teamId,$taskunit_id,$options);
			//(new TaskInstructServicetask)->processTrackData($servicetask_id,$sort_order,$task_id,0,$teamId,$taskunit_id,$options);
		}
		$acces_team_arr 	= (new ProjectSecurity)->getUserTeamsArr(Yii::$app->user->identity->id);
    	$acces_team_loc_arr = (new ProjectSecurity)->getUserTeamsLocArr(Yii::$app->user->identity->id);
    	$roleId             = Yii::$app->user->identity->role_id;
    	$roleInfo=Role::findOne($roleId);
    	$User_Role=explode(',',$roleInfo->role_type);
    	if($roleId=='0') { 
    		$acces_team_arr[1] = 1;
    	} if(in_array(1,$User_Role)) {
    		$acces_team_arr[1] = 1;
    	}
    	$belongtocurr_team_serarr = (new Servicetask)->getBelongto(Yii::$app->user->identity->id);
    	$stlocaccess = (new Servicetask)->getBelongtoLoc(Yii::$app->user->identity->id);
    	$changeFBIds=array();
        $prev_instruction=array();
        $instruction_evidence=array();
		$cnt_instruction_evidence = 0;
    	if(isset($old_instruction_id)){
    		$changeFBIds = (new Tasks)->getChangedFBID($task_id);
            $prev_instruction=TaskInstruct::find()->where('task_id = '.$task_id.' AND instruct_version = '.($taskdata["instruct_version"]-1))->one()->toArray();
            $cnt_instruction_evidence=TaskInstructEvidence::find()->select(['evidence_id'])->where('task_instruct_id IN('.$taskdata["instruction_id"].','.$prev_instruction["id"].')')->groupBy('evidence_id')->having('COUNT(evidence_id) != 2')->count();
        } 
		$html = $this->renderPartial('view-task-instructions',[
		    'task_instructions_data'=>$task_instructions_data,
		    'task_id' => $task_id,
		    'settings_data_footer' => $settings_data_footer,
			'settings_data_header' => $settings_data_header,
		    'task_data' => $task_data,
			'taskdata'=>$taskdata,
			'duedate'=>$duedate,
		    'stlocaccess'=>$stlocaccess,
		    'processTrackData'=>$processTrackData,
		    'servicetask_id'=>$servicetask_id,
		    'teamId'=>$teamId,
		    'team_loc'=>$team_loc,
		    'belongtocurr_team_serarr'=>$belongtocurr_team_serarr,
		    'project_request_type' => $project_request_type,
            'old_instruction_id'=>$old_instruction_id,
		    'changeFBIds'=>$changeFBIds,
            'prev_instruction'=>$prev_instruction,
            'cnt_instruction_evidence'=>$cnt_instruction_evidence,
			'model'=>$model
		]);
		//die;
		$pdf = Yii::$app->pdf; 
		$mpdf = $pdf->api; 
		$mpdf->useSubstitutions = false;
		$mpdf->simpleTables = true;
		# Load a stylesheet
	//	$cssfile = '@basedir/css/pdf-bootstrap.css';
		$inline_css = file_get_contents(Yii::getAlias('@basedir').'/css/bootstrap-style.css');
	//	$pdf->cssFile = $cssfile;
		//$pdf->cssInline = $inline_css.".row div{  font-family: Arial;font-size: 12px;line-height: 1.42857;}.row { margin-left: -7px; margin-right: -7px;}label {display: inline-block;font-weight: bold;margin-bottom: 5px;max-width: 100%;font-family: Arial;font-size: 12px;line-height: 1.42857;}";
		//$pdf->destination = 'D';
		//$pdf->filename = 'taskInstructions.pdf';
		//$pdf->content = $html;
		//$executionEndTime = microtime(true);
 
//The result will be in seconds and milliseconds.
//$seconds = $executionEndTime - $executionStartTime;
 
//Print it out
//echo "This script took $seconds to execute.";
//die;
        $mpdf->WriteHtml($html); // call mpdf write html
        echo $mpdf->Output('taskInstructions.pdf', 'D'); 
		return ;//$pdf->render();
	}  

    function actionRunproductionexcel()
    {
        $this->layout=false;
        $params = Yii::$app->request->post();
		
        $searchModel = new EvidenceProductionSearch();
        $session = Yii::$app->session;
        $filter_arr=$session->get('filter_arr');
        $filter_arr['EvidenceProductionSearch']['case_id']=$params['case_id'];
        $params['EvidenceProductionSearch']['case_id']=$params['case_id'];            
        $dataProvider = $searchModel->searchpdf($params['EvidenceProductionSearch']);
        $query = $dataProvider->query;
        $data = $query->all();
        //echo "<pre>",print_r($params),print_r($data),"</pre>";die;
        //echo "<pre>";print_r($data);die;
        $case_data = ClientCase::findOne($params['case_id']);            
	    foreach($data as $production) {
	        $prod_data = EvidenceProductionMedia::find()->joinWith(['prodbates'=>function (\yii\db\ActiveQuery $query) { $query->joinWith(['task'],false);},'proevidence'=>function (\yii\db\ActiveQuery $query) { $query->joinWith(['evidenceunit','evidencecompunit','evidencetype','evidencecontent'=>function (\yii\db\ActiveQuery $query) { $query->joinWith(['evidenceCustodians'],false);}],false);}],false)->where(['tbl_evidence_production_media.prod_id' => $production->id])->all();
	        $prod_media_data[$production->id]=$prod_data;
	        if(!empty($prod_data)) {
	            foreach ($prod_data as $mids) {
	                if($mids->evid_id ==0){continue;}
	                    if(!empty($mids->prodbates)) {
	                        foreach ($mids->prodbates as $prodbates) {
	                            if(!empty($prodbates->task)) {
	                                $pr_link= (new EvidenceProduction)->getProjectsLink($prodbates->task->id);
	                                $task_arr[$prodbates->task->id]=$pr_link; 
	                        	}
	                    	}
	                	}
	            	}
	        	}
	    	}	 
        $filename = "CaseProductionLog_" . date('m_d_Y', time()) . ".xls";
        $styleArray = array(
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                    )
                );
        $objPHPExcel     = new \PHPExcel();
        $activeSheet =  $objPHPExcel->getActiveSheet();
        $activeSheet->SetCellValue('A'.'1','DOCUMENT PRODUCTION LOG');
        $activeSheet->SetCellValue('A'.'2','Case Name');
        $activeSheet->SetCellValue('B'.'2',strtoupper(html_entity_decode($case_data->client->client_name . ' - ' . $case_data->case_name)));
        
        $activeSheet->SetCellValue('A'.'4','Production Type');
        $activeSheet->SetCellValue('B'.'4','Staff Assigned');
        $activeSheet->SetCellValue('C'.'4','Production Date');
        $activeSheet->SetCellValue('D'.'4','Date Received');
        $activeSheet->SetCellValue('E'.'4','Producing Party');
        $activeSheet->SetCellValue('F'.'4','Production Description');
        $activeSheet->SetCellValue('G'.'4','Cover Letter Link');
        $activeSheet->SetCellValue('H'.'4','Project #');
        $activeSheet->SetCellValue('I'.'4','Media Type');
        $activeSheet->SetCellValue('J'.'4','Media Description');
        $activeSheet->SetCellValue('K'.'4','Media Label');
        $activeSheet->SetCellValue('L'.'4','Media Custodians');
        $activeSheet->SetCellValue('M'.'4','Media Quantity');
        $activeSheet->SetCellValue('N'.'4','Media Size');
        $activeSheet->SetCellValue('O'.'4','Media Size Compressed');
        $activeSheet->SetCellValue('P'.'4','Begin Bates');
        $activeSheet->SetCellValue('Q'.'4','End Bates');
        $activeSheet->SetCellValue('R'.'4','Prod Begin Bates');
        $activeSheet->SetCellValue('S'.'4','Prod End Bates');
        $activeSheet->SetCellValue('T'.'4','Prod Date Loaded');
        $activeSheet->SetCellValue('U'.'4','Production Contains Originals');
        $activeSheet->SetCellValue('V'.'4','Return Production');
        $activeSheet->SetCellValue('W'.'4','Attorney Notes');
        $activeSheet->SetCellValue('X'.'4','Produced in Initial Disclosures');
        $activeSheet->SetCellValue('Y'.'4','Produced to Other Agencies');
        $activeSheet->SetCellValue('Z'.'4','Access Request');
        $activeSheet->SetCellValue('AA'.'4','Misc1');
        $activeSheet->SetCellValue('AB'.'4','Misc2');
        $user_model=new User();
        $merge_array=array();
        if (count($data) > 0) {
            $j = 5;
            foreach ($data as $logdata) {
                $tdcount = 1;
                if (isset($logdata->has_media) && ($logdata->has_media)) {
                    $tdcount = count($media_ids);
                    if (!empty($media_bates_new))
                        $tdcount = count($media_bates_new);
                }
                if ($logdata->production_type == 1)
                    $production_type="Incoming";
                else
                    $production_type="Outgoing";
                $prod_agencies='';
                if (date("Y-m-d", strtotime($logdata->prod_agencies)) != '1970-01-01' && date('m-d-Y', strtotime($logdata->prod_agencies))!='11-30--0001')
                     $prod_agencies=date('m/d/Y', strtotime($logdata->prod_agencies));
                $prod_access_req='';
                if (date("Y-m-d", strtotime($logdata->prod_access_req)) != '1970-01-01' && date('m-d-Y', strtotime($logdata->prod_access_req))!='11-30--0001')
                     $prod_access_req=date('m/d/Y', strtotime($logdata->prod_access_req));
                
                $activeSheet->SetCellValue('A'.$j,$production_type);
                $activeSheet->SetCellValue('B'.$j,$logdata->staff_assigned);
                
                $prod_date = (new EvidenceProduction)->getProdDate($logdata->prod_date); //date('m/d/Y', strtotime($logdata->prod_date))
                
                $activeSheet->SetCellValue('C'.$j,$prod_date);
                $activeSheet->SetCellValue('D'.$j,date('m/d/Y', strtotime($logdata->prod_rec_date)));
                $activeSheet->SetCellValue('E'.$j,$logdata->prod_party);
                $activeSheet->SetCellValue('F'.$j,$logdata->production_desc);
                //$activeSheet->SetCellValue('G'.$j,strip_tags($logdata->cover_let_link));
                $activeSheet->SetCellValue('G'.$j,$logdata->cover_let_link);
                $k=0;
                $m=0;
                if (isset($logdata->has_media) && ($logdata->has_media)) {
                    $media_ids=array();
                    if (!empty($prod_media_data[$logdata->id])) {
                        $k=$j;
                        $merge_array[$j]= $k;
                        foreach ($prod_media_data[$logdata->id] as $prod) {
                            if (!empty($prod->prodbates)) {
                            foreach ($prod->prodbates as $m_new) {
                                if (!empty($m_new->task)){ 
                                    if($user_model->checkAccess(4.01)){
                                      if(isset($task_arr[$m_new->task_id]))
                                            $prj_no=strip_tags ($task_arr[$m_new->task_id]);
                                    } else {
                                            $prj_no=$m_new->task_id; 
                                    }
                                }
                                $custodians='';
                                $media_size='';
                                $media_size_comp='';
                                $bbates='';
                                $ebates='';
                                $prodbates='';
                                $prodebates='';
                                $prod_date_loaded='';
                                foreach($prod->proevidence->evidencecontent as $econtents){
                                    $custodians.=$econtents->evidenceCustodians->cust_lname . ", " . $econtents->evidenceCustodians->cust_fname . " " . $econtents->evidenceCustodians->cust_mi.' ';
                                 } 
                                if ($prod->proevidence->contents_total_size != "" && $prod->proevidence->contents_total_size != 0) 
                                    $media_size=$prod->proevidence->contents_total_size . " " . $prod->proevidence->evidenceunit->unit_name;
                                if ($prod->proevidence->contents_total_size_comp != "" && $prod->proevidence->contents_total_size_comp != 0)
                                    $media_size_comp=$prod->proevidence->contents_total_size_comp . " " . $prod->proevidence->evidencecompunit->unit_name; 
                                if (isset($prod->proevidence->bbates) && $prod->proevidence->bbates != "")
                                    $bbates=$prod->proevidence->bbates;
                                if (isset($prod->proevidence->ebates) && $prod->proevidence->ebates != "")
                                   $ebates=$prod->proevidence->ebates;
                                if (isset($m_new->prod_bbates) && $m_new->prod_bbates != "")
                                    $prodbates=$m_new->prod_bbates;
                                if (isset($m_new->prod_ebates) && $m_new->prod_ebates != "")
                                    $prodebates=$m_new->prod_ebates;
                                if (isset($m_new->prod_date_loaded) && date("Y-m-d", strtotime($m_new->prod_date_loaded)) != "1970-01-01")
                                    $prod_date_loaded=date('m/d/Y', strtotime($m_new->prod_date_loaded));
                                                        
                                $activeSheet->SetCellValue('H'.$k,$prj_no);
                                $activeSheet->SetCellValue('I'.$k,$prod->proevidence->evidencetype->evidence_name);
                                $activeSheet->SetCellValue('J'.$k,$prod->proevidence->evid_desc);
                                $activeSheet->SetCellValue('K'.$k,$prod->proevidence->evid_label_desc);
                                $activeSheet->SetCellValue('L'.$k,$custodians);
                                $activeSheet->SetCellValue('M'.$k,$prod->proevidence->quantity);
                                $activeSheet->SetCellValue('N'.$k,$media_size);
                                $activeSheet->SetCellValue('O'.$k,$media_size_comp);
                                $activeSheet->SetCellValue('P'.$k,$bbates );
                                $activeSheet->SetCellValue('Q'.$k,$ebates);
                                $activeSheet->SetCellValue('R'.$k,$prodbates);
                                $activeSheet->SetCellValue('S'.$k,$prodebates);
                                $activeSheet->SetCellValue('T'.$k,$prod_date_loaded);
                                $k++;
                                $m++;
                                }
                            }
                            else
                            {
                                $custodians='';
                                $media_size='';
                                $media_size_comp='';
                                $bbates='';
                                $ebates='';
                                $prodbates='';
                                $prodebates='';
                                $prod_date_loaded='';
                                foreach($prod->proevidence->evidencecontent as $econtents){
                                    $custodians.=$econtents->evidenceCustodians->cust_lname . ", " . $econtents->evidenceCustodians->cust_fname . " " . $econtents->evidenceCustodians->cust_mi.' ';
                                 } 
                                if ($prod->proevidence->contents_total_size != "" && $prod->proevidence->contents_total_size != 0) 
                                    $media_size=$prod->proevidence->contents_total_size . " " . $prod->proevidence->evidenceunit->unit_name;
                                if ($prod->proevidence->contents_total_size_comp != "" && $prod->proevidence->contents_total_size_comp != 0)
                                    $media_size_comp=$prod->proevidence->contents_total_size_comp . " " . $prod->proevidence->evidencecompunit->unit_name; 
                                if (isset($prod->proevidence->bbates) && $prod->proevidence->bbates != "")
                                    $bbates=$prod->proevidence->bbates;
                                if (isset($prod->proevidence->ebates) && $prod->proevidence->ebates != "")
                                   $ebates=$prod->proevidence->ebates;
                                
                                                        
                                $activeSheet->SetCellValue('H'.$k,'');
                                $activeSheet->SetCellValue('I'.$k,$prod->proevidence->evidencetype->evidence_name);
                                $activeSheet->SetCellValue('J'.$k,$prod->proevidence->evid_desc );
                                $activeSheet->SetCellValue('K'.$k,$prod->proevidence->evid_label_desc);
                                $activeSheet->SetCellValue('L'.$k,$custodians);
                                $activeSheet->SetCellValue('M'.$k,$prod->proevidence->quantity);
                                $activeSheet->SetCellValue('N'.$k,$media_size);
                                $activeSheet->SetCellValue('O'.$k,$media_size_comp);
                                $activeSheet->SetCellValue('P'.$k,$bbates );
                                $activeSheet->SetCellValue('Q'.$k,$ebates);
                                $activeSheet->SetCellValue('R'.$k,'');
                                $activeSheet->SetCellValue('S'.$k,'');
                                $activeSheet->SetCellValue('T'.$k,'');
                                $k++;
                                $m++;
                            }
                        }
                        
                    }
                }
                
                
                
                if($k>$j)
                {
                    $merge_array[$j]= $k-1;
                    $j=$k-1;
                }
                
                
                $n = $j;
                if($m != 0){
                   $n = $k - $m;
                }
                
                //echo "j :".$j." ; k :".$k." ; m: ".$m."notes :".$logdata->attorney_notes."<br>";
                
                /*$activeSheet->SetCellValue('U'.$j,$logdata->prod_orig == 1 ? "Yes" : "No");
                $activeSheet->SetCellValue('V'.$j,$logdata->prod_return == 1 ? "Yes" : "No");
                $activeSheet->SetCellValue('W'.$j,$logdata->attorney_notes.'--'.$j.'---'.$k);
                */
                
                $activeSheet->SetCellValue('U'.$n,$logdata->prod_orig == 1 ? "Yes" : "No");
                $activeSheet->SetCellValue('V'.$n,$logdata->prod_return == 1 ? "Yes" : "No");
                $activeSheet->SetCellValue('W'.$n,$logdata->attorney_notes);
                
                $activeSheet->SetCellValue('X'.$j,$logdata->prod_disclose);
                $activeSheet->SetCellValue('Y'.$j,$prod_agencies);
                $activeSheet->SetCellValue('Z'.$j,$prod_access_req);
                $activeSheet->SetCellValue('AA'.$j,$logdata->prod_misc1);
                $activeSheet->SetCellValue('AB'.$j,$logdata->prod_misc2);
                
           $j++; }
        }
        if(!empty($merge_array))
        {
            foreach($merge_array as $key=>$val)
            {
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells("A$key:A$val");
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells("B$key:B$val");
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells("C$key:C$val");
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells("D$key:D$val");
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells("E$key:E$val");
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells("F$key:F$val");
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells("G$key:G$val");
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells("U$key:U$val");
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells("V$key:V$val");
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells("W$key:W$val");
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells("X$key:X$val");
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells("Y$key:Y$val");
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells("Z$key:Z$val");
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells("AA$key:AA$val");
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells("AB$key:AB$val");
            }
        }
        $activeSheet->getStyle("A1:AB".($key+2))->applyFromArray($styleArray, False);
        
        $activeSheet->getColumnDimension('A')->setAutoSize(true);
        $activeSheet->getColumnDimension('B')->setAutoSize(true);
        $activeSheet->getColumnDimension('C')->setAutoSize(true);
        $activeSheet->getColumnDimension('D')->setAutoSize(true);
        $activeSheet->getColumnDimension('E')->setAutoSize(true);
        $activeSheet->getColumnDimension('F')->setAutoSize(true);
        $activeSheet->getColumnDimension('G')->setAutoSize(true);
        $activeSheet->getColumnDimension('H')->setAutoSize(true);
        $activeSheet->getColumnDimension('I')->setAutoSize(true);
        $activeSheet->getColumnDimension('J')->setAutoSize(true);
        $activeSheet->getColumnDimension('K')->setAutoSize(true);
        $activeSheet->getColumnDimension('L')->setAutoSize(true);
        $activeSheet->getColumnDimension('M')->setAutoSize(true);
        $activeSheet->getColumnDimension('N')->setAutoSize(true);
        $activeSheet->getColumnDimension('O')->setAutoSize(true);
        $activeSheet->getColumnDimension('P')->setAutoSize(true);
        $activeSheet->getColumnDimension('Q')->setAutoSize(true);
        $activeSheet->getColumnDimension('R')->setAutoSize(true);
        $activeSheet->getColumnDimension('S')->setAutoSize(true);
        $activeSheet->getColumnDimension('T')->setAutoSize(true);
        $activeSheet->getColumnDimension('U')->setAutoSize(true);
        $activeSheet->getColumnDimension('V')->setAutoSize(true);
        $activeSheet->getColumnDimension('W')->setAutoSize(true);
        $activeSheet->getColumnDimension('X')->setAutoSize(true);
        $activeSheet->getColumnDimension('Y')->setAutoSize(true);
        $activeSheet->getColumnDimension('Z')->setAutoSize(true);
        $activeSheet->getColumnDimension('AA')->setAutoSize(true);
        $activeSheet->getColumnDimension('AB')->setAutoSize(true);
        
        
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); // header for .xlxs file
        header('Content-Disposition: attachment;filename='.$filename); // specify the download file name
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
      exit;
    }
        function actionRunproductionexcel1(){
            $this->layout=false;
            $params = Yii::$app->request->get();
            $searchModel = new EvidenceProductionSearch();
            $session = Yii::$app->session;
            $filter_arr=$session->get('filter_arr');
            $filter_arr['EvidenceProductionSearch']['case_id']=$params['case_id'];
            echo "<pre>";print_r($filter_arr);die;
            $dataProvider = $searchModel->searchpdf($filter_arr['EvidenceProductionSearch']);
            $data=$dataProvider->query->all();
            $case_data = ClientCase::findOne($params['case_id']);
            //foreach($data as )
            foreach($data as $production)
            {
                $prod_data = EvidenceProductionMedia::find()->joinWith(['prodbates'=>function (\yii\db\ActiveQuery $query) { $query->joinWith(['task']);},'proevidence'=>function (\yii\db\ActiveQuery $query) { $query->joinWith(['evidenceunit','evidencecompunit','evidencetype','evidencecontent'=>function (\yii\db\ActiveQuery $query) { $query->joinWith(['evidenceCustodians']);}]);}])->where(['tbl_evidence_production_media.prod_id'=>$production->id])->all();
                $prod_media_data[$production->id]=$prod_data;
                if(!empty($prod_data))
                {
                    foreach ($prod_data as $mids) {
                        if($mids->evid_id ==0)
                               continue;
                        if(!empty($mids->prodbates))	
                        {
                            foreach ($mids->prodbates as $prodbates) {
                                if(!empty($prodbates->task))
                                {
                                        $pr_link= (new EvidenceProduction)->getProjectsLink($prodbates->task->id);	
                                        $task_arr[$prodbates->task->id]=$pr_link; 
                                }

                            }
                        }

                    }
                }
            }
            $filename = "CaseProductionLog_" . date('m_d_Y', time()) . ".xls";
            $content = $this->renderPartial('excelreport-production',['productionlogdata'=>$data,'prod_media_data'=>$prod_media_data,'task_arr'=>$task_arr,'case_data'=>$case_data]);
          //  $content="<table><tr><td>test</td></tr></table>";
            $table    = $content;
            //echo $content;die;
            $tmpfile = tempnam(sys_get_temp_dir(), 'html');
            file_put_contents($tmpfile, $table);
            
            $objPHPExcel     = new PHPExcel(); 
            $excelHTMLReader = PHPExcel_IOFactory::createReader('HTML');
            $excelHTMLReader->loadIntoExisting($tmpfile, $objPHPExcel);
            $objPHPExcel->getDefaultStyle()
                        ->getBorders()
                        ->getRight()
                        ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
           
            unlink($tmpfile); // delete temporary file because it isn't needed anymore
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); // header for .xlxs file
            header('Content-Disposition: attachment;filename='.$filename); // specify the download file name
            header('Cache-Control: max-age=0');
            // Creates a writer to output the $objPHPExcel's content
            $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $writer->save('php://output');
          exit;
            
            return $this->render('excelreport-production', ['productionlogdata'=>$data,'prod_media_data'=>$prod_media_data,'task_arr'=>$task_arr,'case_data'=>$case_data]);
        }
       
	/**
     * It will generate PDF for Est Report by project ID.
     * On Back button User will lend to below pages as per below criteria
     * If User Lend from Case Projects Main Grid URL will be like : r=case-projects/est-report&caseId=2&task_id=97&qryString=index
     * If User Lend from Cancel Projects Grid URL will be like : r=case-projects/est-report&caseId=2&task_id=97&qryString=load-canceled-projects
     * If User Lend from Closed Projects Grid URL will be like : r=case-projects/est-report&caseId=2&task_id=97&qryString=load-closed-projects
     * If User Lend from Track Project Section URL will be like : r=case-projects/est-report&caseId=2&task_id=97&qryString=track/index
     */    
    public function actionEstReport()
    {
    	$qryString = Yii::$app->request->get('querystr','');
    	$case_id = Yii::$app->request->get('case_id',0);
    	$team_id = Yii::$app->request->get('team_id',0);
    	$team_loc = Yii::$app->request->get('team_loc',0);
		$task_id = Yii::$app->request->get('task_id',0);
		$pdfimage = Yii::$app->request->post('pdfimage','');
		if($case_id!=0){
			$type = "case";
			$this->layout = "mycase";
			$datamodel = ClientCase::findOne($case_id);
		} else {
			$type = "team"; 
			$this->layout = "myteam";
			$datamodel = Team::find()->with([
				'teamLocs'=>function(\yii\db\ActiveQuery $query) use($team_loc){
					$query->where(['team_loc'=>$team_loc]);
				}
			])->where(['id'=>$team_id])->one();
		}
		
		if ($datamodel !== null && ($taskmodel = Tasks::findOne($task_id)) !== null) {
				
			$taskinstruct =  TaskInstruct::find()->with([
				'taskInstructServicetasks' => function(\yii\db\ActiveQuery $query){
					$query->orderBy('sort_order');
				} 
			])->where(['task_id' => $task_id, 'isactive' => 1])->one();
			
			$submitted_date = $taskmodel->created;
	        $duedatetime = $taskinstruct->task_duedate . " " . $taskinstruct->task_timedue;
	        $hourdiff = round(abs((strtotime($duedatetime) - strtotime($submitted_date)) / 3600));
	        $est_times = 0;
	        $est_hours = 0;
	        $actual_times = 0;
	        $actual_hours = 0;
	        $projected_time = 0;
	
	        if ($hourdiff > 0)
	            $projected_time = round($hourdiff);

	        $actualtimes = array();
	        $servicetaskinfo = $taskinstruct->taskInstructServicetasks;
	        $serviceest_data = array();
	        //echo "<pre>",print_r($est_info),"</pre>";    die;
			if (!empty($servicetaskinfo)) {
	            foreach ($servicetaskinfo as $servicetaskdata) {
	                if (isset($servicetaskdata->est_time) && $servicetaskdata->est_time != "") {
	                    $est_hours = $est_hours + $servicetaskdata->est_time;
	                    $stask_id = $servicetaskdata->servicetask_id;
	                    $serviceest_data[$servicetaskdata->servicetask_id] = $servicetaskdata->est_time;
	                }
	                
		            /*$percomplete = 0;
		            $getallserviceTaskUnits = TasksUnits::find()->where(['task_id' => $task_id,'task_instruct_servicetask_id'=>$servicetaskdata->id])->all();
		            if (!empty($getallserviceTaskUnits)) {
		                foreach ($getallserviceTaskUnits as $unit_data) {
		                    $unit_status = $unit_data->unit_status;
		                    $services[$servicetaskdata->servicetask_id] = $servicetaskdata->servicetask_id;
		                    $tasktranslogArr = TasksUnitsTransactionLog::find()->select(['duration'])->where(['tasks_unit_id' =>$unit_data->id])->all();
		                    $taskunittodologArr = TasksUnitsTodoTransactionLog::find()->select(['duration'])->where(['tasks_unit_id' => $unit_data->id])->all();
		                    $todoactualhours = 0;
		                    $taskactualhours = 0;
		                    $totalactualhours = 0;
		                    if ($unit_status == "4") {
		                        if (count($taskunittodologArr) > 0) {
		                            foreach ($taskunittodologArr as $tasktodologdata) {
		                                $tododuration = $tasktodologdata->duration;
		                                $tododurationArr = explode(' ', $tododuration);
		                                $todohours = floor($tododurationArr[4] / 60);
		                                $todohours = $todohours + $tododurationArr[2];
		                                $todototalhours = floor(($tododurationArr[0] * 86400) / 3600) + $todohours;
		                                $todoactualhours = $todoactualhours + $todototalhours;
		                            }
		                        }
		                        if (count($tasktranslogArr) > 0) {
		                            foreach ($tasktranslogArr as $tasktranslogdata) {
		                                $duration = $tasktranslogdata->duration;
		                                $durationArr = explode(' ', $duration);
		                                $hours = floor($durationArr[4] / 60);
		                                $hours = $hours + $durationArr[2];
		                                $totalhours = floor(($durationArr[0] * 86400) / 3600) + $hours;
		                                $taskactualhours = $taskactualhours + $totalhours;
		                            }
		                        }
		                    }
		                    $totalactualhours = $taskactualhours + $todoactualhours;
		                    $actual_hours = $actual_hours + $totalactualhours;
		                }
		            }*/
	            }
	        }
	        if ($est_hours > 0)
	            $est_times = round($est_hours);
	        
	        /*if ($actual_hours > 0)
	            $actual_times = round($actual_hours);*/
	        
//	        $teamLocation = ArrayHelper::map(TeamlocationMaster::find()->orderBy('team_location_name ASC')->where(['remove'=>0])->all(), 'id', 'team_location_name');
//	        $myfinal_arr = (new TasksUnits)->getTrackTaskProgress($servicetaskinfo, $task_id, $taskmodel->task_status, $est_hours, $serviceest_data, $case_id, $team_id, $team_loc, $type, $teamLocation, 'pdf');
                $tasksUnitsData = (new TasksUnits)->getTasksUnitsDetails($task_id,$case_id,$team_id,$taskmodel->task_status,$type, 'pdf');
	        $catAr = array('categories'=>['Projected','Actual']);
	        $serAr = array('series'=>array(['name'=>'Hours','colorByPoint'=>true,'data'=>[['Projected',$projected_time],['Actual',$tasksUnitsData['actual_times']]]]));
	        
                $categories = json_encode($catAr);
                $series = json_encode($serAr);
                // get the rows in the currently requested page
	        $pdf = Yii::$app->pdf; 
                $mpdf = $pdf->api; 

                # Load a stylesheet
                $inline_css = file_get_contents(Yii::getAlias('@basedir').'/css/font-awesome.min.css');
                $inline_css .= file_get_contents(Yii::getAlias('@basedir').'/css/isatask.css');
                $inline_css .= ".row div{  font-family: Arial;font-size: 12px;line-height: 1.42857;}.row { margin-left: -7px; margin-right: -7px;}label {display: inline-block;font-weight: bold;margin-bottom: 5px;max-width: 100%;font-family: Arial;font-size: 12px;line-height: 1.42857;}i{font-family:fontawesome;}";

                $pdf->cssInline = $inline_css; 
                $pdf->destination = 'D';
                $pdf->filename = 'EstReport.pdf';
                //echo 
                $html = $this->renderPartial('est-report', [
                        'case_id' => $case_id,
                        'team_id' => $team_id,
                        'team_loc' => $team_loc,
                        'type' => $type,
                        'task_id' => $task_id,
                        'pdfimage'=> $pdfimage,
                        'taskmodel' => $taskmodel,
                        'taskinstruct' => $taskinstruct,
                        'categories' => $categories,
                        'series' => $series,
                        'inline_css' => $inline_css,
                        'myfinal_arr' => $tasksUnitsData['arrResult'],
                        'est_hours' => $est_hours,
                ]);
                $pdf->content = $html;
                //echo "<pre>",print_r($pdf),"</pre>";
                //die;
                return $pdf->render();
		
		} else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    public function actionEvent()
    {
    	$image_data=Yii::$app->request->post('image_data');
    	$center_datevalue = Yii::$app->request->post('center_datevalue');
    	//echo "<img src='".$image_data."'  />";die; 
    	
    	$pdf = Yii::$app->pdf;
    	
    	$mpdf = $pdf->api;
    	//$mpdf->showImageErrors = true;
    	# Load a stylesheet
    	$inline_css = file_get_contents(Yii::getAlias('@basedir').'/vendor/bower/fullcalendar/dist/fullcalendar.min.css');
		$pdf->cssInline = $inline_css.".row div{  font-family: Arial;font-size: 12px;line-height: 1.42857;}.row { margin-left: -7px; margin-right: -7px;}label {display: inline-block;font-weight: bold;margin-bottom: 5px;max-width: 100%;font-family: Arial;font-size: 12px;line-height: 1.42857;}";
		$pdf->destination = 'D';
     	$pdf->filename = 'Calendar.pdf';
     	$html = "<div style='text-align:center; font-size:7px; font-family:arial; background:#c52d2e; color:#FFF; font-weight:bold; margin:0px; padding:7px; text-align:center;'><h2 style='margin:0px;'>My Events Calendar</h2></div>";
     	$html.= '<div style="text-align:center; font-size:7px; font-family:arial; background:#e9e7e8; color:#333; font-weight:bold; margin:0px; padding:5px; text-align:center;"><h2 style="margin:0px;">'.$center_datevalue.'</h2></div>'; 
     	$html.= "<img src='".$image_data."' />";
     	//echo "<pre>"; echo $html; exit;
     	$mpdf->WriteHTML($html);
     	return $pdf->render();

    }
    
    /**
	 * Finalized billing with / with out summary notes
	 * @param invoiced_id (int)
	 * @param showsummarynote (int)
	 */
	 public function actionPdfInvoice()
	 {
		$this->layout = 'billing';
		
		$invoicedId = Yii::$app->request->get('invoice_id');
		$summarynote = Yii::$app->request->get('showsummarynote', 0);
		$invoicesId = explode(",",$invoicedId);
		$html='';
		foreach($invoicesId as $invoicedId){
			$invoice = InvoiceFinal::find()->where(['id'=>$invoicedId])->asArray()->one();
			$invoice['created_date'] = (new Options)->ConvertOneTzToAnotherTz($invoice['created_date'],'UTC',$_SESSION['usrTZ'],'date');
			
			$preview = InvoiceFinalBilling::find()->joinWith([
				'invoiceFinal',
				'invoiceFinalTaxes',
				'billingUnit' => function(\yii\db\ActiveQuery $query){
					$query->where('invoiced != 2');
					$query->joinWith([
							'tasksUnits'=>function(\yii\db\ActiveQuery $query){
							//$query->select(['task_id']);
							$query->joinWith(['tasks'=>function(\yii\db\ActiveQuery $query){
							$query->select(['tbl_tasks.id','tbl_tasks.client_case_id']);
							$query->joinWith([
								'clientCase'=>function(\yii\db\ActiveQuery $query){
									$query->select(['case_name','case_matter_no','counsel_name','sales_user_id','tbl_client_case.id','client_id']);	
									$query->joinWith([
										'salesRepo'=>function(\yii\db\ActiveQuery $query){
											$query->select(['usr_first_name','usr_lastname']);
										},
										'client'=>function(\yii\db\ActiveQuery $query){
											$query->select(['client_name','tbl_client.id']);
										}
									]);
								},
							]);
							}]);
						},
						'pricing'=>function(\yii\db\ActiveQuery $query){
							$query->joinWith(['unit','pricingUtbmsCodes']);
						}
					]);
				}
			])->where(['invoice_final_id'=>$invoicedId])->asArray()->all();
			
			$taskunitbillingdata1 = array();
			$summarydata = array();
			$taxcodes = array();
			$taxcodewiseAr = array();
			$cases = array();
			if(!empty($preview)){
				$dataArray = array();
				$invoiceArray = array();
				
				foreach($preview as $taskval1){
					//echo "<pre>",print_r($taskval1);die;	
					
					$invoiceArray['invoice_final_id'] = $taskval1['invoice_final_id'];
					$invoiceArray['invoice_created'] = (new Options)->ConvertOneTzToAnotherTz($taskval1['invoiceFinal']['created_date'],'UTC',$_SESSION['usrTZ'],'date');
					$invoiceArray['billing_unit_id'] = $taskval1['billing_unit_id'];
					$invoiceArray['team_loc'] = $taskval1['team_loc'];
					$invoiceArray['final_rate'] = number_format($taskval1['final_rate'],2,'.','');
					$invoiceArray['discount'] = $taskval1['discount'];
					$invoiceArray['discount_reason'] = $taskval1['discount_reason'];
					$invoiceArray['internal_ref_no_id'] = $taskval1['internal_ref_no_id'];
					$invoiceArray['task_id'] = $taskval1['billingUnit']['tasksUnits']['task_id'];
					$invoiceArray['pricing_id'] = $taskval1['billingUnit']['pricing_id'];
					$invoiceArray['quantity'] = number_format(round($taskval1['billingUnit']['quantity'],2),2,'.','');
					$invoiceArray['billing_desc'] = $taskval1['billingUnit']['billing_desc'];
					$invoiceArray['client_id'] = $taskval1['billingUnit']['tasksUnits']['tasks']['clientCase']['client_id'];
					$invoiceArray['client_name'] = $taskval1['billingUnit']['tasksUnits']['tasks']['clientCase']['client']['client_name'];
					$invoiceArray['invoiced'] = $taskval1['billingUnit']['invoiced'];
					$invoiceArray['client_case_id'] = $taskval1['billingUnit']['tasksUnits']['tasks']['client_case_id'];
					$invoiceArray['case_name'] = $taskval1['billingUnit']['tasksUnits']['tasks']['clientCase']['case_name'];
					$invoiceArray['case_matter_no'] = $taskval1['billingUnit']['tasksUnits']['tasks']['clientCase']['case_matter_no'];
					$invoiceArray['counsel_name'] = $taskval1['billingUnit']['tasksUnits']['tasks']['clientCase']['counsel_name'];
					$invoiceArray['sales_user_id'] = $taskval1['billingUnit']['tasksUnits']['tasks']['clientCase']['sales_user_id'];
					$invoiceArray['sales_user_name'] = !empty($taskval1['billingUnit']['tasksUnits']['tasks']['clientCase']['salesRepo'])?$taskval1['billingUnit']['tasksUnits']['tasks']['clientCase']['salesRepo']['usr_first_name']." ".$taskval1['billingUnit']['tasksUnits']['tasks']['clientCase']['salesRepo']['usr_lastname']:'';
					$invoiceArray['price_point'] = $taskval1['billingUnit']['pricing']['price_point'];
					$invoiceArray['utbms_code'] = !empty($taskval1['billingUnit']['pricing']['pricingUtbmsCodes'])?$taskval1['billingUnit']['pricing']['pricingUtbmsCodes']['code']:'';
					$invoiceArray['unit_price_id'] = $taskval1['billingUnit']['pricing']['unit_price_id'];
					$invoiceArray['unit_name'] = $taskval1['billingUnit']['pricing']['unit']['unit_name'];
					$invoiceArray['pricing_description'] = $taskval1['billingUnit']['pricing']['description'];
					$invoiceArray['pricing_cust_desc_template'] = $taskval1['billingUnit']['pricing']['cust_desc_template'];
					$invoiceArray['pricing_is_custom'] = $taskval1['billingUnit']['pricing']['is_custom'];
					$invoiceArray['unit_created'] = (new Options)->ConvertOneTzToAnotherTz($taskval1['billingUnit']['created'],'UTC',$_SESSION['usrTZ'],'date');
					$invoiceArray['invoiceFinalTaxes'] = $taskval1['invoiceFinalTaxes'];
					$cases[$invoiceArray['client_case_id']] = $invoiceArray['client_case_id'];
					$summarydata[$invoiceArray['client_case_id']][] = $invoiceArray; 
					if(!empty($dataArray)){
						$index = (new InvoiceFinal)->findIfSamePPAdded($invoiceArray, $dataArray);
						if(!empty($index)){
							$dataArray[$index['key']]['billing_unit_id'] .= ",".$invoiceArray['billing_unit_id'];
							$dataArray[$index['key']]['quantity'] += $invoiceArray['quantity'];
						} else {
							$dataArray[] = $invoiceArray;
						}
					} else {
						$dataArray[] = $invoiceArray;
					}
				}
				foreach($dataArray as $taskval1){
					$taskunitbillingdata1[$taskval1['client_case_id']][] = $taskval1;
					//echo "<pre>",print_r($taskunitbillingdata1);
				}
				/* Start : If Range PP then rate will be on the bases of total of all same unit along with same pricepoint of same location */
				  if (!empty($taskunitbillingdata1)) {
						foreach ($taskunitbillingdata1 as $keysclientcase => $values){
							foreach ($values as $k => $value){
								$clientcase = explode("||",$keysclientcase);
								$clientdata = explode("=",$clientcase[0]);
								$casedata = explode("=",$clientcase[1]);
								$keysclient = $clientdata[0];
								$keyscase = $casedata[0];
								
								$rate = number_format($value['final_rate'],2,'.','');
								$subtotal = $rate * round($value['quantity'],2);							
								$taskunitbillingdata1[$keysclientcase][$k]['rate'] = $rate;
								$taskunitbillingdata1[$keysclientcase][$k]['subtotal'] = $subtotal;
								//$taskunitbillingdata1[$keysclientcase]['subtotal'] += $subtotal; 
								if(!empty($value['invoiceFinalTaxes'])){
									foreach($value['invoiceFinalTaxes'] as $tax){
										$taxcodewise = number_format(($tax['rate']/100)*$subtotal,2,'.','');
										$taxcodes[$tax['code']] = number_format($tax['rate'],2,'.','');
										$taxcodewiseAr[$tax['code']] += $taxcodewise;
									}
								}  
							}
						}
					}
				/* End : If Range PP then rate will be on the bases of total of all same unit along with same pricepoint of same location */
			}
			
			$clientData = Client::find()->where(['id'=>$invoice['client_id']])->asArray()->one();
			/*$clientcaseData = ClientCase::find()->joinWith([
				'salesRepo'=>function(\yii\db\ActiveQuery $query){
					$query->select(['tbl_user.id','usr_first_name','usr_lastname']);
				}
			])->where(['tbl_client_case.id'=>$invoice['client_case_id']])->asArray()->one();*/
			
			$clientcaseData = array();
			if(!empty($cases)){
				foreach($cases as $case){	
					$clientcaseData[$case] = ClientCase::find()->joinWith([
						'salesRepo'=>function(\yii\db\ActiveQuery $query){
							$query->select(['tbl_user.id','usr_first_name','usr_lastname']);
						}
					])->where(['tbl_client_case.id'=>$case])->asArray()->one();
				}
			}
			
			$contactData = ClientContacts::find()->where(['id'=>$invoice['contact_id']])->asArray()->one();
			$teamLocation = ArrayHelper::map(TeamlocationMaster::find()->select(['id','team_location_name'])->orderBy('team_location_name ASC')->where('remove=0 OR id=0')->all(),'id','team_location_name');
			 
			$pdf = Yii::$app->pdf; 
			$mpdf = $pdf->api; 
			
			# Load a stylesheet
			//$inline_css = file_get_contents(Yii::getAlias('@basedir').'/css/font-awesome.min.css');
			//$inline_css .= file_get_contents(Yii::getAlias('@basedir').'/css/isatask.css');
			//$inline_css .= ".row div{font-family: Arial;font-size: 12px;line-height: 1.42857;}.row { margin-left: -7px; margin-right: -7px;}label {display: inline-block;font-weight: bold;margin-bottom: 5px;max-width: 100%;font-family: Arial;font-size: 12px;line-height: 1.42857;}i{font-family:fontawesome;}";
			
			//$pdf->cssInline = $inline_css; 
			$pdf->destination = 'D';
			$pdf->filename = 'Invoice.pdf';
			
			//echo "<pre>",print_r($summarydata),"</pre>";die;
			if($html!='') {
				$html .= '<div style="page-break-before:always">&nbsp;</div>'.$this->renderPartial('pdfinvoice',['invoice'=>$invoice,'summarynote' => $summarynote,'taskunitbillingdata1'=>$taskunitbillingdata1, 'taxcodes' => $taxcodes, 'taxcodewiseAr'=>$taxcodewiseAr, 'summarydata'=>$summarydata, 'display_by' => $preview->display_by, 'clientData' => $clientData, 'clientcaseData' => $clientcaseData, 'contactData' => $contactData, 'teamLocation'=>$teamLocation]);
			} else {
				$html = $this->renderPartial('pdfinvoice',['invoice'=>$invoice,'summarynote' => $summarynote,'taskunitbillingdata1'=>$taskunitbillingdata1, 'taxcodes' => $taxcodes, 'taxcodewiseAr'=>$taxcodewiseAr, 'summarydata'=>$summarydata, 'display_by' => $preview->display_by, 'clientData' => $clientData, 'clientcaseData' => $clientcaseData, 'contactData' => $contactData, 'teamLocation'=>$teamLocation]);
			}
		}
		$pdf->content = $html;
		return $pdf->render();
	 }
}
