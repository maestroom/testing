<?php

namespace app\controllers;

use Yii;
use app\models\EvidenceProduction;
use app\models\search\EvidenceProductionSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

use app\models\EvidenceProductionMedia;
use app\models\ClientCaseEvidence;
use app\models\TaskInstructEvidence;
use app\models\Evidence;
use app\models\ClientCase;
use app\models\Mydocument;
use app\models\MydocumentsBlob;
use app\models\SettingsEmail;
use app\models\Client;
use app\models\EvidenceType;
use app\models\EvidenceCategory;
use app\models\Unit;
use app\models\EvidenceEncryptType;
use app\models\EvidenceStoredLoc;
use app\models\EvidenceProductionBates;
use app\models\Options;
use app\models\User;
use app\models\FormBuilderSystem;
use app\models\EmailCron;

use yii\helpers\Url;
use yii\db\Query;


/**
 * CaseProductionController implements the CRUD actions for EvidenceProduction model.
 */
class CaseProductionController extends Controller
{
    /**
     * @inheritdoc
     */
    public function beforeAction($action)
	{
		if (Yii::$app->user->isGuest)
			$this->redirect(array('site/login'));
			
		if (!(new User)->checkAccess(4.006))/* 38 */
			throw new \yii\web\HttpException(404, 'User permissions do not allow entry into this module.');	
			
			
		$this->layout = 'mycase'; //your layout name	
		
		return parent::beforeAction($action);
	} 
     
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
     * Lists all EvidenceProduction models.
     * @return mixed
     */
    public function actionIndex()
    {   
        
        $this->layout = "mycase";
        $prod_id = Yii::$app->request->get("prod_id","");
        $searchModel = new EvidenceProductionSearch();
        $params['grid_id']='dynagrid-caseproduction';
        Yii::$app->request->queryParams +=$params;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $case_id=Yii::$app->request->get('case_id');
        /*IRT 67,68,86,87,258*/
        /*IRT 96,398 */
        $filter_type=\app\models\User::getFilterType([
            'tbl_evidence_production.id',
            'tbl_evidence_production.production_type',
            'tbl_evidence_production.staff_assigned',
            'tbl_evidence_production.prod_date',
            'tbl_evidence_production.prod_rec_date',
            'tbl_evidence_production.prod_party',
            'tbl_evidence_production.has_media',
            'tbl_evidence_production.has_hold',
            'tbl_evidence_production.has_projects',
            'tbl_evidence_production.cover_let_link',
            'tbl_evidence_production.prod_orig',
            'tbl_evidence_production.prod_disclose',
            'tbl_evidence_production.prod_agencies',
            'tbl_evidence_production.prod_access_req',
            'tbl_evidence_production.prod_copied_to',
            'tbl_evidence_production.upload_files',
            'tbl_evidence_production.prod_return',
            'tbl_evidence_production.prod_misc1',
            'tbl_evidence_production.prod_misc2'
        ],['tbl_evidence_production']);
        
        $config = ['production_type'=>['All'=>'All',1=>'Incoming',2=>'Outgoing'],'prod_return'=>['All'=>'All',1=>'Yes',0=>'No'],'has_media'=>['All'=>'All',1=>'Yes',0=>'No'],'has_projects'=>['All'=>'All',1=>'Yes',0=>'No'],'has_hold'=>['All'=>'All',1=>'Yes',0=>'No'],'prod_orig'=>['All'=>'All',1=>'Yes',0=>'No']];       
		$config_widget_options = [];		
        $filterWidgetOption=\app\models\User::getFilterWidgetOption($filter_type,Url::toRoute(['case-production/ajax-filter']).'&case_id='.$case_id,$config,$config_widget_options);
        /*IRT 67,68,86,87,258*/
        $fileds=['id','production_type','has_media','has_hold','has_projects','staff_assigned','prod_date','prod_rec_date','prod_party','production_desc','cover_let_link','upload_files','prod_orig','attorney_notes','prod_disclose','prod_agencies','prod_access_req'];	
		$prod_form = ArrayHelper::map(FormBuilderSystem::find()->select(['sys_field_name'])->where(['sys_form'=>'production_form','sys_field_name'=>$fileds])->orderBy('sort_order')->all(),'sys_field_name','sys_field_name');
	    $Sql="update tbl_evidence_production set has_projects=1 WHERE tbl_evidence_production.id in (SELECT tbl_task_instruct_evidence.prod_id from tbl_task_instruct_evidence INNER JOIN tbl_task_instruct on tbl_task_instruct.id=tbl_task_instruct_evidence.task_instruct_id and tbl_task_instruct.isactive=1)";
		//Yii::$app->db->createCommand($sql)->execute();
        //$Sql="update tbl_evidence_production set has_projects=CASE WHEN   
        $sql_update_hasmedia="UPDATE tbl_evidence_production SET has_media=(CASE WHEN (SELECT COUNT(prod_id) FROM tbl_evidence_production_media where tbl_evidence_production_media.prod_id=tbl_evidence_production.id)>0 THEN 1 ELSE 0 END)";
        Yii::$app->db->createCommand($sql_update_hasmedia)->execute();
        $params = Yii::$app->request->queryParams;

        //echo "<pre>",print_r($prod_form),"</pre>"; die();
	    return $this->render('index', [
            'params'=>$params,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'case_id'=>$case_id,
        	'prod_id'=>$prod_id,
        	'filter_display' => $filter_display	,
        	'filter_type'=>$filter_type,
			'filterWidgetOption'=>$filterWidgetOption,
			'prod_form'=>$prod_form
        ]);
    }
    /**
    * Filter GridView with Ajax
    * */
    public function actionAjaxFilter(){
		$searchModel = new EvidenceProductionSearch();
		$dataProvider = $searchModel->searchFilter(Yii::$app->request->queryParams);
		$out['results']=array();
		foreach ($dataProvider as $key=>$val){
			$val1 = $val;
			$val2 = $val;
			if($val == ''){
				$val1 = '(not set)';
				$val='(not set)';
				$val2='(not set)';
			}
			
			$out['results'][] = ['id' => $val1, 'text' => $val,'label' => $val2];
		}
		return json_encode($out);
    }
    /**
     * Displays a single EvidenceProduction detail.
     * @param integer $id
     * @return mixed
     */
    public function actionGetProdDeatail() 
    {
		$production_form = ArrayHelper::map(FormBuilderSystem::find()->select(['sys_field_name'])->where(['sys_form'=>'production_form','grid_type'=>1])->orderBy('sort_order')->all(),'sys_field_name','sys_field_name');
		$prod_id = Yii::$app->request->post("expandRowKey");
		$prod_data = EvidenceProductionMedia::find()->joinWith([
			'prodbates'=>function (\yii\db\ActiveQuery $query) { 
				$query->joinWith(['task']);
			},
			'proevidence' => function (\yii\db\ActiveQuery $query) { 
				$query->joinWith(['evidenceunit','evidencecompunit','evidencecontent'=>function (\yii\db\ActiveQuery $query) {
					$query->joinWith(['evidenceCustodians']);
				}]);
		}])->where(['tbl_evidence_production_media.prod_id'=>$prod_id])->all();
		
    	if(!empty($prod_data)) {
			foreach ($prod_data as $mids) {
				if($mids->evid_id ==0)
					continue;
				if(!empty($mids->prodbates)) {
					foreach ($mids->prodbates as $prodbates) {
						if(!empty($prodbates->task)) {
							//echo "<pre>",$prodbates->task->id,"</pre>";
							$pr_link= (new EvidenceProduction)->getProjectsLink($prodbates->task->id);	
							$task_arr[$prodbates->task->id]=$pr_link; 
						}
					}
				}
			}
		}
		//die();
		return $this->renderPartial('_getprodgriddetail', ['production_form'=>$production_form,'prod_data'=>$prod_data,'task_arr'=>$task_arr]);
    }

    /**
     * Creates a new EvidenceProduction model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($case_id)
    {
        $this->layout = "mycase";
        $params=Yii::$app->request->post();
        $model = new EvidenceProduction();
        $case_data = ClientCase::findOne($case_id);
        /* list all existing staff and prod party list starts */
        //$prod_data = EvidenceProduction::find()->all();
        $staff_assigned_arr = ArrayHelper::map(EvidenceProduction::find()->select('staff_assigned')->where('client_case_id='.$case_id)->groupBy('staff_assigned')->all(),'staff_assigned','staff_assigned');
        $prod_party_arr = ArrayHelper::map(EvidenceProduction::find()->select('prod_party')->where('client_case_id='.$case_id)->groupBy('prod_party')->all(),'prod_party','prod_party');
        //foreach($prod_data as $prod){
            //$staff_assigned_arr[$prod->staff_assigned]=$prod->staff_assigned;
            //$prod_party_arr[$prod->prod_party]=$prod->prod_party;
        //}
        /* list all existing staff and prod party list ends */
        $model->client_case_id=$case_id;
        //$model->client_id=1;
        
        //$media_data = ClientCaseEvidence::find()->orderBy(['evid_num_id'=>SORT_ASC])->where(['client_case_id' => $case_id])->limit(100)->all();
        //->joinWith(['evidence'=>function (\yii\db\ActiveQuery $query) { $query->joinWith(['evidencetype']);}],false)
        //$media_list=array();
        //foreach($media_data as $media)
        //{
        	//$evidence=$media->evidence;
            //$media_list[$evidence->id]=$evidence->id.' '.$evidence->evidencetype->evidence_name;
        //}

        if ($model->load(Yii::$app->request->post())) {
             $prod_agencies=$params['EvidenceProduction']['prod_agencies'];
             if (isset($prod_agencies) && ($prod_agencies != '')) {
                $prod_agencies_arr=explode("/",$prod_agencies);
                $prod_agencies_date=$prod_agencies_arr[2]."/".$prod_agencies_arr[0]."/".$prod_agencies_arr[1];
                $time = (new Options)->ConvertOneTzToAnotherTz(date('H:i:s'), 'UTC', $_SESSION['usrTZ'],  "HIS");
                $prod_agencies_time=$prod_agencies_date.' '.$time;
                $prod_agencies_time = (new Options)->ConvertOneTzToAnotherTz($prod_agencies_time, $_SESSION['usrTZ'],'UTC', "YMDHIS");
                $model->prod_agencies = $prod_agencies_time;
            }
            $prod_access_req=$params['EvidenceProduction']['prod_access_req'];
            if (isset($prod_access_req) && ($prod_access_req != '')) {
                $prod_access_req_arr=explode("/",$prod_access_req);
                $prod_access_req_date=$prod_access_req_arr[2]."/".$prod_access_req_arr[0]."/".$prod_access_req_arr[1];
                $time = (new Options)->ConvertOneTzToAnotherTz(date('H:i:s'), 'UTC', $_SESSION['usrTZ'],  "HIS");
                $prod_access_req_time=$prod_access_req_date.' '.$time;
                $prod_access_req_time = (new Options)->ConvertOneTzToAnotherTz($prod_access_req_time, $_SESSION['usrTZ'],'UTC', "YMDHIS");
                $model->prod_access_req = $prod_access_req_time;
            }
            
            if(isset($params['EvidenceProduction']['prod_date']) && $params['EvidenceProduction']['prod_date']!=""){
                $prod_date = explode("/", $params['EvidenceProduction']['prod_date']);
             	if (Yii::$app->db->driverName == 'sqlsrv') {
					if($prod_date[2]."/".$prod_date[0]."/".$prod_date[1] != '0000/00/00')
                    	$model->prod_date = $prod_date[2]."/".$prod_date[0]."/".$prod_date[1];
                    else 
						$model->prod_date = 0;
                }
                else
                    $model->prod_date = $prod_date[2].'/'.$prod_date[0].'/'.$prod_date[1];
            }
            
            if(isset($params['EvidenceProduction']['prod_rec_date']) && $params['EvidenceProduction']['prod_rec_date']!="") {
                $prod_rec_date = explode("/", $params['EvidenceProduction']['prod_rec_date']);
                if (DB_TYPE == 'sqlsrv')
                    $model->prod_rec_date = $prod_rec_date[2]."/".$prod_rec_date[0]."/".$prod_rec_date[1];
                else
                    $model->prod_rec_date = $prod_rec_date[2] . '/' . $prod_rec_date[0] . '/' . $prod_rec_date[1];
            }
            
            if (isset($params['attachedMedia']) && ($params['attachedMedia'] != "" || $params['attachedMedia'] != 0)) {
                $model->has_media = 1;
            }
//            echo '<pre>';
//            print_r($model);
//            die;
             if($model->save())
             {
                $evid_production_id =Yii::$app->db->getLastInsertId();
                /* Code for evidence attachment start */
                if(!empty($_FILES['EvidenceProduction']['name']['upload_files'][0]))
                {
                    $docmodel = new Mydocument();
                    $doc_arr['p_id']=0;
                    $doc_arr['reference_id']=$evid_production_id;
                    $doc_arr['team_loc']=0;
                    $doc_arr['origination']='Production';
                    $doc_arr['is_private']='';
                    $doc_arr['type']='F';
                    $doc_arr['is_private']='';
                    $file_arr=$docmodel->Savemydocs('EvidenceProduction','upload_files',$doc_arr);
                }   
                /* Code for evidence attachment end */
               
                if (isset($params['attachedMedia']) && ($params['attachedMedia'] != "" || $params['attachedMedia'] != 0)) {
                    foreach (explode(",", $params['attachedMedia']) as $media) {
                        if (isset($media) && $media == 0) {
                            continue;
                        }
                         /* Code for evidence Production media add/update starts */
                        $EvidProdMedia = new EvidenceProductionMedia();
                        if(EvidenceProductionMedia::find()->where(['prod_id'=>0,'evid_id'=> intval($media)])->count() == 0)
                        {
                            $EvidProdMedia->prod_id = $evid_production_id;
                            $EvidProdMedia->evid_id = $media;
                            $EvidProdMedia->save(false);
                        } else {
                            EvidenceProductionMedia::updateAll(['prod_id' => $evid_production_id],['evid_id' => intval($media),'prod_id'=>0]);
                        } 
                        /* Code for evidence Production media add/update ends */
                        
                        /* Code for client case  add/update starts */         
                        $ccevid_model = ClientCaseEvidence::find()->where(['client_case_id'=>$params['EvidenceProduction']['client_case_id'],'evid_num_id'=>intval($media)])->one();
                        if (isset($ccevid_model->id) && $ccevid_model->id != 0) {
                        } else {
                           $ccevid_model = new ClientCaseEvidence();
                           $client_case_data=ClientCase::findOne($params['EvidenceProduction']['client_case_id']);
                           $ccevid_model->client_id = $client_case_data->client_id;
                           $ccevid_model->cust_id = 0;
                        }
                        // $ccevid_model->client_id = $params['EvidenceProduction']['client_id'];
                        $ccevid_model->client_case_id = $params['EvidenceProduction']['client_case_id'];
                        $ccevid_model->evid_num_id = intval($media);
                        $ccevid_model->save(FALSE);
                        /* Code for client case  add/update ends */     
                    }
                }  else {            
                    // Commented below on 28-11-2016 by Nelson 
                    /* IRT 21 Changes Done 14-march-2017 */
                    //SettingsEmail::sendEmail
                    EmailCron::saveBackgroundEmail(23, 'is_sub_production_posted', $data = array('case_id' => $case_id, 'prod_id' => $model->getPrimaryKey()));
                }
             }
             return $this->redirect(['index', 'case_id' => $params['EvidenceProduction']['client_case_id']]);
        } else {
            $evidences_production = (new User)->getTableFieldLimit('tbl_evidence_production'); 			            
            return $this->render('create', [
                'case_id' =>$case_id,'model' => $model, 'media_list' => $media_list, 'case_data' => $case_data, 'staff_assigned_arr' => $staff_assigned_arr,'prod_party_arr' => $prod_party_arr,'evidences_production' => $evidences_production
            ]);
        }
    }
    /**
     * Creates a new EvidenceProduction model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionSearchMedia($case_id)
    {
    	$q = Yii::$app->request->get('q',NULL);
    	$client_case_sql="SELECT evid_num_id FROM tbl_client_case_evidence WHERE client_case_id =".$case_id." GROUP BY tbl_client_case_evidence.evid_num_id ";
    	\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    	$out = ['results' => ['id' => '', 'text' => '']];
    	$query = new Query;
    	$query->select('id, id AS text')->from('tbl_evidence')->where('id IN ('.$client_case_sql.') AND status NOT IN (3,5)');
    	if (!is_null($q)) {
    		$query->andWhere(['like', 'id', $q]);
    	}
    	$query->limit(100);
    	$command = $query->createCommand();
    	$data = $command->queryAll();
    	$out['results'] = array_values($data);
    	return $out;
    }
    public function actionAttachExistingMedia() {
        $params=Yii::$app->request->post();
       
        $data = Evidence::find()->joinWith(['evidencetype','evidenceunit','evidencecompunit'])->where(['tbl_evidence.id'=>intval($params['id'])])->one();
        if (isset($params['EvidenceProductionBates']) && !empty($params['EvidenceProductionBates'])) {
            $model = new EvidenceProductionBates();
            $model->attributes = $params['EvidenceProductionBates'];
            $model->evid_id = $params['Evidence']['id'];
            if (isset($params['EvidenceProductionBates']['prod_date_loaded']) && $_POST['EvidenceProductionBates']['prod_date_loaded'] != "") {
                $prod_date_loaded = explode("-", $params['EvidenceProductionBates']['prod_date_loaded']);
                $model->prod_date_loaded = $prod_date_loaded[2] . "-" . $prod_date_loaded[0] . "-" . $prod_date_loaded[1];
            }
            $model->save(false);
        }
        //echo "<pre>"; print_r($data); die;
        return $this->renderPartial('attachExitingMedia', ['data' => $data]);
    }
     /**
     * Attach New media for current production.
     * If creation is successful, the browser will be redirected to the 'add production' page.
     * @return mixed
     */
    public function actionAttachNewMedia()
    {
        
        $model = new Evidence();
        $params=Yii::$app->request->post();
        //print_r($params);
        if(!empty($params['case_id'])){
			$params['client_id'] = ClientCase::find()->where(['tbl_client_case.id'=>$params['case_id']])->one()->client_id;
		}
        $clientList = ArrayHelper::map(Client::find()->where([])->asArray()->all(), 'id', 'client_name');
        $listEvidenceType = ArrayHelper::map(EvidenceType::find()->where(['remove'=>'0'])->orderBy(['evidence_name'=>SORT_ASC])->asArray()->all(), 'id', 'evidence_name');
        $listEvidenceCategory = ArrayHelper::map(EvidenceCategory::find()->where(['remove'=>'0'])->orderBy(['category'=>SORT_ASC])->asArray()->all(), 'id', 'category');
        $listUnit = ArrayHelper::map(Unit::find()->where(['remove'=>'0'])->orderBy(['unit_name'=>SORT_ASC])->asArray()->all(), 'id', 'unit_name');
        $listEvidenceEncrypt = ArrayHelper::map(EvidenceEncryptType::find()->where(['remove'=>'0'])->orderBy(['encrypt'=>SORT_ASC])->asArray()->all(), 'id', 'encrypt');
        $listEvidenceLoc = ArrayHelper::map(EvidenceStoredLoc::find()->where(['remove'=>'0'])->orderBy(['stored_loc'=>SORT_ASC])->asArray()->all(), 'id', 'stored_loc');
        
        $arr_time = [0,15,30,45];
        $curr_time = (new Options)->ConvertOneTzToAnotherTz(date('H:i:s'), 'UTC', $_SESSION['usrTZ']);

        $current_minute = date('i',strtotime($curr_time));
        foreach ($arr_time as $a) {
            if ($a <= $current_minute) {
                if($a == 0) $rec_time = date("H:00 A",strtotime($curr_time));
                else $rec_time = date("H:$a A",strtotime($curr_time));
            }
        }
        
        $model->received_time=$rec_time;
        
        return $this->renderAjax('attachnewmedia', [
           'model' => $model,
           'params'=>$params,
           'listEvidenceType'=>$listEvidenceType,
           'listEvidenceCategory'=>$listEvidenceCategory,
           'listUnit'=>$listUnit,
           'listEvidenceEncrypt'=>$listEvidenceEncrypt,
           'listEvidenceLoc'=>$listEvidenceLoc,
           'rec_time'=>$rec_time
       ]);
    }
    /**
     * Updates an existing EvidenceProduction model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = EvidenceProduction::find()->where(['tbl_evidence_production.id' => $id])->joinWith(['productionmedia'])->one();
        /* list all existing staff and prod party list starts */
        $case_id = Yii::$app->request->get('case_id',0);
        $staff_assigned_arr = ArrayHelper::map(EvidenceProduction::find()->select('staff_assigned')->where('client_case_id='.$case_id)->groupBy('staff_assigned')->all(),'staff_assigned','staff_assigned');
        $prod_party_arr = ArrayHelper::map(EvidenceProduction::find()->select('prod_party')->where('client_case_id='.$case_id)->groupBy('prod_party')->all(),'prod_party','prod_party');
        /*$prod_data = EvidenceProduction::find()->all();
        $staff_assigned_arr=array();
        $prod_party_arr=array();
        foreach($prod_data as $prod){
            $staff_assigned_arr[$prod->staff_assigned]=$prod->staff_assigned;
            $prod_party_arr[$prod->prod_party]=$prod->prod_party;
        }*/
        /* list all existing staff and prod party list ends */
        $data_exising_media=array();
        $media_ids=array();
        if(!empty($model['productionmedia']))
        {
            foreach($model['productionmedia'] as $prdmedia)
            {
                $media_ids[]=$prdmedia->evid_id;
            }
            $data_exising_media = Evidence::find()->joinWith(['evidencetype','evidenceunit','evidencecompunit'])->where(['in','tbl_evidence.id',$media_ids])->all();
        }    
        //$media_data = ClientCaseEvidence::find()->joinWith(['evidence'=>function (\yii\db\ActiveQuery $query) { $query->joinWith(['evidencetype']);}])->orderBy(['evid_num_id'=>SORT_ASC])->where(['client_case_id' => $model->client_case_id])->all();
        //$media_list=array();
        //foreach($media_data as $media){
        //	$media_list[$media->evidence->id]=$media->evidence->id.' '.$media->evidence->evidencetype->evidence_name;
        //}
        $production_docs = MyDocument::find()->joinWith(['mydocumentsBlobs'])->select(['tbl_mydocument.id','fname', 'doc_size','doc_type','doc_id','tbl_mydocuments_blob.doc'])->where(['tbl_mydocument.reference_id'=>(int) $id,'tbl_mydocument.origination'=>'Production'])->all();
         $this->layout = "mycase";
        if (Yii::$app->request->post()){
            $params=Yii::$app->request->post();
            $model->load(Yii::$app->request->post());
            if(isset($params['EvidenceProduction']['prod_date']) && $params['EvidenceProduction']['prod_date']!=""){
                $prod_date = explode("/", $params['EvidenceProduction']['prod_date']);
             	if (Yii::$app->db->driverName == 'sqlsrv') {
					if($prod_date[2]."/".$prod_date[0]."/".$prod_date[1] != '0000/00/00')
                    	$model->prod_date = $prod_date[2]."/".$prod_date[0]."/".$prod_date[1];
                    else 
						$model->prod_date = 0;
                }
                else
                    $model->prod_date = $prod_date[2] . '/' . $prod_date[0] . '/' . $prod_date[1];
            }
            if(isset($params['EvidenceProduction']['prod_rec_date']) && $params['EvidenceProduction']['prod_rec_date']!=""){
                $prod_rec_date = explode("/", $params['EvidenceProduction']['prod_rec_date']);
                if (DB_TYPE == 'sqlsrv')
                    $model->prod_rec_date = $prod_rec_date[2]."/".$prod_rec_date[0]."/".$prod_rec_date[1];
                else
                    $model->prod_rec_date = $prod_rec_date[2] . '/' . $prod_rec_date[0] . '/' . $prod_rec_date[1];
            }
            /*if(isset($params['EvidenceProduction']['prod_agencies']) && $params['EvidenceProduction']['prod_agencies']!=""){
                $prod_agn_date = explode("-", $params['EvidenceProduction']['prod_agencies']);
                if (DB_TYPE == 'sqlsrv')
                    $model->prod_agencies = $prod_agn_date[2]."-".$prod_agn_date[0]."-".$prod_agn_date[1];
                else
                    $model->prod_agencies = $prod_agn_date[2] . '-' . $prod_agn_date[0] . '-' . $prod_agn_date[1];
            }
            if(isset($params['EvidenceProduction']['prod_access_req']) && $params['EvidenceProduction']['prod_access_req']!=""){
                $prod_acc_date = explode("-", $params['EvidenceProduction']['prod_access_req']);
                if (DB_TYPE == 'sqlsrv')
                    $model->prod_access_req = $prod_acc_date[2]."-".$prod_acc_date[0]."-".$prod_acc_date[1];
                else
                    $model->prod_access_req = $prod_acc_date[2] . '-' . $prod_acc_date[0] . '-' . $prod_acc_date[1];
            } */
            
            
            $prod_agencies=$params['EvidenceProduction']['prod_agencies'];
             if (isset($prod_agencies) && ($prod_agencies != '')) {
                $prod_agencies_arr=explode("/",$prod_agencies);
                $prod_agencies_date=$prod_agencies_arr[2]."/".$prod_agencies_arr[0]."/".$prod_agencies_arr[1];
                $time = (new Options)->ConvertOneTzToAnotherTz(date('H:i:s'), 'UTC', $_SESSION['usrTZ'],  "HIS");
                $prod_agencies_time=$prod_agencies_date.' '.$time;
                $prod_agencies_time = (new Options)->ConvertOneTzToAnotherTz($prod_agencies_time, $_SESSION['usrTZ'],'UTC', "YMDHIS");
                $model->prod_agencies = $prod_agencies_time;
            }
            $prod_access_req=$params['EvidenceProduction']['prod_access_req'];
             if (isset($prod_access_req) && ($prod_access_req != '')) {
                $prod_access_req_arr=explode("/",$prod_access_req);
                $prod_access_req_date=$prod_access_req_arr[2]."/".$prod_access_req_arr[0]."/".$prod_access_req_arr[1];
                $time = (new Options)->ConvertOneTzToAnotherTz(date('H:i:s'), 'UTC', $_SESSION['usrTZ'],  "HIS");
                $prod_access_req_time=$prod_access_req_date.' '.$time;
                $prod_access_req_time = (new Options)->ConvertOneTzToAnotherTz($prod_access_req_time, $_SESSION['usrTZ'],'UTC', "YMDHIS");
                $model->prod_access_req = $prod_access_req_time;
            }
            if (isset($params['attachedMedia']) && ($params['attachedMedia'] != "" || $params['attachedMedia'] != 0)) {
                $model->has_media = 1;
            }
            //echo "<pre>"; print_r($model);die;
            $model->save();
            $deletedMedias = $params['deleted_medias'];
            if ($deletedMedias != "" && $deletedMedias != 0){
                EvidenceProductionMedia::deleteAll("prod_id=$id and evid_id IN ($deletedMedias)");
            }
            /* Code for Production attachment start */
            if(!empty($_FILES['EvidenceProduction']['name']['upload_files'][0]))
            {
                $docmodel = new Mydocument();
                $doc_arr['p_id']=0;
                $doc_arr['reference_id']=$id;
                $doc_arr['team_loc']=0;
                $doc_arr['origination']='Production';
                $doc_arr['is_private']='0';
                $doc_arr['type']='F';
                
                
                $file_arr=$docmodel->Savemydocs('EvidenceProduction','upload_files',$doc_arr);
                
            }   
            /* Code for Production attachment end */
            /* Code for Production attachment delete start */
            if ($params['production_deleted_docs'] != "") {
                $deleted_attach_arr = explode(",", $params['production_deleted_docs']);
                foreach ($deleted_attach_arr as $delte_file) {
                   $evidencedocs = Mydocument::find()->where(["id"=>$delte_file])->select(['doc_id'])->all();
                    if(!empty($evidencedocs))
                    {
                        foreach($evidencedocs as $edoc)
                        {
                            MydocumentsBlob::deleteAll(['id'=>$edoc['doc_id']]);
                        }
                    }
                }
                Mydocument::deleteAll(['in', 'id', $deleted_attach_arr]);
            }
            /* Code for Production attachment delete ends */
            /* Code for Production Media attachment starts */
            if (isset($params['attachedMedia']) && ($params['attachedMedia'] != "" || $params['attachedMedia'] != 0)) {
                $attachedMedias = explode(",", $params['attachedMedia']);
                foreach ($attachedMedias as $media) {
                	$media = intval($media);
                    if (isset($media) && $media == 0) {
                        continue;
                    }
                    if (EvidenceProductionMedia::find()->where(["prod_id"=>$id,'evid_id'=>$media])->count() == 0) {
                        $EvidProdBates = new EvidenceProductionMedia();
                        $EvidProdBates->prod_id = $id;
                        $EvidProdBates->evid_id = $media;
                        $EvidProdBates->save(false);
                    }
                    /* Adding Data in table client case cust */
                    $ccevid_model = ClientCaseEvidence::find()->where(['client_case_id'=>$params['EvidenceProduction']['client_case_id'],'evid_num_id'=>$media])->one();
                    if (isset($ccevid_model->id) && $ccevid_model->id != 0) {

                    } else {
                        $ccevid_model = new ClientCaseEvidence();
                        $client_case_data=ClientCase::findOne($params['EvidenceProduction']['client_case_id']);
						$ccevid_model->client_id = $client_case_data->client_id;
						$ccevid_model->cust_id = 0;
                    }
                   // $ccevid_model->client_id = $params['EvidenceProduction']['client_id'];
                    $ccevid_model->client_case_id =$params['EvidenceProduction']['client_case_id'];
                    $ccevid_model->evid_num_id = $media;
                    $ccevid_model->save(FALSE);
                    /* End of adding data in table client case cust */
                }
            }
            /* Code for Production Media attachment ends */
            return $this->redirect(['index','case_id'=>$params["EvidenceProduction"]["client_case_id"]]);
        } else {
            
            if($model->prod_agencies != '' && $model->prod_agencies != '0000-00-00 00:00:00'){
                $model->prod_agencies=(new Options)->ConvertOneTzToAnotherTz($model->prod_agencies, 'UTC', $_SESSION['usrTZ'],'date');
            }else{$model->prod_agencies = '';}
            
            if($model->prod_access_req != '' && $model->prod_access_req != '0000-00-00 00:00:00'){
                $model->prod_access_req=(new Options)->ConvertOneTzToAnotherTz($model->prod_access_req, 'UTC', $_SESSION['usrTZ'],'date');
            }else{$model->prod_access_req = '';}
            $evidences_production = (new User)->getTableFieldLimit('tbl_evidence_production'); 			            
            return $this->render('update', [
            	'case_id' =>$case_id,'model' => $model,'media_list'=>$media_list,'data_exising_media'=>$data_exising_media,'production_docs'=>$production_docs,'staff_assigned_arr'=>$staff_assigned_arr,'prod_party_arr'=>$prod_party_arr,
            	'evidences_production' => $evidences_production
            ]);
        }
    }
    public function actionRemovemediaproduction() {
        $params=Yii::$app->request->get();
        $is_allow = "Notallow";
        if (isset($params['prod_id']) && ($params['prod_id'] != "" && $params['prod_id'] != 0) && ($params['mid'] != "" && $params['mid'] != 0)) {
            $evidpromedia_data = EvidenceProductionMedia::find()->where(['evid_id'=> $params['mid'],'prod_id'=>$params['prod_id']])->one();
            if(!empty($evidpromedia_data))
            	$isexist = EvidenceProductionBates::find()->where(["prod_media_id"=>$evidpromedia_data->id])->count();
                if ($isexist == 0) {
                   $is_allow = "allow";
				} 
        }
        die($is_allow);
    }
    
    public function actionChkTaskExistInProdbates($record) {
        $rec=$record;
        $record=explode(",",$record);
        //$prodbates = ArrayHelper::map(EvidenceProductionMedia::find()->where(["in","prod_id",$record])->asArray()->all(), 'id', 'id');
        /*$prodbatesmedia = TaskInstructEvidence::find()
            ->joinWith('taskInstruct')->where(['in','prod_id', $record])
            ->andWhere('isactive=1')->all();
        if (!empty($prodbatesmedia)) {
            $tasks_id = [];
            foreach ($prodbatesmedia as $evidProdData) {
                foreach($evidProdData->taskInstruct as $taskInst) {
                    $tasks_id[] = $taskInst->task_id; 
                }
            }
            if(!empty($tasks_id)) {
                $message = 'Notallow';
            } else {
                $message = "allow";     
            }
        } else {
            $message = "allow";
        }*/
        $message = "allow";
        $sql="SELECT COUNT(tbl_task_instruct_evidence.prod_id) FROM tbl_task_instruct_evidence INNER JOIN tbl_task_instruct ON tbl_task_instruct.id=tbl_task_instruct_evidence.task_instruct_id AND tbl_task_instruct.isactive=1 INNER JOIN tbl_tasks on tbl_tasks.id=tbl_task_instruct.task_id WHERE tbl_task_instruct_evidence.prod_id != 0 AND tbl_task_instruct_evidence.prod_id IN ($rec)";
        $result = \Yii::$app->db->createCommand($sql)->queryScalar();
        if($result > 0){
            $message = 'Notallow';
        }
        echo $message;
        die;
    }
    /**
     * Deletes an existing EvidenceProduction model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDeleteProduction($id)
    {
        $ids=explode(",",$id);
        EvidenceProductionMedia::deleteAll(["in","prod_id",$ids]);  
        EvidenceProduction::deleteAll(["in","id",$ids]);
        exit();
       // return $this->redirect(['index','case_id' => $params["EvidenceProduction"]["client_case_id"]]);
    }
    /**
     * Update an existing EvidenceProduction bates model.
     * If updation is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionEditProdcutionBates($case_id, $bates_id) {
        $model = EvidenceProductionBates::findOne($bates_id);
        $params=Yii::$app->request->post();
        if(Yii::$app->request->post()){
            $model->load(Yii::$app->request->post()); 
            if(isset($params['EvidenceProductionBates']['prod_date_loaded']) && $params['EvidenceProductionBates']['prod_date_loaded']!=""){
                $prod_date_loaded = explode("/", $params['EvidenceProductionBates']['prod_date_loaded']);
                if (DB_TYPE == 'sqlsrv')
                    $model->prod_date_loaded = $prod_date_loaded[2]."-".$prod_date_loaded[0]."-".$prod_date_loaded[1];
                else
                    $model->prod_date_loaded = $prod_date_loaded[2] . '-' . $prod_date_loaded[0] . '-' . $prod_date_loaded[1];
            }
            $model->save(false);
        }
        return $this->renderPartial('edit-prodcution-bates', array('model' => $model), false, true);
    }
    
    /**
     * Update an existing EvidenceProduction and Evidence production media hold state.
     * If updation is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionMakeholdCaseproduction() 
    {
        $params=Yii::$app->request->post();
        if(Yii::$app->request->post()){
            $prod_ids=  explode(",",$params['prod_id']);
            $record_ids=  explode(",",$params['record']);
            $isHoldProd = EvidenceProduction::find()->where(['in','id',$prod_ids])->select('has_hold')->one();
            if(isset($isHoldProd->has_hold) && $isHoldProd->has_hold)
                EvidenceProduction::updateAll(['has_hold' => 0],['in','id',$prod_ids]);
            else
                EvidenceProduction::updateAll(['has_hold' => 1],['in','id',$prod_ids]);
            $isHoldProdBates = EvidenceProductionMedia::find()->select(['on_hold','id'])->where(['in','evid_id',$record_ids])->andwhere(['in','prod_id',$prod_ids])->all();
            if(count($isHoldProdBates) > 0){
                foreach($isHoldProdBates as $isHold){
                    echo $isHold->id;
                    if(isset($isHold->on_hold) && $isHold->on_hold)
                        EvidenceProductionMedia::updateAll(['on_hold' => 0],['id'=>$isHold->id]);
                    else
                        EvidenceProductionMedia::updateAll(['on_hold' => 1],['id'=>$isHold->id]);
                }   
            }
        }
        die;
    }
    
    /**
     * Add Attorney to existing EvidenceProduction.
     * If updation is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionAddproductionAttorney($record) {
        $model = EvidenceProduction::findOne($record);
        $params=Yii::$app->request->post();
        if(Yii::$app->request->post()){
            $model->load(Yii::$app->request->post()); 
            
            if(isset($params['EvidenceProduction']['prod_agencies']) && $params['EvidenceProduction']['prod_agencies']!=""){
                $prod_agencies = explode("/", $params['EvidenceProduction']['prod_agencies']);
                if (DB_TYPE == 'sqlsrv')
                    $model->prod_agencies = $prod_agencies[2]."-".$prod_agencies[0]."-".$prod_agencies[1];
                else
                    $model->prod_agencies = $prod_agencies[2] . '-' . $prod_agencies[0] . '-' . $prod_agencies[1];
                
                if(isset($model->prod_agencies) && $model->prod_agencies!="")
                $model->prod_agencies .= " ".date('H:i:s');  
            }
            if(isset($params['EvidenceProduction']['prod_access_req']) && $params['EvidenceProduction']['prod_access_req']!=""){
                $prod_access_req = explode("/", $params['EvidenceProduction']['prod_access_req']);
                if (DB_TYPE == 'sqlsrv')
                    $model->prod_access_req = $prod_access_req[2]."-".$prod_access_req[0]."-".$prod_access_req[1];
                else
                    $model->prod_access_req = $prod_access_req[2] . '-' . $prod_access_req[0] . '-' . $prod_access_req[1];
                
                if(isset($model->prod_access_req) && $model->prod_access_req!="")
                $model->prod_access_req .= " ".date('H:i:s');
            }
            if($model->prod_date=="")
				$model->prod_date = 0;
            //echo "<pre>";print_r($model);die;
            if($model->save()) {
                return "OK";
            } else {
                //echo "<pre>",print_r($model->getErrors());die;
                if($model->prod_agencies == '' || $model->prod_agencies == '0000-00-00 00:00:00') {
                    unset($model->prod_agencies);
                }
                if($model->prod_access_req == '' || $model->prod_access_req == '0000-00-00 00:00:00') {
                    unset($model->prod_access_req);
                }
                $model_field_length = (new User)->getTableFieldLimit($model->tableSchema->name);
                return $this->renderPartial('add-production-attorney', array('model' => $model,'model_field_length'=>$model_field_length), false, true);        
            }
        }
        if($model->prod_agencies == '' || $model->prod_agencies == '0000-00-00 00:00:00'){
            unset($model->prod_agencies);
        }
        if($model->prod_access_req == '' || $model->prod_access_req == '0000-00-00 00:00:00'){
            unset($model->prod_access_req);
        }
        //print_r($model->prod_agencies);
        $model_field_length = (new User)->getTableFieldLimit($model->tableSchema->name);      
        return $this->renderPartial('add-production-attorney', array('model' => $model,'model_field_length'=>$model_field_length), false, true);
    }
     /**
     * Create shortcut for EvidenceProduction for windows only
     * @param integer $case_id
     * @return mixed
     */
    public function actionCaseproductionshortcut($case_id) {
        $caseInfo = ClientCase::findOne($case_id);
        $name = $caseInfo->client->client_name . " - " . $caseInfo->case_name . "(Production)ShortCut.URL";
        $protocol = "http://";
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) {
            $protocol = "https://";
        }
        //$mycaseproductionurl = $protocol. $_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].Url::toRoute(['case-production/index','case_id'=>$case_id]);
        $mycaseproductionurl = $protocol. $_SERVER['SERVER_NAME'].Url::toRoute(['case-production/index','case_id'=>$case_id]);
        $shortText = "[InternetShortcut]\nURL={$mycaseproductionurl}";
        // We'll be outputting an internet shortcut file
        header('Content-type: application/internet-shortcut');
        // It will be called myShortCut.URL
        header('Content-Disposition: attachment; filename="' . $name . '"');
        //output URL file
        echo $shortText;
        die;
    }
    /**
     * Finds the EvidenceProduction model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EvidenceProduction the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EvidenceProduction::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
