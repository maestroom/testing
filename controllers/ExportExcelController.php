<?php

namespace app\controllers;

use Yii;
use app\models\TaskInstruct;
use app\models\Options;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\search\TasksUnitsSearch;
use app\models\TasksUnitsTodos;
use app\models\Tasks;
use app\models\Team;
use app\models\TasksUnitsTransactionLog;
use app\models\TasksUnitsTodoTransactionLog;
use app\models\InvoiceFinal;
use app\models\InvoiceFinalBilling;
use app\models\Client;
use app\models\ClientCase;
use app\models\ClientContacts;
use yii\helpers\ArrayHelper;
use app\models\TeamlocationMaster;

use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Border;
use PHPExcel_Style_Alignment;
use PHPExcel_Shared_Font;
use PHPExcel_Style_NumberFormat;
use PHPExcel_Shared_Date;
use PHPExcel_Worksheet_Drawing;

/**
 * ExportExcelReportController implements the CRUD actions for  model.
 */
class ExportExcelController extends Controller
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
     * Return Excel File for Report
     */
   public function actionClientcaseexcel(){
	   $dataexport = yii::$app->request->post('dataexport');
	   $projstatus = yii::$app->params['task_status'];
		 if (isset($dataexport) && $dataexport == "export") {
            $_REQUEST = json_decode(Yii::$app->request->post('filtervalue'), true);
            $_POST = json_decode(Yii::$app->request->post('filtervalue'), true);

            $final = (new TaskInstruct)->getstatusReport($_POST);
			$start_date = $final['start_date'];
			$end_date = $final['end_date'];
			$task_status = $final['task_status'];
			$exceldata = $final['exceldata'];
            $exportdata = "exportData";
            $this->layout = "";

        }
        if (isset($exportdata) && $exportdata == "exportData")
            {
            	$filename = "ProjectsbyClientCase_" . date('m_d_Y', time()) . ".xls";

				$objPHPExcel     = new \PHPExcel();
				$activeSheet = $objPHPExcel->getActiveSheet();
				$activeSheet->SetCellValue('A'.'2','Project By Client/Case');

				$activeSheet->SetCellValue('B'.'2','Projects Submitted Start Date');
				$activeSheet->SetCellValue('C'.'2','Projects Submitted End Date');
				$activeSheet->SetCellValue('D'.'2','Projects Status');
				$activeSheet->SetCellValue('A'.'3',' ');

				$activeSheet->SetCellValue('B'.'3',date('m/d/Y',strtotime($start_date)));
				$activeSheet->SetCellValue('C'.'3',date('m/d/Y',strtotime($end_date)));
				$sts="";
				foreach ($task_status as $st){
					 if($sts=="")$sts=$projstatus[$st];
					 else $sts.=",".$projstatus[$st];}
				$activeSheet->SetCellValue('D'.'3',$sts);
				$activeSheet->SetCellValue('A'.'5','Client');
				$activeSheet->SetCellValue('B'.'5','Case');
				$activeSheet->SetCellValue('C'.'5','Project#');
				$activeSheet->SetCellValue('D'.'5','Project Submitted');
				$activeSheet->SetCellValue('E'.'5','Project Status');
				$activeSheet->SetCellValue('F'.'5','Project Completed');
				$rowcount = 6;

				foreach($exceldata as $excel => $value){
				$completed_date = "";
				$created_date = "";
				$created_date = (new Options)->ConvertOneTzToAnotherTz($value['Created'], 'UTC', $_SESSION['usrTZ'], "requestdate");
				if($value['Completed'] != "" && $value['Completed'] != '0000-00-00 00:00:00'){
					$completed_date=(new Options)->ConvertOneTzToAnotherTz($value['Completed'], 'UTC', $_SESSION['usrTZ'], "requestdate");
				}
					$activeSheet->SetCellValue('A'.$rowcount,$value['client']);
					$activeSheet->SetCellValue('B'.$rowcount,$value['case']);
					$activeSheet->SetCellValue('C'.$rowcount,$value['taskId']);
					$activeSheet->SetCellValue('D'.$rowcount,$created_date);
					$activeSheet->SetCellValue('E'.$rowcount,$value['task_status']);
					$activeSheet->SetCellValue('F'.$rowcount,$completed_date);
					$rowcount++;
				}

				 header('Content-Type: application/vnd.openxmlformats-   officedocument.spreadsheetml.sheet');
				 header('Content-Disposition: attachment;filename="'.$filename.'"');
				 header('Cache-Control: max-age=0');

				 $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
				 $objWriter->save('php://output');
				 exit();
            }
   }
   /* Return Excel File For Project By Team Service Data. */
   public function actionTeamserviceexcel(){
		$dataexport = Yii::$app->request->post('dataexport');
	    $projstatus = yii::$app->params['task_status'];
	    if (isset($dataexport) && $dataexport == "export") {
            $_REQUEST = json_decode(Yii::$app->request->post('filtervalue'), true);
            $_POST = json_decode(Yii::$app->request->post('filtervalue'), true);
            $final = (new TaskInstruct)->getStatusteamservice($_POST);
			$start_date = $final['start_date'];
			$end_date = $final['end_date'];
			$task_status = $final['task_status'];
			$exceldata = $final['exceldata'];
            $exportdata = "exportData";
            $this->layout = "";
        }

        if (isset($exportdata) && $exportdata == "exportData") {
			$filename = "ProjectbyTeamserviceChartPDF_" . date('m_d_Y', time()) . ".xls";

				$objPHPExcel     = new \PHPExcel();
				$activeSheet = $objPHPExcel->getActiveSheet();
				$activeSheet->SetCellValue('A'.'2','Project By Team Service');

				$activeSheet->SetCellValue('B'.'2','Projects Submitted Start Date');
				$activeSheet->SetCellValue('C'.'2','Projects Submitted End Date');
				$activeSheet->SetCellValue('D'.'2','Projects Status');
				$activeSheet->SetCellValue('A'.'3',' ');

				$activeSheet->SetCellValue('B'.'3',date('m/d/Y',strtotime($start_date)));
				$activeSheet->SetCellValue('C'.'3',date('m/d/Y',strtotime($end_date)));
				$sts=implode(',',$projstatus);

				$activeSheet->SetCellValue('D'.'3',$sts);
				$activeSheet->SetCellValue('A'.'5','Service');
				$activeSheet->SetCellValue('B'.'5','Location');
				$activeSheet->SetCellValue('C'.'5','Project#');
				$activeSheet->SetCellValue('D'.'5','Project Submitted');
				$activeSheet->SetCellValue('E'.'5','Project Status');
				$activeSheet->SetCellValue('F'.'5','Project Completed');
				$rowcount = 6;

				foreach($exceldata as $excel => $value){
				$completed_date = "";
				$created_date = "";
				$created_date = (new Options)->ConvertOneTzToAnotherTz($value['Created'], 'UTC', $_SESSION['usrTZ'], "requestdate");
				if($value['Completed'] != "" && $value['Completed'] != '0000-00-00 00:00:00'){
					$completed_date=(new Options)->ConvertOneTzToAnotherTz($value['Completed'], 'UTC', $_SESSION['usrTZ'], "requestdate");
				}
					$activeSheet->SetCellValue('A'.$rowcount,$value['serive']);
					$activeSheet->SetCellValue('B'.$rowcount,$value['loc']);
					$activeSheet->SetCellValue('C'.$rowcount,$value['taskId']);
					$activeSheet->SetCellValue('D'.$rowcount,$created_date);
					$activeSheet->SetCellValue('E'.$rowcount,$value['task_status']);
					$activeSheet->SetCellValue('F'.$rowcount,$completed_date);
					$rowcount++;
				}

				 header('Content-Type: application/vnd.openxmlformats-   officedocument.spreadsheetml.sheet');
				 header('Content-Disposition: attachment;filename="'.$filename.'"');
				 header('Cache-Control: max-age=0');

				 $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
				 $objWriter->save('php://output');
				 exit();
		}
   }
   public function actionTodoserviceexcel(){
		$dataexport = Yii::$app->request->post('dataexport');
		$todostatus = yii::$app->params['todo_status'];
		if (isset($dataexport) && $dataexport == "export") {
            $_REQUEST = json_decode(Yii::$app->request->post('filtervalue'), true);
            $_POST = json_decode(Yii::$app->request->post('filtervalue'), true);
            $_POST['dataexport'] = $dataexport;
            $final = (new TaskInstruct)->getTodofollowup($_POST);
			$start_date = $final['start_date'];
			$end_date = $final['end_date'];
			$locationArr = $final['locationArr'];
			$exceldata = $final['exceldata'];
			$exportchart = $final['exportchart'];
            $this->layout = "";

            $filename = "ToDoFollow-upItemsbyService_" . date('m_d_Y', time()) . ".xls";

				$objPHPExcel     = new \PHPExcel();
				$activeSheet = $objPHPExcel->getActiveSheet();
				$activeSheet->SetCellValue('A'.'2','ToDo Follow-up Items by Service');

				$activeSheet->SetCellValue('B'.'2','ToDo Submitted Start Date');
				$activeSheet->SetCellValue('C'.'2','ToDo Submitted End Date');
				$activeSheet->SetCellValue('D'.'2','ToDo Status');
				$activeSheet->SetCellValue('A'.'3',' ');

				$activeSheet->SetCellValue('B'.'3',date('m/d/Y',strtotime($start_date)));
				$activeSheet->SetCellValue('C'.'3',date('m/d/Y',strtotime($end_date)));
				$sts=implode(',',$todostatus);

				$activeSheet->SetCellValue('D'.'3',$sts);
				$activeSheet->SetCellValue('A'.'5','Client');
				$activeSheet->SetCellValue('B'.'5','Case');
				$activeSheet->SetCellValue('C'.'5','Project#');
				$activeSheet->SetCellValue('D'.'5','ToDo Follow-up');
				$activeSheet->SetCellValue('E'.'5','Service');
				$activeSheet->SetCellValue('F'.'5','Location');
				$activeSheet->SetCellValue('G'.'5','Task');
				$activeSheet->SetCellValue('H'.'5','ToDo Status');
				$activeSheet->SetCellValue('I'.'5','ToDo Created');
				$activeSheet->SetCellValue('J'.'5','ToDO Completed');
				$rowcount = 6;

				if(!empty($exceldata)){
					foreach ($exceldata as $key => $data) {
						$status = $data['todostatus']==1?'Completed':'Not Completed';
						$completed = ($data['completed']!="" && $data['todostatus']==1)?(new Options)->ConvertOneTzToAnotherTz($data['completed'], 'UTC', $_SESSION['usrTZ'], "requestdate"):"";
						$created = (new Options)->ConvertOneTzToAnotherTz($data['created'], 'UTC', $_SESSION['usrTZ'], "requestdate");
						$activeSheet->SetCellValue('A'.$rowcount,$data['client']);
						$activeSheet->SetCellValue('B'.$rowcount,$data['case']);
						$activeSheet->SetCellValue('C'.$rowcount,$data['task_id']);
						$activeSheet->SetCellValue('D'.$rowcount,$data['todo_cat']);
						$activeSheet->SetCellValue('E'.$rowcount,$data['service']);
						$activeSheet->SetCellValue('F'.$rowcount,$data['teamloc']);
						$activeSheet->SetCellValue('G'.$rowcount,$data['servicetask']);
						$activeSheet->SetCellValue('H'.$rowcount,$status);
						$activeSheet->SetCellValue('I'.$rowcount,$created);
						$activeSheet->SetCellValue('J'.$rowcount,$completed);
						$rowcount++;
					}
				}

				 header('Content-Type: application/vnd.openxmlformats-   officedocument.spreadsheetml.sheet');
				 header('Content-Disposition: attachment;filename="'.$filename.'"');
				 header('Cache-Control: max-age=0');

				 $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
				 $objWriter->save('php://output');
				 exit();
        }
   }

   public function actionTeamTasksExport(){
	$params = Yii::$app->request->post();
	$searchModel = new TasksUnitsSearch();
	$params['export']='export';
	$queryparams=Yii::$app->request->queryParams;
	if(!empty($queryparams[1])){
		foreach($queryparams[1] as $k=>$v){
			if($k!='r'){
				$params[$k]=$v;
			}
		}
	}
	//echo "<pre>",print_r($params);die;
    $dataProvider = $searchModel->search($params);
    $excel_data = $dataProvider->getModels();
	//echo "<prE>",print_r($excel_data),"</pre>";die;
	$this->layout = "";
    $filename = "Team_Tasks_Log_" . date('m_d_Y', time()) . ".xlsx";


     $styleArray = array(
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN,
                                'color' => array('rgb' => '000000')
                            ),
                            'outline' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THICK
                              )
                        ),
                       'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                        )
                    );

				$objPHPExcel     = new \PHPExcel();
				$activeSheet = $objPHPExcel->getActiveSheet();
				$activeSheet->setTitle("Team_Tasks_Log_" . date('m_d_Y', time()));
				$activeSheet->SetCellValue('A'.'1','Team Tasks Log');

				$activeSheet->SetCellValue('A'.'2','Team - Location');
				if(!empty($excel_data)){
					$team_name = Team::findOne($excel_data[0]['team_id'])->team_name;
					$activeSheet->SetCellValue('B'.'2',$team_name.' - '.$excel_data[0]['team_location_name']);
				}
				$activeSheet->SetCellValue('A'.'4','Project #');
				$activeSheet->SetCellValue('B'.'4','Project Priority');
				$activeSheet->SetCellValue('C'.'4','Project % Complete');
				$activeSheet->SetCellValue('D'.'4','Project Submit Date');
				$activeSheet->SetCellValue('E'.'4','Project Due Date');
				$activeSheet->SetCellValue('F'.'4','Team');
				$activeSheet->SetCellValue('G'.'4','Team Location');
				$activeSheet->SetCellValue('H'.'4','Task #');
				$activeSheet->SetCellValue('I'.'4','Task Name');
				$activeSheet->SetCellValue('J'.'4','Task Status');
				$activeSheet->SetCellValue('K'.'4','Task Started Date');
				$activeSheet->SetCellValue('L'.'4','Task Completed Date');
				$activeSheet->SetCellValue('M'.'4','Task Assigned');
				$activeSheet->SetCellValue('N'.'4','ToDo - Follow-up Category');
				$activeSheet->SetCellValue('O'.'4','ToDo - Status');
				$activeSheet->SetCellValue('P'.'4','ToDo - Started Date');
				$activeSheet->SetCellValue('Q'.'4','ToDo - Completed Date');
				$activeSheet->SetCellValue('R'.'4','ToDo - Assigned');
				$activeSheet->SetCellValue('S'.'4','ToDo - Notes');
				$unit_status = Yii::$app->params['unit_status'];
				$rowcount = 5;
				if(!empty($excel_data)){
					foreach($excel_data as $excel => $value){
						$created=date("m/d/Y",strtotime($value['created']));
						//$created = PHPExcel_Shared_Date::PHPToExcel(strtotime($value['created']));
						//$created = PHPExcel_Shared_Date::stringToExcel($value['created']);
						//echo $value['created'];
						//echo "<br>";
						//echo $created;die;
						$submit_date_start = TasksUnitsTransactionLog::find()->where('tbl_tasks_units_transaction_log.tasks_unit_id ='.$value['id'].' AND tbl_tasks_units_transaction_log.transaction_type = 1')->orderby('tbl_tasks_units_transaction_log.id DESC')->one();
						$submit_date_end = TasksUnitsTransactionLog::find()->where('tbl_tasks_units_transaction_log.tasks_unit_id ='.$value['id'].' AND tbl_tasks_units_transaction_log.transaction_type = 4')->orderby('tbl_tasks_units_transaction_log.id DESC')->one();
						if(!empty($submit_date_start)){
							$dates['start_date'] = date("m/d/Y",strtotime($submit_date_start->transaction_date));
						}
						if(!empty($submit_date_end)){
							$dates['completed_date'] = date("m/d/Y",strtotime($submit_date_start->transaction_date));
						}
						$percentage = (new Tasks)->getTaskPercentageCompleted($value['task_id'],"team",'',$params['team_id'],$params['team_loc']);
						$percentage_space = strip_tags($percentage);
						//$percentage_space1 = str_replace(' ', '', $percentage_space);
						$tasktodo_info = TasksUnitsTodos::find()->select(['tbl_tasks_units_todos.id','tbl_tasks_units_todos.complete','tbl_tasks_units_todos.created','tbl_tasks_units_todos.todo','tbl_tasks_units_todos.todo_cat_id','tbl_tasks_units_todos.assigned',"concat(tbl_user.usr_first_name,' ',tbl_user.usr_lastname) as assigned_user"])->join('left join','tbl_user','tbl_user.id=tbl_tasks_units_todos.assigned')->where(['tasks_unit_id'=>$value['id']])->orderBy('tbl_tasks_units_todos.modified desc')->all();
						if(!empty($tasktodo_info)){
							foreach($tasktodo_info as $todo){
								$tasktodo_date_end = TasksUnitsTodoTransactionLog::find()->where('tbl_tasks_units_todo_transaction_log.todo_id = '.$todo->id.' AND tbl_tasks_units_todo_transaction_log.transaction_type = 9')->orderby('tbl_tasks_units_todo_transaction_log.id DESC')->one();
								$todo_start_date = date('m/d/Y',strtotime($todo->created));
								if(!empty($tasktodo_date_end)){
									$todo_end_date = date('m/d/Y',strtotime($tasktodo_date_end->transaction_date));
								}
								if($todo->complete == 0){
									$complete = "InComplete";
								}else{
									$complete = "Complete";
								}
							$activeSheet->SetCellValue('A'.$rowcount,$value['task_id']);
							$activeSheet->SetCellValue('B'.$rowcount,$value['priority']);
							$activeSheet->SetCellValue('C'.$rowcount,(intval($percentage_space)/100));
							$activeSheet->getStyle('C'.$rowcount)->getNumberFormat()->setFormatCode(
								PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE
							);
							$activeSheet->SetCellValue('D'.$rowcount,$created);

							$activeSheet->SetCellValue('E'.$rowcount,date('m/d/Y',strtotime($value['task_duedate']))." ");
							$activeSheet->SetCellValue('F'.$rowcount,$team_name);
							$activeSheet->SetCellValue('G'.$rowcount,$value['team_location_name']);
							$activeSheet->SetCellValue('H'.$rowcount,$value['id']);
							$activeSheet->SetCellValue('I'.$rowcount,$value['service_task']);
							$activeSheet->SetCellValue('J'.$rowcount,$unit_status[$value['unit_status']]);
							$activeSheet->SetCellValue('K'.$rowcount,$dates['start_date']);
							$activeSheet->SetCellValue('L'.$rowcount,$dates['completed_date']);
							$activeSheet->SetCellValue('M'.$rowcount,$value['usr_first_name'].' '.$value['usr_lastname']);
							if($todo->todoCats->todo_cat != ''){
							$activeSheet->SetCellValue('N'.$rowcount,$todo->todoCats->todo_cat.'-'.$todo->todoCats->todo_desc);
							}else{
								$activeSheet->SetCellValue('N'.$rowcount,"");
							}
							$activeSheet->SetCellValue('O'.$rowcount,$complete);
							$activeSheet->SetCellValue('P'.$rowcount,$todo_start_date);
							$activeSheet->SetCellValue('Q'.$rowcount,$todo_end_date);
							$activeSheet->SetCellValue('R'.$rowcount,$todo->assignedUser->usr_first_name.' '.$todo->assignedUser->usr_lastname);
							$activeSheet->SetCellValue('S'.$rowcount,$todo->todo);
							$rowcount++;
							}
						}else{
							$activeSheet->SetCellValue('A'.$rowcount,$value['task_id']);
							$activeSheet->SetCellValue('B'.$rowcount,$value['priority']);
							$activeSheet->SetCellValue('C'.$rowcount,(intval($percentage_space)/100));
							$activeSheet->getStyle('C'.$rowcount)->getNumberFormat()->setFormatCode(
									PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE
							);
							$activeSheet->SetCellValue('D'.$rowcount,$created);
							$activeSheet->SetCellValue('E'.$rowcount,date('m/d/Y',strtotime($value['task_duedate']))." ");
							$activeSheet->SetCellValue('F'.$rowcount,$team_name);
							$activeSheet->SetCellValue('G'.$rowcount,$value['team_location_name']);
							$activeSheet->SetCellValue('H'.$rowcount,$value['id']);
							$activeSheet->SetCellValue('I'.$rowcount,$value['service_task']);
							$activeSheet->SetCellValue('J'.$rowcount,$unit_status[$value['unit_status']]);
							$activeSheet->SetCellValue('K'.$rowcount,$dates['start_date']);
							$activeSheet->SetCellValue('L'.$rowcount,$dates['completed_date']);
							$activeSheet->SetCellValue('M'.$rowcount,$value['usr_first_name'].' '.$value['usr_lastname']);
							$activeSheet->SetCellValue('N'.$rowcount,'');
							$activeSheet->SetCellValue('O'.$rowcount,'');
							$activeSheet->SetCellValue('P'.$rowcount,'');
							$activeSheet->SetCellValue('Q'.$rowcount,'');
							$activeSheet->SetCellValue('R'.$rowcount,'');
							$activeSheet->SetCellValue('S'.$rowcount,'');
							$rowcount++;
						}
					}
				}
			$activeSheet->getStyle("A1:S".($rowcount+2))->applyFromArray($styleArray, False);
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

			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); // header for .xlxs file
            header('Content-Disposition: attachment;filename='.$filename); // specify the download file name
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriter->save('php://output');
			exit();

   }

   /**
    * Excel export invoice
    * @return
    */
    public function actionExcelInvoice()
    {

		$this->layout = 'billing';

		$invoicedId = Yii::$app->request->get('invoice_id');
		$summarynote = Yii::$app->request->get('showsummarynote', 0);
		$invoicesId = explode(",",$invoicedId);

		$summarydata2 = array();
		$taskunitbillingdata12 = array();
		$taxcodes2 = array();
		$taxcodewiseAr2 = array();
		$teamLocation2 = array();

		foreach($invoicesId as $invoicedId)
		{
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
						},
						'createdUser'
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
					//echo "<pre>",print_r($taskval1),"</pre>";
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
					$invoiceArray['client_case_id'] = $taskval1['billingUnit']['tasksUnits']['tasks']['client_case_id'];
					$invoiceArray['case_name'] = $taskval1['billingUnit']['tasksUnits']['tasks']['clientCase']['case_name'];
					$invoiceArray['invoiced'] = $taskval1['billingUnit']['invoiced'];
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
					$invoiceArray['unit_created_by'] = $taskval1['billingUnit']['createdUser']['usr_first_name'].' '.$taskval1['billingUnit']['createdUser']['usr_lastname'];
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
				//echo "<pre>",print_r($summarydata),"</pre>Done<br/>";
				//die;
				foreach($dataArray as $taskval1){
					$taskunitbillingdata1[$taskval1['client_case_id']][] = $taskval1;
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
										$taxcodes2[$tax['code']] = $tax['code'];
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
			$teamLocation = ArrayHelper::map(TeamlocationMaster::find()->select(['id','team_location_name'])->orderBy(['team_location_name' => 'ASC'])->where('remove=0 OR id=0')->all(),'id','team_location_name');

			$summarydata2[$invoicedId] = $summarydata;
			$taskunitbillingdata12[$invoicedId] = $taskunitbillingdata1;
			$taxcodewiseAr2[$invoicedId] = $taxcodewiseAr;
			$teamLocation2[$invoicedId] = $teamLocation;
		}
	//echo "<pre>",print_r($taskunitbillingdata12),"</pre>";die;
		$styleArray = array(
                        'borders' => array(
                            'allborders' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THIN,
                                'color' => array('rgb' => '000000')
                            ),
                            'outline' => array(
                                'style' => PHPExcel_Style_Border::BORDER_THICK
                              )
                        ),
                       'alignment' => array(
                            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                        )
                    );

		$objPHPExcel = new \PHPExcel();
		$activeSheet = $objPHPExcel->getActiveSheet();

		$sheet_title = "Excel_Invoice_".date('m_d_Y', time()).".xls";
		$activeSheet->setTitle($sheet_title);

		/** invoice without notes **/
		if($summarynote == 2){
			// activesheet details
			$activeSheet->SetCellValue('A'.'2','Invoice #');
			$activeSheet->SetCellValue('B'.'2','Invoice Date');
			$activeSheet->SetCellValue('C'.'2','Internal Reference Number');
			$activeSheet->SetCellValue('D'.'2','Client Name');
			$activeSheet->SetCellValue('E'.'2','Case Name');
			$activeSheet->SetCellValue('F'.'2','Case Matter Number');
			$activeSheet->SetCellValue('G'.'2','Counsel Name');
			$activeSheet->SetCellValue('H'.'2','Sales Representative');
			$activeSheet->SetCellValue('I'.'2','Location');
			$activeSheet->SetCellValue('J'.'2','Price Point');
			$activeSheet->SetCellValue('K'.'2','Default Description');
			$activeSheet->SetCellValue('L'.'2','#Units');
			$activeSheet->SetCellValue('M'.'2','Unit Name');
			$activeSheet->SetCellValue('N'.'2','Rate');
			$activeSheet->SetCellValue('O'.'2','Price');


			$alpha='O';
			if(!empty($taxcodes2)){
				krsort($taxcodes2);
				foreach($taxcodes2 as $val){
					$activeSheet->SetCellValue(++$alpha.'2',$val);
				}
			}
			$activeSheet->SetCellValue(++$alpha.'2','Total Invoice Amount');

			// style with bold header
			$objPHPExcel->getActiveSheet()->getStyle("A2:".$alpha.'2')->getFont()->setBold(true);

			// rows count value
			$rowcount=3; $total_invoice_sum = 0;
			foreach($taskunitbillingdata12 as $task_key => $taskunitbillingdata1){
				foreach($taskunitbillingdata1 as $key => $dataVal){
					$total=0;
					foreach($dataVal as $data){
							$activeSheet->SetCellValue('A'.$rowcount,$data['invoice_final_id']);
							$activeSheet->SetCellValue('B'.$rowcount,$data['invoice_created']);
							$activeSheet->SetCellValue('C'.$rowcount,$data['internal_ref_no_id']);
							$activeSheet->SetCellValue('D'.$rowcount,$data['client_name']);
							$activeSheet->SetCellValue('E'.$rowcount,$data['case_name']);
							$activeSheet->SetCellValue('F'.$rowcount,$data['case_matter_no']);
							$activeSheet->SetCellValue('G'.$rowcount,$data['counsel_name']);
							$activeSheet->SetCellValue('H'.$rowcount,$data['sales_user_name']);
							$activeSheet->SetCellValue('I'.$rowcount,$teamLocation[$data['team_loc']]);
							$activeSheet->SetCellValue('J'.$rowcount,$data['price_point']);
							$activeSheet->SetCellValue('K'.$rowcount,$data['pricing_description']);
							$activeSheet->SetCellValue('L'.$rowcount,number_format(round($data['quantity'],2),2,'.',''));
							$activeSheet->getStyle('L'.$rowcount)->getNumberFormat()->setFormatCode('0.00');
							$activeSheet->SetCellValue('M'.$rowcount,$data['unit_name']);
							$activeSheet->SetCellValue('N'.$rowcount,number_format($data['final_rate'],2,'.',''));
							$activeSheet->getStyle('N'.$rowcount)->getNumberFormat()->setFormatCode('0.00');
							$activeSheet->SetCellValue('O'.$rowcount,number_format($data['subtotal'],2,'.',''));
							$activeSheet->getStyle('O'.$rowcount)->getNumberFormat()->setFormatCode('0.00');

							$createArray = array(); $rate_per = '';
							foreach($data['invoiceFinalTaxes'] as $valss){
								$createArray[$valss['code']] = $valss['rate'];
								$rate_per += $valss['rate'];
							}
							$alpha='O';
							$t =  $data['final_rate'] * round($data['quantity'],2);
							$total = $t + (($t * $rate_per)/100);
							$vl='';
							foreach($taxcodes2 as $innerval){
								if(isset($createArray[$innerval])){
									$vl = ($data['final_rate'] * round($data['quantity'],2)) * ($createArray[$innerval]/100);
									++$alpha;
									$activeSheet->SetCellValue($alpha.$rowcount,number_format($vl,2,'.',''));
									$activeSheet->getStyle($alpha.$rowcount)->getNumberFormat()->setFormatCode('0.00');
								}
								else
									$activeSheet->SetCellValue(++$alpha.$rowcount,"");
							}
							$rsst = ++$alpha;
							$activeSheet->SetCellValue($rsst.$rowcount,number_format($total,2,'.',''));
							$activeSheet->getStyle($rsst.$rowcount)->getNumberFormat()->setFormatCode('0.00');

							$total_invoice_sum += $total;
							$rowcount++;
						}
					}
				}
				$activeSheet->SetCellValue($rsst.$rowcount,$total_invoice_sum);
				$activeSheet->getStyle($rsst.$rowcount)->getNumberFormat()->setFormatCode('0.00');
		}

		/** invoice with notes **/
		if($summarynote == 3){
			// activesheet details
			$activeSheet->SetCellValue('A'.'2','Invoice #');
			$activeSheet->SetCellValue('B'.'2','Invoice Date');
			$activeSheet->SetCellValue('C'.'2','Internal Reference Number');
			$activeSheet->SetCellValue('D'.'2','Client Name');
			$activeSheet->SetCellValue('E'.'2','Case Name');
			$activeSheet->SetCellValue('F'.'2','Case Matter Number');
			$activeSheet->SetCellValue('G'.'2','Counsel Name');
			$activeSheet->SetCellValue('H'.'2','Sales Representative');
			$activeSheet->SetCellValue('I'.'2','Location');
			$activeSheet->SetCellValue('J'.'2','Price Point');
			$activeSheet->SetCellValue('K'.'2','Default Description');
			$activeSheet->SetCellValue('L'.'2','Date Created');
			$activeSheet->SetCellValue('M'.'2','Project #');
			$activeSheet->SetCellValue('N'.'2','Qty');
			$activeSheet->SetCellValue('O'.'2','Unit');
			$activeSheet->SetCellValue('P'.'2','Rate');

			$alpha='P';
			if(!empty($taxcodes2)){
				krsort($taxcodes2);
				foreach($taxcodes2 as $val){
					$activeSheet->SetCellValue(++$alpha.'2',$val);
				}
			}

			$activeSheet->SetCellValue(++$alpha.'2','Invoice Total');
		//	$activeSheet->SetCellValue(++$alpha.'2','Price Point');
			$activeSheet->SetCellValue(++$alpha.'2','UTBMS Code');
			$activeSheet->SetCellValue(++$alpha.'2','Billable Item Entered By');
			$activeSheet->SetCellValue(++$alpha.'2','Custom Description');

			// style with bold header
			$assign=$alpha;
			$objPHPExcel->getActiveSheet()->getStyle("A2:".$alpha.'2')->getFont()->setBold(true);

			$rowcount = 3; $total_invoice_sum=0;
			foreach($summarydata2 as $tasks_key => $summarydata){
				foreach($summarydata as $key => $dataVal){
					$total=0;
					foreach($dataVal as $data){
						// Row Value
						$activeSheet->SetCellValue('A'.$rowcount,$data['invoice_final_id']);
						$activeSheet->SetCellValue('B'.$rowcount,$data['invoice_created']);
						$activeSheet->SetCellValue('C'.$rowcount,$data['internal_ref_no_id']);
						$activeSheet->SetCellValue('D'.$rowcount,$data['client_name']);
						$activeSheet->SetCellValue('E'.$rowcount,$data['case_name']);
						$activeSheet->SetCellValue('F'.$rowcount,$data['case_matter_no']);
						$activeSheet->SetCellValue('G'.$rowcount,$data['counsel_name']);
						$activeSheet->SetCellValue('H'.$rowcount,$data['sales_user_name']);
						$activeSheet->SetCellValue('I'.$rowcount,$teamLocation[$data['team_loc']]);
						$activeSheet->SetCellValue('J'.$rowcount,$data['price_point']);
						$activeSheet->SetCellValue('K'.$rowcount,$data['pricing_description']);
						$activeSheet->SetCellValue('L'.$rowcount,$data['unit_created']);
						$activeSheet->SetCellValue('M'.$rowcount,$data['task_id']);
						$activeSheet->SetCellValue('N'.$rowcount,number_format(round($data['quantity'],2),2,'.',''));
						$activeSheet->getStyle('N'.$rowcount)->getNumberFormat()->setFormatCode('0.00');
						$activeSheet->SetCellValue('O'.$rowcount,$data['unit_name']);
						$activeSheet->SetCellValue('P'.$rowcount,number_format($data['final_rate'],2,'.',''));
						$activeSheet->getStyle('P'.$rowcount)->getNumberFormat()->setFormatCode('0.00');

						// sorting
						$createArray = array(); $rate_per = '';
						foreach($data['invoiceFinalTaxes'] as $k => $valss){
							$createArray[$valss['code']] = $valss['rate'];
							$rate_per += $valss['rate'];
						}
						$t =  $data['final_rate'] * round($data['quantity'],2);
						$total = $t + (($t * $rate_per)/100); $alpha = 'P';
						$vl='';

						foreach($taxcodes2 as $innerval){
							if(isset($createArray[$innerval])){
								$vl = ($data['final_rate'] * round($data['quantity'],2)) * ($createArray[$innerval]/100);
								++$alpha;
								$activeSheet->SetCellValue($alpha.$rowcount,number_format($vl,2,'.',''));
								$activeSheet->getStyle($alpha.$rowcount)->getNumberFormat()->setFormatCode('0.00');
							} else
								$activeSheet->SetCellValue(++$alpha.$rowcount,"");
						}
						$rsst = ++$alpha;

						$activeSheet->SetCellValue($rsst.$rowcount,number_format($total,2,'.',''));
						$activeSheet->getStyle($rsst.$rowcount)->getNumberFormat()->setFormatCode('0.00');
						//$activeSheet->SetCellValue(++$alpha.$rowcount,$data['price_point']);
						$activeSheet->SetCellValue(++$alpha.$rowcount,$data['utbms_code']);
						$activeSheet->SetCellValue(++$alpha.$rowcount,$data['unit_created_by']);

						// billing description
						$activeSheet->SetCellValue(++$alpha.$rowcount,$data['billing_desc']);

						$total_invoice_sum += $total;
						$rowcount++;
					}
				}
			}
			$activeSheet->SetCellValue($rsst.$rowcount,number_format($total_invoice_sum,2,'.',''));
			$activeSheet->getStyle($rsst.$rowcount)->getNumberFormat()->setFormatCode('0.00');
		}

		//$objPHPExcel->getActiveSheet()->getStyle("A1:".$alpha.$rowcount)->getAlignment()->setWrapText(true); //  wrap text
		$objPHPExcel->getActiveSheet()->getStyle("A1:".$alpha.$rowcount)->applyFromArray($styleArray, False);
		// wraping contents of field in excel
		for($i='A';$i<=$alpha;$i++){
			//$activeSheet->getColumnDimension($i)->setWidth(30);
			$objPHPExcel->getActiveSheet()->getColumnDimension($i)->setAutoSize(true);
		}

        $filename = "Excel_Invoice_".date('m_d_Y', time()).".xls";

		header('Content-Type: application/vnd.openxmlformats-   officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$filename.'"');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
		die();
	}

	public function actionTotalProjects(){
		$post_data = Yii::$app->request->post();
		$image_data = "";
		$table_data = "";
		$image_data = $post_data['image_data'];
		$report_header=$post_data['casename'];
		$sheet_title="Total Projects";
		$filename="total_projects_".date('m_d_Y',time()).".xlsx";
		if($post_data['chart_report'] == 'totalmedia') {
			$filename="total_media_projects_".date('m_d_Y',time()).".xlsx";
			$sheet_title="Total Media";
		}
		$tmpfile = tempnam(sys_get_temp_dir(), 'html');
        file_put_contents($tmpfile, "<html><head></head><body><table><tr><th colspan='2'>".$sheet_title."</th></tr><tr><td>Case</td><td>".$report_header."</td></tr></table></body></html>");
        $objPHPExcel = new PHPExcel();

      	/*  $final_result = str_replace(array('<b>','<i>','<u>','<div align="center">','<div align="left">','<div align="right">','<font size="1">','<font size="3">','<font size="5">','<strike>','</strike>','</b>','</i>','</u>','</div>','</font>','<br>'),
		array('&B','&I','&U','&C','&L','&R','&6','&12','&18','&S','','','','','','',''), trim($report_header));
		$objPHPExcel->getActiveSheet()->getHeaderFooter()->setOddHeader('&L'.$final_result);*/
		$objPHPExcel->getActiveSheet()->getHeaderFooter()->setOddHeader('&L&B'.trim(strip_tags($report_header)));

		/* HTML */
		$excelHTMLReader = PHPExcel_IOFactory::createReader('HTML');

        if(isset($image_data) && $image_data!="")
        {
			$tempDir = '/temp';
			$img = $image_data;
			$img = str_replace('data:image/jpeg;base64,', '', $img);
			$img = str_replace(' ', '+', $img);
			$data = base64_decode($img);
			$file = sys_get_temp_dir() .'/'. uniqid() . '.png';
			file_put_contents($file, $data.$report_header);
			$activeSheet = $objPHPExcel->getActiveSheet();
			// Add an image to the worksheet
			$objDrawing = new PHPExcel_Worksheet_Drawing();
			$objDrawing->setName('Image');
			$objDrawing->setDescription('Image');
			$objDrawing->setOffsetX(8);    // setOffsetX works properly
			$objDrawing->setPath($file);
			$objDrawing->setCoordinates('B10');
			$objDrawing->setWorksheet($activeSheet);
        }

		@$excelHTMLReader->loadIntoExisting($tmpfile, $objPHPExcel);

        unlink($tmpfile); // Delete temporary file because it isn't needed anymore

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); // header for .xlxs file
        header('Content-Disposition: attachment;filename='.$filename); // specify the download file name
        header('Cache-Control: max-age=0');

        // Creates a writer to output the $objPHPExcel's content
        $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $writer->save('php://output','w');
        exit;
	}

	public function actionTotalMediaUnitSize(){
		$post_data = Yii::$app->request->post();
		$image_data = "";
		$table_data = "";
		$image_data = $post_data['image_data'];
		$report_header=$post_data['casename'];
		$sheet_title="Total Projects";
		$filename="total_media_unit_size_".date('m_d_Y',time()).".xlsx";
		if($post_data['chart_report'] == 'totalmediaunitsize') {
			$filename="total_media_unit_size_".date('m_d_Y',time()).".xlsx";
			$sheet_title="Media Type By Size";
		}
		$tmpfile = tempnam(sys_get_temp_dir(), 'html');
        file_put_contents($tmpfile, "<html><head></head><body><table><tr><th colspan='2'>".$sheet_title."</th></tr><tr><td>Case</td><td>".$report_header."</td></tr></table></body></html>");
        $objPHPExcel = new PHPExcel();

      	/*  $final_result = str_replace(array('<b>','<i>','<u>','<div align="center">','<div align="left">','<div align="right">','<font size="1">','<font size="3">','<font size="5">','<strike>','</strike>','</b>','</i>','</u>','</div>','</font>','<br>'),
		array('&B','&I','&U','&C','&L','&R','&6','&12','&18','&S','','','','','','',''), trim($report_header));
		$objPHPExcel->getActiveSheet()->getHeaderFooter()->setOddHeader('&L'.$final_result);*/
		$objPHPExcel->getActiveSheet()->getHeaderFooter()->setOddHeader('&L&B'.trim(strip_tags($report_header)));

		/* HTML */
		$excelHTMLReader = PHPExcel_IOFactory::createReader('HTML');

        if(isset($image_data) && $image_data!="")
        {
			$tempDir = '/temp';
			$img = $image_data;
			$img = str_replace('data:image/jpeg;base64,', '', $img);
			$img = str_replace(' ', '+', $img);
			$data = base64_decode($img);
			$file = sys_get_temp_dir() .'/'. uniqid() . '.png';
			file_put_contents($file, $data.$report_header);
			$activeSheet = $objPHPExcel->getActiveSheet();
			// Add an image to the worksheet
			$objDrawing = new PHPExcel_Worksheet_Drawing();
			$objDrawing->setName('Image');
			$objDrawing->setDescription('Image');
			$objDrawing->setOffsetX(8);    // setOffsetX works properly
			$objDrawing->setPath($file);
			$objDrawing->setCoordinates('B10');
			$objDrawing->setWorksheet($activeSheet);
        }

		@$excelHTMLReader->loadIntoExisting($tmpfile, $objPHPExcel);

        unlink($tmpfile); // Delete temporary file because it isn't needed anymore

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); // header for .xlxs file
        header('Content-Disposition: attachment;filename='.$filename); // specify the download file name
        header('Cache-Control: max-age=0');

        // Creates a writer to output the $objPHPExcel's content
        $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $writer->save('php://output','w');
        exit;
	}

	public function actionMediaByCustodian(){
		$post_data = Yii::$app->request->post();
		$image_data = "";
		$table_data = "";
		$image_data = $post_data['image_data'];
		$report_header=$post_data['casename'];
		$sheet_title="Media By Custodian";
		$filename="media_by_custodian_".date('m_d_Y',time()).".xlsx";
		if($post_data['chart_report'] == 'mediabycustodian') {
			$filename="media_by_custodian_".date('m_d_Y',time()).".xlsx";
			$sheet_title="Media By Custodian";
		}
		$tmpfile = tempnam(sys_get_temp_dir(), 'html');
        file_put_contents($tmpfile, "<html><head></head><body><table><tr><th colspan='2'>".$sheet_title."</th></tr><tr><td>Case</td><td>".$report_header."</td></tr></table></body></html>");
        $objPHPExcel = new PHPExcel();

      	/*  $final_result = str_replace(array('<b>','<i>','<u>','<div align="center">','<div align="left">','<div align="right">','<font size="1">','<font size="3">','<font size="5">','<strike>','</strike>','</b>','</i>','</u>','</div>','</font>','<br>'),
		array('&B','&I','&U','&C','&L','&R','&6','&12','&18','&S','','','','','','',''), trim($report_header));
		$objPHPExcel->getActiveSheet()->getHeaderFooter()->setOddHeader('&L'.$final_result);*/
		$objPHPExcel->getActiveSheet()->getHeaderFooter()->setOddHeader('&L&B'.trim(strip_tags($report_header)));

		/* HTML */
		$excelHTMLReader = PHPExcel_IOFactory::createReader('HTML');

        if(isset($image_data) && $image_data!="")
        {
			$tempDir = '/temp';
			$img = $image_data;
			$img = str_replace('data:image/jpeg;base64,', '', $img);
			$img = str_replace(' ', '+', $img);
			$data = base64_decode($img);
			$file = sys_get_temp_dir() .'/'. uniqid() . '.png';
			file_put_contents($file, $data.$report_header);
			$activeSheet = $objPHPExcel->getActiveSheet();
			// Add an image to the worksheet
			$objDrawing = new PHPExcel_Worksheet_Drawing();
			$objDrawing->setName('Image');
			$objDrawing->setDescription('Image');
			$objDrawing->setOffsetX(8);    // setOffsetX works properly
			$objDrawing->setPath($file);
			$objDrawing->setCoordinates('B10');
			$objDrawing->setWorksheet($activeSheet);
        }

		@$excelHTMLReader->loadIntoExisting($tmpfile, $objPHPExcel);

        unlink($tmpfile); // Delete temporary file because it isn't needed anymore

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); // header for .xlxs file
        header('Content-Disposition: attachment;filename='.$filename); // specify the download file name
        header('Cache-Control: max-age=0');

        // Creates a writer to output the $objPHPExcel's content
        $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $writer->save('php://output','w');
        exit;
	}

	public function actionProductionByType(){
		$post_data = Yii::$app->request->post();
		$image_data = "";
		$table_data = "";
		$image_data = $post_data['image_data'];
		$report_header=$post_data['casename'];
		$sheet_title="Total Productions";
		$filename="total_productions_".date('m_d_Y',time()).".xlsx";
		if($post_data['chart_report'] == 'productionbytype') {
			$filename="total_productions_".date('m_d_Y',time()).".xlsx";
			$sheet_title="Total Productions";
		}
		$tmpfile = tempnam(sys_get_temp_dir(), 'html');
        file_put_contents($tmpfile, "<html><head></head><body><table><tr><th colspan='2'>".$sheet_title."</th></tr><tr><td>Case</td><td>".$report_header."</td></tr></table></body></html>");
        $objPHPExcel = new PHPExcel();

      	/*  $final_result = str_replace(array('<b>','<i>','<u>','<div align="center">','<div align="left">','<div align="right">','<font size="1">','<font size="3">','<font size="5">','<strike>','</strike>','</b>','</i>','</u>','</div>','</font>','<br>'),
		array('&B','&I','&U','&C','&L','&R','&6','&12','&18','&S','','','','','','',''), trim($report_header));
		$objPHPExcel->getActiveSheet()->getHeaderFooter()->setOddHeader('&L'.$final_result);*/
		$objPHPExcel->getActiveSheet()->getHeaderFooter()->setOddHeader('&L&B'.trim(strip_tags($report_header)));

		/* HTML */
		$excelHTMLReader = PHPExcel_IOFactory::createReader('HTML');

        if(isset($image_data) && $image_data!="")
        {
			$tempDir = '/temp';
			$img = $image_data;
			$img = str_replace('data:image/jpeg;base64,', '', $img);
			$img = str_replace(' ', '+', $img);
			$data = base64_decode($img);
			$file = sys_get_temp_dir() .'/'. uniqid() . '.png';
			file_put_contents($file, $data.$report_header);
			$activeSheet = $objPHPExcel->getActiveSheet();
			// Add an image to the worksheet
			$objDrawing = new PHPExcel_Worksheet_Drawing();
			$objDrawing->setName('Image');
			$objDrawing->setDescription('Image');
			$objDrawing->setOffsetX(8);    // setOffsetX works properly
			$objDrawing->setPath($file);
			$objDrawing->setCoordinates('B10');
			$objDrawing->setWorksheet($activeSheet);
        }

		@$excelHTMLReader->loadIntoExisting($tmpfile, $objPHPExcel);

        unlink($tmpfile); // Delete temporary file because it isn't needed anymore

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); // header for .xlxs file
        header('Content-Disposition: attachment;filename='.$filename); // specify the download file name
        header('Cache-Control: max-age=0');

        // Creates a writer to output the $objPHPExcel's content
        $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $writer->save('php://output','w');
        exit;
	}

	public function actionProductionProducingParties(){
		$post_data = Yii::$app->request->post();
		$image_data = "";
		$table_data = "";
		$image_data = $post_data['image_data'];
		$report_header=$post_data['casename'];
		$sheet_title="Production Producing Parties";
		$filename="production_producing_parties_".date('m_d_Y',time()).".xlsx";
		if($post_data['chart_report'] == 'productionproducingparties') {
			$filename="production_producing_parties_".date('m_d_Y',time()).".xlsx";
			$sheet_title="Production Producing Parties";
		}
		$tmpfile = tempnam(sys_get_temp_dir(), 'html');
        file_put_contents($tmpfile, "<html><head></head><body><table><tr><th colspan='2'>".$sheet_title."</th></tr><tr><td>Case</td><td>".$report_header."</td></tr></table></body></html>");
        $objPHPExcel = new PHPExcel();

      	/*  $final_result = str_replace(array('<b>','<i>','<u>','<div align="center">','<div align="left">','<div align="right">','<font size="1">','<font size="3">','<font size="5">','<strike>','</strike>','</b>','</i>','</u>','</div>','</font>','<br>'),
		array('&B','&I','&U','&C','&L','&R','&6','&12','&18','&S','','','','','','',''), trim($report_header));
		$objPHPExcel->getActiveSheet()->getHeaderFooter()->setOddHeader('&L'.$final_result);*/
		$objPHPExcel->getActiveSheet()->getHeaderFooter()->setOddHeader('&L&B'.trim(strip_tags($report_header)));

		/* HTML */
		$excelHTMLReader = PHPExcel_IOFactory::createReader('HTML');

        if(isset($image_data) && $image_data!="")
        {
			$tempDir = '/temp';
			$img = $image_data;
			$img = str_replace('data:image/jpeg;base64,', '', $img);
			$img = str_replace(' ', '+', $img);
			$data = base64_decode($img);
			$file = sys_get_temp_dir() .'/'. uniqid() . '.png';
			file_put_contents($file, $data.$report_header);
			$activeSheet = $objPHPExcel->getActiveSheet();
			// Add an image to the worksheet
			$objDrawing = new PHPExcel_Worksheet_Drawing();
			$objDrawing->setName('Image');
			$objDrawing->setDescription('Image');
			$objDrawing->setOffsetX(8);    // setOffsetX works properly
			$objDrawing->setPath($file);
			$objDrawing->setCoordinates('B10');
			$objDrawing->setWorksheet($activeSheet);
        }

		@$excelHTMLReader->loadIntoExisting($tmpfile, $objPHPExcel);

        unlink($tmpfile); // Delete temporary file because it isn't needed anymore

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); // header for .xlxs file
        header('Content-Disposition: attachment;filename='.$filename); // specify the download file name
        header('Cache-Control: max-age=0');

        // Creates a writer to output the $objPHPExcel's content
        $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $writer->save('php://output','w');
        exit;
	}

	public function actionCaseBudget(){
		$this->layout = false;
		$post_data = Yii::$app->request->post();
		$image_data = "";
		$table_data = "";
		$image_data = $post_data['image_data'];
		$table_data=json_decode($post_data['table_data'],true);
		$case_info=ClientCase::findOne($post_data['case_id']);
		$filename="casebudget_".date('m_d_Y_H_i_A',time()).".xlsx";
		$datatable = $this->render('CaseBudget', ['table_data'=>$table_data,'case_info'=>$case_info]);
		//echo $datatable;die;
        $table = $datatable;
        $tmpfile = tempnam(sys_get_temp_dir(), 'html');
        file_put_contents($tmpfile, $table);
        $objPHPExcel = new PHPExcel();
		$objPHPExcel->getActiveSheet()->getHeaderFooter()->setOddHeader('&L&B'.trim(strip_tags($report_header)));

		/* HTML */
		$excelHTMLReader = PHPExcel_IOFactory::createReader('HTML');

        if(isset($image_data) && $image_data!="")
        {
			$tempDir = '/temp';
			$img = $image_data;
			$img = str_replace('data:image/jpeg;base64,', '', $img);
			$img = str_replace(' ', '+', $img);
			$data = base64_decode($img);
			$file = sys_get_temp_dir() .'/'. uniqid() . '.png';
			file_put_contents($file, $data);
			$activeSheet = $objPHPExcel->getActiveSheet();
			// Add an image to the worksheet
			$objDrawing = new PHPExcel_Worksheet_Drawing();
			$objDrawing->setName('Image');
			$objDrawing->setDescription('Image');
			$objDrawing->setOffsetX(8);    // setOffsetX works properly
			$objDrawing->setPath($file);
			$objDrawing->setCoordinates('B'.(count($table_data)+15));
			$objDrawing->setWorksheet($activeSheet);
        }

		@$excelHTMLReader->loadIntoExisting($tmpfile, $objPHPExcel);

        unlink($tmpfile); // Delete temporary file because it isn't needed anymore

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); // header for .xlxs file
        header('Content-Disposition: attachment;filename='.$filename); // specify the download file name
        header('Cache-Control: max-age=0');

        // Creates a writer to output the $objPHPExcel's content
        $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $writer->save('php://output','w');
        exit;
	}

}
