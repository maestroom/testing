<?php

namespace app\controllers;

use Yii;
use app\models\EvidenceEncryptType;
use app\models\search\EvidenceEncryptTypeSearch;
use app\models\User;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;

/**
 * EvidenceEncryptTypeController implements the CRUD actions for EvidenceEncryptType model.
 */
class EvidenceEncryptTypeController extends Controller
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
     * Lists all EvidenceEncryptType models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new EvidenceEncryptTypeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		/*IRT 67,68,86,87,258*/
		/*IRT 96,398 */
		$filter_type=\app\models\User::getFilterType(['tbl_evidence_encrypt_type.id','tbl_evidence_encrypt_type.encrypt'],['tbl_evidence_encrypt_type']);
		$config = [];       
		$config_widget_options = [];		
		$filterWidgetOption=\app\models\User::getFilterWidgetOption($filter_type,Url::toRoute(['evidence-encrypt-type/ajax-filter']),$config,$config_widget_options);
		/*IRT 67,68,86,87,258*/
        return $this->renderAjax('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'filter_type'=>$filter_type,
			'filterWidgetOption'=>$filterWidgetOption
        ]);
    }
    
	/**
	 * Filter GridView with Ajax
	 * */
	public function actionAjaxFilter(){
		$searchModel = new EvidenceEncryptTypeSearch();
		$params = Yii::$app->request->queryParams;				
		$dataProvider = $searchModel->searchFilter($params);
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
		//print_r($out);die;
		return json_encode($out);
	}
    /**
     * Displays a single EvidenceEncryptType model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new EvidenceEncryptType model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new EvidenceEncryptType();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return 'OK';//$this->redirect(['view', 'id' => $model->id]);
        } else {
			$eet_length = (new User)->getTableFieldLimit('tbl_evidence_encrypt_type'); 
            return $this->renderAjax('create', [
                'model' => $model,
                'eet_length' =>$eet_length
            ]);
        }
    }

    /**
     * Updates an existing EvidenceEncryptType model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return 'OK';//$this->redirect(['view', 'id' => $model->id]);
        } else {
			$eet_length = (new User)->getTableFieldLimit('tbl_evidence_encrypt_type'); 
            return $this->renderAjax('update', [
                'model' => $model,
                'eet_length' =>$eet_length
            ]);
        }
    }

    /**
     * Deletes an existing EvidenceEncryptType model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
	public function actionDelete($id){
     	$model = $this->findModel($id);
     	$model->remove = 1;
     	if ($model->save()) {
     		return 'OK';
     	}
     	exit;
    }
    /**
     * Deletes an selected existing CaseCloseType model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    
    public function actionDeleteselected() {
    	if (isset($_POST['keylist'])) {
    		$keys = implode(",",$_POST['keylist']);
    		EvidenceEncryptType::updateAll(['remove' => 1], 'id IN ('.$keys.') AND remove = 0' );
    		return 'OK';
    	}
    }
    /**
     * Finds the EvidenceEncryptType model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EvidenceEncryptType the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EvidenceEncryptType::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
