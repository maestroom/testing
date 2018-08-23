<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\Mydocument;
use yii\helpers\ArrayHelper;
use yii\data\ArrayDataProvider;
use yii\helpers\Url;


use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Border;
use PHPExcel_Style_Alignment;
use PHPExcel_Shared_Font;
use PHPExcel_Worksheet_Drawing;

$action=Yii::$app->controller->action->id;

class ZipController extends Controller
{

   public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout','login'],
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



    public function actions()
    {
    	return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }


	public function actionZipBlob(){
		$type=Yii::$app->request->get("type","");
		$start=Yii::$app->request->get("start",0);
		$limit=Yii::$app->request->get("limit",0);
		if($type==""){
			echo "type is required";die;
		}
		if(!in_array($type,array('I','IN','T','C','DS','case','team'))){
			echo "invalid Type";die;
		}
		
		ini_set('memory_limit', '-1');
		ini_set('upload_max_filesize', '-1');
		ini_set('post_max_size', '-1');
		ini_set('max_execution_time', '-1'); 

		$types=['I'=>"instruct",'IN'=>"instruct N",'T'=>"Todo",'C'=>"Comment",'DS'=>"Data Statistics",'case'=>'Case','team'=>'Team'];
		$type=$types[$type];
		$basepath =Yii::$app->basePath.'/download/';
		$zip = new \ZipArchive();
        $zipfilename = $basepath.ucwords(str_replace(' ','_',$type))."_export_document_" . date('m_d_Y_H_i_s') . ".zip";
		if ($zip->open($zipfilename, \ZIPARCHIVE::CREATE | \ZIPARCHIVE::OVERWRITE) !== true) {
			echo "Cannot Open for writing";die;
		}
		$filename = ucwords(str_replace(' ','_',$type))."_documentLog_" . date('m_d_Y_H_i_s', time()) . ".xls";
        $styleArray = array('alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER));
		$objPHPExcel     = new \PHPExcel();
        $activeSheet =  $objPHPExcel->getActiveSheet();
		if(strtolower($type)=='case'){
			$activeSheet->SetCellValue('A'.'1','Client id');
			$activeSheet->SetCellValue('B'.'1','Client name');
			$activeSheet->SetCellValue('C'.'1','Case Id');
			$activeSheet->SetCellValue('D'.'1','Case Name');
			$activeSheet->SetCellValue('E'.'1','Attachment type');
			$activeSheet->SetCellValue('F'.'1','Attachment name');
			$activeSheet->SetCellValue('G'.'1','Attachment name with extension');
		}if(strtolower($type)=='team'){
			$activeSheet->SetCellValue('A'.'1','Team id');
			$activeSheet->SetCellValue('B'.'1','Team name');
			$activeSheet->SetCellValue('C'.'1','Attachment type');
			$activeSheet->SetCellValue('D'.'1','Attachment name');
			$activeSheet->SetCellValue('E'.'1','Attachment name with extension');
		}else{
			$activeSheet->SetCellValue('A'.'1','Client id');
			$activeSheet->SetCellValue('B'.'1','Client name');
			$activeSheet->SetCellValue('C'.'1','Case Id');
			$activeSheet->SetCellValue('D'.'1','Case Name');
			$activeSheet->SetCellValue('E'.'1','Project #');
			$activeSheet->SetCellValue('F'.'1','Project Name');
			$activeSheet->SetCellValue('G'.'1','Task #');
			$activeSheet->SetCellValue('H'.'1','Service - Task Name');
			$activeSheet->SetCellValue('I'.'1','Attachment type');
			$activeSheet->SetCellValue('J'.'1','Attachment name');
			$activeSheet->SetCellValue('K'.'1','Attachment name with extension');
		}
        
		//echo $type;die;
		$j = 2;
		$fileSize=0;
		//foreach($types as $type) 
		{
			$query = Mydocument::find()->joinWith(['mydocumentsBlobs']);
			if(strtolower($type)=='case') {
				$query->joinWith(['clientCase']);
				$query->where(['type'=>0]);
			}
			if(strtolower($type)=='team') {
				$query->joinWith(['team']);
				$query->where(['type'=>0]);
			}
			if($type=="instruct") {
				$query->joinWith(['taskInstructServicetask'=>function (\yii\db\ActiveQuery $query) { $query->joinWith(['tasks']);},]);
			}
			if($type == 'instruct N') {
                $query->joinWith(['taskInstructNotes'=>function (\yii\db\ActiveQuery $query) { $query->joinWith(['tasks']);},]);
            }
			if($type == 'Todo') {
                $query->joinWith(['tasksUnitsTodos'=>function (\yii\db\ActiveQuery $query) { $query->joinWith(['taskUnit'=>function(\yii\db\ActiveQuery $query){$query->joinWith('taskInstruct');}]);},	]);
            }
			if($type == 'Comment') {
                $query->joinWith(['comments'=>function (\yii\db\ActiveQuery $query) { $query->joinWith(['tasks']);},]);
            }
			if($type == 'Data Statistics') {
            	$query->joinWith(['tasksUnits'=>function (\yii\db\ActiveQuery $query) { $query->joinWith(['tasks']);},]);
            }
			$query->andwhere(['origination'=>$type]);
			if($type=="instruct") {
				if($limit==0){
					echo "params limit is required";die;
				}
				$query->offset($start);
				$query->limit($limit);
			}
			$dataProvider = $query->all();
			if(!empty($dataProvider)) {
				foreach($dataProvider as $data) {
					$project_id=$taskunit_id=$client_id=$case_id=$team_id=0;
					$client_name=$case_name=$team_name=$project_name=$servicetask="";
					if(strtolower($type)=='case'){
						$client_id=$data->clientCase->client_id;
						$case_id=$data->clientCase->id;
						$client_name=$data->clientCase->client->client_name;
						$case_name=$data->clientCase->case_name;
					}
					if(strtolower($type)=='team'){
						$team_id=$data->team->id;
						$team_name=$data->team->team_name;
					}
					if($type=="instruct") {
						if(!empty($data->taskInstructServicetask)){
							if(isset($data->taskInstructServicetask->task_id) && isset($data->taskInstructServicetask->servicetask_id)){
								$sql="SELECT id from tbl_tasks_units where task_id=".$data->taskInstructServicetask->task_id." AND servicetask_id=".$data->taskInstructServicetask->servicetask_id;
								$taskunit_id= Yii::$app->db->createCommand($sql)->queryScalar();
								if($taskunit_id>0){
									$servicesql="SELECT service_task FROM tbl_servicetask WHERE id IN (select servicetask_id from tbl_tasks_units where id=$taskunit_id)";
									$servicetask=Yii::$app->db->createCommand($servicesql)->queryScalar();
								}
							}
							$project_id=$data->taskInstructServicetask->tasks->id;
							$project_name=$data->taskInstructServicetask->tasks->activeTaskInstruct->project_name;
							$client_id=$data->taskInstructServicetask->tasks->clientCase->client_id;
							$case_id=$data->taskInstructServicetask->tasks->clientCase->id;
							$client_name=$data->taskInstructServicetask->tasks->clientCase->client->client_name;
							$case_name=$data->taskInstructServicetask->tasks->clientCase->case_name;
						}
					}
					if($type == 'instruct N') {
						if(!empty($data->taskInstructNotes)){
							if(isset($data->taskInstructNotes->task_id) && isset($data->taskInstructNotes->servicetask_id)){
								//echo "<pre>",print_r($data->taskInstructNotes);die;
								$sql="SELECT id from tbl_tasks_units where task_id=".$data->taskInstructNotes->task_id." AND servicetask_id=".$data->taskInstructNotes->servicetask_id;
								$taskunit_id= Yii::$app->db->createCommand($sql)->queryScalar();
								if($taskunit_id>0){
									$servicesql="SELECT service_task FROM tbl_servicetask WHERE id IN (select servicetask_id from tbl_tasks_units where id=$taskunit_id)";
									$servicetask=Yii::$app->db->createCommand($servicesql)->queryScalar();
								}

							}
							$project_id=$data->taskInstructNotes->tasks->id;
							$project_name=$data->taskInstructNotes->tasks->activeTaskInstruct->project_name;
							$client_id=$data->taskInstructNotes->tasks->clientCase->client_id;
							$case_id=$data->taskInstructNotes->tasks->clientCase->id;
							$client_name=$data->taskInstructNotes->tasks->clientCase->client->client_name;
							$case_name=$data->taskInstructNotes->tasks->clientCase->case_name;
						}
					}
					if($type == 'Todo') {
						if(!empty($data->tasksUnitsTodos)){
							$taskunit_id=$data->tasksUnitsTodos->tasks_unit_id;
							if($taskunit_id>0){
								$servicesql="SELECT service_task FROM tbl_servicetask WHERE id IN (select servicetask_id from tbl_tasks_units where id=$taskunit_id)";
								$servicetask=Yii::$app->db->createCommand($servicesql)->queryScalar();
							}
							$project_id=$data->tasksUnitsTodos->taskUnit->task_id;
							$project_name=$data->tasksUnitsTodos->taskUnit->tasks->activeTaskInstruct->project_name;
							$client_id=$data->tasksUnitsTodos->taskUnit->tasks->clientCase->client_id;
							$case_id=$data->tasksUnitsTodos->taskUnit->tasks->clientCase->id;
							$client_name=$data->tasksUnitsTodos->taskUnit->tasks->clientCase->client->client_name;
							$case_name=$data->tasksUnitsTodos->taskUnit->tasks->clientCase->case_name;
						}
					}
					if($type == 'Comment') {
						if(!empty($data->comments)) {
							$project_id=$data->comments->task_id;
							$project_name=$data->comments->tasks->activeTaskInstruct->project_name;
							$client_id=$data->comments->tasks->clientCase->client_id;
							$case_id=$data->comments->tasks->clientCase->id;
							$client_name=$data->comments->tasks->clientCase->client->client_name;
							$case_name=$data->comments->tasks->clientCase->case_name;
						}
					}
					if($type == 'Data Statistics') {
						if(!empty($data->tasksUnits)) {
							$taskunit_id=$data->tasksUnits->id;
							if($taskunit_id>0){
								$servicesql="SELECT service_task FROM tbl_servicetask WHERE id IN (select servicetask_id from tbl_tasks_units where id=$taskunit_id)";
								$servicetask=Yii::$app->db->createCommand($servicesql)->queryScalar();
							}
							$project_id=$data->tasksUnits->task_id;
							$project_name=$data->tasksUnits->tasks->activeTaskInstruct->project_name;
							$client_id=$data->tasksUnits->tasks->clientCase->client_id;
							$case_id=$data->tasksUnits->tasks->clientCase->id;
							$client_name=$data->tasksUnits->tasks->clientCase->client->client_name;
							$case_name=$data->tasksUnits->tasks->clientCase->case_name;
						}
					}
					if($data->origination=="instruct N") {
						$data->origination="instruct note";
					}
					if($project_id > 0) {

						$file = urlencode($data->fname); //Trace the filename
						$file_blob_data=utf8_decode($data->mydocumentsBlobs->doc);
						
						$zip->addFromString($file, $file_blob_data); //adding blob data from DB
						
						//$fileSize += $data->doc_size;
						if (2 & ini_get('mbstring.func_overload')) {
							$fileSize += mb_strlen($file_blob_data, '8bit');
						} else {
							$fileSize += strlen($file_blob_data);
						}
						$export_data=explode(" ",$this->formatSizeUnits($fileSize));
						//print_r($export_data);die;
						if($export_data[0] > 1 && $export_data[1]=='GB'){
							$fileSize=0;
							$zip->close();
							$zip = new \ZipArchive();
							$zipfilename = $basepath."export_document_" . date('m_d_Y_H_i_s') . ".zip";
							//echo $zipfilename;die;
							if ($zip->open($zipfilename, \ZIPARCHIVE::CREATE | \ZIPARCHIVE::OVERWRITE) !== true) {
								echo "Cannot Open for writing";die;
							}
						}
				        //echo 'size of the file is : ' . $this->formatSizeUnits($fileSize) ."<br>";

						$path_parts = pathinfo($data->fname);
						$activeSheet->SetCellValue('A'.$j,$client_id);
						$activeSheet->SetCellValue('B'.$j,$client_name);
						$activeSheet->SetCellValue('C'.$j,$case_id);
						$activeSheet->SetCellValue('D'.$j,$case_name);
						$activeSheet->SetCellValue('E'.$j,$project_id);
						$activeSheet->SetCellValue('F'.$j,$project_name);
						$activeSheet->SetCellValue('G'.$j,$taskunit_id);
						$activeSheet->SetCellValue('H'.$j,$servicetask);
						$activeSheet->SetCellValue('I'.$j,ucwords($data->origination));
						$activeSheet->SetCellValue('J'.$j,$path_parts['filename']);
						$activeSheet->SetCellValue('K'.$j,$data->fname);
						$j++;
					}
					if(strtolower($type)=='case'){

						$file = urlencode($data->fname);
						$file_blob_data=utf8_decode($data->mydocumentsBlobs->doc);
						$zip->addFromString($file, $file_blob_data); 
						if (2 & ini_get('mbstring.func_overload')) {
							$fileSize += mb_strlen($file_blob_data, '8bit');
						} else {
							$fileSize += strlen($file_blob_data);
						}
						$export_data=explode(" ",$this->formatSizeUnits($fileSize));
						if($export_data[0] > 1 && $export_data[1]=='GB'){
							$fileSize=0;
							$zip->close();
							$zip = new \ZipArchive();
							$zipfilename = $basepath."export_document_" . date('m_d_Y_H_i_s') . ".zip";
							//echo $zipfilename;die;
							if ($zip->open($zipfilename, \ZIPARCHIVE::CREATE | \ZIPARCHIVE::OVERWRITE) !== true) {
								echo "Cannot Open for writing";die;
							}
						}
						$path_parts = pathinfo($data->fname);
						$activeSheet->SetCellValue('A'.$j,$client_id);
						$activeSheet->SetCellValue('B'.$j,$client_name);
						$activeSheet->SetCellValue('C'.$j,$case_id);
						$activeSheet->SetCellValue('D'.$j,$case_name);
						$activeSheet->SetCellValue('E'.$j,ucwords($data->origination));
						$activeSheet->SetCellValue('F'.$j,$path_parts['filename']);
						$activeSheet->SetCellValue('G'.$j,$data->fname);
						$j++;
					}
					if(strtolower($type)=='team'){
						$file = urlencode($data->fname);
						$file_blob_data=utf8_decode($data->mydocumentsBlobs->doc);
						$zip->addFromString($file, $file_blob_data); 
						if (2 & ini_get('mbstring.func_overload')) {
							$fileSize += mb_strlen($file_blob_data, '8bit');
						} else {
							$fileSize += strlen($file_blob_data);
						}
						$export_data=explode(" ",$this->formatSizeUnits($fileSize));
						if($export_data[0] > 1 && $export_data[1]=='GB'){
							$fileSize=0;
							$zip->close();
							$zip = new \ZipArchive();
							$zipfilename = $basepath."export_document_" . date('m_d_Y_H_i_s') . ".zip";
							if ($zip->open($zipfilename, \ZIPARCHIVE::CREATE | \ZIPARCHIVE::OVERWRITE) !== true) {
								echo "Cannot Open for writing";die;
							}
						}
						$path_parts = pathinfo($data->fname);
						$activeSheet->SetCellValue('A'.$j,$team_id);
						$activeSheet->SetCellValue('B'.$j,$team_name);
						$activeSheet->SetCellValue('C'.$j,ucwords($data->origination));
						$activeSheet->SetCellValue('D'.$j,$path_parts['filename']);
						$activeSheet->SetCellValue('E'.$j,$data->fname);
						$j++;
					}
				}
			}
		}
		
		$zip->close();
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save($basepath.$filename);
		echo "Zip created successfully";
      	exit;
		return ;
	}

	public function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824)
        {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        }
        elseif ($bytes >= 1048576)
        {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        }
        elseif ($bytes >= 1024)
        {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        }
        elseif ($bytes > 1)
        {
            $bytes = $bytes . ' bytes';
        }
        elseif ($bytes == 1)
        {
            $bytes = $bytes . ' byte';
        }
        else
        {
            $bytes = '0 bytes';
        }

        return $bytes;
}



}
