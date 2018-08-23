<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use app\models\Evidence;
use app\models\EvidenceStoredLoc;
use app\models\EvidenceTransaction;
use app\models\ActivityLog;
use app\models\User;
use app\models\EvidenceTo;

/**
 * BarcodeController implements the CRUD actions for Evidence model.
 */
class BarcodeController extends Controller
{
    /**
     * @inheritdoc
     */
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
     * Get list of evidence from barcode number.
     * @return mixed
     */
    public function actionGetscannedmedia() {
		$params=Yii::$app->request->get();
        $media_data = array();
        $json_arr = array();
        $is_allow = true;
        if ((isset($params['barcode']) && $params['barcode'] != "") && (isset($params['scanned_media']) && $params['scanned_media'] != ""))
            $media_data = Evidence::find()->where(["barcode"=>$params['barcode']])->where(["not in",'id',explode(",",$params['scanned_media'])])->select(['id', 'barcode'])->all();
        if ((isset($params['barcode']) && $params['barcode'] != ""))
            $media_data = Evidence::find()->where(["barcode"=>$params['barcode']])->select(['id', 'barcode'])->all();

        if (!empty($media_data)) {
            foreach ($media_data as $m_data) {
                $json_arr[$m_data->id] = $m_data->barcode;
                $Evid_model = Evidence::findOne(['id' => $m_data->id]);
                $current_status = $Evid_model->status;
                if ($_REQUEST['trans_type'] == 1) {//want to make check in
                    if (!in_array($current_status, array(2, 5))) {//current status must be in check out , return
                        $is_allow = false;
                    }
                }
                if ($_REQUEST['trans_type'] == 2) {
                    if (!in_array($current_status, array(1))) {//current status must be in check out , return
                        $is_allow = false;
                    }
                }
                if ($_REQUEST['trans_type'] == 3) {//want to make Destroy
                    if (!in_array($current_status, array(1, 5))) {//current status must be in check in,return
                        $is_allow = false;
                    }
                }
                if ($_REQUEST['trans_type'] == 4) {//want to make move
                    if (!in_array($current_status, array(1))) {//current status must be in check in
                        $is_allow = false;
                    }
                }
            }
        }
        if ($is_allow)
            echo json_encode($json_arr);
        else
            echo "notallow";
        die;
    }

    /**
     * Get form of check out barcode Transaction with all required field list.
     * @return mixed
     */
    public function actionCheckOutInBarcode() {
        $model = new EvidenceTransaction();
        $listUser = ArrayHelper::map(User::find()->orderBy(['id'=>SORT_ASC])->select(["id", "CONCAT(usr_first_name,' ',usr_lastname) as FULLNAME"])->orderBy('usr_lastname','ASC')->asArray()->all(),'id', 'FULLNAME');
        $listEvidenceLoc = ArrayHelper::map(EvidenceStoredLoc::find()->orderBy(['stored_loc'=>SORT_ASC])->where(['remove' => '0'])->select(['id', 'stored_loc'])->asArray()->all(),'id', 'stored_loc');
        $listEvidenceTo = ArrayHelper::map(EvidenceTo::find()->orderBy(['to_name'=>SORT_ASC])->where(['remove' => '0'])->select(['id', 'to_name'])->asArray()->all(),'id', 'to_name');
        $evidences_tr = (new User)->getTableFieldLimit('tbl_evidence_transactions');    
        return $this->renderAjax('AddcheckOutInbarcode', ['model' => $model, 'evidNum' => $record, 'listUser' => $listUser, 'listEvidenceLoc' => $listEvidenceLoc, 'listEvidenceTo' => $listEvidenceTo,'evidences_tr'=>$evidences_tr]);
    }
     /**
     * check out barcode Transaction stauts allowed or not for selected evidence
     * @return mixed
     */
    public function actionCheckBulkBarcodechkinout() {
        $is_allow = true;
        $current_status = array();
        $msg = '';
        $params=Yii::$app->request->post();
        $tran_type=$params['tran_type'];
        $evid_ids = explode(',', $params['evid']);
        $evid_data = Evidence::find()->where(['in','id',$evid_ids])->all();
        foreach ($evid_data as $eids) {
                $current_status[$eids->id] = $eids->status;
        }
        if (!empty($current_status)) {
            foreach ($current_status as $sta) {
                if ($tran_type == 1) {//want to make check in
                    if (!in_array($sta, array(2, 5))) {//current status must be in check out , return 
                        $is_allow = false;
                        $msg = "Media can only be 'checked in' if it is already 'checked out' or 'returned'";
                    }
                }
                if ($tran_type == 2) {
                    if (!in_array($sta, array(1))) {//current status must be in check out , return
                        $is_allow = false;
                        $msg = "Media that already has a 'checked out' status can only be allowed to be 'checked in'";
                    }
                }
                if ($tran_type == 3) {//want to make Destroy
                    if (!in_array($sta, array(1, 5))) {//current status must be in check in,return 
                        $is_allow = false;
                        $msg = "Media can be 'destroyed' if it is already 'checked in' or 'returned'";
                    }
                }
                if ($tran_type == 4) {//want to make move
                    if (!in_array($sta, array(1))) {//current status must be in check in
                        $is_allow = false;
                        $msg = "Media can only be 'moved' if it is already 'checked in'";
                    }
                }
            }
        } else {
            $is_allow = false;
            $msg = "NOTALLOW";
        }
        if ($is_allow)
            echo "allow";
        else
            echo $msg;
        die;
    }
     /**
     * Change status of selected evidence
     * @return mixed
     */
    public function actionBulkBarcodeChkinout() {
		$params=Yii::$app->request->post();
        $evid_ids = explode(',', $params['evid']);
        foreach ($evid_ids as $eid) {
			if ($params['is_duplicate'] == 1 || $params['is_duplicate'] == 'on') {
				$Evid_model2 = Evidence::findOne($eid);
				if ($Evid_model2->dup_evid != 0) {
					$evid_num = $Evid_model2->org_link;
					array_push($evid_ids, $evid_num);
				}
				unset($Evid_model2);
			}
        }
        $evid_ids = array_unique($evid_ids);
        $trans_type = $params['trans_type'];
        $trans_requested_by = $params['trans_requested_by'];
        $trans_reason = $params['trans_reason'];
        $moved_to = $params['moved_to'];
        $trans_to = $params['Trans_to'];
        foreach ($evid_ids as $eids) {
            $model = new Evidence();
            $model = Evidence::findOne($eids);
           // $model->id = $eids;
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
            if (!empty($model)) {
                $model->save(false);
                $ETmodel->save(false);
            }
			$activityLog = new ActivityLog();
			$activityLog->generateLog('Media','Updated',$eid, $eid);
            
        }
        exit;
    }
}
