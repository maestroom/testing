<?php

namespace app\controllers;

use Yii;
use app\models\EvidenceStoredLoc;
use app\models\search\EvidenceStoredLocSearch;
use app\models\User;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;

/**
 * EvidenceStoredLocController implements the CRUD actions for EvidenceStoredLoc model.
 */
class EvidenceStoredLocController extends Controller
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
     * Lists all EvidenceStoredLoc models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new EvidenceStoredLocSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		/*IRT 67,68,86,87,258*/
		/*IRT 96,398 */
		$filter_type=\app\models\User::getFilterType(['tbl_evidence_stored_loc.id','tbl_evidence_stored_loc.stored_loc'],['tbl_evidence_stored_loc']);
		$config = [];       
		$config_widget_options = [];		
		$filterWidgetOption=\app\models\User::getFilterWidgetOption($filter_type,Url::toRoute(['evidence-stored-loc/ajax-filter']),$config,$config_widget_options);
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
		$searchModel = new EvidenceStoredLocSearch();
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
     * Displays a single EvidenceStoredLoc model.
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
     * Creates a new EvidenceStoredLoc model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new EvidenceStoredLoc();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return 'OK';//$this->redirect(['view', 'id' => $model->id]);
        } else {
			$esl_length = (new User)->getTableFieldLimit('tbl_evidence_stored_loc'); 	
            return $this->renderAjax('create', [
                'model' => $model,
                'esl_length'=>$esl_length
            ]);
        }
    }

    /**
     * Updates an existing EvidenceStoredLoc model.
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
			$esl_length = (new User)->getTableFieldLimit('tbl_evidence_stored_loc'); 	
            return $this->renderAjax('update', [
                'model' => $model,
				'esl_length'=>$esl_length
            ]);
        }
    }

    /**
     * Deletes an existing EvidenceStoredLoc model.
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
    		EvidenceStoredLoc::updateAll(['remove' => 1], 'id IN ('.$keys.') AND remove = 0' );
    		return 'OK';
    	}
    }

    /**
     * Finds the EvidenceStoredLoc model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EvidenceStoredLoc the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EvidenceStoredLoc::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
