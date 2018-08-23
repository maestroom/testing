<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use app\models\Options;
use yii\data\ActiveDataProvider;
use app\models\EvidenceTransaction;

/**
 * EvidenceSearch represents the model behind the search form about `app\models\Evidence`.
 */
class EvidenceTransactionSearch extends EvidenceTransaction
{
	public $fullName;
	
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
			 //[['trans_type'], 'integer'],	
			 [['trans_type','trans_date','trans_by','trans_requested_by','moved_to','Trans_to','trans_reason'],'safe']
			  //[['other_evid_num', 'received_date', 'received_time', 'received_from', 'evd_Internal_no', 'serial', 'model', 'hash', 'cont', 'evid_desc', 'evid_label_desc', 'contents_copied_to', 'mpw', 'bbates', 'ebates', 'm_vol', 'ftpun', 'ftppw', 'encpw', 'evid_notes', 'barcode', 'created', 'modified'], 'safe'],	
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied 
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
    	$query = EvidenceTransaction::find()->where(['evid_num_id'=>$params['id']])->joinWith(['transby','transRequstedby','evidenceTo']);
		
		/*if($params['sort'] == 'trans_type') {
			$query->orderBy(['trans_type' => SORT_DESC]);
        } else if($params['sort'] == 'trans_by') {
            $query->orderBy(['trans_by' => SORT_DESC]);
        } else if($params['sort'] == 'moved_to') {
            $query->orderBy(['moved_to' => SORT_DESC]);
        } else if($params['sort'] == 'trans_to') {
            $query->orderBy(['trans_to' => SORT_DESC]);
        } else if($params['sort'] == 'trans_reason') {
            $query->orderBy(['trans_reason' => SORT_DESC]);
        } else if($params['sort'] == 'trans_date') {
			$query->orderBy(['trans_date' => SORT_DESC]);
		}*/
		
		/*multiple*/
		if ($params['EvidenceTransactionSearch']['trans_type'] != null && is_array ($params['EvidenceTransactionSearch']['trans_type'])) {
			if(!empty($params['EvidenceTransactionSearch']['trans_type'])){
				foreach($params['EvidenceTransactionSearch']['trans_type'] as $k=>$v){
					if($v=='All' || $v==''){ // || strpos($v,",") !== false 
						unset($params['EvidenceTransactionSearch']['trans_type']);break;
					}
				}
			}
		}
		if ($params['EvidenceTransactionSearch']['trans_by'] != null && is_array ($params['EvidenceTransactionSearch']['trans_by'])) {
			if(!empty($params['EvidenceTransactionSearch']['trans_by'])){
				foreach($params['EvidenceTransactionSearch']['trans_by'] as $k=>$v){
					if($v=='All' || $v==''){ // || strpos($v,",") !== false
						unset($params['EvidenceTransactionSearch']['trans_by']); break;
					}
				}
			}
		}
		if ($params['EvidenceTransactionSearch']['trans_requested_by'] != null && is_array ($params['EvidenceTransactionSearch']['trans_requested_by'])) {
			if(!empty($params['EvidenceTransactionSearch']['trans_requested_by'])){
				foreach($params['EvidenceTransactionSearch']['trans_requested_by'] as $k=>$v){
					if($v=='All' || $v==''){ // || strpos($v,",") !== false
						unset($params['EvidenceTransactionSearch']['trans_requested_by']);break;
					}
				}
			}
		}
		if ($params['EvidenceTransactionSearch']['moved_to'] != null && is_array ($params['EvidenceTransactionSearch']['moved_to'])) {
			if(!empty($params['EvidenceTransactionSearch']['moved_to'])){
				foreach($params['EvidenceTransactionSearch']['moved_to'] as $k=>$v){
					if($v=='All' || $v==''){ // || strpos($v,",") !== false
						unset($params['EvidenceTransactionSearch']['moved_to']);break;
					}
				}
			}
		}
		if ($params['EvidenceTransactionSearch']['Trans_to'] != null && is_array ($params['EvidenceTransactionSearch']['Trans_to'])) {
			if(!empty($params['EvidenceTransactionSearch']['Trans_to'])){
				foreach($params['EvidenceTransactionSearch']['Trans_to'] as $k=>$v){
					if($v=='All' || $v==''){ // || strpos($v,",") !== false
						unset($params['EvidenceTransactionSearch']['Trans_to']);break;
					}
				}
			}
		}
		/*multiple*/
		
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' =>['pageSize'=>25],
        //	'sort'=> ['defaultOrder' => ['id' => 'ASC']]
        ]);
        $dataProvider->sort->enableMultiSort=true;
		
		/*IRT-67*/
		$grid_id='dynagrid-transaction_'.Yii::$app->user->identity->id;
		$sql="SELECT * FROM tbl_dynagrid_dtl WHERE id IN (SELECT sort_id FROM tbl_dynagrid WHERE id='$grid_id')";
		$sort_data=Yii::$app->db->createCommand($sql)->queryOne();
		if(!empty($sort_data)){
				$dataProvider->sort->defaultOrder=json_decode($sort_data['data'],true);
		}
		/*IRT-67*/
        $dataProvider->sort->attributes['trans_requested_by'] = [
    		'asc' => ["CONCAT(trans_requested_user.usr_first_name,' ',trans_requested_user.usr_lastname)" => SORT_ASC],
    		'desc' => ["CONCAT(trans_requested_user.usr_first_name,' ',trans_requested_user.usr_lastname)" => SORT_DESC],
        ];

		$dataProvider->sort->attributes['trans_by'] = [
    		'asc' => ["CONCAT(trans_by_user.usr_first_name,' ',trans_by_user.usr_lastname)" => SORT_ASC],
    		'desc' => ["CONCAT(trans_by_user.usr_first_name,' ',trans_by_user.usr_lastname)" => SORT_DESC],
        ];
		$dataProvider->sort->attributes['Trans_to'] = [
			'asc' => ["tbl_evidence_to.to_name" => SORT_ASC],
    		'desc' => ["tbl_evidence_to.to_name" => SORT_DESC],
        ];
      
	    $trans_date="";
        if($params['EvidenceTransactionSearch']['trans_date'] != ''){
            $trans_date=explode("-",$params['EvidenceTransactionSearch']['trans_date']);
            $trans_date_start=explode("/",trim($trans_date[0]));
            $trans_date_end=explode("/",trim($trans_date[1]));
            $trans_date_s=$trans_date_start[2]."-".$trans_date_start[0]."-".$trans_date_start[1];
            $trans_date_e=$trans_date_end[2]."-".$trans_date_end[0]."-".$trans_date_end[1];
        }
        $UTCtimezoneOffset = (new Options)->getOffsetOfTimeZone();
        $timezoneOffset = (new Options)->getOffsetOfCurrenttimeZone();
        $query->andFilterWhere([
			'id' => $params['EvidenceTransactionSearch']['id'],
			'trans_type' => $params['EvidenceTransactionSearch']['trans_type'],
			'trans_requested_by' => $params['EvidenceTransactionSearch']['trans_requested_by'],
			'Trans_to' => $params['EvidenceTransactionSearch']['Trans_to'],
			'moved_to' => $params['EvidenceTransactionSearch']['moved_to'],
			'trans_by'=> $params['EvidenceTransactionSearch']['trans_by'],
		]);
		
        /** Default Order By last name Ascending **/         
//        if(!$params['sort']){
//        	$query->orderBy('trans_requested_user.usr_lastname','ASC');
//        }
        
        if (Yii::$app->db->driverName == 'mysql'){    
			$query->andFilterWhere(['between',"DATE_FORMAT(CONVERT_TZ(trans_date,'+00:00','{$timezoneOffset}'), '%Y-%m-%d')", $trans_date_s, $trans_date_e]);
		}else{
			$query->andFilterWhere(['between',"CAST(switchoffset(todatetimeoffset(Cast(CAST(trans_date as varchar) as datetime), '+00:00'), '{$timezoneOffset}') as date)", $trans_date_s, $trans_date_e]);
		}
		$query->andFilterWhere(['like', 'trans_reason', $params['EvidenceTransactionSearch']['trans_reason']]); 
		//$query->andFilterWhere(['or like', "CONCAT(tbl_user.usr_first_name,' ',tbl_user.usr_lastnam)" , $params['EvidenceTransactionSearch']['trans_by']]);
        /*$query->andFilterWhere(['OR',
			['like', 'tbl_user.usr_first_name', $params['EvidenceTransactionSearch']['trans_by']],
			['like', 'tbl_user.usr_lastname', $params['EvidenceTransactionSearch']['trans_by']]
        ]);*/
        
        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }
//        echo '<pre>';print_r($dataProvider->getModels());die;
        return $dataProvider;
    }
}
