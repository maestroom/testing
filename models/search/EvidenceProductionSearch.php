<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\EvidenceProduction;
use app\models\Options;
use yii\helpers\ArrayHelper;
/**
 * EvidenceProductionSearch represents the model behind the search form about `app\models\EvidenceProduction`.
 */
class EvidenceProductionSearch extends EvidenceProduction
{

    public $has_attachment;
    /**
     * @inheritdoc
     */
    public function rules()
    {

        return [
            [['id',  'client_case_id', 'production_type', 'prod_orig', 'prod_return', 'has_media', 'has_hold', 'has_projects', 'created_by', 'modified_by'], 'integer'],
            [['staff_assigned', 'prod_date', 'prod_rec_date', 'prod_party', 'production_desc', 'cover_let_link', 'attorney_notes', 'prod_disclose', 'prod_agencies', 'prod_access_req', 'prod_misc1', 'prod_misc2', 'created', 'modified'], 'safe'],
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
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        //cover_let_link
        //print_r($params);// die;
        $timezoneOffset = (new Options)->getOffsetOfCurrenttimeZone();
        $query = EvidenceProduction::find();
        //->joinWith('productionattachments');
        if (Yii::$app->db->driverName == 'mysql') {
			$prod_date_query = "DATE_FORMAT(CONVERT_TZ(tbl_evidence_production.`prod_date`,'+00:00','+00:00'), '%Y-%m-%d')";
			$prod_rec_date_query = "DATE_FORMAT(CONVERT_TZ(tbl_evidence_production.prod_rec_date,'+00:00','+00:00'), '%Y-%m-%d')";
			$prod_agencies_query = "DATE_FORMAT(CONVERT_TZ(tbl_evidence_production.`prod_agencies`,'+00:00','+00:00'), '%Y-%m-%d')";
			$prod_access_req_query = "DATE_FORMAT(CONVERT_TZ(tbl_evidence_production.`prod_access_req`,'+00:00','+00:00'), '%Y-%m-%d')";
		}else{
			$prod_date_query = "CAST(switchoffset(todatetimeoffset(prod_date, '+00:00'), '+00:00') as date)";
			$prod_rec_date_query = "CAST(switchoffset(todatetimeoffset(prod_rec_date, '+00:00'), '+00:00') as date)";
			$prod_agencies_query = "CAST(switchoffset(todatetimeoffset(prod_agencies, '+00:00'), '+00:00') as date)";
			$prod_access_req_query = "CAST(switchoffset(todatetimeoffset(prod_access_req, '+00:00'), '+00:00') as date)";
		}


        /**  (IRT 270)  * */
        if (Yii::$app->db->driverName == 'sqlsrv') {
            /* Get prod date with '0000-00-00'  format */
            $query->select(['id', 'client_case_id', 'staff_assigned', '('.$prod_rec_date_query.') as prod_rec_date', 'prod_party', 'production_desc', 'prod_copied_to', 'production_type', 'cover_let_link', 'prod_orig', 'prod_return', 'attorney_notes', 'prod_disclose', '('.$prod_agencies_query.') as prod_agencies', '('.$prod_access_req_query.') as prod_access_req', 'has_media', 'has_hold', 'has_projects', 'prod_misc1', 'prod_misc2', 'created', 'created_by', 'modified', 'modified_by', "(".$prod_date_query.")  as prod_date","(SELECT count(*) FROM tbl_mydocument WHERE (reference_id=tbl_evidence_production.id) AND (origination='Production')) as has_attachment"]);
        }else{
            $query->select(['id', 'client_case_id', 'staff_assigned', '('.$prod_rec_date_query.') as prod_rec_date', 'prod_party', 'production_desc', 'prod_copied_to', 'production_type', 'cover_let_link', 'prod_orig', 'prod_return', 'attorney_notes', 'prod_disclose', '('.$prod_agencies_query.') as prod_agencies', '('.$prod_access_req_query.') as prod_access_req', 'has_media', 'has_hold', 'has_projects', 'prod_misc1', 'prod_misc2', 'created', 'created_by', 'modified', 'modified_by', "(".$prod_date_query.")  as prod_date","(SELECT count(*) FROM tbl_mydocument WHERE (reference_id=tbl_evidence_production.id) AND (origination='Production')) as has_attachment"]);
        }


		//if(!isset($params['sort'])){
        	//$query->orderBy(['tbl_evidence_production.id' => SORT_DESC]);
		//}
	$defaultSort="";
    	if(!isset($params['sort'])) {
            $defaultSort=['id'=>SORT_DESC];
            //$query->orderBy('tbl_evidence_custodians.cust_id');
    	}
	$case_id=$params['case_id'];
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' =>['pageSize'=>25],
        ]);


	$dataProvider->sort->enableMultiSort=true;
        /*IRT-67*/
		if(isset($params['grid_id']) && $params['grid_id']!=""){
			$grid_id=$params['grid_id'].'_'.Yii::$app->user->identity->id;
			$sql="SELECT * FROM tbl_dynagrid_dtl WHERE id IN (SELECT sort_id FROM tbl_dynagrid WHERE id='$grid_id')";
			$sort_data=Yii::$app->db->createCommand($sql)->queryOne();
			if(!empty($sort_data)){
					$defaultSort=json_decode($sort_data['data'],true);
			}
		}
		/*IRT-67*/
        if(!isset($params['sort']) && $defaultSort!=""){
			$dataProvider->sort->defaultOrder=$defaultSort;
		}

		$query->andFilterWhere(['client_case_id' => $case_id]);
		if(isset($params['prod_id'])){
            if(!isset($params['EvidenceProductionSearch']['id']))
                $params['EvidenceProductionSearch']['id'] =$params['prod_id'];
        }
		if ($params['EvidenceProductionSearch']['id'] != null && is_array($params['EvidenceProductionSearch']['id'])) {
			if(!empty($params['EvidenceProductionSearch']['id'])){
				foreach($params['EvidenceProductionSearch']['id'] as $k=>$v){
					if($v=='All') { // || strpos($v,",") !== false){
						unset($params['EvidenceProductionSearch']['id']);break;
						}
					}
			}
			$query->andFilterWhere(['in','tbl_evidence_production.id',$params['EvidenceProductionSearch']['id']]);
		} else {
            if ($params['EvidenceProductionSearch']['id'] != null) {
                $query->andFilterWhere(['tbl_evidence_production.id'=>$params['EvidenceProductionSearch']['id']]);
            }
        }

		if ($params['EvidenceProductionSearch']['production_type'] != null && is_array($params['EvidenceProductionSearch']['production_type'])) {
			if(!empty($params['EvidenceProductionSearch']['production_type'])) {
				foreach($params['EvidenceProductionSearch']['production_type'] as $k=>$v) {
					if($v=='All') { // || strpos($v,",") !== false){
						unset($params['EvidenceProductionSearch']['production_type']); break;
					}
				}
			}
			$query->andFilterWhere(['in','tbl_evidence_production.production_type',$params['EvidenceProductionSearch']['production_type']]);
		}

		if ($params['EvidenceProductionSearch']['has_media'] != null && is_array($params['EvidenceProductionSearch']['has_media'])) {
			if(!empty($params['EvidenceProductionSearch']['has_media'])){
                            foreach($params['EvidenceProductionSearch']['has_media'] as $k=>$v){
                                if($v=='All') { // || strpos($v,",") !== false){
                                        unset($params['EvidenceProductionSearch']['has_media']);break;
                                }
                            }
			}
			$query->andFilterWhere(['in','tbl_evidence_production.has_media',$params['EvidenceProductionSearch']['has_media']]);
		}

		if ($params['EvidenceProductionSearch']['has_hold'] != null && is_array($params['EvidenceProductionSearch']['has_hold'])) {
			if(!empty($params['EvidenceProductionSearch']['has_hold'])) {
				foreach($params['EvidenceProductionSearch']['has_hold'] as $k=>$v) {
					if($v=='All') { // || strpos($v,",") !== false) {
						unset($params['EvidenceProductionSearch']['has_hold']); break;
					}
				}
			}
			$query->andFilterWhere(['in','tbl_evidence_production.has_hold',$params['EvidenceProductionSearch']['has_hold']]);
		}

		if ($params['EvidenceProductionSearch']['has_projects'] != null && is_array($params['EvidenceProductionSearch']['has_projects'])) {
                    if(!empty($params['EvidenceProductionSearch']['has_projects'])) {
                        foreach($params['EvidenceProductionSearch']['has_projects'] as $k=>$v){
                            if($v=='All') { // || strpos($v,",") !== false) {
                                unset($params['EvidenceProductionSearch']['has_projects']); break;
                            }
                        }
                    }
                    $query->andFilterWhere(['in', 'tbl_evidence_production.has_projects', $params['EvidenceProductionSearch']['has_projects']]);
		}

		if ($params['EvidenceProductionSearch']['staff_assigned'] != null && is_array($params['EvidenceProductionSearch']['staff_assigned'])) {
			if(!empty($params['EvidenceProductionSearch']['staff_assigned'])) {
				foreach($params['EvidenceProductionSearch']['staff_assigned'] as $k=>$v){
					if($v=='All') { // || strpos($v,",") !== false){
						unset($params['EvidenceProductionSearch']['staff_assigned']);break;
					}
				}
			}
			$query->andFilterWhere(['or like','tbl_evidence_production.staff_assigned',$params['EvidenceProductionSearch']['staff_assigned']]);
		}else{
            $query->andFilterWhere(['like','tbl_evidence_production.staff_assigned',$params['EvidenceProductionSearch']['staff_assigned']]);
        }

		if ($params['EvidenceProductionSearch']['prod_misc1'] != null && is_array($params['EvidenceProductionSearch']['prod_misc1'])) {
			$prod_party_data=$params['EvidenceProductionSearch']['prod_misc1'];
			if(!empty($params['EvidenceProductionSearch']['prod_misc1'])){
				foreach($params['EvidenceProductionSearch']['prod_misc1'] as $k=>$v){
					if($v=='All') { // || strpos($v,",") !== false){
						unset($params['EvidenceProductionSearch']['prod_misc1']);break;
					}else if($v=='(not set)'){
						$params['EvidenceProductionSearch']['prod_misc1'][$k]='';
					}
				}
			}
			$query->andFilterWhere(['or like','tbl_evidence_production.prod_misc1',$params['EvidenceProductionSearch']['prod_misc1']]);
			$params['EvidenceProductionSearch']['prod_misc1']=$prod_party_data;
		}

		if ($params['EvidenceProductionSearch']['prod_misc2'] != null && is_array($params['EvidenceProductionSearch']['prod_misc2'])) {
			$prod_party_data=$params['EvidenceProductionSearch']['prod_misc2'];
			if(!empty($params['EvidenceProductionSearch']['prod_misc2'])){
				foreach($params['EvidenceProductionSearch']['prod_misc2'] as $k=>$v){
					if($v=='All') { //} || strpos($v,",") !== false){
						unset($params['EvidenceProductionSearch']['prod_misc2']); break;
					}else if($v=='(not set)'){
						$params['EvidenceProductionSearch']['prod_misc2'][$k]='';
					}
				}
			}
			$query->andFilterWhere(['or like','tbl_evidence_production.prod_misc2',$params['EvidenceProductionSearch']['prod_misc2']]);
			$params['EvidenceProductionSearch']['prod_misc2']=$prod_party_data;
		}

		if ($params['EvidenceProductionSearch']['prod_return'] != null && is_array($params['EvidenceProductionSearch']['prod_return'])) {
			if(!empty($params['EvidenceProductionSearch']['prod_return'])){
				foreach($params['EvidenceProductionSearch']['prod_return'] as $k=>$v){
					if($v=='All') { // || strpos($v,",") !== false){
						unset($params['EvidenceProductionSearch']['prod_return']); break;
					}
				}
			}
			$query->andFilterWhere(['or like','tbl_evidence_production.prod_return',$params['EvidenceProductionSearch']['prod_return']]);
		}

        if ($params['EvidenceProductionSearch']['prod_date'] != '') {
            $prod_date = explode("-", $params['EvidenceProductionSearch']['prod_date']);
            $prod_date_start = explode("/", trim($prod_date[0]));
            $prod_date_end = explode("/", trim($prod_date[1]));
            $prod_date_s = $prod_date_start[2] . "-" . $prod_date_start[0] . "-" . $prod_date_start[1];
            $prod_date_e = $prod_date_end[2] . "-" . $prod_date_end[0] . "-" . $prod_date_end[1];
            if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$prod_date_s) && preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$prod_date_e)) {
                $query->andWhere(" $prod_date_query >= '$prod_date_s' AND $prod_date_query  <= '$prod_date_e' ");
            }else{
             	$params['EvidenceProductionSearch']['prod_date'] ='';
			}
        }

        if ($params['EvidenceProductionSearch']['prod_rec_date'] != '') {
            $prod_rec_date = explode("-", $params['EvidenceProductionSearch']['prod_rec_date']);
            $prod_rec_date_start = explode("/", trim($prod_rec_date[0]));
            $prod_rec_date_end = explode("/", trim($prod_rec_date[1]));
            $prod_rec_date_s = $prod_rec_date_start[2] . "-" . $prod_rec_date_start[0] . "-" . $prod_rec_date_start[1];
            $prod_rec_date_e = $prod_rec_date_end[2] . "-" . $prod_rec_date_end[0] . "-" . $prod_rec_date_end[1];
            if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$prod_rec_date_s) && preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$prod_rec_date_e)) {
                $query->andWhere(" $prod_rec_date_query >= '$prod_rec_date_s' AND $prod_rec_date_query  <= '$prod_rec_date_e' ");
            }else{
                $params['EvidenceProductionSearch']['prod_rec_date'] ='';
            }
        }

        if ($params['EvidenceProductionSearch']['prod_party'] != null && is_array($params['EvidenceProductionSearch']['prod_party'])) {
        	$prod_party_data=$params['EvidenceProductionSearch']['prod_party'];
			if(!empty($params['EvidenceProductionSearch']['prod_party'])) {
//                            echo '<pre>';print_r($params['EvidenceProductionSearch']['prod_party']);echo '</pre>';die;
				foreach($params['EvidenceProductionSearch']['prod_party'] as $k=>$v){
					if($v=='All') /* || strpos($v,",") !== false)*/{
						unset($params['EvidenceProductionSearch']['prod_party']);break;
					}else if($v=='(not set)') {
						$params['EvidenceProductionSearch']['prod_party'][$k]='';
					}
				}
			}
             $query->andFilterWhere(['or like','tbl_evidence_production.prod_party',$params['EvidenceProductionSearch']['prod_party']]);
			//$params['EvidenceProductionSearch']['prod_party']=$prod_party_data;
		}else{
            $query->andFilterWhere(['like','tbl_evidence_production.prod_party',$params['EvidenceProductionSearch']['prod_party']]);
        }

		if ($params['EvidenceProductionSearch']['cover_let_link'] != null && is_array($params['EvidenceProductionSearch']['cover_let_link'])) {
			$cover_let_link_data=$params['EvidenceProductionSearch']['cover_let_link'];
			if(!empty($params['EvidenceProductionSearch']['cover_let_link'])){
				foreach($params['EvidenceProductionSearch']['cover_let_link'] as $k=>$v){
					if($v=='All'){// || strpos($v,",") !== false){
						unset($params['EvidenceProductionSearch']['cover_let_link']);break;
					}else if($v=='(not set)'){
						$params['EvidenceProductionSearch']['cover_let_link'][$k]='';
					}
				}
			}
			//$query->andFilterWhere(['or like','tbl_evidence_production.cover_let_link',$params['EvidenceProductionSearch']['cover_let_link'],false]);
            $query->andFilterWhere(['in','tbl_evidence_production.cover_let_link',$params['EvidenceProductionSearch']['cover_let_link'],'']);
          //  $params['EvidenceProductionSearch']['cover_let_link']=$cover_let_link_data;
		}else{
            $query->andFilterWhere(['like','tbl_evidence_production.cover_let_link','%'.$params['EvidenceProductionSearch']['cover_let_link'].'%','']);
        }
		if ($params['EvidenceProductionSearch']['prod_orig'] != null && is_array($params['EvidenceProductionSearch']['prod_orig'])) {
			if(!empty($params['EvidenceProductionSearch']['prod_orig'])){
				foreach($params['EvidenceProductionSearch']['prod_orig'] as $k=>$v){
					if($v=='All') { // || strpos($v,",") !== false){
						unset($params['EvidenceProductionSearch']['prod_orig']);break;
					}
				}
			}
			$query->andFilterWhere(['in','tbl_evidence_production.prod_orig',$params['EvidenceProductionSearch']['prod_orig']]);
		}

		if ($params['EvidenceProductionSearch']['prod_disclose'] != null && is_array($params['EvidenceProductionSearch']['prod_disclose'])) {
			$prod_disclose_data=$params['EvidenceProductionSearch']['prod_disclose'];
			if(!empty($params['EvidenceProductionSearch']['prod_disclose'])){
				foreach($params['EvidenceProductionSearch']['prod_disclose'] as $k=>$v){
					if($v=='All'){ // || strpos($v,",") !== false){
						unset($params['EvidenceProductionSearch']['prod_disclose']);break;
					}else if($v=='(not set)'){
						$params['EvidenceProductionSearch']['prod_disclose'][$k]='';
					}
				}
			}
			$query->andFilterWhere(['in','tbl_evidence_production.prod_disclose',$params['EvidenceProductionSearch']['prod_disclose']]);
			//$params['EvidenceProductionSearch']['prod_disclose']=$prod_disclose_data;
		}else{
            $query->andFilterWhere(['like','tbl_evidence_production.prod_disclose',$params['EvidenceProductionSearch']['prod_disclose']]);
        }

        if($params['EvidenceProductionSearch']['prod_agencies'] != ''){
            $prod_agencies=explode("-",$params['EvidenceProductionSearch']['prod_agencies']);
            $prod_agencies_start=explode("/",trim($prod_agencies[0]));
            $prod_agencies_end=explode("/",trim($prod_agencies[1]));
            $prod_agencies_s=$prod_agencies_start[2]."-".$prod_agencies_start[0]."-".$prod_agencies_start[1];
            $prod_agencies_e=$prod_agencies_end[2]."-".$prod_agencies_end[0]."-".$prod_agencies_end[1];
            $query->andWhere(" $prod_agencies_query >= '$prod_agencies_s' AND $prod_agencies_query <= '$prod_agencies_e' ");
        }

        if($params['EvidenceProductionSearch']['prod_access_req'] != ''){
            $prod_access_req=explode("-",$params['EvidenceProductionSearch']['prod_access_req']);
            $prod_access_req_start=explode("/",trim($prod_access_req[0]));
            $prod_access_req_end=explode("/",trim($prod_access_req[1]));
            $prod_access_req_s=$prod_access_req_start[2]."-".$prod_access_req_start[0]."-".$prod_access_req_start[1];
            $prod_access_req_e=$prod_access_req_end[2]."-".$prod_access_req_end[0]."-".$prod_access_req_end[1];
            $query->andWhere(" $prod_access_req_query >= '$prod_access_req_s' AND $prod_access_req_query <= '$prod_access_req_e' ");
        }

		if($params['EvidenceProductionSearch']['production_desc'] != '')
			$query->andWhere("production_desc LIKE '%".$params['EvidenceProductionSearch']['production_desc']."%'");

		if($params['EvidenceProductionSearch']['attorney_notes'] != '')
			$query->andWhere("attorney_notes LIKE '%".$params['EvidenceProductionSearch']['attorney_notes']."%'");

        $prod_date="";
        if($params['prod_id'] != 0){
        	$this->id=$params['prod_id'];
        }

        /*if($params['EvidenceProductionSearch']['cover_let_link'] == 'blank' ){
			$query->andWhere(['cover_let_link' => '']);
		}
		if($params['EvidenceProductionSearch']['production_desc'] == 'blank'){
			$query->andWhere(['production_desc' => '']);
		//	unset($params['EvidenceProductionSearch']['production_desc']);
		}
		if($params['EvidenceProductionSearch']['attorney_notes'] == 'blank'){
			$query->andWhere(['attorney_notes' => '']);
		}
		if($params['EvidenceProductionSearch']['prod_disclose'] == 'blank'){
			$query->andWhere(['prod_disclose' => '']);
		}
		if($params['EvidenceProductionSearch']['staff_assigned'] == 'blank'){
			$query->andWhere(['staff_assigned' => '']);
		}*/

		/*if($params['EvidenceProductionSearch']['prod_date'] != '')
        {
            $prod_date_arr=explode("/",$params['EvidenceProductionSearch']['prod_date']);
            $prod_date=$prod_date_arr[2]."-".$prod_date_arr[0]."-".$prod_date_arr[1];
        }

        $prod_rec_date="";
        if($params['EvidenceProductionSearch']['prod_rec_date'] != '')
        {
            $prod_rec_date_arr=explode("/",$params['EvidenceProductionSearch']['prod_rec_date']);
            $prod_rec_date=$prod_rec_date_arr[2]."-".$prod_rec_date_arr[0]."-".$prod_rec_date_arr[1];
        }

        $prod_agencies="";
        if($params['EvidenceProductionSearch']['prod_agencies'] != '')
        {
            $prod_agencies_arr=explode("/",$params['EvidenceProductionSearch']['prod_agencies']);
            $prod_agencies=$prod_agencies_arr[2]."-".$prod_agencies_arr[0]."-".$prod_agencies_arr[1];
            if (Yii::$app->db->driverName == 'mysql') {
            	$datesql = "DATE_FORMAT( CONVERT_TZ(prod_agencies,'+00:00','{$timezoneOffset}'), '%Y-%m-%d') = '{$prod_agencies}'";
            } else {
            	$datesql = "CAST(switchoffset(todatetimeoffset(prod_agencies, '+00:00'), '{$timezoneOffset}') as date) = '".$prod_agencies."'";
            }
            $query->andWhere($datesql);
        }

        $prod_access_req="";
        if($params['EvidenceProductionSearch']['prod_access_req'] != '')
        {
            $prod_access_req_arr=explode("/",$params['EvidenceProductionSearch']['prod_access_req']);
            $prod_access_req=$prod_access_req_arr[2]."-".$prod_access_req_arr[0]."-".$prod_access_req_arr[1];
            if (Yii::$app->db->driverName == 'mysql') {
            	$datesql = "DATE_FORMAT( CONVERT_TZ(prod_access_req,'+00:00','{$timezoneOffset}'), '%Y-%m-%d') = '{$prod_access_req}'";
            } else {
            	$datesql = "CAST(switchoffset(todatetimeoffset(prod_access_req, '+00:00'), '{$timezoneOffset}') as date) = '".$prod_access_req."'";
            }
            $query->andWhere($datesql);
        }*/

        // grid filtering conditions
        /*$query->andFilterWhere([
            'id' => $this->id,
            //'client_id' => $this->client_id,
            'prod_date' => $prod_date,
            'prod_rec_date' => $prod_rec_date,
            'production_type' =>  $params['EvidenceProductionSearch']['production_type'],
            'prod_orig' => $this->prod_orig,
            'prod_return' => $this->prod_return,
            // 'prod_agencies' => $prod_agencies,
            // 'prod_access_req' => $prod_access_req,
            'has_media' =>  $this->has_media,
            'has_hold' => $this->has_hold,
            'has_projects' => $this->has_projects,
            'created' => $this->created,
            'created_by' => $this->created_by,
            'modified' => $this->modified,
            'modified_by' => $this->modified_by,
        ]);

        if($params['EvidenceProductionSearch']['cover_let_link'] != 'blank')
			$query->andFilterWhere(['like', 'cover_let_link', $this->cover_let_link]);
		if($params['EvidenceProductionSearch']['production_desc'] != 'blank')
			$query->andFilterWhere(['like', 'production_desc', $this->production_desc]);
		if($params['EvidenceProductionSearch']['attorney_notes'] != 'blank')
			$query->andFilterWhere(['like', 'attorney_notes', $this->attorney_notes]);
		if($params['EvidenceProductionSearch']['prod_disclose'] != 'blank')
			$query->andFilterWhere(['like', 'prod_disclose', $this->prod_disclose]);
		if($params['EvidenceProductionSearch']['staff_assigned'] != 'blank')
			$query->andFilterWhere(['like', 'staff_assigned', $this->staff_assigned]);

        $query->andFilterWhere(['like', 'prod_party', $params['EvidenceProductionSearch']['prod_party']])
            ->andFilterWhere(['like', 'prod_misc1', $this->prod_misc1])
            ->andFilterWhere(['like', 'prod_misc2', $this->prod_misc2]);

        if($params['EvidenceProductionSearch']['production_desc'] == 'blank'){
			unset($params['EvidenceProductionSearch']['production_desc']);
		}*/

		$this->load($params);

        return $dataProvider;
    }

    public function searchFilter($params)
    {
		$dataProvider = array();
		$query = EvidenceProduction::find()->orderBy(['tbl_evidence_production.id'=>SORT_DESC]);
		$query->andWhere(['client_case_id' => $params['case_id']]);
        if($params['field']=='id')  {
    		$query->select(['tbl_evidence_production.id']);
    		if(isset($params['q']) && $params['q']!=""){
	    		$query->andFilterWhere(['like','tbl_evidence_production.id', $params['q'].'%',false]);
    		}
    		$query->groupBy('tbl_evidence_production.id');
    		$query->orderBy('tbl_evidence_production.id ASC');
    		$dataProvider = ArrayHelper::map($query->all(),'id','id');
    	}if($params['field']=='staff_assigned'){
    		$query->select(['staff_assigned','staff_assigned']);
    		if(isset($params['q']) && $params['q']!=""){$query->andFilterWhere(['like','staff_assigned',$params['q']]);
			}
			$query->groupBy('tbl_evidence_production.staff_assigned');
    		$query->orderBy('tbl_evidence_production.staff_assigned ASC');
    		$dataProvider = ArrayHelper::map($query->all(),'staff_assigned','staff_assigned');
    	}if($params['field']=='prod_party' ){
    		$query->select(['prod_party','prod_party']);
    		if(isset($params['q']) && $params['q']!=""){$query->andFilterWhere(['like','prod_party', $params['q']]);}
    		$query->groupBy('tbl_evidence_production.prod_party');
    		$query->orderBy('tbl_evidence_production.prod_party ASC');
    		$dataProvider = ArrayHelper::map($query->all(), 'prod_party', 'prod_party');
    	}
    	if($params['field']=='production_desc' ){
    		$query->select(['production_desc','production_desc']);
    		if(isset($params['q']) && $params['q']!=""){$query->andFilterWhere(['like','production_desc', $params['q']]);}
    		$query->groupBy('tbl_evidence_production.production_desc');
    		$query->orderBy('tbl_evidence_production.production_desc ASC');
    		$dataProvider = ArrayHelper::map($query->all(),'production_desc','production_desc');
    	}
    	if($params['field']=='cover_let_link' ){
    		$query->select(['cover_let_link','cover_let_link']);
    		if(isset($params['q']) && $params['q']!=""){$query->andFilterWhere(['or like','cover_let_link',$params['q']."%",false]);}
            $query->groupBy('tbl_evidence_production.cover_let_link');
    		$query->orderBy('tbl_evidence_production.cover_let_link ASC');
    		$dataProvider = ArrayHelper::map($query->all(),'cover_let_link','cover_let_link');
    	}
    	if($params['field']=='attorney_notes' ){
    		$query->select(['attorney_notes','attorney_notes']);
    		if(isset($params['q']) && $params['q']!=""){$query->andFilterWhere(['like','attorney_notes',$params['q']]);}
    		$query->groupBy('tbl_evidence_production.attorney_notes');
    		$query->orderBy('tbl_evidence_production.attorney_notes ASC');
    		$dataProvider = ArrayHelper::map($query->all(),'attorney_notes','attorney_notes');
    	}
    	if($params['field']=='prod_disclose' ){
    		$query->select(['prod_disclose','prod_disclose']);
    		if(isset($params['q']) && $params['q']!=""){$query->andFilterWhere(['like','prod_disclose',$params['q']]);}
    		$dataProvider = ArrayHelper::map($query->all(),'prod_disclose','prod_disclose');
    	}
    	if($params['field']=='prod_return' ){
    		$query->select(['prod_return']);
    		if(isset($params['q']) && $params['q']!=""){$query->andFilterWhere(['like','prod_return',$params['q']]);}
    		$dataProvider = ArrayHelper::map($query->all(),'prod_return','prod_return');
    	}
    	if($params['field']=='prod_misc1' ){
    		$query->select(['prod_misc1']);
    		if(isset($params['q']) && $params['q']!=""){$query->andFilterWhere(['like','prod_misc1',$params['q']]);}
    		$dataProvider = ArrayHelper::map($query->all(),'prod_misc1','prod_misc1');
    	}
    	if($params['field']=='prod_misc2' ){
    		$query->select(['prod_misc2']);
    		if(isset($params['q']) && $params['q']!=""){$query->andFilterWhere(['like','prod_misc2',$params['q']]);}
    		$dataProvider = ArrayHelper::map($query->all(),'prod_misc2','prod_misc2');
    	}
    	if($params['field']=='contents_total_size_comp' ){
    		$query->select(['contents_total_size_comp','tbl_unit.unit_name']);
    		if(isset($params['q']) && $params['q']!=""){$query->andFilterWhere(['like','contents_total_size',$params['q']]);}
    		$query->groupBy('tbl_evidence_production.contents_total_size_comp');
    		$query->orderBy('tbl_evidence_production.contents_total_size_comp ASC');
    		$dataProvider = ArrayHelper::map($query->all(),'contents_total_size_comp','contents_total_size_comp');
    	}
    	return array_merge(array('All'=>'All'), $dataProvider);
    }
    public function searchpdf($params)
    {
        $query = EvidenceProduction::find() //->joinWith('productionattachments');
            ->orderBy(['tbl_evidence_production.id'=>SORT_DESC]);

        if (Yii::$app->db->driverName == 'sqlsrv') {
            /* Get prod date with '0000-00-00'  format */
            $query->select(['id', 'client_case_id', 'staff_assigned', 'prod_rec_date', 'prod_party', 'production_desc', 'prod_copied_to', 'production_type', 'cover_let_link', 'prod_orig', 'prod_return', 'attorney_notes', 'prod_disclose', 'prod_agencies', 'prod_access_req', 'has_media', 'has_hold', 'has_projects', 'prod_misc1', 'prod_misc2', 'created', 'created_by', 'modified', 'modified_by', "CASE WHEN prod_date IS NULL THEN '0000-00-00' ELSE CAST(prod_date as VARCHAR(50)) END as prod_date"]);
        }
        $timezoneOffset = (new Options)->getOffsetOfCurrenttimeZone();
        if (Yii::$app->db->driverName == 'mysql') {
			$prod_date_query = "DATE_FORMAT(CONVERT_TZ(tbl_evidence_production.`prod_date`,'+00:00','+00:00'), '%Y-%m-%d')";
			$prod_rec_date_query = "DATE_FORMAT(CONVERT_TZ(tbl_evidence_production.prod_rec_date,'+00:00','+00:00'), '%Y-%m-%d')";
			$prod_agencies_query = "DATE_FORMAT(CONVERT_TZ(tbl_evidence_production.`prod_agencies`,'+00:00','+00:00'), '%Y-%m-%d')";
			$prod_access_req_query = "DATE_FORMAT(CONVERT_TZ(tbl_evidence_production.`prod_access_req`,'+00:00','+00:00'), '%Y-%m-%d')";
		}else{
			$prod_date_query = "CAST(switchoffset(todatetimeoffset(prod_date, '+00:00'), '+00:00') as date)";
			$prod_rec_date_query = "CAST(switchoffset(todatetimeoffset(prod_rec_date, '+00:00'), '+00:00') as date)";
			$prod_agencies_query = "CAST(switchoffset(todatetimeoffset(prod_agencies, '+00:00'), '+00:00') as date)";
			$prod_access_req_query = "CAST(switchoffset(todatetimeoffset(prod_access_req, '+00:00'), '+00:00') as date)";
		}

        //echo "<pre>";print_r($params);
        // add conditions that should always apply here
        if($params['id'] == 'All')
            $params['id']='';
        if($params['prod_party'] == 'All')
            $params['prod_party']='';
		if($params['production_desc'] == 'All')
            $params['production_desc']='';
        if($params['cover_let_link'] == 'All')
            $params['cover_let_link']='';
        if($params['attorney_notes'] == 'All')
            $params['attorney_notes']='';
        if($params['prod_disclose'] == 'All')
            $params['prod_disclose']='';

        $case_id=$params['case_id'];
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            //'pagination' =>['pageSize'=>25],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

	$query->andWhere(['client_case_id' => $case_id]);

        $prod_date="";
//        if(isset($params['prod_date']) && $params['prod_date'] != '')
//        {
//            $prod_date_arr=explode("/",$params['prod_date']);
//            $prod_date=$prod_date_arr[2]."-".$prod_date_arr[0]."-".$prod_date_arr[1];
//            $query->andFilterWhere(['prod_date' => $prod_date]);
//        }
        if (isset($params['prod_date']) && $params['prod_date'] != '') {
            $prod_date = explode("-", $params['prod_date']);
            $prod_date_start = explode("/", trim($prod_date[0]));
            $prod_date_end = explode("/", trim($prod_date[1]));
            $prod_date_s = $prod_date_start[2] . "-" . $prod_date_start[0] . "-" . $prod_date_start[1];
            $prod_date_e = $prod_date_end[2] . "-" . $prod_date_end[0] . "-" . $prod_date_end[1];
            $query->andWhere(" $prod_date_query >= '$prod_date_s' AND $prod_date_query  <= '$prod_date_e' ");
        }
//        echo '<pre>';print_r($prod_date);print_r($prod_date_end);die;
        $prod_rec_date="";
//        if($params['prod_rec_date'] != '')
//        {
//            $prod_rec_date_arr=explode("/",$params['prod_rec_date']);
//            $prod_rec_date=$prod_rec_date_arr[2]."-".$prod_rec_date_arr[0]."-".$prod_rec_date_arr[1];
//            $query->andFilterWhere(['prod_rec_date' => $prod_rec_date]);
//        }
        if ($params['prod_rec_date'] != '') {
            $prod_rec_date = explode("-", $params['prod_rec_date']);
            $prod_rec_date_start = explode("/", trim($prod_rec_date[0]));
            $prod_rec_date_end = explode("/", trim($prod_rec_date[1]));
            $prod_rec_date_s = $prod_rec_date_start[2] . "-" . $prod_rec_date_start[0] . "-" . $prod_rec_date_start[1];
            $prod_rec_date_e = $prod_rec_date_end[2] . "-" . $prod_rec_date_end[0] . "-" . $prod_rec_date_end[1];
            $query->andWhere(" $prod_rec_date_query >= '$prod_rec_date_s' AND $prod_rec_date_query  <= '$prod_rec_date_e' ");
        }
//        $prod_agencies="";
//        if($params['prod_agencies'] != '')
//        {
//            $prod_agencies_arr=explode("/",$params['prod_agencies']);
//            $prod_agencies=$prod_agencies_arr[2]."-".$prod_agencies_arr[0]."-".$prod_agencies_arr[1];
//        }
//        $prod_access_req="";
//        if($params['prod_access_req'] != '')
//        {
//            $prod_access_req_arr=explode("/",$params['prod_access_req']);
//            $prod_access_req=$prod_access_req_arr[2]."-".$prod_access_req_arr[0]."-".$prod_access_req_arr[1];
//        }
//        if (DB_TYPE == 'sqlsrv') {
//            $query->andFilterWhere(["CAST(prod_agencies as date)" => $prod_agencies]);
//            $query->andFilterWhere(["CAST(prod_access_req as date)"=>$prod_access_req]);
//        } else {
//            $query->andFilterWhere(["DATE_FORMAT(prod_agencies, '%Y-%m-%d')"=>$prod_agencies]);
//            $query->andFilterWhere(["DATE_FORMAT(prod_access_req, '%Y-%m-%d')"=>$prod_access_req]);
//        }
        if ($params['prod_agencies'] != '') {
            $prod_agencies = explode("-", $params['prod_agencies']);
            $prod_agencies_start = explode("/", trim($prod_agencies[0]));
            $prod_agencies_end = explode("/", trim($prod_agencies[1]));
            $prod_agencies_s = $prod_agencies_start[2] . "-" . $prod_agencies_start[0] . "-" . $prod_agencies_start[1];
            $prod_agencies_e = $prod_agencies_end[2] . "-" . $prod_agencies_end[0] . "-" . $prod_agencies_end[1];
            $query->andWhere(" $prod_agencies_query >= '$prod_agencies_s' AND $prod_agencies_query <= '$prod_agencies_e' ");
        }
        if ($params['prod_access_req'] != '') {
            $prod_access_req = explode("-", $params['prod_access_req']);
            $prod_access_req_start = explode("/", trim($prod_access_req[0]));
            $prod_access_req_end = explode("/", trim($prod_access_req[1]));
            $prod_access_req_s = $prod_access_req_start[2] . "-" . $prod_access_req_start[0] . "-" . $prod_access_req_start[1];
            $prod_access_req_e = $prod_access_req_end[2] . "-" . $prod_access_req_end[0] . "-" . $prod_access_req_end[1];
            $query->andWhere(" $prod_access_req_query >= '$prod_access_req_s' AND $prod_access_req_query <= '$prod_access_req_e' ");
        }
        if(isset($params['id']) && $params['id'] != '' && $params['id'] != 'All')
            $query->andFilterWhere(['id' => $params['id']]);
        if(isset($params['client_id']) && $params['client_id'] != '')
            $query->andFilterWhere(['client_id' => $params['client_id']]);
        if(isset($params['production_type']) && $params['production_type'] != '')
            $query->andFilterWhere(['production_type' => $params['production_type']]);
        if(isset($params['prod_orig']) && $params['prod_orig'] != '')
            $query->andFilterWhere(['prod_orig' => $params['prod_orig']]);
        if(isset($params['prod_return']) && $params['prod_return'] != '')
            $query->andFilterWhere(['prod_return' => $params['prod_return']]);
        if(isset($params['has_media']) && $params['has_media'] != '')
            $query->andFilterWhere(['has_media' => $params['has_media']]);
        if(isset($params['has_hold']) && $params['has_hold'] != '')
            $query->andFilterWhere(['has_hold' => $params['has_hold']]);
        if(isset($params['has_projects']) && $params['has_projects'] != '')
            $query->andFilterWhere(['has_projects' => $params['has_projects']]);
        if(isset($params['staff_assigned']) && $params['staff_assigned'] != '')
            $query->andFilterWhere(['or like', 'staff_assigned', $params['staff_assigned']]);
        if(isset($params['prod_party']) && $params['prod_party'] != '')
            $query->andFilterWhere(['or like', 'prod_party', $params['prod_party']]);
        if(isset($params['production_desc']) && $params['production_desc'] != '')
            $query->andFilterWhere(['or like', 'production_desc', $params['production_desc']]);
        if(isset($params['cover_let_link']) && $params['cover_let_link'] != '')
            $query->andFilterWhere(['or like', 'cover_let_link', $params['cover_let_link']]);
        if(isset($params['attorney_notes']) && $params['attorney_notes'] != '')
            $query->andFilterWhere(['or like', 'attorney_notes', $params['attorney_notes']]);
        if(isset($params['prod_disclose']) && $params['prod_disclose'] != '')
            $query->andFilterWhere(['or like', 'prod_disclose', $params['prod_disclose']]);
        if(isset($params['prod_misc1']) && $params['prod_misc1'] != '')
            $query->andFilterWhere(['or like', 'prod_misc1', $params['prod_misc1']]);
        if(isset($params['prod_misc2']) && $params['prod_misc2'] != '')
            $query->andFilterWhere(['or like', 'prod_misc2', $params['prod_misc2']]);

        $query->andFilterWhere(['like', 'staff_assigned', $this->staff_assigned])
            ->andFilterWhere(['like', 'prod_party', $params['EvidenceProductionSearch']['prod_party']])
            ->andFilterWhere(['like', 'production_desc', $this->production_desc])
            ->andFilterWhere(['like', 'cover_let_link', $this->cover_let_link])
            ->andFilterWhere(['like', 'attorney_notes', $this->attorney_notes])
            ->andFilterWhere(['like', 'prod_disclose', $this->prod_disclose])
            ->andFilterWhere(['like', 'prod_misc1', $this->prod_misc1])
            ->andFilterWhere(['like', 'prod_misc2', $this->prod_misc2]);
//        $query->groupBy('tbl_evidence_production.id');
//        echo '<pre>';print_r($dataProvider->getTotalCount());print_r($params);echo '</pre>';die;
        return $dataProvider;
    }
}
