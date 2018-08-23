<?php

namespace app\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use app\models\EvidenceCustodians;
use app\models\EvidenceCustodiansForms;
use app\models\ClientCaseCustodians;
use app\models\ClientCase;
use app\models\FormBuilder;
use app\models\FormCustodianValues;
use app\models\User;
use app\models\search\EvidenceCustodianSearch;
use app\models\FormBuilderSystem;
use yii\web\Controller;

class CaseCustodiansController extends \yii\web\Controller
{
	
	public function beforeAction($action)
	{
		if (Yii::$app->user->isGuest)
			$this->redirect(array('site/login'));
			
		if (!(new User)->checkAccess(4.001))/* 38 */
			throw new \yii\web\HttpException(404, 'User permissions do not allow entry into this module.');	
			
			
		$this->layout = 'mycase'; //your layout name	
		
		return parent::beforeAction($action);
	}
	/**
	 * Show list of Case Custodiant in gridView
	 * */
    public function actionIndex($case_id)
    {
    	$case_id= Yii::$app->request->get('case_id',0);
    	/*tbl_client_case_custodians join remaining in search*/
    	$this->layout = "mycase";
    	
    	/*IRT 67,68,86,87,258*/
        /*IRT 96,398 */
        $filter_type=\app\models\User::getFilterType(['tbl_evidence_custodians.cust_fname','tbl_evidence_custodians.cust_lname','tbl_evidence_custodians.cust_email','tbl_evidence_custodians.title','tbl_evidence_custodians.dept', 'custodians_media', 'custodians_project', 'custodians_form'],['tbl_evidence_custodians']);
       // echo "<prE>",print_r($filter_type),"</pre>";die;				
        $config = ['custodians_media'=>['All'=>'All','Y'=>'Yes','N'=>'No'],'custodians_project'=>['All'=>'All','Y'=>'Yes','N'=>'No'],'custodians_form'=>['All'=>'All','Y'=>'Yes','N'=>'No']];
        $config_widget_options = [];		
        $filterWidgetOption=\app\models\User::getFilterWidgetOption($filter_type,Url::toRoute(['case-custodians/ajax-filter']).'&case_id='.$case_id,$config,$config_widget_options);
		//echo "<prE>",print_r($filterWidgetOption),"</pre>";die;				
        /*IRT 67,68,86,87,258*/
    	
    	$searchModel = new EvidenceCustodianSearch();
		$params['grid_id']='dynagrid-casecustodians';
		Yii::$app->request->queryParams +=$params;
    	$dataProvider = $searchModel->search(Yii::$app->request->queryParams,$case_id);
    	
    	$params = Yii::$app->request->queryParams;
    	$tital_evidence = array();
    	$tital_evidence['title'] = $params['EvidenceCustodianSearch']['title'];
    	$tital_evidence['dept'] = $params['EvidenceCustodianSearch']['dept'];
    	if($tital_evidence['title'] == 'blank')
			$tital_evidence['title'] = '';
		if($tital_evidence['dept'] == 'blank')
			$tital_evidence['dept'] = '';	
		
		$cust_form = ArrayHelper::map(FormBuilderSystem::find()->select(['sys_field_name'])->where(['sys_form'=>'custodian_form','grid_type'=>0])->orderBy('sort_order')->all(),'sys_field_name','sys_field_name');
		//echo "<pre>",print_r($cust_form);		die();
    	return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
			'case_id'=>$case_id,
			'custodian_filter'=>$custodian_filter,
			'tital_evidence' => $tital_evidence,
			'filter_type'=>$filter_type,
			'filterWidgetOption'=>$filterWidgetOption,
			'cust_form'=>$cust_form
    	]);
   }
   
   /**
    * Creates a new CaseCustodiant model.
    * If creation is successful, the browser will be redirected to the 'view' page.
    * @return mixed
    */
   public function actionCreate()
   {
   	$model = new EvidenceCustodians();
   	$client_case_id= Yii::$app->request->get('client_case_id',0);
   	$client_data =ClientCase::findOne($client_case_id);
   	if ($model->load(Yii::$app->request->post()) && $model->save()) {
   		$cust_id = Yii::$app->db->getLastInsertId();
   		$modelclientcasecust =new ClientCaseCustodians();
   		//$modelclientcasecust->client_id=$client_data->client_id;
   		$modelclientcasecust->client_case_id=$client_case_id;
   		$modelclientcasecust->cust_id= $cust_id;
   		$modelclientcasecust->save(false);
   		return 'OK';
   	} else {
		$evidences_cust_len = (new User)->getTableFieldLimit('tbl_evidence_custodians');   
   		return $this->renderAjax('create', [
   				'model' => $model,
   				'evidences_cust_len'=>$evidences_cust_len
   		]);
   	}
   }
   /**
    * Update a CaseCustodiant model.
    * If creation is successful, the browser will be redirected to the 'view' page.
    * @return mixed
    */
   public function actionUpdate($id)
   {
   	$model =$this->findModel($id);
   	if ($model->load(Yii::$app->request->post()) && $model->save()) {
   		return 'OK';
   	} else {
		$evidences_cust_len = (new User)->getTableFieldLimit('tbl_evidence_custodians');  
   		return $this->renderAjax('create', [
   				'model' => $model,
   				'evidences_cust_len'=>$evidences_cust_len
   		]);
   	}
   }
   /**
    * Check a CaseCustodiant is associated with form media or project or not.
    * @return mixed
    */
   public function actionCheckassociated($id){
   	$client_case_id= Yii::$app->request->get('client_case_id',0);
   	$is_associated=false;
   	$model =$this->findModel($id);
   	$is_form= $model->isForm($model,'status');
   	$is_project= $model->isProjects($client_case_id, $id , 'status');
   	$is_media= $model->isMedia($id,'status');
   	if($is_form==1 || $is_project==1 || $is_media==1){
   		$is_associated=true;
   	}
   	return $is_associated;
   }
   
   /**
    * Delete a CaseCustodiant modal.
    * @return mixed
    */
   public function actionDelete($id){
   	ClientCaseCustodians::deleteAll('cust_id IN ('.$id.')');
   	$model =$this->findModel($id);
   	$model->delete();
   	return ;
   }
   /**
    * Show Detail of Media and 
    **/
	public function actionGetdetails(){
		$id=Yii::$app->request->post('expandRowKey',0);
		$model =$this->findModel($id);
		$case_id=Yii::$app->request->get('case_id',0);
		$clientMediaNum = (new EvidenceCustodians)->getCountEvidenceNumIdByCid($id,$case_id);
		$taskintructs   = (new EvidenceCustodians)->getTotalCaseActiveTasks($case_id,$id);
		$cust_form = ArrayHelper::map(FormBuilderSystem::find()->select(['sys_field_name'])->where(['sys_form'=>'custodian_form','grid_type'=>1])->orderBy('sort_order')->all(),'sys_field_name','sys_field_name');
		return $this->renderPartial('getgriddetails', ['model'=>$model,'cust_form'=>$cust_form,'clientMediaNum' => $clientMediaNum, 'taskintructs' => $taskintructs,'case_id'=>$case_id]);
	}
	
	/**
	 * Filter GridView with Ajax
	 * */
	public function actionAjaxFilter(){
		$case_id=Yii::$app->request->get('case_id',0);
		$searchModel = new EvidenceCustodianSearch();
		$dataProvider = $searchModel->searchFilter(Yii::$app->request->queryParams,$case_id);
		$out['results']=array();
		foreach ($dataProvider as $key=>$val){
			$val1 = $val;
			$val2 = $val;
			if($val == ''){
				continue;
				//$val1 = '(not set)';
				//$val='(not set)';
				//$val2='(not set)';
			}							
			
			$out['results'][] = ['id' => $val1, 'text' => $val,'label' => $val2];
		}
		//echo "<pre>"; print_r($dataProvider); exit;
		return json_encode($out);
	}
	/**
	 * Show Custodian Interview Form Layout
	 * */
	public function actionInterviewForm($id){
		$custodian = $this->findModel($id);
		$case_id=Yii::$app->request->get('case_id',0);
		$getCustomFromId=FormBuilder::find()->joinWith('formCustodianValues formCustodianValues')->select('formref_id')->where(['form_type'=>3,'formCustodianValues.cust_id'=>$id])->one();                
		$form_list = ArrayHelper::map(EvidenceCustodiansForms::find()->where(['Publish'=>1,'remove'=>0])->orderBy('Form_name')->select(['Id','Form_name'])->all(),'Id','Form_name');
		if (Yii::$app->request->post()){

			//echo "<pre>",print_r($_POST),"</pre>";die;

			$form_id = Yii::$app->request->post('form_id');
			$post_data = Yii::$app->request->post();
			$formbuilder_data = new FormBuilder();
			$formbuilder_data->saveCustInterviewFrom($post_data,$id,$form_id);
			return 'OK';
		}
		return $this->renderAjax('custodian-interview-form', array('cust' => $custodian, 'case_id' => $caseId, 'form_list' => $form_list,'getCustomFromId'=>$getCustomFromId));
	}
	/**
	 * Delete Custodian Interview Form
	 * */
	public function actionDeleteinterviewForm($id){
		FormCustodianValues::deleteAll('cust_id='.$id);	
		return;
	}
	/**
	 * Get Custodian Interview Form By Custodian id 
	 * */
	public function actionGetinterviewform($id){
		$model = EvidenceCustodiansForms::findOne($id);
		$formbuilder_data = new FormBuilder();
		$formbuilder_data = $formbuilder_data->getFromData($id,3,'DESC','formbuilder',0,'system');
		return $this->renderAjax('getcustodianforms', [
				'model' => $model,
				'formbuilder_data'=>$formbuilder_data,
				'id'=>$id,
				'formtype' => 'custodianadd'
		]);
	}
	/**
	 * Load Custodian Interview Form By Custodian id with values
	 * */
	public function actionGetinterviewformwithvalue($id){
		$cust_id=Yii::$app->request->get('cust_id',0);
		$model = EvidenceCustodiansForms::findOne($id);
		$formbuilder_data = new FormBuilder();
		$formbuilder_data = $formbuilder_data->getFromData($id,3,'DESC','formvalues',$cust_id,'front');
        //echo "<pre>",print_r($formbuilder_data),"</pre>";die;
		//$formValues=ArrayHelper::map(FormCustodianValues::find()->select(['form_builder_id','element_value'])->where(['cust_id'=>$cust_id])->all(),'form_builder_id','element_value');
		
		$formcustval = FormCustodianValues::find()->select(['form_builder_id','element_value','element_unit'])->where(['cust_id'=>$cust_id])->all();
		$formValues = array();
		$unitValues = array();
		if(!empty($formcustval)){
			foreach($formcustval as $custval){
				$formValues[$custval['form_builder_id']] = $custval['element_value'];
				$unitValues[$custval['form_builder_id']] = $custval['element_unit'];
			}
		}
		
		return $this->renderAjax('getcustodianforms', [
				'model' => $model,
				'formValues'=>$formValues,
				'unitValues' => $unitValues,
				'formbuilder_data'=>$formbuilder_data,
				'id'=>$id,
				'cust_id'=>$cust_id,
				'formtype' => 'custodianedit'
		]);
	}
	/**
	 * Finds the CaseCustodiant model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 * @param integer $id
	 * @return CaseCloseType the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id){
		if (($model = EvidenceCustodians::findOne($id)) !== null) {
			return $model;
		} else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}
}
