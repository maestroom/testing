<?php
namespace app\controllers;
use Yii;
use app\models\Mydocument;
//use app\models\search\EvidenceProductionSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
//use yii\helpers\ArrayHelper;
use app\models\ClientCase;
use app\models\User;
use app\models\MydocumentsBlob;
use yii\data\ActiveDataProvider;



/**
 * CaseDocumentController implements the CRUD actions for MyDocument model.
 */
class CaseDocumentsController extends Controller
{
    /**
     * @inheritdoc
     */
     
    public function beforeAction($action)
	{
		if (Yii::$app->user->isGuest)
			$this->redirect(array('site/login'));
			
		if (!(new User)->checkAccess(4.10) && $action->id == 'index')/* 38 */
			throw new \yii\web\HttpException(404, 'User permissions do not allow entry into this module.');	
			
		
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
     * Lists all MyDocument models.
     * @return mixed
     */
    public function actionIndex($case_id)
    {
        $node_id= Yii::$app->request->get('node_id');
        $this->layout = "mycase";
        $uid = Yii::$app->user->identity->id;
        $caseInfo = ClientCase::findOne($case_id);
        $data = (new Mydocument)->fecthDataRec($case_id,"Case",0,0);
        //echo "<pre>"; print_r($data);die;
		$mydocument_length = (new User)->getTableFieldLimit('tbl_mydocument'); 			
        return $this->render('index', [
            'caseInfo' => $caseInfo,
            'data' => $data,
            'case_id'=>$case_id,'node_id'=>$node_id,
            'mydocument_length' => $mydocument_length
            
        ]);
    }
     /**
     * Check file/folder type
     * @return mixed
     */
    public function actionChkfilefolder() {
        $node= Yii::$app->request->post('selected_node');
        $selected_data = Mydocument::findOne($node);           
        if($node =='root')
        	echo 'folder';
        else{
        if ($selected_data->type == 0)
            echo 'file';
        else
            echo 'folder';
        }
        die;
    }
    /**
     * 
     * @abstract This action is define to create new Folder for My Document model
     * @access Public
     * @since 1.0.0
     */
    public function actionCreatefolder() {
        $params= Yii::$app->request->post();
        $model = new Mydocument(); // Load MyDocument Model
        $case_id = $params['case_id'];
        $folder_name = $params['name'];
        if(is_array($params['selected_node'])){
        	$selected_node=$params['selected_node'][0];
        }else{
        	$selected_node=$params['selected_node'];
        }
        if ($selected_node == 'root') {
                    //$model->scenario = 'addDoc';
                    $model->reference_id = $case_id;
                    $model->origination = "Case";
                    $model->u_id = Yii::$app->user->identity->id;
                    $model->fname = $folder_name;
                    $model->p_id = 0;
                    $model->type =1; // 1=folder, 0=document 
                    $model->is_private = 0;
                    //"folder";
                    //$model->path='';
                    $model->save(false);
                    echo $document_id = Yii::$app->db->getLastInsertId();
                    exit();
        } else {
            $selected_data = Mydocument::findOne($selected_node);
            $private = $selected_data->is_private;
           // $model->scenario = 'addDoc';
            $model->reference_id = $case_id;
            $model->origination = "Case";
            $model->u_id = Yii::$app->user->identity->id;
            $model->fname = $folder_name;
            if(isset($private) && $private!=0 && $private!=""){
            	$model->is_private = $private;
            }else{
            	$model->is_private = 0;
            }
            $model->p_id = $selected_node;
            $model->type =1; // 1=folder, 0=document
            $model->save(false);
            echo $document_id = Yii::$app->db->getLastInsertId();
        }
        die;
    }
    /**
     * 
     * @abstract This action is define to rename Folder for My Document model
     * @access Public
     * @since 1.0.0
     */
    
    public function actionRenamefolder() {
        $params= Yii::$app->request->post();
        $caseId = $params['case_id'];
        $folder_name = $params['name'];
        $selected_data = Mydocument::findOne($params['selected_node']);
        $selected_data->fname = $folder_name;
        $selected_data->save(false);
        exit();
    }
    /**
     * 
     * @abstract This action is define to delete Folder and old its contains subfolder and files for My Document model
     * @access Public
     * @since 1.0.0
     */
     public function actionDeletefolder() {
        $params= Yii::$app->request->post();
        $case_id = $params['case_id'];
        $folder_name = $params['name'];
        $model = Mydocument::findOne($params['selected_node']);
        $name = $model->fname;
        if($model->type == 0)
            $type='file';
        else
            $type='folder';
        $data = $model->fecthDataRec($case_id, "Case", $model->id,"");
        (new Mydocument)->removeAttachments($model->id);
        if (!empty($data['mydoc'])) {
            foreach ($data['mydoc'] as $ids) {
                (new Mydocument)->removeAttachments($ids);
            }
        }
        echo $type.', "'.$name.'"';
        //echo $type.",'".$name."'";
        exit;
    }
     /**
     * @abstract This action is define to upload Files for My Document model
     * @access Public
     * @since 1.0.0
     */
    public function actionUploadfiles($case_id) {
        $params= Yii::$app->request->post();
         if(is_array($params['selected_node'])){
        	$selected_node=$params['selected_node'][0];
        }else{
        	$selected_node=$params['selected_node'];
        }
        if($selected_node=='root'){$selected_node=0;}
        $selected_data = Mydocument::findOne($selected_node);
        $private =0;
        if(!empty($selected_data))
            $private = $selected_data->is_private;
        /* Code for Case attachment start */
            if(!empty($_FILES['Case']['name']['upload_files'][0]))
            {
                $docmodel = new Mydocument();
                $doc_arr['p_id']=$params['selected_node'];
                $doc_arr['reference_id']=$case_id;
                $doc_arr['team_loc']=0;
                $doc_arr['origination']='Case';
                $doc_arr['is_private']=$private;
                $doc_arr['type']=0;
//                echo "<pre>";print_r($doc_arr);
                $file_arr=$docmodel->Savemydocs('Case','upload_files',$doc_arr);
                $files_str = json_encode($file_arr);
            }   
        /* Code for Case attachment end */
      return $this->redirect(['index','case_id'=>$case_id,'node_id'=>$params['selected_node']]);
        exit();    
    }
    
    public function actionPastefolder() {
        $params= Yii::$app->request->post();
        
        
        $case_id = $params['case_id'];
        //$caseInfo = ClientCase::model()->findByPk((int) $caseId);
        //$client_caseName = strtoupper($caseInfo->client->client_name . ' - ' . $caseInfo->case_name);
        //$selected_data = Mydocument::model()->findByPk($params['selected_node']);
        if ($params['type'] == 'copy') {
            $copy_selected_data = Mydocument::findOne($params['copy_selected']);
            $source = ""; //$selected_data->path;
            $name = $copy_selected_data->fname;
            //$destination = Yii::app()->basePath . '/../mydocuments/' . $client_caseName . '/' . $name;
            $alldata_with_childs = (new Mydocument)->fecthDataRec($case_id, "Case", $params['copy_selected'],'');
            //echo "<pre>"; print_r($alldata_with_childs);die;
            /* $oldmask = @umask(0);
              @mkdir($destination,0777);
              @umask($oldmask); */
            $copy_MyDocumentBlobmodel = MydocumentsBlob::find()->where(['id' => $copy_selected_data->doc_id])->one();
            //echo "<pre>";print_r($copy_selected_data);die;
            $blob_doc_id =0;
            if (isset($copy_MyDocumentBlobmodel->id)) {
                $MydocumentsBlob_model = new MydocumentsBlob();
                $MydocumentsBlob_model->doc = $copy_MyDocumentBlobmodel->doc;
                $MydocumentsBlob_model->save(false);
                $blob_doc_id = $MydocumentsBlob_model->getPrimaryKey();
            }
             if(is_array($params['selected_node'])){
				$selected_node=$params['selected_node'][0];
			}else{
				$selected_node=$params['selected_node'];
			}
			if($selected_node=='root'){$selected_node=0;}
            $model = new Mydocument();
            $model->p_id = $selected_node;
            $model->reference_id = $case_id;
            $model->fname = $copy_selected_data->fname;
            $model->origination = "Case";
            $model->u_id = Yii::$app->user->identity->id;
            $model->is_private = $copy_selected_data->is_private;
            $model->type = $copy_selected_data->type;
            $model->doc_id = $blob_doc_id;
            $model->doc_size = $copy_selected_data->doc_size;
            $model->doc_type = $copy_selected_data->doc_type;
            $model->save(false);
            $p_id = Yii::$app->db->getLastInsertId();
            
            $newly_added_childs = array();
            $newly_added_childs[$_REQUEST['copy_selected']] = $p_id;
            foreach ($alldata_with_childs['mydoc'] as $data) {
                $blob_doc_id = 0;
                $copy_MyDocumentBlobmodel = MydocumentsBlob::find()->where(['id' => $data])->one();
                if (isset($copy_MyDocumentBlobmodel->id)) {
                    $MydocumentsBlob_model = new MydocumentsBlob();
                    $MydocumentsBlob_model->doc = $copy_MyDocumentBlobmodel->doc;
                    $MydocumentsBlob_model->save(false);
                    $blob_doc_id = $MydocumentsBlob_model->getPrimaryKey();
                }

                $doc_data = Mydocument::findOne($data);
                $model = new Mydocument();
                $model->p_id = $doc_data->p_id;
                $model->reference_id = $case_id;
                $model->fname = $doc_data->fname;
                $model->origination = "Case";
                $model->u_id = Yii::$app->user->identity->id;
                $model->is_private = $doc_data->is_private;
                $model->type = $doc_data->type;
                $model->doc_id = $blob_doc_id;
                $model->doc_size = $doc_data->doc_size;
                $model->doc_type = $doc_data->doc_type;
                $model->save(false);
                $newly_added_childs[$data] = $model->getPrimaryKey();
            }
           // echo "<pre>"; print_r($newly_added_childs);die;
            $i = 0;
            foreach ($newly_added_childs as $key => $newlyid) { 
                if ($i > 0) {
                    $copiedmodel = Mydocument::findOne($key);
                    $newlymodel = Mydocument::findOne($newlyid);
                    $newlymodel->p_id = $newly_added_childs[$copiedmodel->p_id];
                    $newlymodel->save(false);
                }
                $i++;
            }
        } else if ($params['type'] == 'cut') {
			if(is_array($params['selected_node'])) {
				$selected_node=$params['selected_node'][0];
			}else{
				$selected_node=$params['selected_node'];
			}
			if($selected_node=='root'){$selected_node=0;}
			
            $selected_data = Mydocument::findOne($selected_node);
            //echo "<pre>";print_r($params);die;
            $cut_selected_data = Mydocument::findOne($params['cut_selected']);
            if(isset($selected_data->id) && $selected_data->id > 0){
            	$cut_selected_data->p_id = $selected_data->id;
            }else{
            	$cut_selected_data->p_id = 0;
            }
            $cut_selected_data->save();
        }
        die;
    }
    public function actionGetpermission() {
        $params= Yii::$app->request->get();
        $model = Mydocument::findOne($params['selected_node']);
        return $this->renderPartial('getpermission', ['id' => $params['selected_node'], 'data' => $model]);
    }
    public function actionChkusertochangepermission() {
        $uid = Yii::$app->user->identity->id;
        $params= Yii::$app->request->post();
        $model = Mydocument::findOne($params['selected_node']);
        $created_by = $model->created_by;
        if ($uid == $created_by)
            echo "Done";
        else
            echo "Denied";
        exit();
    }
    public function actionChangepermission() {
        $params= Yii::$app->request->post();
        $model = Mydocument::findOne($params['selected_node']);
        $mode_dec = $params['per'];
        //echo $params;die;
        $model->is_private = $mode_dec;
        //echo "<pre>";print_r($params['per']);die;
        $model->save(false);
        $fname=$model->fname;
        if($model->type == 0)
            $type='file';
        else
            $type='folder';
        $my_docmodel = new Mydocument();
        $getchild_node = $my_docmodel->fecthDataRec($params['case_id'], "Case", $params['selected_node'],"");
        if (!empty($getchild_node['mydoc'])) {
            foreach ($getchild_node['mydoc'] as $ch_id) {
                $model = Mydocument::findOne($ch_id);
                $mode_dec = $params['per'];
                $model->is_private = $mode_dec;
                $model->save(false);
            }
        }
        echo $type.', "'.$fname.'"';
        //echo $type.",'".$fname."'";
        die;
    }
    public function actionProjectdoc($case_id) {
        $this->layout = "mycase";
        $uid = Yii::$app->user->identity->id;
        $params = Yii::$app->request->get();
        $query = Mydocument::find();
        $type="instruct";
        if($params['type'] != '' && isset($params['type']))
        {
            if($params['type'] == 'I')
            {
                $type='instruct';
                $query->andwhere(['tbl_tasks.client_case_id'=> $case_id])->joinWith(['taskInstructServicetask'=>function (\yii\db\ActiveQuery $query) { $query->joinWith(['tasks']);},]);
            }
            if($params['type'] == 'IN')
            {
                $type='instruct N';
                $query->andwhere(['tbl_tasks.client_case_id'=> $case_id])->joinWith(['taskInstructNotes'=>function (\yii\db\ActiveQuery $query) { $query->joinWith(['tasks']);},]);
            }
            if($params['type'] == 'T')
            {
                $type='Todo';
	                $query->andwhere(['tbl_task_instruct.client_case_id'=> $case_id])->joinWith(['tasksUnitsTodos'=>function (\yii\db\ActiveQuery $query) { $query->joinWith(['taskUnit'=>function(\yii\db\ActiveQuery $query){
							$query->joinWith('taskInstruct');
						}]);	
               		},	
                ]);
            }
            
            if($params['type'] == 'C')
            {
                $type='Comment';
                $query->andwhere(['tbl_tasks.client_case_id'=> $case_id])->joinWith(['comments'=>function (\yii\db\ActiveQuery $query) { $query->joinWith(['tasks']);},]);
            }
            
            if($params['type'] == 'TD')
            {
            	$type = 'Data Statistics';	
            	$query->andwhere(['tbl_tasks.client_case_id'=> $case_id])->joinWith(['tasksUnits'=>function (\yii\db\ActiveQuery $query) { 
            		$query->joinWith(['tasks']);
            	},]);
            }
        }
        $query->andwhere(['origination'=>$type]);
        /* $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' =>['pageSize'=>25],
        ]); */
        $dataProvider = $query->all();
	//	echo "<pre>"; print_r($dataProvider); exit;
        $caseInfo = ClientCase::findOne($case_id);
        return $this->render('project-documents', ['model' => $model, 'data' => $data, 'dataProvider' => $dataProvider, 'case_id'=>$case_id, 'type'=>$params['type']]);
    }
    public function actionProjectdocsearch($case_id) {
        $this->layout = "mycase";
        $params = Yii::$app->request->post();
       // print_r($params);die;
        $uid = Yii::$app->user->identity->id;
        $type=$params['type'];
        $query = Mydocument::find();
            if($params['type'] == 'I')
            {
                $type='instruct';
                $query->andwhere(['tbl_tasks.client_case_id'=> $case_id])->joinWith(['taskInstructServicetask'=>function (\yii\db\ActiveQuery $query) { $query->joinWith(['tasks']);},]);
            }
            if($params['type'] == 'IN')
            {
                $type='instruct N';
                $query->andwhere(['tbl_tasks.client_case_id'=> $case_id])->joinWith(['taskInstructNotes'=>function (\yii\db\ActiveQuery $query) { $query->joinWith(['tasks']);},]);
            }
            if($params['type'] == 'T')
            {
                $type='Todo';
                $query->andwhere(['tbl_task_instruct.client_case_id'=> $case_id])->joinWith(['tasksUnitsTodos'=>function (\yii\db\ActiveQuery $query) { $query->joinWith(['taskUnit'=>function(\yii\db\ActiveQuery $query){
					$query->joinWith('taskInstruct');
				}]);},]);
            }
            if($params['type'] == 'C')
            {
                $type='Comment';
                $query->andwhere(['tbl_tasks.client_case_id'=> $case_id])->joinWith(['comments'=>function (\yii\db\ActiveQuery $query) { $query->joinWith(['tasks']);},]);
            }
            
            if($params['type'] == 'TD')
            {
            	$type = 'Data Statistics';
            	$query->andwhere(['tbl_tasks.client_case_id'=> $case_id])->joinWith(['tasksUnits'=>function (\yii\db\ActiveQuery $query) {
            		$query->joinWith(['tasks']);
            	},]);
            }
        $query->andwhere(['origination'=>$type]);    
        if($params['term'] != '')
            $query->andwhere("fname LIKE '%" . $params['term'] . "%'");
        
        /*$dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' =>['pageSize'=>25],
        ]);*/
        $dataProvider = $query->all();
        //echo "<pre>"; print_r($dataProvider); exit;
         $caseInfo = ClientCase::findOne($case_id);
        return $this->renderAjax('project-docsearch', ['model' => $model, 'data' => $data, 'dataProvider' => $dataProvider,'case_id'=>$case_id,'type'=>$params['type'],'term'=>$params['term']]);
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