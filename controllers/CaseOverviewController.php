<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\helpers\HtmlPurifier;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use yii\db\Query;

use app\models\ClientCase;
use app\models\Tasks;
use app\models\User;
use app\models\ProjectSecurity;
use app\models\ClientCaseEvidence;
use app\models\Evidence;
use app\models\EvidenceCustodians;
use app\models\EvidenceProduction;

class CaseOverviewController extends \yii\web\Controller
{
	public function beforeAction($action)
	{
		if (Yii::$app->user->isGuest)
			$this->redirect(array('site/login'));
			
		if (!(new User)->checkAccess(4.13))/* 38 */
			throw new \yii\web\HttpException(404, 'User permissions do not allow entry into this module.');	
			
		$this->layout = 'mycase'; //your layout name
		return parent::beforeAction($action);
	}

	/**
     * Retrieve statistics of Tasks of a Case and present it using Pie Chart by Case ID.
     * @param integer $case_id
     * @return mixed
     */
    public function actionTotalProjects()
    {
    	$case_id = HtmlPurifier::process(Yii::$app->request->get('case_id',0));
    	$clientCase = ClientCase::find()->where(['id'=>$case_id]);
    	if ($case_id=="" || !is_numeric($case_id) || $case_id == 0) 
    	{
    		throw new \yii\web\NotFoundHttpException();
    	}
    	
    	$roleId = Yii::$app->user->identity->role_id;
    	$userId = Yii::$app->user->identity->id; 
    	$caseInfo = $clientCase->select('case_name')->one();
    	
	    if ($roleId != 0)
            $casedata = ProjectSecurity::find()->where(["user_id"=>$userId, 'client_case_id'=>$caseId]);
        else
            $casedata = ProjectSecurity::find()->where(["client_case_id"=>$caseId]);

	$taskdata_activestatus = array();
        $taskdata_cancel = 0;
        $taskdata_close = 0;
        $taskdata_count = 0;
        if (!empty($casedata)) {
            $taskdata_count = Tasks::find()->where(["client_case_id"=>$case_id])->count();
            $sql = "SELECT task_status, count(*) as statuscount FROM tbl_tasks t WHERE client_case_id=:client_case_id AND task_status IN (0,1,3,4) AND task_closed=0 AND task_cancel=0 GROUP BY task_status";
            $sql_new = "SELECT 6 as task_status, count(*) as statuscount FROM tbl_tasks t WHERE client_case_id=$case_id AND task_status IN (0,1,3,4) AND task_closed=0 AND task_cancel=1 
            UNION ALL
            SELECT 5 as task_status, count(*) as statuscount FROM tbl_tasks t WHERE client_case_id=$case_id AND task_status IN (0,1,3,4) AND task_closed=1 AND task_cancel=0 
            UNION ALL
            SELECT task_status, count(*) as statuscount FROM tbl_tasks t WHERE client_case_id=$case_id AND task_status IN (0,1,3,4) AND task_closed=0 AND task_cancel=0 GROUP BY task_status";
            $taskdata_activestatus = Tasks::findBySql($sql_new)->all();
            //$taskdata_activestatus = Tasks::findBySql($sql_new, [':client_case_id'=>$case_id])->all();
            //$taskdata_cancel = Tasks::find()->where(["client_case_id"=>$case_id, 'task_cancel'=>1, 'task_closed'=>0])->count();
            //$taskdata_close = Tasks::find()->where(["client_case_id"=>$case_id, 'task_closed'=>1, 'task_cancel'=>0])->count();
        }
        //echo "<pre>",print_r($taskdata_activestatus),"</pre>";die;
		
        $notstarted = 0;
        $started = 0;
        $onhold = 0;
        $complate = 0;
        $closed = 0;
        $cancelled = 0;
        if (!empty($taskdata_activestatus) && $taskdata_count > 0) {
            foreach ($taskdata_activestatus as $taskastatus) {
            	if( $taskastatus->statuscount > 0) {
                    if ($taskastatus->task_status == 0)
                        $notstarted = number_format((($taskastatus->statuscount / $taskdata_count) * 100), 2, '.', ',');
                    else if ($taskastatus->task_status == 1)
                        $started = number_format((($taskastatus->statuscount / $taskdata_count) * 100), 2, '.', ',');
                    else if ($taskastatus->task_status == 3)
                        $onhold = number_format((($taskastatus->statuscount / $taskdata_count) * 100), 2, '.', ',');
                    else if ($taskastatus->task_status == 4)
                        $complate = number_format((($taskastatus->statuscount / $taskdata_count) * 100), 2, '.', ',');
                    else if ($taskastatus->task_status == 5) // closed
                        $closed = number_format((($taskastatus->statuscount / $taskdata_count) * 100), 2, '.', ',');
                    else if ($taskastatus->task_status == 6) // cancelled
                        $cancelled = number_format((($taskastatus->statuscount / $taskdata_count) * 100), 2, '.', ',');                        
            	}
            }	
        }
        //echo $taskdata_close,"=> Cancelled",$taskdata_cancel;die;
        /*if($taskdata_close > 0 && $taskdata_count > 0)	
			$closed = number_format((($taskdata_close / $taskdata_count) * 100), 2, '.', ',');
		
		if($taskdata_cancel > 0 && $taskdata_count > 0)
        	$cancelled = number_format((($taskdata_cancel / $taskdata_count) * 100), 2, '.', ',');*/
        	
        $totalcount = $notstarted + $started + $onhold + $complate + $closed + $cancelled;
        
        return $this->render('total-projects',['notstarted' => $notstarted, 'started' => $started, "onhold" => $onhold, "complate" => $complate, "closed" => $closed, "cancelled" => $cancelled,
            'case_id' => $case_id, 'caseInfo' => $caseInfo, 'totalcount'=>$totalcount]);
    }
	
    /**
     * Retrieve statistics of Medias of a Case and present it using Pie Chart by Case ID.
     * @param integer $case_id
     * @return mixed
     */
    public function actionTotalMediaProjects()
    {
    	$case_id = HtmlPurifier::process(Yii::$app->request->get('case_id',0));
    	$clientCase = ClientCase::find()->where(['id'=>$case_id]);
    	if ($case_id=="" || !is_numeric($case_id) || $case_id == 0 || $clientCase->count() == 0) 
    	{
            throw new \yii\web\NotFoundHttpException();
    	}
    	
    	$roleId = Yii::$app->user->identity->role_id;
    	$userId = Yii::$app->user->identity->id; 
    	$caseInfo = $clientCase->one();
    	
	if ($roleId != 0)
            $casedata = ProjectSecurity::find()->where(["user_id"=>$userId, 'client_case_id'=>$case_id]);
        else
            $casedata = ProjectSecurity::find()->where(["client_case_id"=>$case_id]);
            
    	$checkedIn = 0;
        $checkedOut = 0;
        $destroyed = 0;
        $moved = 0;
        $returned = 0;
        
        if (!empty($casedata)) {
            $caseEvidenceData = ClientCaseEvidence::find()->select('evid_num_id')->where('client_case_id='.$case_id)->groupBy('evid_num_id');
            $evidenceDataCount = Evidence::find()->where(['in','id',$caseEvidenceData])->andWhere('status!=0')->count();
            $evidenceData = Evidence::find()->select(['status','count(*) as evidence_by_case'])->where(['in','id',$caseEvidenceData])->groupBy('status')->all();

        if (!empty($evidenceData) && $evidenceDataCount > 0) 
        {
            foreach ($evidenceData as $eviddata) {
                if($eviddata->evidence_by_case > 0) {
                    if ($eviddata->status == 1)
                        $checkedIn = number_format((($eviddata->evidence_by_case / $evidenceDataCount) * 100), 2, '.', ',');
                    else if ($eviddata->status == 2)
                        $checkedOut = number_format((($eviddata->evidence_by_case / $evidenceDataCount) * 100), 2, '.', ',');
                    else if ($eviddata->status == 3)
                        $destroyed = number_format((($eviddata->evidence_by_case / $evidenceDataCount) * 100), 2, '.', ',');
                    else if ($eviddata->status == 4)
                        $moved = number_format((($eviddata->evidence_by_case / $evidenceDataCount) * 100), 2, '.', ',');
                    else if ($eviddata->status == 5)
                        $returned = number_format((($eviddata->evidence_by_case / $evidenceDataCount) * 100), 2, '.', ',');
                    }
                }
            }
        }    

        $totalcount = $checkedIn + $checkedOut + $destroyed + $moved + $returned;
            
    	return $this->render('total-media-projects',['checkedIn' => $checkedIn, 'checkedOut' => $checkedOut, 'destroyed' => $destroyed,
            'moved' => $moved, 'returned' => $returned, 'case_id' => $case_id, 'caseInfo' => $caseInfo, 'totalcount'=>$totalcount]);
    }
    
    /**
     * Retrieve statistics of Medias Type by size of a Case and present it using Expanded Column Chart by Case ID.
     * @param integer $case_id
     * @return mixed
     */
    public function actionTotalMediaUnitSize()
    {
    	$case_id = HtmlPurifier::process(Yii::$app->request->get('case_id',0));
    	$clientCase = ClientCase::find()->where(['id'=>$case_id]);
    	if ($case_id=="" || !is_numeric($case_id) || $case_id == 0 || $clientCase->count() == 0) 
    	{
    		throw new \yii\web\NotFoundHttpException();
    	}
    	
    	$roleId = Yii::$app->user->identity->role_id;
    	$userId = Yii::$app->user->identity->id; 
    	$caseInfo = $clientCase->one();
    	
    	if ($roleId != 0)
            $casedata = ProjectSecurity::find()->where(["user_id"=>$userId, 'client_case_id'=>$case_id]);
        else
            $casedata = ProjectSecurity::find()->where(["client_case_id"=>$case_id]);
    	
        $eviddataArr = array();
        $unitArr = array();
        $evidTypeArr = array();
        $mediasizeArr = array();    
    	if (!empty($casedata)) 
    	{
            $sql = "SELECT tbl_evidence_type.evidence_name, COUNT(tbl_evidence.evid_type) as count_evid_type, SUM(CASE WHEN tbl_evidence.unit <> '' THEN tbl_evidence.contents_total_size ELSE tbl_evidence.contents_total_size_comp END) as totalsize, (CASE WHEN tbl_evidence.unit <> '' THEN tbl_unit.unit_name ELSE evidcomp.unit_name END) as unitname
                FROM tbl_evidence_type 
                INNER JOIN tbl_evidence ON tbl_evidence.evid_type = tbl_evidence_type.id 
                LEFT JOIN tbl_unit ON tbl_evidence.unit = tbl_unit.id 
                LEFT JOIN tbl_unit evidcomp ON tbl_evidence.comp_unit = evidcomp.id 
                WHERE tbl_evidence.id IN ( 
                    SELECT evid_num_id FROM tbl_client_case_evidence WHERE client_case_id={$case_id} GROUP BY evid_num_id 
                ) GROUP BY tbl_evidence_type.evidence_name, (CASE WHEN tbl_evidence.unit <> '' THEN tbl_unit.unit_name ELSE evidcomp.unit_name END)";
            $connection = \Yii::$app->db;
            $unitArr = $connection->createCommand($sql)->queryAll();
	}
	
        //echo "<pre>",print_r($unitArr),"</pre>";die;
    	$mediaTypeseries = array();
    	if (!empty($unitArr)) 
    	{
            foreach ($unitArr as $unitdata) 
            {
                $mediasizeArr[$unitdata['evidence_name']][] = array($unitdata['unitname'],(int)$unitdata['totalsize']);
                $mediaTypeseries[$unitdata['evidence_name']] += $unitdata['count_evid_type'];
            }
        }
        
        //echo "<pre>",print_r($mediasizeArr),"</pre>";die;
        $mediaseries = array();
        foreach ($mediasizeArr as $key => $data) 
        {
            $mediaseries[] = array(
                'name' => $key,
                'id' => $key,
                'data' => $data
            );
        }
        
        //echo 
        $mdeiaunitsizejson = json_encode($mediaseries);
        $evidtypejson = 0;
        if (!empty($mediaTypeseries)) {
            $evidtypejson = json_encode($mediaTypeseries);
        }
        
    	return $this->render('total-media-unit-size',['evidtypejson' => $evidtypejson, 'mdeiaunitsizejson' => $mdeiaunitsizejson, 'case_id' => $case_id, 'caseInfo' => $caseInfo]);
    }
    
    /**
     * Retrieve statistics of Medias by custodian of a Case and present it using Pie Chart by Case ID.
     * @param integer $case_id
     * @return mixed
     */
    public function actionMediaByCustodian() 
    {
    	$case_id = HtmlPurifier::process(Yii::$app->request->get('case_id',0));
    	$clientCase = ClientCase::find()->where(['id'=>$case_id]);
    	if ($case_id=="" || !is_numeric($case_id) || $case_id == 0 || $clientCase->count() == 0) 
    	{
    		throw new \yii\web\NotFoundHttpException();
    	}
    	
    	$roleId = Yii::$app->user->identity->role_id;
    	$userId = Yii::$app->user->identity->id; 
    	$caseInfo = $clientCase->one();
    	
    	if ($roleId != 0)
            $casedata = ProjectSecurity::find()->where(["user_id"=>$userId, 'client_case_id'=>$case_id]);
        else
            $casedata = ProjectSecurity::find()->where(["client_case_id"=>$case_id]);
            
		$mediacustodianArr = array();
        if (!empty($casedata)) 
        {
        	$mediacustodianArr = ArrayHelper::map(ClientCaseEvidence::find()
        		->select(['tbl_client_case_evidence.cust_id', "concat(tbl_evidence_custodians.cust_fname, ' ', tbl_evidence_custodians.cust_mi, ' ', tbl_evidence_custodians.cust_lname) as custodianname", 'count(tbl_client_case_evidence.evid_num_id) as evidnum'])
        		->joinWith('evidenceCustodians', true, 'INNER JOIN')
        		->where('tbl_client_case_evidence.cust_id !=0 AND tbl_client_case_evidence.evid_num_id !=0 AND tbl_client_case_evidence.client_case_id='.$case_id)
        		->groupBy(["tbl_client_case_evidence.cust_id", "concat(tbl_evidence_custodians.cust_fname, ' ', tbl_evidence_custodians.cust_mi, ' ', tbl_evidence_custodians.cust_lname)"])
        		->all(),'custodianname','evidnum');
        }
        
        $mediacustodianjson = json_encode($mediacustodianArr);
        
		return $this->render('media-by-custodians',['mediacustodianjson' => $mediacustodianjson, 'case_id' => $case_id, 'caseInfo' => $caseInfo]);
    }
    
    /**
     * Retrieve statistics of Production by custodian of a Case and present it using Pie Chart by Case ID.
     * @param integer $case_id
     * @return mixed
     */
    public function actionProductionByType() 
    {
    	$case_id = HtmlPurifier::process(Yii::$app->request->get('case_id',0));
    	$clientCase = ClientCase::find()->where(['id'=>$case_id]);
    	if ($case_id=="" || !is_numeric($case_id) || $case_id == 0 || $clientCase->count() == 0) 
    	{
    		throw new \yii\web\NotFoundHttpException();
    	}
    	
    	$roleId = Yii::$app->user->identity->role_id;
    	$userId = Yii::$app->user->identity->id; 
    	$caseInfo = $clientCase->one();
    	
    	if ($roleId != 0)
            $casedata = ProjectSecurity::find()->where(["user_id"=>$userId, 'client_case_id'=>$case_id]);
        else
            $casedata = ProjectSecurity::find()->where(["client_case_id"=>$case_id]);
            
		$productionArr = array();
        if (!empty($casedata)) 
        {
        	$totalProduction = EvidenceProduction::find()->where(['client_case_id'=>$case_id])->andWhere('production_type!=0')->count();
        	$totalIncoming = EvidenceProduction::find()->where(['client_case_id'=>$case_id])->andWhere('production_type=1')->count();
        	$totalOutgoing = EvidenceProduction::find()->where(['client_case_id'=>$case_id])->andWhere('production_type=2')->count();
        	
        	if($totalProduction > 0 && $totalIncoming > 0) {
        		$productionIncoming = number_format((($totalIncoming / $totalProduction) * 100), 2, '.', ',');
        		$productionArr['Incoming'] = $productionIncoming;
        	}
			if($totalProduction > 0 && $totalOutgoing > 0){
		        $productionOutgoing = number_format((($totalOutgoing / $totalProduction) * 100), 2, '.', ',');
		        $productionArr['Outgoing'] = $productionOutgoing;
			}
        	
		    
        }
        $productionjson = json_encode($productionArr);
    	
    	return $this->render('production-by-type',['productionjson' => $productionjson, 'case_id' => $case_id, 'caseInfo' => $caseInfo]);
    }

    /**
     * Retrieve statistics of Production producing parties of a Case and present it using Pie Chart by Case ID.
     * @param integer $case_id
     * @return mixed
     */
    public function actionProductionProducingParties()
    {
    	$case_id = HtmlPurifier::process(Yii::$app->request->get('case_id',0));
    	$clientCase = ClientCase::find()->where(['id'=>$case_id]);
    	if ($case_id=="" || !is_numeric($case_id) || $case_id == 0 || $clientCase->count() == 0) 
    	{
    		throw new \yii\web\NotFoundHttpException();
    	}
    	
    	$roleId = Yii::$app->user->identity->role_id;
    	$userId = Yii::$app->user->identity->id; 
    	$caseInfo = $clientCase->one();
    	
    	if ($roleId != 0)
            $casedata = ProjectSecurity::find()->where(["user_id"=>$userId, 'client_case_id'=>$case_id]);
        else
            $casedata = ProjectSecurity::find()->where(["client_case_id"=>$case_id]);
            
		$productionArr = array();
        if (!empty($casedata)) 
        {
        	$productionArr = ArrayHelper::map(EvidenceProduction::find()->select(['count(prod_party) as production_party_count', 'prod_party'])->where(["client_case_id"=>$case_id])->andWhere("prod_party!=''")->groupBy(['prod_party'])->all(),'prod_party','production_party_count');
        }
        
   		$productionjson = json_encode($productionArr);
   		
    	return $this->render('production-producing-parties',['productionjson' => $productionjson, 'case_id' => $case_id, 'caseInfo' => $caseInfo]);
    }
}
