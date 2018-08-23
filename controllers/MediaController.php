<?php
namespace app\controllers;

use Yii;
use app\models\Evidence;
use app\models\search\EvidenceSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\base\DynamicModel;
use app\models\EvidenceType;
use app\models\EvidenceCategory;
use app\models\Unit;
use app\models\EvidenceEncryptType;
use app\models\EvidenceStoredLoc;
use app\models\Client;
use app\models\ClientCase;
use app\models\EvidenceContents;
use app\models\EvidenceCustodians;
use app\models\DataType;
use app\models\ClientCaseEvidence;
use app\models\EvidenceTransaction;
use app\models\ActivityLog;
use app\models\MydocumentsBlob;
use app\models\Mydocument;
use app\models\User;
use app\models\Role;
use app\models\Tasks;
use app\models\EvidenceProduction;
use app\models\ClientCaseCustodians;
use app\models\TaskInstruct;
use app\models\EvidenceTo;
use app\models\search\EvidenceTransactionSearch;
use app\models\EvidenceProductionMedia;
use app\models\ProjectSecurity;
use app\models\Options;
use app\models\TaskInstructEvidence;
use app\models\SettingsEmail;
use app\models\TasksUnitsBilling;
use app\models\TasksUnitsData;
use app\models\FormBuilderSystem;
use app\models\EmailCron;
/**
 * MediaController implements the CRUD actions for Evidence model.
 */
class MediaController extends Controller
{
    /**
     * @inheritdoc
     */

    public function beforeAction($action) {
		if (Yii::$app->user->isGuest)
			$this->redirect(array('site/login'));


		if (!(new User)->checkAccess(3) && !(new User)->checkAccess(3.009) && !in_array($action->id,array('downloadfiles'))) {/* 38 */
			/* IRT 31 Default landing page */
			$def_land_page = Options::find()->where(['user_id' => Yii::$app->user->identity->id])->one()->default_landing_page;
            if($def_land_page=='') $def_land_page = '';
            if($def_land_page==1){ // Show My Assignments
				if((new User)->checkAccess(1)) {
					$redirect_method[] = 'site/index';
				} else {
					$redirect_method[]=(new User)->checkOtherModuleAccess(1);
				}
			} else if ($def_land_page==2){ // Show Media
				if((new User)->checkAccess(3) && (new User)->checkAccess(3.009)) {
					$redirect_method[] = 'media/index';
				} else {
					$redirect_method[]=(new User)->checkOtherModuleAccess(2);
				}
			} else if ($def_land_page==3){ // Show My Teams
				if((new User)->checkAccess(4)) {
					$redirect_method[] = 'mycase/index';
				} else {
					$redirect_method[]=(new User)->checkOtherModuleAccess(3);
				}
			} else if ($def_land_page==4){ // Show My Cases
				if((new User)->checkAccess(5)) {
					$redirect_method[] = 'team/index';
				} else {
					$redirect_method[]=(new User)->checkOtherModuleAccess(4);
				}
			} else if ($def_land_page==5){ // Show Global Projects
				if((new User)->checkAccess(2)) {
					$redirect_method[] = 'global-projects/index';
				} else {
					$redirect_method[]=(new User)->checkOtherModuleAccess(5);
				}
			} else if ($def_land_page==6){ // Show Billing
				if((new User)->checkAccess(7)) {
					$redirect_method[] = 'billing-pricelist/internal-team-pricing';
				} else {
					$redirect_method[]=(new User)->checkOtherModuleAccess(6);
				}
			} else if ($def_land_page==7){ // Show Report
				if((new User)->checkAccess(11)) {
					$redirect_method[] = 'custom-report/index';
				} else {
					$redirect_method[]=(new User)->checkOtherModuleAccess(7);
				}
			} else if ($def_land_page==8){ // Show Administrator
				if((new User)->checkAccess(8)) {
					$redirect_method[] =  'site/administration';
				} else {
					$redirect_method[]=(new User)->checkOtherModuleAccess(8);
				}
			} else {
				if((new User)->checkAccess(1)) {
					return $this->redirect(array(
						'site/index'
					));
				} elseif((new User)->checkAccess(3) && (new User)->checkAccess(3.009)) {
					return $this->redirect(array(
						'media/index'
					));
				} elseif((new User)->checkAccess(4)) {
					return $this->redirect(array(
						'mycase/index'
					));
				} elseif((new User)->checkAccess(5)) {
					return $this->redirect(array(
						'team/index'
					));
				} elseif((new User)->checkAccess(2)) {
					return $this->redirect(array(
						'global-projects/index'
					));
				} elseif((new User)->checkAccess(7)) {
					return $this->redirect(array(
						'billing-pricelist/internal-team-pricing'
					));
				} elseif((new User)->checkAccess(75)) {
					return $this->redirect(array(
						'site/reports'
					));
				} elseif((new User)->checkAccess(8)) {
					return $this->redirect(array(
						'site/administration'
					));
				} else{
					return $this->goBack();
				}
			}
			return $this->redirect($redirect_method);

            //throw new \yii\web\HttpException(404, 'User permissions do not allow entry into this module.');
        }

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
     * Lists all Evidence models.
     * @return mixed
     */
    public function actionIndex()
    {
        $this->layout = 'media';
        $searchModel = new EvidenceSearch();
        $filter_status='';
        $userId = Yii::$app->user->identity->id;
        $_REQUEST['cust_id']='';
        $_REQUEST['return_ids']=0;
        $_REQUEST['columns']='';
        $params=Yii::$app->request->get();
        /*IRT 67,68,86,87,258*/
        /*IRT 96,398 */
        $client_case_selected = $clients_selected =array();
        $filter_type=\app\models\User::getFilterType(['tbl_evidence.id','tbl_evidence.created_by','tbl_evidence.received_from','tbl_evidence.status','tbl_evidence.barcode','tbl_evidence.received_date','tbl_evidence.received_time','tbl_evidence.serial','tbl_evidence.model','tbl_evidence.evid_type','tbl_evidence.cat_id','tbl_evidence.quantity','tbl_evidence.contents_total_size','tbl_evidence.unit','tbl_evidence.comp_unit','tbl_evidence.contents_total_size_comp','tbl_evidence.hash','tbl_evidence.evd_Internal_no','tbl_evidence.other_evid_num','tbl_evidence.dup_evid','tbl_evidence.org_link','tbl_evidence.contents_copied_to','tbl_evidence.mpw','tbl_evidence.bbates','tbl_evidence.ebates','tbl_evidence.m_vol','tbl_evidence.ftpun','tbl_evidence.ftppw','tbl_evidence.enctype','tbl_evidence.encpw','tbl_evidence.evid_stored_location','tbl_evidence.has_contents','tbl_evidence.cont','tbl_client_case_evidence.client_id','tbl_client_case_evidence.client_case_id'],['tbl_evidence','tbl_client_case_evidence']);
		/*IRT 96,398 Code Starts*/
        /*if (isset($params['EvidenceSearch']['client_case_id']) && !empty($params['EvidenceSearch']['client_case_id'])) {
            $client_case_selected = (new User)->getSelectedGridCases($params['EvidenceSearch']['client_case_id'], 'All');
            if ($client_case_selected == 'ALL') {
                unset($params['EvidenceSearch']['client_case_id']);
                $client_case_selected = array();
            }
        }
        if (isset($params['EvidenceSearch']['client_id']) && !empty($params['EvidenceSearch']['client_id'])) {
            $clients_selected = (new User)->getSelectedGridClients($params['EvidenceSearch']['client_id'], 'All');
            if ($clients_selected == 'ALL') {
                unset($params['EvidenceSearch']['client_id']);
                $clients_selected = array();
            }
        }*/
        if (Yii::$app->request->isAjax) {
						$this->layout = false;
						Yii::$app->request->queryParams += Yii::$app->request->post();
                        //$params = $params + Yii::$app->request->queryParams;
				}
        /* IRT 96,398 Code Code Ends */
        $config = ['dup_evid'=>[1=>'Yes',0=>'No'],'status'=>['All'=>'All',1=>'Checked In',2=>'Checked Out',3=>'Destroyed',4=>'Moved',5=>'Returned']];
		$config_widget_options = ['client_id'=>['initValueText' =>$clients_selected],'client_case_id'=>['initValueText' => $client_case_selected] ];
        $filterWidgetOption=\app\models\User::getFilterWidgetOption($filter_type,Url::toRoute(['media/ajax-filter']),$config,$config_widget_options, Yii::$app->request->queryParams, 'media');
       // echo  "<pre>",print_r($filter_type),"</pre>";die;
        /*IRT 67,68,86,87,258*/
        $media_form = ArrayHelper::map(FormBuilderSystem::find()->select(['sys_field_name'])->where(['sys_form'=>'media_form','grid_type'=>0])->orderBy('sort_order')->all(),'sys_field_name','sys_field_name');
        //echo  "<pre>",print_r($media_form),"</pre>";die;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,'',$media_form);
        //$models = $dataProvider->getModels();

        //echo "<pre>",print_r($models),"</pre>";
        //die;

        if(!empty($params['EvidenceSearch'])){
            $filter_status='yes';
        }
        $returns=0;
        //$searchModel->search(Yii::$app->request->queryParams,'Y');
        //echo "<pre>",print_r($dataProvider),"</pre>";die;
        //echo "<pre>",print_r($barcode_display['barcode']);die;
	    /* $securitySql="SELECT client_case_id FROM tbl_project_security WHERE user_id=".$userId." and client_id!=0 and client_case_id!=0 group by client_case_id";
        $clientDropdownData = ClientCase::find()->joinWith('client')->select(['tbl_client_case.id','client_id','case_name','client_name'])->where('tbl_client_case.id IN ('.$securitySql.')')->orderBy('tbl_client.client_name')->all(); */
        //echo "<pre>",print_r($media_form),"</pre>";die;
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'filter_status'=>$filter_status,
            'params'=>$params,
        	'returns'=>$returns,
			'barcode_display' => $barcode_display,
			'filter_type'=>$filter_type,
			'filterWidgetOption'=>$filterWidgetOption,
			'media_form'=>$media_form
        ]);
    }
    /**
	 * Filter GridView with Ajax
	 * */
	public function actionAjaxFilter(){
		$searchModel = new EvidenceSearch();
    $params = array();
		$params = array_merge(Yii::$app->request->queryParams, Yii::$app->request->bodyParams, $params);
		$dataProvider = $searchModel->searchFilter($params);
		if(isset($params['type']) && $params['type']=='typehead'){
			$out = [];
			foreach ($dataProvider as $key=>$val) {
				if($val == ''){
                    continue;
					//$val='(not set)';
				}
				$out[] = ['value' => $val];
			}
			return json_encode($out);
		}
		$out['results']=array();
		foreach ($dataProvider as $key=>$val){
			$val1 = $val;
			$val2 = $val;
			if($params['field'] == 'client_id' || $params['field'] == 'client_case_id'){
				$val1 = $key;
			}
			if($val == ''){
                 continue;
				/*$val1 = '(not set)';
				$val='(not set)';
				$val2='(not set)';*/
			}

			$out['results'][] = ['id' => $val1, 'text' => $val,'label' => $val2];
		}
        return json_encode($out);
	}
	public function actionAjaxfiltercust(){
		$params=Yii::$app->request->queryParams;
		$query = EvidenceTransaction::find()->where(['evid_num_id'=>$params['id']])->joinWith(['transby','transRequstedby']);
		$dataProvider = ArrayHelper::map($query->all(),'trans_reason','trans_reason');
		if(isset(Yii::$app->request->queryParams['type']) && Yii::$app->request->queryParams['type']=='typehead') {
			$out = [];
			foreach ($dataProvider as $key=>$val) {
				if($val == '') {
					$val = '(not set)';
				}
				$out[] = ['value' => $val];
			}
			return json_encode($out);
		}
	}
    /**
     * Get detail of Evidence on expand .
     * @return mixed
     */
    public function actionGetDetails()
    {
		$media_form = ArrayHelper::map(FormBuilderSystem::find()->select(['sys_field_name'])->where(['sys_form'=>'media_form','grid_type'=>1])->orderBy('sort_order')->all(),'sys_field_name','sys_field_name');

        $media_id   = Yii::$app->request->post("expandRowKey");
        $media_data = Evidence::find()->select([])->joinWith(['evidenceattachments','evidencestoredloc','evidencecheckedin','evidencetaskinstruct'])->where(['tbl_evidence.id'=>$media_id])->one();
        $task_data=array();
        $task_arr=array();
        $prod_arr=array();
        $roleId = Yii::$app->user->identity->role_id;
        $currentUser = Role::findOne(Yii::$app->user->identity->role_id);
        $current_user_roles = explode(",", $currentUser->role_type);
        $user_model = new User();
        $tasks_ids=array();
        $prods_ids=array();
        $str_tasks_ids="";
        $str_prods_ids="";
        $userCases = (new ProjectSecurity)->getUserCases(Yii::$app->user->identity->id);

        if(!empty($media_data['evidencetaskinstruct']))
        {
        	foreach($media_data['evidencetaskinstruct'] as $task)
            {
                $isactive_media = TaskInstruct::find()->select(['tbl_task_instruct.id'])->where(['tbl_task_instruct.id'=>$task->task_instruct_id,'tbl_task_instruct.isactive'=>1])->all();
			    $billing_media = TasksUnitsBilling::find()->select(['tbl_tasks_units_billing.id'])->innerJoinWith('tasksUnits')->where(['tbl_tasks_units.task_id'=>$task->taskInstruct[0]->task_id,'tbl_tasks_units_billing.evid_num_id'=>$task->evidence_id])->all();
			    $unit_data = TasksUnitsData::find()->select(['tbl_tasks_units_data.id'])->innerJoinWith('tasksUnits')->where(['tbl_tasks_units.task_id'=>$task->taskInstruct[0]->task_id, 'tbl_tasks_units_data.evid_num_id'=>$task->evidence_id])->all();
				if(!empty($isactive_media) || !empty($billing_media) || !empty($unit_data)){

					if (in_array(1, $current_user_roles) || $roleId == 0){
						if ($user_model->checkAccess(4) && $user_model->checkAccess(4.01)) {
							$task_info = Tasks::findOne($task->taskInstruct[0]->task_id);
                                                        //echo '<pre>',print_r($task_info);die;
							if(in_array($task_info->client_case_id,$userCases)) {
                                if($task_info->clientCase->is_close == 1){
                                    $tasks_ids[$task_info->id] = $task_info->id;
                                }
								else if($task_info->task_cancel == 1 && isset($task_info->id) && $task_info->id!="") {
									$tasks_ids[$task_info->id] = "<a title='Project #".$task_info->id."'  href='" . Yii::$app->getUrlManager()->getBaseUrl()."/index.php?r=case-projects/load-canceled-projects&case_id=" . $task_info->client_case_id . "&task_id=" . $task_info->id."'>" . $task_info->id . "</a>";
								} else if($task_info->task_closed == 1 && isset($task_info->id) && $task_info->id!="") {
									$tasks_ids[$task_info->id] = "<a title='Project #".$task_info->id."' href='" . Yii::$app->getUrlManager()->getBaseUrl()."/index.php?r=case-projects/load-closed-projects&case_id=" . $task_info->client_case_id . "&task_id=" . $task_info->id."'>" . $task_info->id . "</a>";
								} else {
									if(isset($task_info->id) && $task_info->id!="")
									   $tasks_ids[$task_info->id] = "<a title='Project #".$task_info->id."' href='" . Yii::$app->getUrlManager()->getBaseUrl()."/index.php?r=case-projects/index&case_id=" . $task_info->client_case_id . "&task_id=" . $task_info->id. "'>" . $task_info->id . "</a>";
								}
							}else{
								if(isset($task_info->id) && $task_info->id!="")
									$tasks_ids[$task_info->id] = $task_info->id;
							}
						}else{
							if(isset($task_info->id) && $task_info->id!="")
								$tasks_ids[$task_info->id] = $task_info->id;
						}

						$prods_info=EvidenceProduction::findOne($task['prod_id']);
						if ($user_model->checkAccess(4) && $user_model->checkAccess(4.006) && in_array($prods_info->client_case_id,$userCases)) {
							if($prods_info->id != ''){
                                if($prods_info->clientCase->is_close == 1){
                                    $prods_ids[$task['prod_id']] = $prods_info->id;
                                }else{
								    $prods_ids[$prods_info->id] = "<a title='Production #".$prods_info->id."' href='".Yii::$app->getUrlManager()->getBaseUrl()."/index.php?r=case-production/index/&filter=1&case_id=".$prods_info->client_case_id."&prod_id[]=".$prods_info->id."'>".$prods_info->id."</a>";
                                }
							}
						} else {
							if($task['prod_id'] != ''){
								$prods_ids[$task['prod_id']] = $task['prod_id'];
							}
						}
                } else {
                        $task_info = Tasks::find()->select(['tbl_tasks.id','tbl_tasks_teams.team_id','tbl_tasks_teams.team_loc','tbl_tasks_teams.task_id'])->where(['tbl_tasks.id'=>$task['task_id']])->joinWith(['tasksTeams'])->one();
                        $team_data = $task_info->tasksTeams;
                        $te_amid = 0;
                        $team_loc = 0;
                        $te_amid = "";
                        $team_loc = "";
                        foreach ($team_data as $tda) {
                            if ($user_model->checkTeamAccess($tda->team_id, $tda->team_loc))
                            {
                                $te_amid = $tda->team_id;
                                $team_loc = $tda->team_loc;
                                break;
                            }
                        }
                        if($task_info->task_cancel == 1 && isset($task_info->id) && $task_info->id!=""){
                        	$tasks_ids[$task_info->id] = $task_info->id;
                    	}
                        else if($task_info->clientCase->is_close == 1){
                            $tasks_ids[$task_info->id] = $task_info->id;
                        }
                        else{
                    		if ($user_model->checkAccess(5) && $user_model->checkAccess(5.01) && isset($task_info->id) && $task_info->id!="" && $te_amid!="" && $team_loc != "") {
                    			$tasks_ids[$task_info->id] = "<a title='Project #".$task_info->id."' href='" . Yii::$app->getUrlManager()->getBaseUrl() . "/index.php?r=team-projects/index&team_id=" . $te_amid . "&team_loc=" . $team_loc . "&task_id=" . $task_info->id . "'>" . $task_info->id . "</a>";
                    		} else {
                    			if(isset($task_info->id) && $task_info->id!="")
                    				$tasks_ids[$task_info->id] = $task_info->id;
                    		}
                        }

                        if(isset($task['prod_id']) && $task['prod_id']!="")
							$prods_ids[$task['prod_id']] = $task['prod_id'];
					}
				}
		    }
		}

		if(!empty($prods_ids)){
        	$prodctions = EvidenceProductionMedia::find()->select('prod_id')->where('evid_id='.$media_id.' AND prod_id NOT IN ('.implode(",",array_keys($prods_ids)).')')->all();
        } else {
        	$prodctions = EvidenceProductionMedia::find()->select('prod_id')->where('evid_id='.$media_id)->all();
        }

        if(!empty($prodctions)){
        	foreach ($prodctions as $prodction){
		        if(isset($prodction->prod_id) && $prodction->prod_id!=""){
		         	$prods_info=EvidenceProduction::findOne($prodction->prod_id);
		            if ($user_model->checkAccess(4) && $user_model->checkAccess(4.006) && in_array($prods_info->client_case_id,$userCases)) {
						if($prods_info->id != ''){
                            if($prods_info->clientCase->is_close == 1){
                                $prods_ids[$prods_info->id] = $prods_info->id;
                            }else{
		                        $prods_ids[$prods_info->id] = "<a title='Production #".$prods_info->id."' href='".Yii::$app->getUrlManager()->getBaseUrl()."/index.php?r=case-production/index/&filter=1&case_id=".$prods_info->client_case_id."&prod_id[]=".$prods_info->id."'>".$prods_info->id."</a>";
                            }
		                }
		            } else {
		            	if($prods_info->id != ''){
		                    $prods_ids[$prods_info->id] = $prods_info->id;
		                }
		            }
		        }
        	}
        }

        if(!empty($tasks_ids))
        {
            $tasks_ids=array_unique($tasks_ids);
            $str_tasks_ids=implode(", ",$tasks_ids);
        }
        if(!empty($prods_ids))
        {
            $prods_ids=array_unique($prods_ids);
            $str_prods_ids=implode(", ",$prods_ids);
        }

        return $this->renderPartial('getgriddetails', ['media_form'=>$media_form,'data' => $media_data,'tasks_ids'=>$str_tasks_ids,'prods_ids'=>$str_prods_ids]);
      }

      /**
       * Download attach files
       * @return mixed
       */
      public function actionDownloadfiles() {
        $name = Yii::$app->request->get("name");
        if ($name == '' || $name == 0) {
            ///throw new CHttpException(404, 'The specified post cannot be found.');
        }
        /* DB media download */
        $selected_data = Mydocument::find()->joinWith(['mydocumentsBlobs'])->select(['tbl_mydocument.id','fname', 'doc_size','doc_type','doc_id','tbl_mydocuments_blob.doc'])->where(['tbl_mydocument.id'=>$name])->one();

        $filename = urlencode($selected_data->fname); //Trace the filename
        //echo "<pre>".$filename; print_r($selected_data);die;
        header("Content-disposition: attachment; filename={$filename}"); // Tell the filename to the browser
        header("Content-Length: " . $selected_data->doc_size);
        header('Content-type: application/' . $selected_data->doc_type); // Stream as a binary file! So it would force browser to download
        echo utf8_decode($selected_data->mydocumentsBlobs->doc);
        exit;
    }
    /**
     * Displays a single Evidence model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }
    /* Get Evidence Status
     */
    public function actionGetEvidenceStatus($id){
        $evidNum = (int) $id;
        $modelold = Evidence::findOne($evidNum);
        if($modelold->status == 3){
            return 'false';
        }else{
            return 'true';
        }
    }
     /**
     * Copy a new Evidence model.
     * If copied is successful, the browser will be redirected to the 'index' page.
     * @return mixed
     */
    public function actionCopy($id)
    {
        $model = new Evidence();

        $params=Yii::$app->request->post();
        $evidNum = (int) $id;
        $user_id=Yii::$app->user->identity->id;
        $modelold = Evidence::findOne($evidNum);
       // echo '<pre>',print_r($modelold);die;
//        if($modelold->status == 3){
//            return 'false';
//        }
        /************** Copy Media Starts *****************/
        $model->checkedin_by=$modelold->checkedin_by;
        $model->dup_evid=$modelold->dup_evid;
        $model->org_link=$modelold->org_link;
        $model->other_evid_num=$modelold->other_evid_num;
        $model->received_date=$modelold->received_date;
        $model->received_time=$modelold->received_time;
        $model->received_from=$modelold->received_from;
        $model->evd_Internal_no=$modelold->evd_Internal_no;
        $model->evid_type=$modelold->evid_type;
        $model->cat_id=$modelold->cat_id;
        $model->serial=$modelold->serial;
        $model->model=$modelold->model;
        $model->hash=$modelold->hash;
        $model->quantity=$modelold->quantity;
        $model->cont=$modelold->cont;
        $model->evid_desc="Copy of ".$modelold->evid_desc;
        $model->evid_label_desc=$modelold->evid_label_desc;
        $model->contents_total_size=$modelold->contents_total_size;
        $model->contents_total_size_comp=$modelold->contents_total_size_comp;
        $model->unit=$modelold->unit;
        $model->comp_unit=$modelold->comp_unit;
        $model->contents_copied_to=$modelold->contents_copied_to;
        $model->mpw=$modelold->mpw;
        $model->bbates=$modelold->bbates;
        $model->ebates=$modelold->ebates;
        $model->m_vol=$modelold->m_vol;
        $model->ftpun=$modelold->ftpun;
        $model->ftppw=$modelold->ftppw;
        $model->enctype=$modelold->enctype;
        $model->encpw=$modelold->encpw;
        $model->evid_stored_location=$modelold->evid_stored_location;
        $model->evid_notes=$modelold->evid_notes;
        $model->status=$modelold->status;
        /*502*/
        $model->barcode= 0 ;
        //isset($modelold->barcode)?$modelold->barcode:'';
        /*502*/
        $model->has_contents=$modelold->has_contents;
        /*$model->created=$modelold->created;
        $model->created_by=$modelold->created_by;
        $model->modified=$modelold->modified;
        $model->modified_by=$modelold->modified_by;*/
        $model->created = date('Y-m-d H:i:s');
        $model->created_by = Yii::$app->user->identity->id;
        $model->modified = date('Y-m-d H:i:s');
        $model->modified_by = Yii::$app->user->identity->id;

        //echo "<pre>",print_r($model->toArray()),"</pre>";die;
        if($model->save(false))
        {
            /************** Copy Media Ends *****************/
            $media_id = $model->getPrimaryKey();

            /************** Copy Media Document Starts *****************/

            $evid_docs = Mydocument::find()->joinWith(['mydocumentsBlobs'])->select(['tbl_mydocument.id','p_id','fname', 'doc_size','doc_type','doc_id','tbl_mydocuments_blob.doc'])->where(['tbl_mydocument.reference_id'=>(int) $evidNum,'tbl_mydocument.origination'=>'Media'])->all();
            if(!empty($evid_docs))
            {
                foreach($evid_docs as $doc)
                {
                    $MydocumentsBlob_model = new MydocumentsBlob();
                    $MydocumentsBlob_model->doc = $doc->mydocumentsBlobs->doc;
                    $MydocumentsBlob_model->save(false);
                    $blob_doc_id = $MydocumentsBlob_model->getPrimaryKey();

                    $docmodel = new Mydocument();
                    $docmodel->p_id = 0;
                    $docmodel->reference_id = $media_id;
                    $docmodel->team_loc = 0;
                    $docmodel->fname = $doc->fname;
                    $docmodel->origination = 'Media';
                    $docmodel->u_id = Yii::$app->user->identity->id;
                    $docmodel->is_private = 0;
                    $docmodel->type = 'F';
                    $docmodel->doc_id = $blob_doc_id;
                    $docmodel->doc_size = $doc->doc_size;
                    $docmodel->doc_type = $doc->doc_type;
                    $docmodel->save(false);
                }
            }
            /************** Copy Media Document Ends *****************/

            /************** Copy Media Content Starts *****************/
            $evidenceContent = EvidenceContents::find()->joinWith([])->where(["evid_num_id"=>$evidNum])->orderBy(['id'=>SORT_ASC])->select(['tbl_evidence_contents.id', 'tbl_evidence_contents.cust_id', 'data_type', 'data_size', 'unit', 'data_copied_to'])->all();
            if(!empty($evidenceContent))
            {
                foreach($evidenceContent as $evidcont)
                {
                    $modelEvidenceCont = new EvidenceContents();
                    $modelEvidenceCont->evid_num_id=$media_id;
                    $modelEvidenceCont->cust_id=$evidcont->cust_id;
                    $modelEvidenceCont->data_type=$evidcont->data_type;
                    $modelEvidenceCont->data_size=$evidcont->data_size;
                    $modelEvidenceCont->unit=$evidcont->unit;
                    $modelEvidenceCont->data_copied_to=$evidcont->data_copied_to;
                    $modelEvidenceCont->save(false);
                    //$ar_cust_id[$evidcont->cust_id;] = $evidcont->cust_id;;
                }
            }

            /************** Copy Media Content Ends *****************/

            /************** Copy Media clientcase Starts *****************/
                $clientCaseEvidences_data = ClientCaseEvidence::find()->where(["evid_num_id"=>$evidNum])->orderBy(['tbl_client_case_evidence.id'=>SORT_ASC])->select(['tbl_client_case_evidence.*'])->all();
                if(!empty($clientCaseEvidences_data))
                {
                    foreach($clientCaseEvidences_data as $ccevid)
                    {
                        $case_id=$ccevid->client_case_id;
                        $modelClientCaseEvidence = new ClientCaseEvidence();
                        $modelClientCaseEvidence->evid_num_id = $media_id;
                        $modelClientCaseEvidence->client_id = $ccevid->client_id;
                        $modelClientCaseEvidence->client_case_id = $ccevid->client_case_id;
                        $modelClientCaseEvidence->cust_id = $ccevid->cust_id;
                        $modelClientCaseEvidence->save(false);
                    }
                }

             //SettingsEmail::sendEmail
             EmailCron::saveBackgroundEmail(3, 'is_sub_new_media', $data = array('case_id' => $case_id, 'evidence' => $model->toArray(), 'newcontents' => $modelEvidenceCont, 'evid_clientcase' => $clientCaseEvidences_data));

            /************** Copy Media clientcase Starts *****************/

            /************** Copy Evidence Transaction Starts *****************/
             /*$allEvidenceOld = EvidenceTransaction::find()->where(['evid_num_id'=>$id]);
             if($allEvidenceOld->count() > 0){
                 $eviTransLogCount = 0;
                 foreach($allEvidenceOld->all() as $singleEvidence){
                    $EvidenceTransaction = new EvidenceTransaction();
                    $EvidenceTransaction->evid_num_id = $media_id;
                    $EvidenceTransaction->trans_type = $singleEvidence->trans_type;
                    $EvidenceTransaction->trans_date = date('Y-m-d H:i:s');
                    $EvidenceTransaction->trans_requested_by = Yii::$app->user->identity->id;
                    $EvidenceTransaction->moved_to = $singleEvidence->moved_to;
                    $EvidenceTransaction->trans_reason = $singleEvidence->trans_reason;
                    $EvidenceTransaction->trans_by = Yii::$app->user->identity->id;
                    $EvidenceTransaction->save(false);
                    if($eviTransLogCount == 0)
                        $mediaOrigination = 'Added';
                    else
                        $mediaOrigination = 'Updated';
                    (new ActivityLog())->generateLog('Media',$mediaOrigination, $media_id, $model->id);
                    $eviTransLogCount++;
                 }
             }*/
             /* Save Evidence Transaction Starts */
                $EvidenceTransaction = new EvidenceTransaction();
                $EvidenceTransaction->evid_num_id = $media_id;
                $EvidenceTransaction->trans_type = 1;
                $EvidenceTransaction->trans_date = date('Y-m-d H:i:s');
                $EvidenceTransaction->trans_requested_by = Yii::$app->user->identity->id;
                $EvidenceTransaction->moved_to = 0;
                $EvidenceTransaction->trans_reason = "";
                $EvidenceTransaction->trans_by = Yii::$app->user->identity->id;
                $EvidenceTransaction->save(false);
                /* Save Evidence Transaction Ends */
                (new ActivityLog())->generateLog('Media','Added', $media_id, $model->id);
            /************** Copy Evidence Transaction Ends *****************/

             return $this->redirect(['index']);
        }
       // return $this->redirect(['index']);

    }
    /**
     * Creates a new Evidence model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Evidence();
        $user_id=Yii::$app->user->identity->id;
        $roleId = Yii::$app->user->identity->role_id;
       /* echo '<pre>';
        print_r($_POST);die;*/
        $sql=" SELECT client_id FROM tbl_project_security WHERE user_id=$user_id AND client_id!=0 AND client_case_id !=0 GROUP BY client_id ";
        if($roleId!=0){
			$clientList = ArrayHelper::map(Client::find()->where('id IN ('.$sql.')')->orderBy('tbl_client.client_name ASC')->asArray()->all(), 'id', 'client_name');
		}else{
			$clientList = ArrayHelper::map(Client::find()->orderBy('tbl_client.client_name ASC')->orderBy('tbl_client.client_name ASC')->asArray()->all(), 'id', 'client_name');
		}
        $listEvidenceType = ArrayHelper::map(EvidenceType::find()->where(['remove'=>'0'])->orderBy(['evidence_name'=>SORT_ASC])->asArray()->all(), 'id', 'evidence_name');
        $listEvidenceCategory = ArrayHelper::map(EvidenceCategory::find()->where(['remove'=>'0'])->orderBy(['category'=>SORT_ASC])->asArray()->all(), 'id', 'category');
        $listUnit = ArrayHelper::map(Unit::find()->where(['remove'=>'0','is_hidden'=>0])->orderBy(['sort_order'=>SORT_ASC])->asArray()->all(), 'id', 'unit_name');
        $listEvidenceEncrypt = ArrayHelper::map(EvidenceEncryptType::find()->where(['remove'=>'0'])->orderBy(['encrypt'=>SORT_ASC])->asArray()->all(), 'id', 'encrypt');
        $listEvidenceLoc = ArrayHelper::map(EvidenceStoredLoc::find()->where(['remove'=>'0'])->orderBy(['stored_loc'=>SORT_ASC])->asArray()->all(), 'id', 'stored_loc');
        if ($model->load(Yii::$app->request->post())) {

            $params=Yii::$app->request->post();
            if($_POST['Evidence']['case_id'] != ''){
                $_POST['Evidence']['case_id']=explode(",",$_POST['Evidence']['case_id']);
                if(isset($_POST['Evidence']['case_id'][0]) && count($_POST['Evidence']['case_id']) == 1){
                    $case_arr = explode('|',$_POST['Evidence']['case_id'][0]);
                    $model->client_id = $case_arr[1];
                    $model->client_case_id = $case_arr[0];
                }
            }else
                $_POST['Evidence']['case_id']=array();


				$model->checkedin_by = Yii::$app->user->identity->id;
				$model->status = 1;
            if (isset($_POST['EvidenceContent']) && !empty($_POST['EvidenceContent'])) {
                $model->has_contents = 1;
            } else {
                $model->has_contents = 0;
            }
            $received_date = Yii::$app->request->post('Evidence')['received_date'];
            $received_time = Yii::$app->request->post('Evidence')['received_time'];
            if (isset($received_date) && ($received_date != '')) {
                $received_date_arr = explode("/",$received_date);
                $received_date = $received_date_arr[2]."-".$received_date_arr[0]."-".$received_date_arr[1];
                $received_date_time = $received_date.' '.$received_time;
                $received_date_time = (new Options)->ConvertOneTzToAnotherTz($received_date_time, $_SESSION['usrTZ'],'UTC', "YMDHIA");
                $received = explode(" ", $received_date_time);
                $model->received_date = $received[0];
                $model->received_time = $received[1].' '.$received[2];
            }

                if($model->save()){

                $media_id =Yii::$app->db->getLastInsertId();
                /* Code for evidence attachment start */
                if(!empty($_FILES['Evidence']['name']['upload_files'][0]))
                {
                    $docmodel = new Mydocument();
                    $doc_arr['p_id']=0;
                    $doc_arr['reference_id']=$media_id;
                    $doc_arr['team_loc']=0;
                    $doc_arr['origination']='Media';
                    $doc_arr['is_private']='';
                    $doc_arr['type']='F';
                    $doc_arr['is_private']='';
                    $file_arr=$docmodel->Savemydocs('Evidence','upload_files',$doc_arr);
                    $files_str = json_encode($file_arr);
                }
                /* Code for evidence attachment end */
                /* Save Evident Content start */

                $evidcontent_data=$_POST['EvidenceContent'];
                $evidcustodian_data=$_POST['EvidenceCustodian'];
                $ar_cust_id = array();
                if (isset($evidcontent_data) && !empty($evidcontent_data)) {
                    foreach ($evidcontent_data as $evidContent) {
                        $evidContent['evid_num_id'] = $media_id;
                        if (isset($evidcustodian_data[$evidContent['cust_id']]) && !empty($evidcustodian_data[$evidContent['cust_id']])) {
                            $cust_fname = $evidcustodian_data[$evidContent['cust_id']]['cust_fname'];
                            $cust_lname = $evidcustodian_data[$evidContent['cust_id']]['cust_lname'];
                            $cust_mi = $evidcustodian_data[$evidContent['cust_id']]['cust_mi'];
                            $cust_email = $evidcustodian_data[$evidContent['cust_id']]['cust_email'];
                            $title = $evidcustodian_data[$evidContent['cust_id']]['title'];
                            $dept = $evidcustodian_data[$evidContent['cust_id']]['dept'];
                            $modelEvidenceCustodians = new EvidenceCustodians();
                            $modelEvidenceCustodians->cust_fname = $cust_fname;
                            $modelEvidenceCustodians->cust_lname = $cust_lname;
                            $modelEvidenceCustodians->cust_mi = $cust_mi;
                            $modelEvidenceCustodians->cust_email = $cust_email;
                            $modelEvidenceCustodians->title = $title;
                            $modelEvidenceCustodians->dept = $dept;
                            $modelEvidenceCustodians->save(false);
                            $lastcust_id = Yii::$app->db->getLastInsertId();
                            $evidContent['cust_id'] = $lastcust_id;
                        }
                        $ar_cust_id[$evidContent['cust_id']] = $evidContent['cust_id'];
                        $evidContent_data['EvidenceContents']=$evidContent;
                        $modelEvidenceCont = new EvidenceContents();
                        $modelEvidenceCont->load($evidContent_data);
                        $modelEvidenceCont->save(false);
                    }
                }
                /* Save Evident Content End */
                /* Save Evident Client Case Starts */
                $evidcase_data=$_POST['Evidence'];

                if (isset($evidcase_data['case_id']) && !empty($evidcase_data['case_id'])) {
                    $evidenceClientCase = array();
                    foreach ($evidcase_data['case_id'] as $key => $value) {
                        $case_arr=explode("|",$value);
                        $client_id = $case_arr[1];
                        $case_id = $case_arr[0];
                        $evidenceClientCase['ClientCaseEvidence']['client_case_id'] = $case_arr[0];
                        $evidenceClientCase['ClientCaseEvidence']['client_id'] = $client_id;
                        $evidenceClientCase['ClientCaseEvidence']['evid_num_id'] = $media_id;
                        if (!empty($ar_cust_id)) {
                            $cust_ids = implode(",", $ar_cust_id);
                            $evidenceCont = ClientCaseEvidence::find()->select(['cust_id'])->where("client_case_id=$case_arr[0] AND evid_num_id=$media_id AND cust_id IN ($cust_ids)")->all();
                            if (!empty($evidenceCont)) {
                                $ar_custodin_ids = array();
                                foreach ($evidenceCont as $evdCon) {
                                    $ar_custodin_ids[$evdCon->cust_id] = $evdCon->cust_id;
                                }
                                if (!empty($ar_custodin_ids)) {
                                    foreach (array_diff($ar_cust_id, $ar_custodin_ids) as $cus_ids) {
                                        $evidenceClientCase['cust_id'] = $cus_ids;
                                        $modelClientCaseEvidence = new ClientCaseEvidence('addClientCaseEvidence');
                                        $modelClientCaseEvidence->load($evidenceClientCase);
                                        $modelClientCaseEvidence->cust_id = $cus_ids;
                                        $modelClientCaseEvidence->save(false);
                                    }
                                }
                            } else {
                                foreach ($ar_cust_id as $cust) {
                                    $evidenceClientCase['cust_id'] = $cust;
                                    $modelClientCaseEvidence = new ClientCaseEvidence();
                                    $modelClientCaseEvidence->load($evidenceClientCase);
                                    $modelClientCaseEvidence->cust_id = $cust;
                                    $modelClientCaseEvidence->save(false);
                                }
                            }
                        }else {
                            $modelClientCaseEvidence = new ClientCaseEvidence();
                            $modelClientCaseEvidence->load($evidenceClientCase);
                            $modelClientCaseEvidence->cust_id=0;
                            $modelClientCaseEvidence->save(false);
                        }

                        // Add New Media Email
                        //SettingsEmail::sendEmail
                        EmailCron::saveBackgroundEmail(3, 'is_sub_new_media', $data = array('case_id' => $case_id, 'evidence' => $_POST['Evidence'], 'newcontents' => $_POST['EvidenceContent'], 'evid_clientcase' => $evidenceClientCase));
                    }
                }
                /* Save Evident Client Case End */
                /* Save Evidence Transaction Starts */
                $EvidenceTransaction = new EvidenceTransaction();
                $EvidenceTransaction->evid_num_id = $media_id;
                $EvidenceTransaction->trans_type = 1;
                $EvidenceTransaction->trans_date = date('Y-m-d H:i:s');
                $EvidenceTransaction->trans_requested_by = Yii::$app->user->identity->id;
                $EvidenceTransaction->moved_to = 0;
                $EvidenceTransaction->trans_reason = "";
                $EvidenceTransaction->trans_by = Yii::$app->user->identity->id;
                $EvidenceTransaction->save(false);
                /* Save Evidence Transaction Ends */
                (new ActivityLog())->generateLog('Media','Added', $media_id, $model->id);

                if(isset($params['Evidence_flag']) && $params['Evidence_flag'] == 1)
                {
                    echo $media_id.'|'.'attach';
                    exit;
                }
                if(isset($params['Evidence_flag']) && $params['Evidence_flag'] == 2)
                {
                    echo $media_id.'|'.'attach_another';
                    exit;
                }
                else
                    return $this->redirect(['index']);
            }else{
                if(isset($params['Evidence_flag']) && $params['Evidence_flag'] == 1)
                {
                    echo $media_id.'|'.'attach';
                    exit;
                }
                if(isset($params['Evidence_flag']) && $params['Evidence_flag'] == 2)
                {
                    echo $media_id.'|'.'attach_another';
                    exit;
                }
                else
                    return $this->redirect(['index']);
                        /* return $this->renderAjax('create', [
                    'model' => $model,
                    'clientList'=>$clientList,
                    'listEvidenceType'=>$listEvidenceType,
                    'listEvidenceCategory'=>$listEvidenceCategory,
                    'listUnit'=>$listUnit,
                    'listEvidenceEncrypt'=>$listEvidenceEncrypt,
                    'listEvidenceLoc'=>$listEvidenceLoc
                ]); */
            }
        } else {
			$evidences_length = (new User)->getTableFieldLimit('tbl_evidence');
			return $this->renderAjax('create', [
                'model' 				=> 	$model,
                'clientList'			=>	$clientList,
                'listEvidenceType'		=>	$listEvidenceType,
                'listEvidenceCategory'	=>	$listEvidenceCategory,
                'listUnit'				=>	$listUnit,
                'listEvidenceEncrypt'	=>	$listEvidenceEncrypt,
                'listEvidenceLoc'		=>	$listEvidenceLoc,
                'evidences_length'		=>	$evidences_length
            ]);
        }
    }
    /**
     * Creates a new Evidence Content.
     * @return mixed
     */
    public function actionAddEvidenceContent() {
		$model = new EvidenceContents();
        $model_cust = new EvidenceCustodians();
        $temp_evid_id=Yii::$app->request->post('temp_evid_id');
        $case_ids=Yii::$app->request->post('case_id');
        $case_ids=implode(",",$case_ids);
        $client_id=Yii::$app->request->post('client_id');
        $client_id=implode(",",$client_id);
        $listcustdata = array();
        $listDataType = ArrayHelper::map(DataType::find()->where(['remove'=>'0'])->orderBy(['data_type'=>SORT_ASC])->asArray()->all(), 'id', 'data_type');
        $listUnit = ArrayHelper::map(Unit::find()->where(['remove'=>'0','is_hidden'=>0])->orderBy(['sort_order'=>SORT_ASC])->asArray()->all(), 'id', 'unit_name');
        $cust_data = EvidenceCustodians::find()->joinWith(['clientCaseEvidence','clientCaseCustodians'])->select(['tbl_evidence_custodians.cust_id', 'cust_fname','cust_lname'])->where('(tbl_client_case_evidence.client_case_id IN (' . $case_ids . ') OR tbl_client_case_custodians.client_case_id IN (' . $case_ids . '))')->orderBy(['cust_lname'=>SORT_ASC,'cust_fname'=>SORT_ASC])->all();
        foreach ($cust_data as $cd) {
            $listcustdata[$cd->cust_id] = $cd->cust_lname . ", " . $cd->cust_fname . " " . $cd->cust_mi; ///EvidenceCustodians::model()->custdetails($cd->cust_id);
        }
        $this->renderAjax('_loadAddEvidContentForm',
        array(
			'model' => $model,
			'data' => Yii::$app->request->post(),
			'model_cust' => $model_cust,
			'listcustdata'=>$listcustdata,
			'listDataType'=>$listDataType,'listUnit'=>$listUnit,'temp_evid_id'=>$temp_evid_id)
        );
        //die;
    }
    /**
     * Creates a new Evidence Content.
     * @return mixed
     */
    public function actionEditEvidenceContent() {
        $model = new EvidenceContents();
        $model_cust = new EvidenceCustodians();
        $temp_evid_id=Yii::$app->request->post('temp_evid_id');
        $case_ids=Yii::$app->request->post('case_id');
        $case_ids=implode(",",$case_ids);
        $client_id=Yii::$app->request->post('client_id');
        $client_id=implode(",",$client_id);
        $evid_id=Yii::$app->request->post('evid_id');
        $cust_id=Yii::$app->request->post('cust_id');
        $listcustdata = array();
        $listDataType = ArrayHelper::map(DataType::find()->where(['remove'=>'0'])->orderBy(['data_type'=>SORT_ASC])->asArray()->all(), 'id', 'data_type');
        $listUnit = ArrayHelper::map(Unit::find()->where(['remove'=>'0','is_hidden'=>0])->orderBy(['sort_order'=>SORT_ASC])->asArray()->all(), 'id', 'unit_name');
        /*$cust_data = EvidenceCustodians::find()->joinWith(['clientCaseEvidence','clientCaseCustodians'])->select(['tbl_evidence_custodians.cust_id', 'cust_fname','cust_lname'])->where('(tbl_client_case_evidence.client_id IN ( '. $client_id . ') AND tbl_client_case_evidence.client_case_id IN (' . $case_ids . ') OR tbl_client_case_custodians.client_id IN (' . $client_id . ') AND tbl_client_case_custodians.client_case_id IN (' . $case_ids . '))')->orderBy(['cust_lname'=>SORT_ASC,'cust_fname'=>SORT_ASC])->all();*/
        $cust_data = EvidenceCustodians::find()->joinWith(['clientCaseEvidence','clientCaseCustodians'])->select(['tbl_evidence_custodians.cust_id', 'cust_fname','cust_lname'])->where('(tbl_client_case_evidence.client_case_id IN (' . $case_ids . ') OR tbl_client_case_custodians.client_case_id IN (' . $case_ids . '))')->orderBy(['cust_lname'=>SORT_ASC,'cust_fname'=>SORT_ASC])->all();
        foreach ($cust_data as $cd) {
            $listcustdata[$cd->cust_id] = $cd->cust_lname . ", " . $cd->cust_fname . " " . $cd->cust_mi; ///EvidenceCustodians::model()->custdetails($cd->cust_id);
        }
        $custodian = EvidenceCustodians::findOne($cust_id);
        $this->renderPartial('_loadAddEvidContentForm', array('model' => $model,'data' => Yii::$app->request->post(),'custodian' => $custodian,'model_cust' => $model_cust,'listcustdata'=>$listcustdata,'listDataType'=>$listDataType,'listUnit'=>$listUnit,'temp_evid_id'=>$temp_evid_id), false, true);
        die;
    }
    /**
     * Validate Custodian using ajax
     * @return
     */
    public function actionCustodianvalidate(){
    	$model = new EvidenceCustodians();
    	if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
    		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    		return ActiveForm::validate($model);
    	}
    }
    /**
     * Validate Custodian using ajax
     * @return
     */
    public function actionContentvalidate(){
    	$model = new EvidenceContents();
    	if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
    		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    		return ActiveForm::validate($model);
    	}
    }
    /**
     * Append Evidence content in add media form
     * @return
     */
    public function actionAppendEvidenceContent() {
        $temp_evid_id=Yii::$app->request->post('temp_evid_id');
        $evid_id=Yii::$app->request->post('evid_id');
        $post_data= Yii::$app->request->post();
        // echo "<pre>",print_r($post_data),"</pre>";
        $datatype = DataType::find()->where(['remove'=>'0','id'=>$post_data['EvidenceContents']['data_type']])->one();
        $unit = Unit::find()->where(['remove'=>'0','id'=>$post_data['EvidenceContents']['unit']])->one();
        $evid_custdata=EvidenceCustodians::find()->where(['cust_id'=>$post_data['EvidenceContents']['cust_id']])->one();
        return $this->renderPartial('appendEvidContent', ['data' => $post_data, "temp_evid_id" => $temp_evid_id, 'evid_id' => $evid_id, 'datatype' => $datatype, 'unit' => $unit, 'evid_custdata' => $evid_custdata]);
    }

    /*
     *  Get the Totalsize and Total Size Units of Media Type
     * */
    public function actionGettotalsizebyevidencetype()
    {
		$evid_id = Yii::$app->request->post('evidence_id');
		$qty = Yii::$app->request->post('quantity');
		$media_type = EvidenceType::find()->where('id = '.$evid_id)->one();
		$data = array();
		$data['est_size'] = $media_type->est_size;
		$data['media_unit_id'] = $media_type->media_unit_id;
		$getmaxunit = 0;

		if(!empty($media_type->unit->unitMasters)){
			$estunittype = $media_type->unit->unitMasters->unit_type;
			$estunitsize = $media_type->unit->unitMasters->unit_size;
			$estsize = $media_type->est_size;
			$kb = ($estsize * $estunitsize); //get value in kb first
			$total_kbs = ($kb * $qty); //get qty value in kb
			//$total_bytes = ($total_kbs * 1024); //get total values in bytes to convert it to max unit
			$getmaxunit = (new Unit)->formatSizeUnits($total_kbs, $estunittype);
		}
		/*if(intval($getmaxunit) > 0)*/
		echo $getmaxunit;
		die;
	}

    public function actionDeleteMedia() {
        $records=Yii::$app->request->get('records');
        $evid_ids = explode(',', $records);
        foreach ($evid_ids as $eids) {
            $re = "Allow";
            $sql=" SELECT tbl_task_instruct_evidence.evidence_id FROM tbl_task_instruct_evidence INNER JOIN tbl_task_instruct ON tbl_task_instruct.id=tbl_task_instruct_evidence.task_instruct_id AND tbl_task_instruct.isactive=1 INNER JOIN tbl_tasks on tbl_tasks.id=tbl_task_instruct.task_id WHERE evidence_id=$eids";
            $project = TaskInstructEvidence::findBySql($sql)->count();
            if($project > 0){
            	$re = "Denied";
                echo $re;
                die;
            }else{
            	$sql="SELECT tbl_evidence_production.id FROM tbl_evidence_production INNER JOIN tbl_evidence_production_media ON tbl_evidence_production_media.prod_id=tbl_evidence_production.id WHERE tbl_evidence_production_media.evid_id=$eids ";
            	$production=EvidenceProduction::findBySql($sql)->count();
            	if($production > 0){
            		$re = "Denied";
	                echo $re;
	                die;
            	}
            }
        }
        foreach ($evid_ids as $rec) {
            $evidNum=$rec;
            $model = Evidence::findOne((int) $rec);
            EvidenceContents::deleteAll(['evid_num_id'=>$evidNum]);
            EvidenceTransaction::deleteAll(['evid_num_id'=>$evidNum]);
            $evidenceClCs = ClientCaseEvidence::find()->where(["evid_num_id"=>$evidNum])->select(['client_case_id', 'cust_id'])->all();
            foreach ($evidenceClCs as $clcs) {
                $caseCustodian = ClientCaseEvidence::find()->where(["client_case_id"=>$clcs->client_case_id,"cust_id"=>$clcs->cust_id])->andWhere(['<>','evid_num_id', $evidNum])->count();
                $evidContent=EvidenceContents::find()->where(["cust_id"=>$clcs->cust_id])->andWhere(['<>','evid_num_id', $evidNum])->count();
                if ($caseCustodian == 0) {
                    ClientCaseCustodians::deleteAll(["cust_id"=>$clcs->cust_id]);
                    if($evidContent == 0){
                    	EvidenceCustodians::deleteAll(["cust_id"=>$clcs->cust_id]);
                    }
                }
                ClientCaseEvidence::deleteAll(["evid_num_id" => $evidNum]);
            }
            EvidenceProductionMedia::deleteAll(["evid_id" => $evidNum]);

            $evidencedocs = Mydocument::find()->where(["reference_id"=>$evidNum,"origination"=>"Media"])->select(['doc_id'])->all();
            if(!empty($evidencedocs))
            {
                foreach($evidencedocs as $edoc)
                {
                    MydocumentsBlob::deleteAll(['id'=>$edoc['doc_id']]);
                }
            }
            Mydocument::deleteAll(["reference_id"=>$evidNum,"origination"=>"Media"]);

            $model->delete();
            $activityLog = new ActivityLog();
            $activityLog->generateLog('Media','Deleted',$evidNum, $evidNum);
        }
        echo $re;
        die;
    }
    /**
     * Updates an existing Evidence model.
     * If update is successful, the browser will be redirected to the 'list' page.
     * @param integer $id
     * @return mixed
     */
      public function actionEditEvidence($id) {
        $evidNum = (int) $id;
        $user_id=Yii::$app->user->identity->id;
        $roleId = Yii::$app->user->identity->role_id;
        $model = Evidence::findOne($evidNum);
        $sql=" SELECT client_id FROM tbl_project_security WHERE user_id=$user_id AND client_id!=0 AND client_case_id !=0 GROUP BY client_id ";
        if($roleId!=0){
			$clientList = ArrayHelper::map(Client::find()->where('id IN ('.$sql.')')->orderBy('tbl_client.client_name ASC')->asArray()->all(), 'id', 'client_name');
		}else{
			$clientList = ArrayHelper::map(Client::find()->orderBy('tbl_client.client_name ASC')->asArray()->all(), 'id', 'client_name');
		}
        $clientList = json_decode(htmlspecialchars_decode(json_encode($clientList)),true);
        $clientids = array_keys($clientList);
        $clientids = array();
        $userId = Yii::$app->user->identity->id;
        $roleId = Yii::$app->user->identity->role_id;
        $currentUser = Role::findOne(Yii::$app->user->identity->role_id);
        $role_type=array();
        if($currentUser->role_type != '')
            $role_type= explode(",",$currentUser->role_type);

        $clientCaseEvidences = ClientCaseEvidence::find()->joinWith(['clientcase'])->where(["evid_num_id"=>$evidNum])->orderBy(['tbl_client_case_evidence.id'=>SORT_ASC])->select(['client_case_id'])->all();
        $CCE = array();
        $CC_data = array();
        $evidence_case_id=array();
        $client_id=0;
        if (!empty($clientCaseEvidences)) {
            foreach ($clientCaseEvidences as $clientCaseEvidence) {
                $CCE['client_id'] = $clientCaseEvidence->clientcase->client_id;
                $CCE[$clientCaseEvidence->client_case_id]['client_id'] = $clientCaseEvidence->clientcase->client_id;
                $CCE['client_case_id'][$clientCaseEvidence->client_case_id] = $clientCaseEvidence->client_case_id;
                $CCE['client'][$clientCaseEvidence->clientcase->client_id] = $clientCaseEvidence->clientcase->client_id;
                array_push($clientids,$clientCaseEvidence->clientcase->client_id);

                $CC_data[$clientCaseEvidence->clientcase->client_id]['case_ids'][$clientCaseEvidence->client_case_id] = $clientCaseEvidence->clientcase->case_name;
                $CC_data[$clientCaseEvidence->clientcase->client_id]['client_name'] = $clientCaseEvidence->clientcase->client->client_name;
                $evidence_case_id[]=$clientCaseEvidence->client_case_id.'|'.$clientCaseEvidence->clientcase->client_id;
                $client_id=$clientCaseEvidence->clientcase->client_id;
            }
        }

        //print_r($evidence_case_id);die;
        $case_arr=array();
        if(!empty($clientids))
        {
           //$model->client_id=$clientids;
           $clientids=implode(",",array_unique($clientids));
           if ($roleId != 0 && (!in_array(2,$role_type))) {
                   $case_data = ProjectSecurity::find()->select('client_case_id')->where('client_id IN ('.$clientids.') AND user_id='.$userId.' AND team_id=0');
                   $caseList = ClientCase::find()->select(['id', 'case_name','client_id'])->where(['in', 'id', $case_data])->orderBy('case_name')->all();
           } else {

                   $caseList = ClientCase::find()->select(['id', 'case_name','client_id'])->where(['in', 'client_id', explode(",",$clientids)])->orderBy('case_name')->all();
           }
           if(!empty($caseList)){
                foreach ($caseList as $case){
                    $case_arr[$case->id.'|'.$case->client_id]=$case->case_name;
                }
           }
           $model->case_id=$CCE['client_case_id'];
        }
        $evidenceContent = EvidenceContents::find()->joinWith(['evidenceCustodians','evidenceContentUnit'])->where(["evid_num_id"=>$evidNum])->orderBy(['id'=>SORT_ASC])->select(['tbl_evidence_contents.id', 'tbl_evidence_contents.cust_id', 'data_type', 'data_size', 'unit', 'data_copied_to','unit_name'])->all();
        $listUnit = ArrayHelper::map(Unit::find()->where(['remove'=>'0','is_hidden'=>0])->orderBy(['sort_order'=>SORT_ASC])->asArray()->all(), 'id', 'unit_name');
        $usernameList = ArrayHelper::map(User::find()->where(['not in','id',array(1,2)])->orderBy(['usr_username'=>SORT_ASC])->asArray()->all(), 'usr_username', 'usr_username');
        $listEvidenceType = ArrayHelper::map(EvidenceType::find()->where(['remove'=>'0'])->orderBy(['evidence_name'=>SORT_ASC])->asArray()->all(), 'id', 'evidence_name');
        $listEvidenceCategory = ArrayHelper::map(EvidenceCategory::find()->where(['remove'=>'0'])->orderBy(['category'=>SORT_ASC])->asArray()->all(), 'id', 'category');
        $listEvidenceEncrypt = ArrayHelper::map(EvidenceEncryptType::find()->where(['remove'=>'0'])->orderBy(['encrypt'=>SORT_ASC])->asArray()->all(), 'id', 'encrypt');
        $listEvidenceLoc = ArrayHelper::map(EvidenceStoredLoc::find()->where(['remove'=>'0'])->orderBy(['stored_loc'=>SORT_ASC])->asArray()->all(), 'id', 'stored_loc');
        //if (isset($model->received_time) && date('Y-m-d', strtotime($model->received_time)) != "1970-01-01") {
          //	$model->received_time = $model->received_time;
        //}
        $evid_docs = Mydocument::find()->joinWith(['mydocumentsBlobs'])->select(['tbl_mydocument.id','fname', 'doc_size','doc_type','doc_id','tbl_mydocuments_blob.doc'])->where(['tbl_mydocument.reference_id'=>(int) $id,'tbl_mydocument.origination'=>'Media'])->all();
        $org_time=$model->received_time;
		$org_date=$model->received_date;
		if($model->received_time != '') {
            $received_date_time = $org_date.' '.$org_time;
		    $model->received_time=(new Options)->ConvertOneTzToAnotherTz($received_date_time, 'UTC', $_SESSION['usrTZ'],'time');
        } if($model->received_date != '') {
			$received_date_time = $org_date.' '.$org_time;
		    $model->received_date=(new Options)->ConvertOneTzToAnotherTz($received_date_time, 'UTC', $_SESSION['usrTZ'],'YMD');
		}
        $evidences_length = (new User)->getTableFieldLimit('tbl_evidence');
        return $this->renderAjax('update', [
                'clientList' => $clientList,
                'model' => $model,
                'evidNum' => $evidNum,
                'usernameList' => $usernameList,
                'clientCaseEvidences' => $CCE,
                'evidencecontents_data' => $evidenceContent,
                'listEvidenceType' => $listEvidenceType,
                'listUnit' => $listUnit,
                'listEvidenceLoc' => $listEvidenceLoc,
                'listEvidenceNum' => $listEvidenceNum,
                'listEvidenceCategory' => $listEvidenceCategory,
                'listEvidenceEncrypt' => $listEvidenceEncrypt,
                'case_arr'=>$case_arr,
                'evid_docs'=>$evid_docs,
                'CC_data'=>$CC_data,
                'evidence_case_id'=>$evidence_case_id,
                'evidNum' => $evidNum,
                'evidences_length' => $evidences_length,
                'client_id'=>$client_id
            ]);
        $listEvidenceNum = array();
        if (Yii::app()->request->isAjaxRequest) {
            Yii::app()->clientscript->scriptMap = array('jquery.js' => true, 'jquery-ui.min.js' => true);
            $tUrl=Yii::app()->theme->baseUrl;
            Yii::app()->clientscript->registerScriptFile($tUrl.'/js/jquery.form.js');
        }
        if (isset($model->received_time) && date('Y-m-d', strtotime($model->received_time)) != "1970-01-01") {
          	$model->received_time = $model->received_time;
        }
    }
    public function actionUpdateEvidenceProcess() {

        $params= Yii::$app->request->post('Evidence');

        if($params['case_id'] != '')
            $params['case_id']=explode(",",$params['case_id']);
        else
            $params['case_id']=array();
        $media_id=$params['id'];
        $model = Evidence::findOne($media_id);
        $model->load(Yii::$app->request->post());
        //$model->checkedin_by = Yii::$app->user->identity->id;
       // $model->status = 1;
        if (isset($_POST['EvidenceContent']) && !empty($_POST['EvidenceContent'])) {
            $model->has_contents = 1;
        } else {
            $model->has_contents = 0;
        }
        $received_date=Yii::$app->request->post('Evidence')['received_date'];
        $received_time=Yii::$app->request->post('Evidence')['received_time'];
         if (isset($received_date) && ($received_date != '')) {
             $received_date_arr=explode("/",$received_date);
             $received_date=$received_date_arr[2]."-".$received_date_arr[0]."-".$received_date_arr[1];
             $received_date_time=$received_date.' '.$received_time;
             $received_date_time = (new Options)->ConvertOneTzToAnotherTz($received_date_time, $_SESSION['usrTZ'],'UTC', "YMDHIA");
             $received=explode(" ",$received_date_time);
             $model->received_date = $received[0];
             $model->received_time = $received[1].' '.$received[2];
         }
        $model->save(false);
        /* Code for evidence attachment start */
        if(!empty($_FILES['Evidence']['name']['upload_files'][0]))
        {
            $docmodel = new Mydocument();
            $doc_arr['p_id']=0;
            $doc_arr['reference_id']=$media_id;
            $doc_arr['team_loc']=0;
            $doc_arr['origination']='Media';
            $doc_arr['is_private']='';
            $doc_arr['type']='F';
            $doc_arr['is_private']='';
            $file_arr=$docmodel->Savemydocs('Evidence','upload_files',$doc_arr);
            $files_str = json_encode($file_arr);
        }
        /* Code for evidence attachment end */
        /* Code for evidence attachment delete start */
        if ($params['deleted_img'] != "") {
            $deleted_attach_arr = explode(",", $params['deleted_img']);
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
        /* Code for evidence attachment delete ends */
        /* Save Evident Content start */
        $evidcontent_data=$_POST['EvidenceContent'];
        $evidcustodian_data=$_POST['EvidenceCustodian'];
        $ar_cust_id = array();
        if (isset($evidcontent_data) && !empty($evidcontent_data)) {
            EvidenceContents::deleteAll(["evid_num_id" =>$media_id]);
            foreach ($evidcontent_data as $evidContent) {
                $evidContent['evid_num_id'] = $media_id;
                if (isset($evidcustodian_data[$evidContent['cust_id']]) && !empty($evidcustodian_data[$evidContent['cust_id']])) {
                    $cust_fname = $evidcustodian_data[$evidContent['cust_id']]['cust_fname'];
                    $cust_lname = $evidcustodian_data[$evidContent['cust_id']]['cust_lname'];
                    $cust_mi = $evidcustodian_data[$evidContent['cust_id']]['cust_mi'];
                    $cust_email = $evidcustodian_data[$evidContent['cust_id']]['cust_email'];
                    $title = $evidcustodian_data[$evidContent['cust_id']]['title'];
                    $dept = $evidcustodian_data[$evidContent['cust_id']]['dept'];
                    $modelEvidenceCustodians = new EvidenceCustodians();
                    $modelEvidenceCustodians->cust_fname = $cust_fname;
                    $modelEvidenceCustodians->cust_lname = $cust_lname;
                    $modelEvidenceCustodians->cust_mi = $cust_mi;
                    $modelEvidenceCustodians->cust_email = $cust_email;
                    $modelEvidenceCustodians->title = $title;
                    $modelEvidenceCustodians->dept = $dept;
                    $modelEvidenceCustodians->save(false);
                    $lastcust_id = Yii::$app->db->getLastInsertId();
                    $evidContent['cust_id'] = $lastcust_id;
                }
                $ar_cust_id[$evidContent['cust_id']] = $evidContent['cust_id'];
                $evidContent_data['EvidenceContents']=$evidContent;
                $modelEvidenceCont = new EvidenceContents();
                $modelEvidenceCont->load($evidContent_data);
                $modelEvidenceCont->save(false);
            }
        }
        /* Save Evident Content End */
        /* Save Client case data Starts */
        ClientCaseEvidence::deleteAll(["evid_num_id"=>$media_id]);
        $evidcase_data=$params;

            if (isset($evidcase_data['case_id']) && !empty($evidcase_data['case_id'])) {
                $evidenceClientCase = array();

                foreach ($evidcase_data['case_id'] as $key => $value) {
                    $case_arr=explode("|",$value);
                    $client_id = $case_arr[1];
                    $evidenceClientCase['ClientCaseEvidence']['client_case_id'] = $case_arr[0];
                    $evidenceClientCase['ClientCaseEvidence']['client_id'] = $client_id;
                    $evidenceClientCase['ClientCaseEvidence']['evid_num_id'] = $media_id;
                    if (!empty($ar_cust_id)) {
                        $cust_ids = implode(",", $ar_cust_id);
                        $evidenceCont = ClientCaseEvidence::find()->select(['cust_id'])->where("client_case_id=$case_arr[0] AND evid_num_id=$media_id AND cust_id IN ($cust_ids)")->all();

                        if (!empty($evidenceCont)) {
                            $ar_custodin_ids = array();
                            foreach ($evidenceCont as $evdCon) {
                                $ar_custodin_ids[$evdCon->cust_id] = $evdCon->cust_id;
                            }
                            if (!empty($ar_custodin_ids)) {
                                foreach (array_diff($ar_cust_id, $ar_custodin_ids) as $cus_ids) {
                                    $evidenceClientCase['cust_id'] = $cus_ids;
                                    $modelClientCaseEvidence = new ClientCaseEvidence('addClientCaseEvidence');
                                    $modelClientCaseEvidence->load($evidenceClientCase);
                                    $modelClientCaseEvidence->cust_id = $cus_ids;
                                    $modelClientCaseEvidence->save(false);
                                }
                            }
                        } else {
                            foreach ($ar_cust_id as $cust) {
                                $evidenceClientCase['cust_id'] = $cust;
                                $modelClientCaseEvidence = new ClientCaseEvidence();
                                $modelClientCaseEvidence->load($evidenceClientCase);
                                $modelClientCaseEvidence->cust_id = $cust;
                                $modelClientCaseEvidence->save(false);
                            }
                        }
                    } else {
                        $modelClientCaseEvidence = new ClientCaseEvidence();
                        $modelClientCaseEvidence->load($evidenceClientCase);
                        $modelClientCaseEvidence->cust_id = 0;
                        $modelClientCaseEvidence->save(false);
                    }
                }
            }
        /* Save Client case data Ends */
         (new ActivityLog())->generateLog('Media','Updated', $media_id, $media_id);
         return $this->redirect(['index']);
    }
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Evidence model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Change status of an existing Evidence model.
     * If changed is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
	public function actionCheckOutInstatus($id) {
			$evid_ids = explode(',', $id);
			$cnt_evid=Evidence::find()->where(['status'=>'3'])->andwhere(['in','id',$evid_ids])->count();
			if($cnt_evid > 0){
				echo "Denied";
				exit;
		     }
		    echo "Approved";
			exit;
	}
	public function actionChangeStatus($id) {
		$params= Yii::$app->request->post();
	    if (isset($params['EvidenceTransaction']) && !empty($params)) {
            $evid_ids = explode(',', $params['evid_nums']);
            foreach ($evid_ids as $eid) {
                if ($params['EvidenceTransaction']['is_duplicate'] == 1 || $params['EvidenceTransaction']['is_duplicate'] == 'on') {
                    $Evid_model2 = Evidence::findOne($eid);//Evidence::model()->findByAttributes(array('id' => $eid));
                    if ($Evid_model2->dup_evid != 0) {
                        $evid_num = $Evid_model2->org_link;
                        array_push($evid_ids, $evid_num);
                    }
                    unset($Evid_model2);
                }
            }
            $evid_ids = array_unique($evid_ids);
            $trans_type = $params['EvidenceTransaction']['trans_type'];
            if ($trans_type == "") {
                $this->redirect(array('media/index'));
            }
            $trans_requested_by = $params['EvidenceTransaction']['trans_requested_by'];
            $trans_reason = $params['EvidenceTransaction']['trans_reason'];
            $moved_to = $params['EvidenceTransaction']['moved_to'];
            $trans_to = $params['EvidenceTransaction']['Trans_to'];
            foreach ($evid_ids as $eids) {
                $model = new Evidence();
                $model = Evidence::findOne($eids);
                if($model)
                {
                    //$model->id = $eids;
                    $ETmodel = new EvidenceTransaction();
                    $ETmodel->evid_num_id = $eids;
                    $ETmodel->trans_type = $trans_type;
                    $ETmodel->trans_date = date('Y-m-d H:i:s');
                    $ETmodel->trans_by = Yii::$app->user->identity->id;
                    if ($trans_to != "") {
                        $ETmodel->Trans_to = $trans_to;
                    }
                    $ETmodel->trans_requested_by = $trans_requested_by;
                    $ETmodel->trans_reason = $trans_reason;
                    if ($moved_to != "") {
                        $ETmodel->moved_to = $moved_to;
                        $model->evid_stored_location = $moved_to;
                    }
                    $model->status = $trans_type;
                    if (!empty($model))
                        $model->save(false);
                    $ETmodel->save(false);

                    $activityLog = new ActivityLog();
                    $activityLog->generateLog('Media','Updated',$eids, $eids);
                }
            }
            return $this->redirect(['index']);
        }

        $model = new EvidenceTransaction();
        $UserData = User::find()->where('id != 1')->orderBy(['id' => SORT_ASC])->all();
        $UserName = array();
        foreach ($UserData as $user) {
            $UserName[$user['id']] = $user['usr_first_name'] . " " . $user['usr_lastname'];
        }
        //$evid_ids = explode(',', $id);
        $evid_ids = $id;
        $evid_data = Evidence::find()->where("id IN (".$evid_ids.")")->all();

        $transType=array();
        if(!empty($evid_data)) {
            foreach ($evid_data as $eid)
            {
                if($eid->status==2 || $eid->status==5)
                {
                    $transType[1]='Check in';
                }
                if($eid->status==1)
                {
                    $transType[2]='Check out';
                    $transType[3]='Destroy';
                    $transType[4]='Move';
                    $transType[5]='Return';
                    if(isset($transType[1])) unset($transType[1]);
                }
                if($eid->status==4)
                {
                    $transType[2]='Check out';
                    $transType[3]='Destroy';
                    $transType[1]='Check in';
                    $transType[5]='Return';
                }
            }
        }

	$listEvidenceLoc = ArrayHelper::map(EvidenceStoredLoc::find()->where(['remove' => 0])->orderBy(['stored_loc'=>SORT_ASC])->select(['id', 'stored_loc'])->asArray()->all(),'id', 'stored_loc');
        $listEvidenceTo = ArrayHelper::map(EvidenceTo::find()->where(['remove' => 0])->orderBy(['to_name'=>SORT_ASC])->select(['id', 'to_name'])->asArray()->all(),'id', 'to_name');
        $model_field_length = (new User)->getTableFieldLimit($model->tableSchema->name);
        return $this->renderAjax('AddcheckOutInEvidContent', ['model' => $model, 'evidNum' => $id, 'UserName' => $UserName, 'listEvidenceLoc' => $listEvidenceLoc, 'listEvidenceTo' => $listEvidenceTo,'transType'=>$transType,'model_field_length' => $model_field_length]);
    }
    /*
     * Show only Fields as per evidence type
     * @params type_id
     * @return mixed
     * */
     public function actionGetEvidenceTypeFields()
     {
		$params= Yii::$app->request->post();
		$id = 	$params['id'];
		$model = new EvidenceTransaction();
		$model->trans_type = $params['trans_type'];
		$sql = 'select form_builder_system_id from tbl_evidence_transaction_type_fields where transaction_type_id = '.$params['trans_type'];
                $reqFormData = FormBuilderSystem::find()->select(['sys_field_name','required'])->where('id IN ('.$sql.')')->all();
                $reqFormStatus = $tran_show_field = [];
                if(!empty($reqFormData)){
                    foreach($reqFormData as $single){
                        if($single->required == 1)
                            $reqFormStatus[$single->sys_field_name] = 'true';
                        else
                            $reqFormStatus[$single->sys_field_name] = 'false';
                        $tran_show_field[$single->sys_field_name] = $single->sys_field_name;
                    }
                }
//		$tran_show_field = ArrayHelper::map($reqFormData,'sys_field_name','sys_field_name');
//                echo '<pre>',print_r($tran_show_field);die;
		$UserData = User::find()->where('id != 1 and status = 1')->orderBy(['usr_lastname' => SORT_ASC])->all();
		$UserName = array();
		foreach ($UserData as $user) {
                        if($user['usr_first_name'] == '' && $user['usr_lastname'] == '')
                            $UserName[$user['id']] = $user['usr_username'];
                        else
                            $UserName[$user['id']] = $user['usr_first_name'] . " " . $user['usr_lastname'];
		}
		//$evid_ids = explode(',', $id);
        //echo "<pre>",print_r($UserName),"</pre>";die;
		$evid_ids = $id;
       	$evid_data=Evidence::find()->where("id IN (".$evid_ids.")")->all();
        $transType=array();
        /* Evidence Data */
        if(!empty($evid_data)){
	        foreach ($evid_data as $eid)
			{
				if($eid->status==2 || $eid->status==5)
				{
					$transType[1]='Check in';
				}
				if($eid->status==1)
				{
					$transType[2]='Check out';
					$transType[3]='Destroy';
					$transType[4]='Move';
					$transType[5]='Return';
					if(isset($transType[1])) unset($transType[1]);
				}
				if($eid->status==4)
				{
					$transType[2]='Check out';
					$transType[3]='Destroy';
					$transType[1]='Check in';
					$transType[5]='Return';
				}
			}
        }
		$listEvidenceLoc = ArrayHelper::map(EvidenceStoredLoc::find()->where(['remove' => 0])->orderBy(['stored_loc'=>SORT_ASC])->select(['id', 'stored_loc'])->asArray()->all(),'id', 'stored_loc');
        $listEvidenceTo = ArrayHelper::map(EvidenceTo::find()->where(['remove' => 0])->orderBy(['to_name'=>SORT_ASC])->select(['id', 'to_name'])->asArray()->all(),'id', 'to_name');
        $model_field_length = (new User)->getTableFieldLimit($model->tableSchema->name);
		 return $this->renderAjax('GetEvidenceTypeFields',
		 [
			 'model' => $model,
			 'UserName' => $UserName,
			 'tran_show_field'=>$tran_show_field,
                         'reqFormStatus'=>$reqFormStatus,
			 'evidNum' => $id, 'UserName' => $UserName, 'listEvidenceLoc' => $listEvidenceLoc, 'listEvidenceTo' => $listEvidenceTo,'transType'=>$transType,'model_field_length' => $model_field_length
		 ]);
		if (Yii::app()->request->isAjaxRequest) {
            Yii::app()->clientscript->scriptMap = array('jquery.js' => true, 'jquery-ui.min.js' => true);
            $tUrl=Yii::app()->theme->baseUrl;
            Yii::app()->clientscript->registerScriptFile($tUrl.'/js/jquery.form.js');
        }
	 }
	 /*
	  * Adhoc validation
	  * */
	  public function actionValidateEvidenceTypeFields(){
		  $params = Yii::$app->request->post();
		  $transaction_type = $params['EvidenceTransaction']['trans_type'];
		  $sql = 'select form_builder_system_id from tbl_evidence_transaction_type_fields where transaction_type_id = '.$transaction_type;
		  $tran_show_field = ArrayHelper::map(FormBuilderSystem::find()->select(['sys_field_name','required'])->where('id IN ('.$sql.') AND required =1 ')->all(),'sys_field_name','required');
		  Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
			if(isset($tran_show_field) && !empty($tran_show_field)){
				if(isset($params['EvidenceTransaction']['is_duplicate']) && $params['EvidenceTransaction']['is_duplicate'] == 0){
					$params['EvidenceTransaction']['is_duplicate'] ='';
					}
				 $model = new DynamicModel($params['EvidenceTransaction']);
				 foreach($tran_show_field as $key => $single){
					$model->addRule([$key], 'required');
				 }
				 $model->validate();

				 $errors=$model->getErrors();
				 if (Yii::$app->request->isAjax && !empty($errors)) {
					Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
					return $model->getErrors();
				 } else {
					return [];
				 }
			}
		  return [];
	  }
     /**
     * Display list of Change status of an existing Evidence model.
     * @param integer $id
     * @return mixed
     */
    public function actionChainOfCustody($id)
    {
        $model = new EvidenceTransaction();
        $params= Yii::$app->request->post();
        if (isset($_REQUEST['EvidenceTransactions'])) {
            $model->setAttributes($_REQUEST['EvidenceTransactions']);
            if (isset($_REQUEST['EvidenceTransactions']['Trans_to']) && $_REQUEST['EvidenceTransactions']['Trans_to'] != "")
                $model->Trans_to = $_REQUEST['EvidenceTransactions']['Trans_to'];
        }
        $model->evid_num_id = $id;
        $evidtrans = EvidenceTransaction::find()->where(['evid_num_id' => $id])->joinWith(['transby','transRequstedby','storedLoc','evidenceTo'])->orderBy(['trans_date'=>SORT_DESC])->all();
        // $trans_req_by_ar = array();
        $trans_req_by_ar = "";
        $EvidenceLoc_ar=array('All'=>'All');
        $EvidenceTo_ar=array('All'=>'All');
        $trans_by_ar=array('All'=>'All');
        $trans_req_by_ar_desc = array('All'=>'All');
        foreach($evidtrans as $evidrec) {
            if(isset($evidrec->transRequstedby->id) && $evidrec->transRequstedby->id > 0){
			    $trans_req_by_ar_desc[$evidrec->transRequstedby->id] = ucfirst($evidrec->transRequstedby->usr_first_name . " " . $evidrec->transRequstedby->usr_lastname);
            }
            if(isset($evidrec->storedLoc->id) && $evidrec->storedLoc->id > 0){
			$EvidenceLoc_ar[$evidrec->storedLoc->id] = $evidrec->storedLoc->stored_loc;
            }
            if(isset($evidrec->evidenceTo->id) && $evidrec->evidenceTo->id > 0){
			$EvidenceTo_ar[$evidrec->evidenceTo->id] = $evidrec->evidenceTo->to_name;
            }
            $trans_by_ar[$evidrec->trans_by]=ucfirst($evidrec->transby->usr_first_name.' '.$evidrec->transby->usr_lastname);

		}
		$trans_req_by_ar = $trans_req_by_ar_desc;
        //echo "<pre>",print_r($trans_req_by_ar),"</pre>";die;
        @asort($trans_req_by_ar);
		$searchmodel = new EvidenceTransactionSearch();
        $dataProvider = $searchmodel->search(Yii::$app->request->queryParams,$params);
         /*IRT 67,68,86,87,258*/
        $filter_type=\app\models\User::getFilterType(['trans_type','trans_date','trans_by','trans_requested_by','moved_to','Trans_to','trans_reason'],'tbl_evidence_transactions');

        $config = ['trans_type'=>['All'=>'All',1=>'Checked In',2=>'Checked Out',3=>'Destroyed',4=>'Moved',5=>'Returned'],'trans_by'=>$trans_by_ar,'trans_requested_by'=>$trans_req_by_ar,'moved_to'=>$EvidenceLoc_ar,'Trans_to'=>$EvidenceTo_ar];

        $filterWidgetOption=\app\models\User::getFilterWidgetOption($filter_type,Url::toRoute(['media/ajaxfiltercust','id'=>$id]),$config);

        /*IRT 67,68,86,87,258*/
        $coc_form = ArrayHelper::map(FormBuilderSystem::find()->select(['sys_field_name'])->where(['sys_form'=>'media_check_in_out_form','grid_type'=>0])->orderBy('sort_order')->all(),'sys_field_name','sys_field_name');
        $qparam=Yii::$app->request->queryParams;
       // echo "<pre>",print_r($qparam),"</pre>";
       // echo "<pre>",print_r($params),"</pre>"; die;
        //if (Yii::$app->request->isAjax) {
            $result = $this->renderAjax('load_chainofcustody', ['trans_by_ar'=>$trans_by_ar,'filter_type'=>$filter_type,'filterWidgetOption'=>$filterWidgetOption,'model' => $model,'dataProvider'=>$dataProvider,'searchModel'=>$searchmodel,'trans_req_by_ar' => $trans_req_by_ar, 'EvidenceLoc_ar' => $EvidenceLoc_ar, 'EvidenceTo_ar' => $EvidenceTo_ar, 'trans_by_ar' => $trans_by_ar, 'evidNum' => $id,'params'=>$params,'coc_form'=>$coc_form]);
        //}//else{
            //$this->layout = 'media';
           // $result = $this->render('load_chainofcustody', ['trans_by_ar'=>$trans_by_ar,'filter_type'=>$filter_type,'filterWidgetOption'=>$filterWidgetOption,'model' => $model,'dataProvider'=>$dataProvider,'searchModel'=>$searchmodel,'trans_req_by_ar' => $trans_req_by_ar, 'EvidenceLoc_ar' => $EvidenceLoc_ar, 'EvidenceTo_ar' => $EvidenceTo_ar, 'trans_by_ar' => $trans_by_ar, 'evidNum' => $id,'params'=>$params,'coc_form'=>$coc_form]);
        //}

        if (array_key_exists('dynagrid-transaction-dynagrid', $params) || (isset($params['DynaGridSettings']) && $params['DynaGridSettings']['dynaGridId']=='dynagrid-transaction'))
        {
            $response=Yii::$app->getResponse();
            //echo "<pre>",print_r($response);die;
            // Remove the redirection headers set in
            // DynaGrid.php line 525: Yii::$app->controller->refresh();
            Yii::$app->getResponse()->setStatusCode(200);

            Yii::$app->getResponse()->getHeaders()->remove('X-Pjax-Url');
            Yii::$app->getResponse()->getHeaders()->remove('X-Redirect');
            Yii::$app->getResponse()->getHeaders()->remove('Location');
            Yii::$app->getResponse()->getHeaders()->remove('location');
            Yii::$app->getResponse()->getHeaders()->remove('cache-control');

            return Json::encode([
            'success' => true,
            ]);
        }
        else
        {
            return $result;
        }


    }



    /**
     * Finds the Evidence model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Evidence the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Evidence::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /* Get media list on autocomplete text box action*/

    public function actionBringMediaList($term)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		$params=Yii::$app->request->get();
        if (isset($params['term'])) {
            $uid = Yii::$app->user->identity->id;
            $access_caseids = (new ProjectSecurity)->getUserCases($uid);
            $term = $params['term'];
            $caseId = isset($params['caseId']) ? $params['caseId'] : "";
            $suggest = array();
            $out = ['results' => ['id' => '', 'text' => '']];
            if (!empty($access_caseids)) {
                if ($caseId == ""){
					//$caseId = ArrayHelper::map(ProjectSecurity::find()->andWhere(['user_id'=>$uid,'team_id'=>0])->select('client_case_id')->asArray()->all(),'client_case_id','client_case_id');
                    $caseId = "select client_case_id from tbl_project_security where user_id = $uid AND team_id = 0";
                }
              $query=Evidence::find()->joinWith(['evidenceclientcase'])->andWhere(['like','tbl_evidence.id',$term])->select(['tbl_evidence.id']);
              if($caseId != ''){
				//$query->andWhere(['in','tbl_client_case_evidence.client_case_id',$caseId]);
                $query->andWhere('tbl_client_case_evidence.client_case_id in ('.$caseId.')');
              }
                $listEvidenceNums =$query->asArray()->limit(100)->all();
                if (!empty($listEvidenceNums)) {
                    $out['results']=array();
                    foreach ($listEvidenceNums as $listnum) {
                        $suggest[$listnum['id']] = $listnum['id'];
                        $out['results'][]=['id'=>$listnum['id'],'text'=>$listnum['id']];
                    }
                }
            }
        }
        return $out;
        //echo json_encode($suggest);
        //exit;
    }
    /* Get clientwise case data */
    public function actionGetcasesbyclient() {
    	$params = Yii::$app->request->post('depdrop_parents',0);
    	$client_id = $params[0];
        $clientcase = new ClientCase();
//        $clientcase->scenario ='clientcase_list';
    	if(isset($client_id) && $client_id!=0 && $client_id!="")
    	{
                $roleId = Yii::$app->user->identity->role_id;
    		$userId = Yii::$app->user->identity->id;
    	 	if ($roleId != 0) {
    			$case_data = ProjectSecurity::find()->select('client_case_id')->where('client_id='.$client_id.' AND user_id='.$userId.' AND team_id=0');
    			$caseList = $clientcase->find()->select(['id', 'case_name as name'])->where(['in', 'id', $case_data])->orderBy('case_name')->asArray()->all();
    		} else {
    			$caseList = $clientcase->find()->select(['id', 'case_name as name'])->where([ 'client_id' => $client_id])->orderBy('case_name')->asArray()->all();
    		}
    		echo htmlspecialchars_decode(Json::encode(['output'=>$caseList, 'selected'=>'']));
    		return;
    	}
    	echo Json::encode(['output'=>'', 'selected'=>'']);
    }
    /*check media attach to case*/
    public function actionChkMediaattachtocase(){
    	$evid    = Yii::$app->request->get('evid',0);
    	$case_id = Yii::$app->request->get('case_id',0);
    	$sql=" SELECT tbl_task_instruct_evidence.evidence_id FROM tbl_task_instruct_evidence INNER JOIN tbl_task_instruct ON tbl_task_instruct.id=tbl_task_instruct_evidence.task_instruct_id AND tbl_task_instruct.isactive=1 INNER JOIN tbl_tasks on tbl_tasks.id=tbl_task_instruct.task_id WHERE evidence_id=$evid AND tbl_tasks.client_case_id=$case_id";
    	$project = TaskInstructEvidence::findBySql($sql)->count();
    	if($project > 0){
    		return  "You cannot detach the Case since this Media has already been used in a Case Production and/or Project";
    	}else{
    		$sql="SELECT tbl_evidence_production.id FROM tbl_evidence_production INNER JOIN tbl_evidence_production_media ON tbl_evidence_production_media.prod_id=tbl_evidence_production.id WHERE client_case_id=$case_id AND tbl_evidence_production_media.evid_id=$evid ";
    		$production=EvidenceProduction::findBySql($sql)->count();
    		if($production > 0){
    			return  "You cannot detach the Case since this Media has already been used in a Case Production and/or Project";
    		}
    	}
    	return;
    }
    /*605*/
    /**
     * Get Client case Data for filter
     * */
    public function actionGetClientCase() {
        $uid = Yii::$app->user->identity->id;
        $roleId = Yii::$app->user->identity->role_id;
        $ids=Yii::$app->request->get('ids','-1');
        $ids=explode(",",$ids);
        $list_cases = array();
        $clientListAr = (new Client)->getClientCasesWithPermissiondetailsArray();
        $clientList = [];
		$selectedCases = [];
		foreach($clientListAr as $client_name => $clientCases){
			$client = [];
			foreach($clientCases as $client_id => $cases){
				$client['title'] = $client_name;
				$client['isFolder'] = true;
				$client['key'] = $client_id;
				$case = [];
				foreach($cases as $case_id => $case_name){
					$case['title'] = $case_name;
					$case['key'] = $case_id;
					if(in_array($case['key'],$ids)){
						$case['select'] = true;
						$selectedCases[] = $case['key'];
					} else {
						$case['select'] = false;
					}

					$client['children'][] = $case;
				}
				if(!empty($client['children']))
					$clientList[] = $client;
			}
		}
        if(!empty($clientList)){
            $client = [];
            $client['title'] = 'By No Associated Client/Cases';
			$client['isFolder'] = false;
			$client['key'] = 0;
            if(in_array($client['key'],$ids)){
                $client['select'] = true;
            }
            $clientList[] = $client;
        }

        return $this->renderAjax('getclientcase', ['clientList' =>$clientList,'list_clients' => $list_clients, 'list_cases' => $list_cases]);
    }
}
