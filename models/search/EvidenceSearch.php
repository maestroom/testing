<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Client;
use app\models\ClientCase;
use app\models\Evidence;
use app\models\EvidenceCategory;
use app\models\Options;
use app\models\ProjectSecurity;
use app\models\Role;
use app\models\FormBuilderSystem;
use yii\helpers\ArrayHelper;

/**
 * EvidenceSearch represents the model behind the search form about `app\models\Evidence`.
 */
class EvidenceSearch extends Evidence
{
	public $contentstotalsize_comp,$contentstotalsize_compunit;
	public $evidence_name=null;
	public $category=null;
	public $evidenceunitunit_name=null;
	public $evidencecompunit_name=null;
	public $evidcreateduser=null;
	public $client_id=null;
	public $client_case_id=null;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'checkedin_by', 'dup_evid', 'org_link',   'unit', 'comp_unit', 'enctype', 'evid_stored_location', 'status', 'has_contents', 'created_by', 'modified_by','received_from','evidence_name','category','evidenceunitunit_name','evidencecompunit_name'], 'string'],
            [['id','status','other_evid_num', 'evid_type',  'cat_id', 'quantity', 'contents_total_size', 'contents_total_size_comp', 'received_date', 'received_time', 'received_from', 'evd_Internal_no', 'serial', 'model', 'hash', 'cont', 'evid_desc', 'evid_label_desc', 'contents_copied_to', 'mpw', 'bbates', 'ebates', 'm_vol', 'ftpun', 'ftppw', 'encpw', 'evid_notes', 'barcode', 'created', 'modified','client_id','client_case_id','evidence_name','category','evidenceunitunit_name','evidencecompunit_name'], 'safe'],
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
    public function search($params,$is_return="",$media_form=array())
    {
		$userId = Yii::$app->user->identity->id;
		$roleId = Yii::$app->user->identity->role_id; // Role Id
		$role_info = $_SESSION['role'];
		$role_type = explode(',',$role_info->role_type);
		//$role_type = explode(',',Role::findOne($roleId)->role_type);
	    //print_r($params);die;
        //$criteria = $this->getEvidenceData($cust_id, $return_ids,'searchmodel');
		$createduser_sql="SELECT CONCAT(usr_first_name,' ',usr_lastname) full_name FROM tbl_user WHERE id=tbl_evidence.created_by";
        $timezoneOffset = (new Options)->getOffsetOfCurrenttimeZone();
        if (Yii::$app->db->driverName == 'mysql') {
			$received_time_query="time_format(str_to_date(received_time,'%h:%i %p'),'%H:%i:%s')";
			$received_time_q = "DATE_FORMAT(CONVERT_TZ(CONCAT(tbl_evidence.`received_date` , ' ', STR_TO_DATE(tbl_evidence.`received_time` , '%l:%i %p' )),'+00:00','{$timezoneOffset}'), '%H:%i')";
			$data_query = "DATE_FORMAT(CONVERT_TZ(CONCAT(tbl_evidence.`received_date` , ' ', STR_TO_DATE(tbl_evidence.`received_time` , '%l:%i %p' )),'+00:00','{$timezoneOffset}'), '%Y-%m-%d %H:%i')";
			$where_date_query = "DATE_FORMAT(CONVERT_TZ(CONCAT(tbl_evidence.`received_date` , ' ', STR_TO_DATE(tbl_evidence.`received_time` , '%l:%i %p' )),'+00:00','{$timezoneOffset}'), '%Y-%m-%d')";
			$where_datetime_query = "DATE_FORMAT(CONVERT_TZ(CONCAT(tbl_evidence.`received_date` , ' ', STR_TO_DATE(tbl_evidence.`received_time` , '%l:%i %p' )),'+00:00','{$timezoneOffset}'), '%h:%i %p')";
		}else{
			$received_time_query="CAST(received_time as time)";
			$received_time_q = "CAST(switchoffset(todatetimeoffset(Cast((CAST(tbl_evidence.received_date as varchar)  + ' ' + tbl_evidence.received_time) as datetime), '+00:00'), '{$timezoneOffset}') as time)";
			$data_query = "CAST(switchoffset(todatetimeoffset(Cast((CAST(tbl_evidence.received_date as varchar)  + ' ' + tbl_evidence.received_time) as datetime), '+00:00'), '{$timezoneOffset}') as datetime)";
			$where_date_query = "CAST(switchoffset(todatetimeoffset(Cast((CAST(tbl_evidence.received_date as varchar)  + ' ' + tbl_evidence.received_time) as datetime), '+00:00'), '{$timezoneOffset}') as date)";
			$where_datetime_query = "right(convert(varchar(32),CAST(switchoffset(todatetimeoffset(Cast((CAST(tbl_evidence.received_date as varchar)  + ' ' + tbl_evidence.received_time) as datetime), '+00:00'), '{$timezoneOffset}') as datetime),100),8)";
		}
	   // echo $where_time_query;die;
        //if($params['EvidenceSearch']['id'] == 'All')
          //  $params['EvidenceSearch']['id']='';
        //if($params['EvidenceSearch']['evid_type'] == 'All')
          //  $params['EvidenceSearch']['evid_type']='';
        if($params['EvidenceSearch']['evid_desc'] == 'All')
           $params['EvidenceSearch']['evid_desc']='';
        //if($params['EvidenceSearch']['cat_id'] == 'All')
          //  $params['EvidenceSearch']['cat_id']='';
       //if($params['EvidenceSearch']['quantity'] == 'All')
		//	$params['EvidenceSearch']['quantity']='';
		//if($params['EvidenceSearch']['barcode'] == 'All')
			//$params['EvidenceSearch']['barcode']='';
			if (Yii::$app->db->driverName == 'mysql') {

					$client_case_sql =	"(SELECT GROUP_CONCAT(tbl_client_case.case_name) FROM tbl_client_case WHERE tbl_client_case.id IN (select client_case_id from tbl_client_case_evidence where evid_num_id = tbl_evidence.id)) as client_case_id";
					$client_sql = "(SELECT GROUP_CONCAT(tbl_client.client_name) FROM tbl_client WHERE tbl_client.id IN (select client_id from tbl_client_case_evidence where evid_num_id = tbl_evidence.id)) as client_id";
				//$client_case_sql = "tbl_client_case_evidence.client_case_id";
				//$client_sql = "tbl_client_case_evidence.client_id";
			}
			else {
					$client_case_sql = "(SELECT DISTINCT client_case_id = STUFF((SELECT ',' + tbl_client_case.case_name
					          FROM tbl_client_case
					          WHERE tbl_client_case.id IN (select client_case_id from tbl_client_case_evidence where evid_num_id = tbl_evidence.id)
					          FOR XML PATH(''), TYPE).value('.', 'NVARCHAR(MAX)'), 1, 1, '')
					FROM tbl_client_case
					INNER JOIN tbl_client_case_evidence ON tbl_client_case.id = tbl_client_case_evidence.client_case_id
					WHERE tbl_client_case_evidence.evid_num_id = tbl_evidence.id
					) AS client_case_id";

					$client_sql = "(SELECT DISTINCT client_id = STUFF((SELECT ',' + tbl_client.client_name
					          FROM tbl_client
					          WHERE tbl_client.id IN (select client_id from tbl_client_case_evidence where evid_num_id = tbl_evidence.id)
					          FOR XML PATH(''), TYPE).value('.', 'NVARCHAR(MAX)'), 1, 1, '')
					FROM tbl_client
					INNER JOIN tbl_client_case_evidence ON tbl_client.id = tbl_client_case_evidence.client_id
					WHERE tbl_client_case_evidence.evid_num_id = tbl_evidence.id
					) AS client_id";
					//$client_case_sql = "tbl_client_case_evidence.client_case_id";
					//$client_sql = "tbl_client_case_evidence.client_id";
			}
			/*,$client_sql,$client_case_sql*/
        $select_fields=['tbl_evidence.id','tbl_evidence.status','('.$createduser_sql.') as evidcreateduser'];
		//$media_form = FormBuilderSystem::find()->where(['sys_form'=>'media_form','grid_type'=>1])->andWhere("sys_field_name NOT IN ('client_id','client_case_id')")->all();
		$i=3;
		if($is_return == "Y") {
		} else {
			$grid_id='dynagrid-media_'.Yii::$app->user->identity->id;
			$sql="SELECT * FROM tbl_dynagrid_dtl WHERE id IN (SELECT sort_id FROM tbl_dynagrid WHERE id='$grid_id')";
			$sort_data=Yii::$app->db->createCommand($sql)->queryOne();
		}
		if(!empty($media_form)){
			foreach($media_form as $column){
				if(in_array($column,array('id','status','created_by','upload_files'))){
					continue;
				}
				if($column=='received_date'){
					$select_fields[$i]='('.$data_query.') as received_date';
				}else if($column=='evid_type') {
					$select_fields[$i]='tbl_evidence_type.evidence_name';
				}else if($column=='cat_id') {
					$select_fields[$i]='tbl_evidence_category.category as category';
				}else if($column=='unit'){
					$select_fields[$i]='tbl_unit.unit_name as evidenceunitunit_name';
				}else if($column=='comp_unit'){
					$select_fields[$i]='evidcomp.unit_name as evidencecompunit_name';
				}else if($column=='received_time'){
					$select_fields[$i]='('.$received_time_q.') as received_time';
				}else{
					$select_fields[$i]='tbl_evidence.'.$column;
				}
				$i++;
			}
			
			$sort_columns=json_decode($sort_data['data'],true);
			if(!empty($sort_columns) && !empty($media_form)){
				foreach($sort_columns as $column=>$scc){
						if(in_array($column,array('id','status','created_by','upload_files'))){
							continue;
						}
						if(!in_array($column,$media_form)){
							if($column=='received_date'){
								$select_fields[$i]='('.$data_query.') as received_date';
							}else if($column=='evid_type') {
								$select_fields[$i]='tbl_evidence_type.evidence_name';
							}else if($column=='cat_id') {
								$select_fields[$i]='tbl_evidence_category.category as category';
							}else if($column=='unit'){
								$select_fields[$i]='tbl_unit.unit_name as evidenceunitunit_name';
							}else if($column=='comp_unit'){
								$select_fields[$i]='evidcomp.unit_name as evidencecompunit_name';
							}else if($column=='received_time'){
								$select_fields[$i]='('.$received_time_q.') as received_time';
							}else{
								$select_fields[$i]='tbl_evidence.'.$column;
							}
							$i++;
							//unset($sort_columns[$k]);
						}
					}
				}
				//echo "<pre>",print_r($select_fields),"</pre>";die;
		}else{
			$select_fields=['tbl_evidence.id','tbl_evidence.status','('.$createduser_sql.') as evidcreateduser','tbl_evidence.checkedin_by','tbl_evidence.dup_evid','tbl_evidence.org_link','tbl_evidence.other_evid_num','tbl_evidence.received_from','tbl_evidence.evd_Internal_no','tbl_evidence.evid_type','tbl_evidence.cat_id','tbl_evidence.serial','tbl_evidence.model','tbl_evidence.hash','tbl_evidence.quantity','tbl_evidence.cont','tbl_evidence.evid_desc','tbl_evidence.evid_label_desc','tbl_evidence.contents_total_size','tbl_evidence.contents_total_size_comp','tbl_evidence.unit','tbl_evidence.comp_unit','tbl_evidence.contents_copied_to','tbl_evidence.mpw','tbl_evidence.bbates','tbl_evidence.ebates','tbl_evidence.m_vol','tbl_evidence.ftpun','tbl_evidence.ftppw','tbl_evidence.enctype','tbl_evidence.encpw','tbl_evidence.evid_stored_location','tbl_evidence.evid_notes','tbl_evidence.has_contents','tbl_evidence.barcode','tbl_evidence.created','tbl_evidence.created_by','tbl_evidence.modified','tbl_evidence.modified_by','tbl_evidence_type.evidence_name','tbl_evidence_category.category as category','tbl_unit.unit_name as evidenceunitunit_name','evidcomp.unit_name as evidencecompunit_name','('.$received_time_q.') as received_time','tbl_user.usr_first_name','tbl_user.usr_lastname'];
		}
		//echo "<pre>",print_r($select_fields),"</pre>";die;
		$query = Evidence::find()
		->select($select_fields)
		->joinWith(['evidencetype','evidencecategory','evidenceunit','evidencecompunit','user'],false)
			->joinWith(['evidenceclientcase' => function(\yii\db\ActiveQuery $query) use($userId, $roleId,$role_type,$params){
				$query->joinWith(['clientcase' => function(\yii\db\ActiveQuery $query) use($userId, $roleId,$role_type,$params){
					if($roleId!=0 && $role_type[0]!=2){
						if(isset($params['client_case_id']) && $params['client_case_id']==0){}else{
							$query->joinWith(['projectSecurity' => function(\yii\db\ActiveQuery $query) use ($userId){
								$query->where(['tbl_project_security.user_id' => $userId]);
							}],false);
						}
					}
				}],false);
			}],false);
		//echo "<pre>",print_r($query->asArray()->all()),"</pre>"; die;
        if(isset($params['id']) && $params['id']!=''){
			$query->andWhere(['tbl_evidence.id' => $params['id']]);
		}
		$query->distinct();

		//if(!isset($params['sort'])){
            //$query->orderBy(['tbl_evidence.id'=>SORT_DESC]);
        //}
        //$result=$query->all();        echo "<pre>";print_r($result);die;

		/*if (isset($_GET['EvidenceSearch']) && !($this->load($params) && $this->validate())) {
			return $dataProvider;
		}*/

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' =>['pagesize'=>25],
        	//'sort' => ['attributes' => ['contents_total_size_comp']]
        ]);
		$dataProvider->sort->enableMultiSort=true;
		/*IRT-67*/
		if($is_return == "Y") {

		} else {
			if(!empty($sort_data)){
				$dataProvider->sort->defaultOrder=json_decode($sort_data['data'],true);
				//echo "<pre>",print_r($sort_columns),"</pre>";die;
			}else{
				if(!isset($params['sort'])){
					$dataProvider->sort->defaultOrder=['id'=>SORT_DESC];
				}
			}
		}
		/*IRT-67*/
		//$query->addSelect(['tbl_user.id',"CONCAT(tbl_user.usr_first_name,' ',tbl_user.usr_lastname) full_name"]);

		$dataProvider->sort->attributes['created_by'] = [
        		// The tables are the ones our relation are configured to
        		// in my case they are prefixed with "tbl_"
				//CONCAT(tbl_user.usr_first_name,' ',tbl_user.usr_lastname) full_name
        		//'asc' => ['tbl_user.usr_first_name' => SORT_ASC,'tbl_user.usr_lastname' => SORT_ASC],
        		//'desc' => ['tbl_user.usr_first_name' => SORT_DESC,'tbl_user.usr_lastname' => SORT_DESC],
				'asc'=>['evidcreateduser' => SORT_ASC],
				'desc'=>['evidcreateduser' => SORT_DESC],
        ];
		
		$dataProvider->sort->attributes['unit'] = [
			'asc' => ['evidenceunitunit_name' => SORT_ASC],
        	'desc' => ['evidenceunitunit_name' => SORT_DESC],
		];
		$dataProvider->sort->attributes['comp_unit'] = [
			'asc' => ['evidencecompunit_name' => SORT_ASC],
        	'desc' => ['evidencecompunit_name' => SORT_DESC],
		];
		
		
		$dataProvider->sort->attributes['cat_id'] = [
			'asc' => ['tbl_evidence_category.category' => SORT_ASC],
        	'desc' => ['tbl_evidence_category.category' => SORT_DESC],
		];
        $dataProvider->sort->attributes['evid_type'] = [
        		// The tables are the ones our relation are configured to
        		// in my case they are prefixed with "tbl_"
        		'asc' => ['tbl_evidence_type.evidence_name' => SORT_ASC],
        		'desc' => ['tbl_evidence_type.evidence_name' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['contents_total_size_comp'] = [
        		// The tables are the ones our relation are configured to
        		// in my case they are prefixed with "tbl_"
        		'asc' => ['contents_total_size_comp' => SORT_ASC],
        		'desc' => ['contents_total_size_comp' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['received_date'] = [
            'asc' => ["$data_query" => SORT_ASC],
            'desc' => ["$data_query" => SORT_DESC],
        ];
		$dataProvider->sort->attributes['received_time'] = [
            'asc' => ["$received_time_q" => SORT_ASC],
            'desc' => ["$received_time_q" => SORT_DESC],
        ];
		/*
         * IRT-101
         * Modification Date : 15-2-2017
         * Mdified By : Nelson Rana
         * Code Start
         * */
        $role_type = explode(',',$role_info->role_type);
		if(isset($params['client_case_id']) && $params['client_case_id']==0){}else{
			if(in_array('1',$role_type)){
				if(!in_array('2',$role_type)){
					$projectsecurity_cases = (new ProjectSecurity)->getUserCases($userId);
					if(!empty($projectsecurity_cases)){
					$clientcase_evid_sql = "SELECT evid_num_id FROM tbl_client_case_evidence WHERE client_case_id IN (".implode(',',$projectsecurity_cases).") Group By evid_num_id";
					$query->andWhere('tbl_evidence.id IN ('.$clientcase_evid_sql.')');
					}
				}
			}
		}
		/*
		 * Code Ends
		 */
        $received_date="";
        if($params['EvidenceSearch']['received_date'] != ''){
            $received_date=explode("-",$params['EvidenceSearch']['received_date']);
            $received_date_start=explode("/",trim($received_date[0]));
            $received_date_end=explode("/",trim($received_date[1]));
            $received_date_s=$received_date_start[2]."-".$received_date_start[0]."-".$received_date_start[1];
            $received_date_e=$received_date_end[2]."-".$received_date_end[0]."-".$received_date_end[1];
            $query->andWhere(" $where_date_query >= '$received_date_s' AND $where_date_query  <= '$received_date_e' ");
        }



		/*if(isset($params['EvidenceSearch']['barcode']) && $params['EvidenceSearch']['barcode'] == '(not set)'){
			$query->andWhere(['barcode' => '']);
		}
		else if(isset($params['EvidenceSearch']['barcode']) && $params['EvidenceSearch']['barcode'] != '(not set)' && $params['EvidenceSearch']['barcode'] != ''){
			$query->andWhere(['barcode' => $params['EvidenceSearch']['barcode']]);
		}*/
        /*multiselect*/
        if ($params['EvidenceSearch']['id'] != null && is_array($params['EvidenceSearch']['id'])) {
			if(!empty($params['EvidenceSearch']['id'])){
				foreach($params['EvidenceSearch']['id'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['EvidenceSearch']['id']);break;
					}
				}
			}
			$query->andFilterWhere(['in','tbl_evidence.id',$params['EvidenceSearch']['id']]);
		} else {
			if ($params['EvidenceSearch']['id'] != null) {
				$query->andFilterWhere(['tbl_evidence.id'=>$params['EvidenceSearch']['id']]);
			}
		}
		if ($params['EvidenceSearch']['barcode'] != null && is_array($params['EvidenceSearch']['barcode'])) {
			$barcode_data=$params['EvidenceSearch']['barcode'];
			if(!empty($params['EvidenceSearch']['barcode'])){
				foreach($params['EvidenceSearch']['barcode'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['EvidenceSearch']['barcode']);break;
					}
					if($v=='(not set)'){
						$params['EvidenceSearch']['barcode'][$k]='';
					}/*else if(strpos($v,",") !== false){
						unset($params['EvidenceSearch']['barcode']);
					}*/
				}
			}
			$query->andFilterWhere(['in','barcode',$params['EvidenceSearch']['barcode']]);
		//	$params['EvidenceSearch']['barcode']=$barcode_data;
		}if ($params['EvidenceSearch']['barcode'] != null && !is_array($params['EvidenceSearch']['barcode'])) {
			$query->andFilterWhere(['like','barcode',$params['EvidenceSearch']['barcode']]);
		}if ($params['EvidenceSearch']['evid_type'] != null && is_array ($params['EvidenceSearch']['evid_type'])) {
			if(!empty($params['EvidenceSearch']['evid_type'])){
				foreach($params['EvidenceSearch']['evid_type'] as $k=>$v){
					if($v=='All'){
						unset($params['EvidenceSearch']['evid_type']);break;
					}
				}
			}
		}if ($params['EvidenceSearch']['cat_id'] != null && is_array ($params['EvidenceSearch']['cat_id'])) {
			if(!empty($params['EvidenceSearch']['cat_id'])){
				foreach($params['EvidenceSearch']['cat_id'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['EvidenceSearch']['cat_id']);break;
					}
				}
			}
		}if ($params['EvidenceSearch']['received_from'] != null && is_array ($params['EvidenceSearch']['received_from'])) {
			if(!empty($params['EvidenceSearch']['received_from'])){
				foreach($params['EvidenceSearch']['received_from'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['EvidenceSearch']['received_from']);break;
					}
				}
			}
		}if ($params['EvidenceSearch']['received_time'] != null && is_array ($params['EvidenceSearch']['received_time'])) {
			if(!empty($params['EvidenceSearch']['received_time'])){
				foreach($params['EvidenceSearch']['received_time'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['EvidenceSearch']['received_time']);break;
					}
				}
			}
		}if ($params['EvidenceSearch']['serial'] != null && is_array ($params['EvidenceSearch']['serial'])) {
			if(!empty($params['EvidenceSearch']['serial'])){
				foreach($params['EvidenceSearch']['serial'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['EvidenceSearch']['serial']);break;
					}
				}
			}
		}if ($params['EvidenceSearch']['model'] != null && is_array ($params['EvidenceSearch']['model'])) {
			if(!empty($params['EvidenceSearch']['model'])){
				foreach($params['EvidenceSearch']['model'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['EvidenceSearch']['model']);break;
					}
				}
			}
		}if ($params['EvidenceSearch']['quantity'] != null && is_array ($params['EvidenceSearch']['quantity'])) {
			if(!empty($params['EvidenceSearch']['quantity'])){
				foreach($params['EvidenceSearch']['quantity'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['EvidenceSearch']['quantity']);break;
					}
				}
			}
			$query->andFilterWhere(['in','quantity',$params['EvidenceSearch']['quantity']]);
		}if ($params['EvidenceSearch']['contents_total_size'] != null && is_array ($params['EvidenceSearch']['contents_total_size'])) {
			if(!empty($params['EvidenceSearch']['contents_total_size'])){
				foreach($params['EvidenceSearch']['contents_total_size'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['EvidenceSearch']['contents_total_size']);break;
					}
				}
			}
		}if ($params['EvidenceSearch']['contents_total_size_comp'] != null && is_array ($params['EvidenceSearch']['contents_total_size_comp'])) {
			if(!empty($params['EvidenceSearch']['contents_total_size_comp'])){
				foreach($params['EvidenceSearch']['contents_total_size_comp'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['EvidenceSearch']['contents_total_size_comp']);break;
					}
				}
			}
		}if ($params['EvidenceSearch']['status'] != null && is_array ($params['EvidenceSearch']['status'])) {
			if(!empty($params['EvidenceSearch']['status'])){
				foreach($params['EvidenceSearch']['status'] as $k=>$v){
					if($v=='All'){// || strpos($v,",") !== false
						unset($params['EvidenceSearch']['status']);break;
					}
				}
			}
		}if ($params['EvidenceSearch']['client_id'] != null && is_array ($params['EvidenceSearch']['client_id'])) {
			if(!empty($params['EvidenceSearch']['client_id'])){
				foreach($params['EvidenceSearch']['client_id'] as $k=>$v){
					if($v == 'All'){ //  || strpos($v,",") !== false
						unset($params['EvidenceSearch']['client_id']);break;
					}
				}
			}
		}if ($params['EvidenceSearch']['client_case_id'] != null && is_array ($params['EvidenceSearch']['client_case_id'])) {
			if(!empty($params['EvidenceSearch']['client_case_id'])){
				foreach($params['EvidenceSearch']['client_case_id'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['EvidenceSearch']['client_case_id']);break;
					}
				}
			}
		}if ($params['EvidenceSearch']['unit'] != null && is_array ($params['EvidenceSearch']['unit'])) {
			if(!empty($params['EvidenceSearch']['unit'])){
				foreach($params['EvidenceSearch']['unit'] as $k=>$v){
					if($v=='All'){ // || strpos($v,",") !== false
						unset($params['EvidenceSearch']['unit']);break;
					}
				}
			}
		}if ($params['EvidenceSearch']['comp_unit'] != null && is_array ($params['EvidenceSearch']['comp_unit'])) {
			if(!empty($params['EvidenceSearch']['comp_unit'])){
				foreach($params['EvidenceSearch']['comp_unit'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['EvidenceSearch']['comp_unit']);break;
					}
				}
			}
		}if ($params['EvidenceSearch']['contents_copied_to'] != null && is_array ($params['EvidenceSearch']['contents_copied_to'])) {
			if(!empty($params['EvidenceSearch']['contents_copied_to'])){
				foreach($params['EvidenceSearch']['contents_copied_to'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['EvidenceSearch']['contents_copied_to']);break;
					}
				}
			}
		}if ($params['EvidenceSearch']['mpw'] != null && is_array ($params['EvidenceSearch']['mpw'])) {
			if(!empty($params['EvidenceSearch']['mpw'])){
				foreach($params['EvidenceSearch']['mpw'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['EvidenceSearch']['mpw']);break;
					}
				}
			}
		}if ($params['EvidenceSearch']['bbates'] != null && is_array ($params['EvidenceSearch']['bbates'])) {
			if(!empty($params['EvidenceSearch']['bbates'])){
				foreach($params['EvidenceSearch']['bbates'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['EvidenceSearch']['bbates']);break;
					}
				}
			}
		}if ($params['EvidenceSearch']['ebates'] != null && is_array ($params['EvidenceSearch']['ebates'])) {
			if(!empty($params['EvidenceSearch']['ebates'])){
				foreach($params['EvidenceSearch']['ebates'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['EvidenceSearch']['ebates']);break;
					}
				}
			}
		}if ($params['EvidenceSearch']['m_vol'] != null && is_array ($params['EvidenceSearch']['m_vol'])) {
			if(!empty($params['EvidenceSearch']['m_vol'])){
				foreach($params['EvidenceSearch']['m_vol'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['EvidenceSearch']['m_vol']);break;
					}
				}
			}
		}if ($params['EvidenceSearch']['ftpun'] != null && is_array ($params['EvidenceSearch']['ftpun'])) {
			if(!empty($params['EvidenceSearch']['ftpun'])){
				foreach($params['EvidenceSearch']['ftpun'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['EvidenceSearch']['ftpun']);break;
					}
				}
			}
		}if ($params['EvidenceSearch']['ftppw'] != null && is_array ($params['EvidenceSearch']['ftppw'])) {
			if(!empty($params['EvidenceSearch']['ftppw'])){
				foreach($params['EvidenceSearch']['ftppw'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['EvidenceSearch']['ftppw']);break;
					}
				}
			}
		}if ($params['EvidenceSearch']['enctype'] != null && is_array ($params['EvidenceSearch']['enctype'])) {
			if(!empty($params['EvidenceSearch']['enctype'])){
				foreach($params['EvidenceSearch']['enctype'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['EvidenceSearch']['enctype']);break;
					}
				}
			}
		}if ($params['EvidenceSearch']['encpw'] != null && is_array ($params['EvidenceSearch']['encpw'])) {
			if(!empty($params['EvidenceSearch']['encpw'])){
				foreach($params['EvidenceSearch']['encpw'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['EvidenceSearch']['encpw']);break;
					}
				}
			}
		}if ($params['EvidenceSearch']['evid_stored_location'] != null && is_array ($params['EvidenceSearch']['evid_stored_location'])) {
			if(!empty($params['EvidenceSearch']['evid_stored_location'])){
				foreach($params['EvidenceSearch']['evid_stored_location'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['EvidenceSearch']['evid_stored_location']);break;
					}
				}
			}
		}if ($params['EvidenceSearch']['has_contents'] != null && is_array ($params['EvidenceSearch']['has_contents'])) {
			if(!empty($params['EvidenceSearch']['has_contents'])){
				foreach($params['EvidenceSearch']['has_contents'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['EvidenceSearch']['has_contents']);break;
					}
				}
			}
		}if ($params['EvidenceSearch']['hash'] != null && is_array ($params['EvidenceSearch']['hash'])) {
			if(!empty($params['EvidenceSearch']['hash'])){
				foreach($params['EvidenceSearch']['hash'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['EvidenceSearch']['hash']);break;
					}
				}
			}
		}if ($params['EvidenceSearch']['evd_Internal_no'] != null && is_array ($params['EvidenceSearch']['evd_Internal_no'])) {
			if(!empty($params['EvidenceSearch']['evd_Internal_no'])){
				foreach($params['EvidenceSearch']['evd_Internal_no'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['EvidenceSearch']['evd_Internal_no']);break;
					}
				}
			}
		}if ($params['EvidenceSearch']['other_evid_num'] != null && is_array ($params['EvidenceSearch']['other_evid_num'])) {
			if(!empty($params['EvidenceSearch']['other_evid_num'])){
				foreach($params['EvidenceSearch']['other_evid_num'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['EvidenceSearch']['other_evid_num']);break;
					}
				}
			}
		}if ($params['EvidenceSearch']['dup_evid'] != null && is_array ($params['EvidenceSearch']['dup_evid'])) {
			if(!empty($params['EvidenceSearch']['dup_evid'])){
				foreach($params['EvidenceSearch']['dup_evid'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['EvidenceSearch']['dup_evid']);break;
					}
				}
			}
		}if ($params['EvidenceSearch']['org_link'] != null && is_array ($params['EvidenceSearch']['org_link'])) {
			if(!empty($params['EvidenceSearch']['org_link'])){
				foreach($params['EvidenceSearch']['org_link'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['EvidenceSearch']['org_link']);break;
					}
				}
			}
		}if ($params['EvidenceSearch']['cont'] != null && is_array ($params['EvidenceSearch']['cont'])) {
			if(!empty($params['EvidenceSearch']['cont'])){
				foreach($params['EvidenceSearch']['cont'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['EvidenceSearch']['cont']);break;
					}
				}
			}
		}if ($params['EvidenceSearch']['created_by'] != null && is_array ($params['EvidenceSearch']['created_by'])) {
			if(!empty($params['EvidenceSearch']['created_by'])){
				foreach($params['EvidenceSearch']['created_by'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($params['EvidenceSearch']['created_by']);break;
					}
				}
			}
		}

		$query->andFilterWhere([
			'cont'=>$params['EvidenceSearch']['cont'],
			'org_link'=>$params['EvidenceSearch']['org_link'],
			'dup_evid'=>$params['EvidenceSearch']['dup_evid'],
			'other_evid_num'=>$params['EvidenceSearch']['other_evid_num'],
			'hash' => $params['EvidenceSearch']['hash'],
			'evd_Internal_no' => $params['EvidenceSearch']['evd_Internal_no'],
			'contents_copied_to' => $params['EvidenceSearch']['contents_copied_to'],
			'mpw' => $params['EvidenceSearch']['mpw'],
			'bbates' => $params['EvidenceSearch']['bbates'],
			'ebates' => $params['EvidenceSearch']['ebates'],
			'm_vol' => $params['EvidenceSearch']['m_vol'],
			'ftpun' => $params['EvidenceSearch']['ftpun'],
			'ftppw' => $params['EvidenceSearch']['ftppw'],
			'enctype' => $params['EvidenceSearch']['enctype'],
			'encpw' => $params['EvidenceSearch']['encpw'],
			'evid_stored_location' => $params['EvidenceSearch']['evid_stored_location'],
			'has_contents' => $params['EvidenceSearch']['has_contents'],
			'contents_total_size'=> $params['EvidenceSearch']['contents_total_size'],
			'contents_total_size_comp'=> $params['EvidenceSearch']['contents_total_size_comp'],
		]);
		/*multiselect*/

		if(isset($params['EvidenceSearch']['status']) && $params['EvidenceSearch']['status'] !=''){
			$query->andFilterWhere(['tbl_evidence.status'=>$params['EvidenceSearch']['status']]);
		}else{
			if($is_return == ""){
				$query->andFilterWhere(['<>','tbl_evidence.status', 5]);
			}
		}
		/*$query->andFilterWhere([
            //'status' => $params['EvidenceSearch']['status'],
			//'in','tbl_evidence.id',$params['EvidenceSearch']['id'],
            //'barcode' => $params['EvidenceSearch']['barcode'],
            //"$where_date_query" => $received_date,
            //'evid_type' => $params['EvidenceSearch']['evid_type'],
            //'cat_id' => $params['EvidenceSearch']['cat_id'],
            //'quantity' => $params['EvidenceSearch']['quantity'],
            ]);
            */

        if(isset($params['EvidenceSearch']['contents_total_size']) && is_array($params['EvidenceSearch']['contents_total_size'])){
        	$where_contents_total_size="";
        	foreach($params['EvidenceSearch']['contents_total_size'] as $contentstotalsize){
				$contents_total_size = preg_replace("/[^0-9]/","",$contentstotalsize);
				$unit_name  = preg_replace("/[^a-zA-Z]/","",($contentstotalsize));
				if($where_contents_total_size==""){
					$where_contents_total_size="(contents_total_size = ".$contents_total_size.")";
					//$where_contents_total_size="(contents_total_size = ".$contents_total_size." AND tbl_unit.unit_name = '".$unit_name."')";
				}else{
					$where_contents_total_size.=" OR (contents_total_size = ".$contents_total_size.")";
					//$where_contents_total_size.=" OR (contents_total_size = ".$contents_total_size." AND tbl_unit.unit_name = '".$unit_name."')";
				}
			}
			if($where_contents_total_size!=''){
				$query->andWhere("(".$where_contents_total_size.")");
			}
        }
        if(isset($params['EvidenceSearch']['unit']) && is_array($params['EvidenceSearch']['unit'])){
        	$where_unit="";
        	foreach($params['EvidenceSearch']['unit'] as $contentstotalsize){
				$contents_total_size = preg_replace("/[^0-9]/","",$contentstotalsize);
				$unit_name  = preg_replace("/[^a-zA-Z]/","",($contentstotalsize));
				if($where_unit==""){
					$where_unit="(tbl_unit.unit_name = '".$unit_name."')";
					// $where_contents_total_size="(contents_total_size = ".$contents_total_size." AND tbl_unit.unit_name = '".$unit_name."')";
				}else{
					$where_unit.=" OR (tbl_unit.unit_name = '".$unit_name."')";
					// $where_contents_total_size.=" OR (contents_total_size = ".$contents_total_size." AND tbl_unit.unit_name = '".$unit_name."')";
				}
			}
			if($where_unit!=''){
				$query->andWhere("(".$where_unit.")");
			}
        }

        if(isset($params['EvidenceSearch']['contents_total_size_comp']) && is_array($params['EvidenceSearch']['contents_total_size_comp'])){
        	$where_contents_total_size_comp="";
        	foreach($params['EvidenceSearch']['contents_total_size_comp'] as $contentstotalsizecomp) {
				$contents_total_size_comp = preg_replace("/[^0-9]/","",$contentstotalsizecomp);
				$unit_name  = preg_replace("/[^a-zA-Z]/","",($contentstotalsizecomp));
				if($where_contents_total_size_comp=="")
					$where_contents_total_size_comp="(contents_total_size_comp = ".$contents_total_size_comp.")";
				else
					$where_contents_total_size_comp.=" OR (contents_total_size_comp = ".$contents_total_size_comp.")";
			}
        	if($where_contents_total_size_comp!='') {
				$query->andWhere("(".$where_contents_total_size_comp.")");
			}
        }

        if(isset($params['EvidenceSearch']['comp_unit']) && is_array($params['EvidenceSearch']['comp_unit'])){
        	$where_comp_unit="";
        	foreach($params['EvidenceSearch']['comp_unit'] as $contentstotalsizecomp){
				$contents_total_size_comp = preg_replace("/[^0-9]/","",$contentstotalsizecomp);
				$unit_name  = preg_replace("/[^a-zA-Z]/","",($contentstotalsizecomp));
				if($where_comp_unit=="")
					$where_comp_unit="(evidcomp.unit_name = '".$unit_name."')";
				else
					$where_comp_unit.=" OR (evidcomp.unit_name = '".$unit_name."')";
			}
        	if($where_comp_unit!='') {
				$query->andWhere("(".$where_comp_unit.")");
			}
        }
        //'contents_total_size' => ,
        //'contents_total_size_comp' => $params['EvidenceSearch']['contents_total_size_comp'],

        if(isset($params['EvidenceSearch']['created_by']) && is_array($params['EvidenceSearch']['created_by'])){
        	$query->joinWith(['user' => function(\yii\db\ActiveQuery $query) use($params){
        		$query->select(['tbl_user.id',"CONCAT(tbl_user.usr_first_name,' ',tbl_user.usr_lastname) full_name"]);
        		$query->andFilterWhere(['or like', "CONCAT(tbl_user.usr_first_name,' ',tbl_user.usr_lastname)", $params['EvidenceSearch']['created_by']]);
        	}]);
        }
		if($params['EvidenceSearch']['received_time'] != ''){
			//echo "<pre>",print_r($params['EvidenceSearch']['received_time']);
			/*$received_time=array();
			if(!empty($params['EvidenceSearch']['received_time'])){
				foreach($params['EvidenceSearch']['received_time'] as $ert){
				$received_time[]=(new Options)->ConvertOneTzToAnotherTz($ert,  $_SESSION['usrTZ'],'UTC','time');
				}
			}
			if(!empty($received_time))
				$params['EvidenceSearch']['received_time']=$received_time;*/

			//echo "<pre>",print_r($params['EvidenceSearch']['received_time']);die;
			//$params['EvidenceSearch']['received_time']=(new Options)->ConvertOneTzToAnotherTz($params['EvidenceSearch']['received_time'],  $_SESSION['usrTZ'],'UTC','time');
			//$query->andWhere($where_datetime_query , $params['EvidenceSearch']['received_time']]);
			if(is_array($params['EvidenceSearch']['received_time'])){
				$query->andWhere(" $where_datetime_query IN  ('" . implode("', '", $params['EvidenceSearch']['received_time']) . "') ");
			}else{
				$query->andFilterWhere(['like', $where_datetime_query, $params['EvidenceSearch']['received_time']]);
			}
		}


        $query->andFilterWhere(['tbl_evidence.serial'=>$params['EvidenceSearch']['serial']]);
        $query->andFilterWhere(['tbl_evidence.model'=>$params['EvidenceSearch']['model']]);
        $query->andFilterWhere(['like', 'evid_desc', $params['EvidenceSearch']['evid_desc']])
              ->andFilterWhere(['like', 'tbl_evidence.evid_notes' , $params['EvidenceSearch']['evid_notes']])
              ->andFilterWhere(['or like', 'tbl_evidence.received_from' , $params['EvidenceSearch']['received_from']])
              ->andFilterWhere(['like', 'tbl_evidence.evid_label_desc' , $params['EvidenceSearch']['evid_label_desc']])
              ->andFilterWhere(['or like', 'tbl_evidence_type.evidence_name' , $params['EvidenceSearch']['evid_type']])
              ->andFilterWhere(['or like', 'tbl_evidence_category.category' , $params['EvidenceSearch']['cat_id']]);
        $this->load($params);
        // if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            // return $dataProvider;
        // }
		//echo "<pre>",print_r($params);die;
        if(isset($params['client_case_id']) && $params['client_case_id']!=0){
			$clientcasevid_sql = "SELECT evid_num_id FROM tbl_client_case_evidence WHERE client_case_id IN (".$params['client_case_id'].") Group By evid_num_id";
        	$query->andWhere('tbl_evidence.id IN ('.$clientcasevid_sql.')');
        }
		if(isset($params['client_case_id']) && $params['client_case_id']==0){
			$clientcasevid_sql = "SELECT evid_num_id FROM tbl_client_case_evidence Group By evid_num_id";
        	$query->andWhere('tbl_evidence.id NOT IN ('.$clientcasevid_sql.')');
		}
        if(isset($params['EvidenceSearch']['client_case_id']) && !empty($params['EvidenceSearch']['client_case_id'])) {
        	$clientcasevid_sql = "SELECT evid_num_id FROM tbl_client_case_evidence WHERE client_case_id IN (".implode(',',$params['EvidenceSearch']['client_case_id']).") Group By evid_num_id";
        	$query->andWhere('tbl_evidence.id IN ('.$clientcasevid_sql.')');
        }
         if(isset($params['EvidenceSearch']['client_id']) && !empty($params['EvidenceSearch']['client_id'])) {
			$clientcasevid_sql = "SELECT evid_num_id
			FROM tbl_client_case_evidence
			WHERE client_id IN (".implode(',',$params['EvidenceSearch']['client_id']).") Group By evid_num_id";
        	$query->andWhere('tbl_evidence.id IN ('.$clientcasevid_sql.')');
        }
        if($is_return == "Y") {
        	$query->andWhere(['tbl_evidence.status'=>5]);
        	$query->orderBy(null);
        	return $query->count();
        }
        //echo "<pre>",print_r($query),"</pre>";die;
        return $dataProvider;
    }
    public function searchFilter($params)
    {
		$roleId = Yii::$app->user->identity->role_id; // Role Id
		$userId = Yii::$app->user->identity->id;
		$role_type = explode(',',Role::findOne($roleId)->role_type);
		$media_params = $params['params'];
//		echo "<pre>";print_r($params);die;
    	$dataProvider = array();
		//$query = EvidenceCustodians::find()->joinWith('clientCaseCustodians clientcasecust')->joinWith('clientCaseEvidence clientcaseevid')->where('clientcasecust.cust_id !=0 AND  clientcasecust.client_case_id=' . $case_id.' OR clientcaseevid.cust_id !=0 AND clientcaseevid.client_case_id=' . $case_id);
		//$query = Evidence::find()->orderBy(['tbl_evidence.id'=>SORT_DESC])


		$query = Evidence::find()->joinWith(['evidencetype','evidencecategory','evidenceunit','evidencecompunit'])
			->joinWith(['evidenceclientcase' => function(\yii\db\ActiveQuery $query) use($userId, $roleId,$role_type,$params){
				$query->joinWith(['clientcase' => function(\yii\db\ActiveQuery $query) use($userId, $roleId,$role_type, $params){
					if($roleId!=0 && $role_type[0]!=2){
						if(isset($params['client_case_id']) && $params['client_case_id']==0){}else{
						$query->joinWith(['projectSecurity' => function(\yii\db\ActiveQuery $query) use ($userId){
							$query->where(['tbl_project_security.user_id' => $userId]);
						}]);
					 }
					}
				}]);
			}])->limit(100);

		//echo "<pre>",print_r($query->asArray()->all()),"</pre>"; die;
        if(isset($params['id']) && $params['id']!=''){
			$query->andWhere(['tbl_evidence.id' => $params['id']]);
		}
		if(isset($params['q']) && $params['q']=="undefined"){
			$params['q']='';
		}
		/*Filters*/
	/*	 $received_date="";
        if($media_params['EvidenceSearch']['received_date'] != ''){
            $received_date=explode("-",$media_params['EvidenceSearch']['received_date']);
            $received_date_start=explode("/",trim($received_date[0]));
            $received_date_end=explode("/",trim($received_date[1]));
            $received_date_s=$received_date_start[2]."-".$received_date_start[0]."-".$received_date_start[1];
            $received_date_e=$received_date_end[2]."-".$received_date_end[0]."-".$received_date_end[1];
            $query->andWhere(" $where_date_query >= '$received_date_s' AND $where_date_query  <= '$received_date_e' ");
        }
        if(isset($media_params['EvidenceSearch']['status']) && $media_params['EvidenceSearch']['status'] !=''){
			$query->andFilterWhere(['tbl_evidence.status'=>$media_params['EvidenceSearch']['status']]);
		}else{*/
			if($is_return == ""){
				$query->andFilterWhere(['<>','tbl_evidence.status', 5]);
			}
		//}



        /*multiselect*/
        if ($media_params['EvidenceSearch']['id'] != null && is_array($media_params['EvidenceSearch']['id'])) {
			if(!empty($media_params['EvidenceSearch']['id'])){
				foreach($media_params['EvidenceSearch']['id'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($media_params['EvidenceSearch']['id']);break;
					}
				}
			}
	//		$query->andFilterWhere(['in','tbl_evidence.id',$media_params['EvidenceSearch']['id']]);
		}if ($media_params['EvidenceSearch']['barcode'] != null && is_array($media_params['EvidenceSearch']['barcode'])) {
			$barcode_data=$media_params['EvidenceSearch']['barcode'];
			if(!empty($media_params['EvidenceSearch']['barcode'])){
				foreach($media_params['EvidenceSearch']['barcode'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($media_params['EvidenceSearch']['barcode']);break;
					}
					if($v=='(not set)'){
						$media_params['EvidenceSearch']['barcode'][$k]='';
					}/*else if(strpos($v,",") !== false){
						unset($params['EvidenceSearch']['barcode']);
					}*/
				}
			}
		//	$query->andFilterWhere(['in','barcode',$media_params['EvidenceSearch']['barcode']]);
	//		$media_params['EvidenceSearch']['barcode']=$barcode_data;
		}if ($media_params['EvidenceSearch']['evid_type'] != null && is_array ($media_params['EvidenceSearch']['evid_type'])) {
			if(!empty($media_params['EvidenceSearch']['evid_type'])){
				foreach($media_params['EvidenceSearch']['evid_type'] as $k=>$v){
					if($v=='All'){
						unset($media_params['EvidenceSearch']['evid_type']);break;
					}
				}
			}
		}if ($media_params['EvidenceSearch']['cat_id'] != null && is_array ($media_params['EvidenceSearch']['cat_id'])) {
			if(!empty($media_params['EvidenceSearch']['cat_id'])){
				foreach($media_params['EvidenceSearch']['cat_id'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($media_params['EvidenceSearch']['cat_id']);break;
					}
				}
			}
		}if ($media_params['EvidenceSearch']['received_from'] != null && is_array ($media_params['EvidenceSearch']['received_from'])) {
			if(!empty($media_params['EvidenceSearch']['received_from'])){
				foreach($media_params['EvidenceSearch']['received_from'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($media_params['EvidenceSearch']['received_from']);break;
					}
				}
			}
		}if ($media_params['EvidenceSearch']['received_time'] != null && is_array ($media_params['EvidenceSearch']['received_time'])) {
			if(!empty($media_params['EvidenceSearch']['received_time'])){
				foreach($media_params['EvidenceSearch']['received_time'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($media_params['EvidenceSearch']['received_time']);break;
					}
				}
			}
		}if ($media_params['EvidenceSearch']['serial'] != null && is_array ($media_params['EvidenceSearch']['serial'])) {
			if(!empty($media_params['EvidenceSearch']['serial'])){
				foreach($media_params['EvidenceSearch']['serial'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($media_params['EvidenceSearch']['serial']);break;
					}
				}
			}
		}if ($media_params['EvidenceSearch']['model'] != null && is_array ($media_params['EvidenceSearch']['model'])) {
			if(!empty($media_params['EvidenceSearch']['model'])){
				foreach($media_params['EvidenceSearch']['model'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($media_params['EvidenceSearch']['model']);break;
					}
				}
			}
		}if ($media_params['EvidenceSearch']['quantity'] != null && is_array ($media_params['EvidenceSearch']['quantity'])) {
			if(!empty($media_params['EvidenceSearch']['quantity'])){
				foreach($media_params['EvidenceSearch']['quantity'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($media_params['EvidenceSearch']['quantity']);break;
					}
				}
			}
		//	$query->andFilterWhere(['in','quantity',$media_params['EvidenceSearch']['quantity']]);
		}if ($media_params['EvidenceSearch']['contents_total_size'] != null && is_array ($media_params['EvidenceSearch']['contents_total_size'])) {
			if(!empty($media_params['EvidenceSearch']['contents_total_size'])){
				foreach($media_params['EvidenceSearch']['contents_total_size'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($media_params['EvidenceSearch']['contents_total_size']);break;
					}
				}
			}
		}if ($media_params['EvidenceSearch']['contents_total_size_comp'] != null && is_array ($media_params['EvidenceSearch']['contents_total_size_comp'])) {
			if(!empty($media_params['EvidenceSearch']['contents_total_size_comp'])){
				foreach($media_params['EvidenceSearch']['contents_total_size_comp'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($media_params['EvidenceSearch']['contents_total_size_comp']);break;
					}
				}
			}
		}if ($media_params['EvidenceSearch']['status'] != null && is_array ($media_params['EvidenceSearch']['status'])) {
			if(!empty($media_params['EvidenceSearch']['status'])){
				foreach($media_params['EvidenceSearch']['status'] as $k=>$v){
					if($v=='All'){// || strpos($v,",") !== false
						unset($media_params['EvidenceSearch']['status']);break;
					}
				}
			}
		}if ($media_params['EvidenceSearch']['client_id'] != null && is_array ($media_params['EvidenceSearch']['client_id'])) {
			if(!empty($media_params['EvidenceSearch']['client_id'])){
				foreach($media_params['EvidenceSearch']['client_id'] as $k=>$v){
					if($v == 'All'){ //  || strpos($v,",") !== false
						unset($media_params['EvidenceSearch']['client_id']);break;
					}
				}
			}
		}if ($media_params['EvidenceSearch']['client_case_id'] != null && is_array ($media_params['EvidenceSearch']['client_case_id'])) {
			if(!empty($media_params['EvidenceSearch']['client_case_id'])){
				foreach($media_params['EvidenceSearch']['client_case_id'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($media_params['EvidenceSearch']['client_case_id']);break;
					}
				}
			}
		}if ($media_params['EvidenceSearch']['unit'] != null && is_array ($media_params['EvidenceSearch']['unit'])) {
			if(!empty($media_params['EvidenceSearch']['unit'])){
				foreach($media_params['EvidenceSearch']['unit'] as $k=>$v){
					if($v=='All'){ // || strpos($v,",") !== false
						unset($media_params['EvidenceSearch']['unit']);break;
					}
				}
			}
		}if ($media_params['EvidenceSearch']['comp_unit'] != null && is_array ($media_params['EvidenceSearch']['comp_unit'])) {
			if(!empty($media_params['EvidenceSearch']['comp_unit'])){
				foreach($media_params['EvidenceSearch']['comp_unit'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($media_params['EvidenceSearch']['comp_unit']);break;
					}
				}
			}
		}if ($media_params['EvidenceSearch']['contents_copied_to'] != null && is_array ($media_params['EvidenceSearch']['contents_copied_to'])) {
			if(!empty($media_params['EvidenceSearch']['contents_copied_to'])){
				foreach($media_params['EvidenceSearch']['contents_copied_to'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($media_params['EvidenceSearch']['contents_copied_to']);break;
					}
				}
			}
		}if ($media_params['EvidenceSearch']['mpw'] != null && is_array ($media_params['EvidenceSearch']['mpw'])) {
			if(!empty($media_params['EvidenceSearch']['mpw'])){
				foreach($media_params['EvidenceSearch']['mpw'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($media_params['EvidenceSearch']['mpw']);break;
					}
				}
			}
		}if ($media_params['EvidenceSearch']['bbates'] != null && is_array ($media_params['EvidenceSearch']['bbates'])) {
			if(!empty($media_params['EvidenceSearch']['bbates'])){
				foreach($media_params['EvidenceSearch']['bbates'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($media_params['EvidenceSearch']['bbates']);break;
					}
				}
			}
		}if ($media_params['EvidenceSearch']['ebates'] != null && is_array ($media_params['EvidenceSearch']['ebates'])) {
			if(!empty($media_params['EvidenceSearch']['ebates'])){
				foreach($media_params['EvidenceSearch']['ebates'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($media_params['EvidenceSearch']['ebates']);break;
					}
				}
			}
		}if ($media_params['EvidenceSearch']['m_vol'] != null && is_array ($media_params['EvidenceSearch']['m_vol'])) {
			if(!empty($media_params['EvidenceSearch']['m_vol'])){
				foreach($media_params['EvidenceSearch']['m_vol'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($media_params['EvidenceSearch']['m_vol']);break;
					}
				}
			}
		}if ($media_params['EvidenceSearch']['ftpun'] != null && is_array ($media_params['EvidenceSearch']['ftpun'])) {
			if(!empty($media_params['EvidenceSearch']['ftpun'])){
				foreach($media_params['EvidenceSearch']['ftpun'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($media_params['EvidenceSearch']['ftpun']);break;
					}
				}
			}
		}if ($media_params['EvidenceSearch']['ftppw'] != null && is_array ($media_params['EvidenceSearch']['ftppw'])) {
			if(!empty($media_params['EvidenceSearch']['ftppw'])){
				foreach($media_params['EvidenceSearch']['ftppw'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($media_params['EvidenceSearch']['ftppw']);break;
					}
				}
			}
		}if ($media_params['EvidenceSearch']['enctype'] != null && is_array ($media_params['EvidenceSearch']['enctype'])) {
			if(!empty($media_params['EvidenceSearch']['enctype'])){
				foreach($media_params['EvidenceSearch']['enctype'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($media_params['EvidenceSearch']['enctype']);break;
					}
				}
			}
		}if ($media_params['EvidenceSearch']['encpw'] != null && is_array ($media_params['EvidenceSearch']['encpw'])) {
			if(!empty($media_params['EvidenceSearch']['encpw'])){
				foreach($media_params['EvidenceSearch']['encpw'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($media_params['EvidenceSearch']['encpw']);break;
					}
				}
			}
		}if ($media_params['EvidenceSearch']['evid_stored_location'] != null && is_array ($media_params['EvidenceSearch']['evid_stored_location'])) {
			if(!empty($media_params['EvidenceSearch']['evid_stored_location'])){
				foreach($media_params['EvidenceSearch']['evid_stored_location'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($media_params['EvidenceSearch']['evid_stored_location']);break;
					}
				}
			}
		}if ($media_params['EvidenceSearch']['has_contents'] != null && is_array ($media_params['EvidenceSearch']['has_contents'])) {
			if(!empty($media_params['EvidenceSearch']['has_contents'])){
				foreach($media_params['EvidenceSearch']['has_contents'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($media_params['EvidenceSearch']['has_contents']);break;
					}
				}
			}
		}if ($media_params['EvidenceSearch']['hash'] != null && is_array ($media_params['EvidenceSearch']['hash'])) {
			if(!empty($media_params['EvidenceSearch']['hash'])){
				foreach($media_params['EvidenceSearch']['hash'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($media_params['EvidenceSearch']['hash']);break;
					}
				}
			}
		}if ($media_params['EvidenceSearch']['evd_Internal_no'] != null && is_array ($media_params['EvidenceSearch']['evd_Internal_no'])) {
			if(!empty($media_params['EvidenceSearch']['evd_Internal_no'])){
				foreach($media_params['EvidenceSearch']['evd_Internal_no'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($media_params['EvidenceSearch']['evd_Internal_no']);break;
					}
				}
			}
		}if ($media_params['EvidenceSearch']['other_evid_num'] != null && is_array ($media_params['EvidenceSearch']['other_evid_num'])) {
			if(!empty($media_params['EvidenceSearch']['other_evid_num'])){
				foreach($media_params['EvidenceSearch']['other_evid_num'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($media_params['EvidenceSearch']['other_evid_num']);break;
					}
				}
			}
		}if ($media_params['EvidenceSearch']['dup_evid'] != null && is_array ($media_params['EvidenceSearch']['dup_evid'])) {
			if(!empty($media_params['EvidenceSearch']['dup_evid'])){
				foreach($media_params['EvidenceSearch']['dup_evid'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($media_params['EvidenceSearch']['dup_evid']);break;
					}
				}
			}
		}if ($media_params['EvidenceSearch']['org_link'] != null && is_array ($media_params['EvidenceSearch']['org_link'])) {
			if(!empty($media_params['EvidenceSearch']['org_link'])){
				foreach($media_params['EvidenceSearch']['org_link'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($media_params['EvidenceSearch']['org_link']);break;
					}
				}
			}
		}if ($media_params['EvidenceSearch']['cont'] != null && is_array ($media_params['EvidenceSearch']['cont'])) {
			if(!empty($media_params['EvidenceSearch']['cont'])){
				foreach($media_params['EvidenceSearch']['cont'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($media_params['EvidenceSearch']['cont']);break;
					}
				}
			}
		}if ($media_params['EvidenceSearch']['created_by'] != null && is_array ($media_params['EvidenceSearch']['created_by'])) {
			if(!empty($media_params['EvidenceSearch']['created_by'])){
				foreach($media_params['EvidenceSearch']['created_by'] as $k=>$v){
					if($v=='All'){ //  || strpos($v,",") !== false
						unset($media_params['EvidenceSearch']['created_by']);break;
					}
				}
			}
		}

		$query->andFilterWhere([
			'cont'=>$media_params['EvidenceSearch']['cont'],
			'org_link'=>$media_params['EvidenceSearch']['org_link'],
			'dup_evid'=>$media_params['EvidenceSearch']['dup_evid'],
			'other_evid_num'=>$media_params['EvidenceSearch']['other_evid_num'],
			'hash' => $media_params['EvidenceSearch']['hash'],
			'evd_Internal_no' => $media_params['EvidenceSearch']['evd_Internal_no'],
			'contents_copied_to' => $media_params['EvidenceSearch']['contents_copied_to'],
			'mpw' => $media_params['EvidenceSearch']['mpw'],
			'bbates' => $media_params['EvidenceSearch']['bbates'],
			'ebates' => $media_params['EvidenceSearch']['ebates'],
			'm_vol' => $media_params['EvidenceSearch']['m_vol'],
			'ftpun' => $media_params['EvidenceSearch']['ftpun'],
			'ftppw' => $media_params['EvidenceSearch']['ftppw'],
			'enctype' => $media_params['EvidenceSearch']['enctype'],
			'encpw' => $media_params['EvidenceSearch']['encpw'],
			'evid_stored_location' => $media_params['EvidenceSearch']['evid_stored_location'],
			'has_contents' => $media_params['EvidenceSearch']['has_contents'],
		]);
		/*multiselect*/

    /*    if(isset($media_params['EvidenceSearch']['contents_total_size']) && is_array($media_params['EvidenceSearch']['contents_total_size'])){
        	$where_contents_total_size="";
        	foreach($media_params['EvidenceSearch']['contents_total_size'] as $contentstotalsize){
				$contents_total_size = preg_replace("/[^0-9]/","",$contentstotalsize);
				$unit_name  = preg_replace("/[^a-zA-Z]/","",($contentstotalsize));
				if($where_contents_total_size==""){
					$where_contents_total_size="(contents_total_size = ".$contents_total_size.")";
					//$where_contents_total_size="(contents_total_size = ".$contents_total_size." AND tbl_unit.unit_name = '".$unit_name."')";
				}else{
					$where_contents_total_size.=" OR (contents_total_size = ".$contents_total_size.")";
					//$where_contents_total_size.=" OR (contents_total_size = ".$contents_total_size." AND tbl_unit.unit_name = '".$unit_name."')";
				}
			}
			if($where_contents_total_size!=''){
				$query->andWhere("(".$where_contents_total_size.")");
			}
        }
        if(isset($media_params['EvidenceSearch']['unit']) && is_array($media_params['EvidenceSearch']['unit'])){
        	$where_unit="";
        	foreach($media_params['EvidenceSearch']['unit'] as $contentstotalsize){
				$contents_total_size = preg_replace("/[^0-9]/","",$contentstotalsize);
				$unit_name  = preg_replace("/[^a-zA-Z]/","",($contentstotalsize));
				if($where_unit==""){
					$where_unit="(tbl_unit.unit_name = '".$unit_name."')";
					// $where_contents_total_size="(contents_total_size = ".$contents_total_size." AND tbl_unit.unit_name = '".$unit_name."')";
				}else{
					$where_unit.=" OR (tbl_unit.unit_name = '".$unit_name."')";
					// $where_contents_total_size.=" OR (contents_total_size = ".$contents_total_size." AND tbl_unit.unit_name = '".$unit_name."')";
				}
			}
			if($where_unit!=''){
				$query->andWhere("(".$where_unit.")");
			}
        }

        if(isset($media_params['EvidenceSearch']['contents_total_size_comp']) && is_array($media_params['EvidenceSearch']['contents_total_size_comp'])){
        	$where_contents_total_size_comp="";
        	foreach($params['EvidenceSearch']['contents_total_size_comp'] as $contentstotalsizecomp) {
				$contents_total_size_comp = preg_replace("/[^0-9]/","",$contentstotalsizecomp);
				$unit_name  = preg_replace("/[^a-zA-Z]/","",($contentstotalsizecomp));
				if($where_contents_total_size_comp=="")
					$where_contents_total_size_comp="(contents_total_size_comp = ".$contents_total_size_comp.")";
				else
					$where_contents_total_size_comp.=" OR (contents_total_size_comp = ".$contents_total_size_comp.")";
			}
        	if($where_contents_total_size_comp!='') {
				$query->andWhere("(".$where_contents_total_size_comp.")");
			}
        }

        if(isset($media_params['EvidenceSearch']['comp_unit']) && is_array($media_params['EvidenceSearch']['comp_unit'])){
        	$where_comp_unit="";
        	foreach($media_params['EvidenceSearch']['comp_unit'] as $contentstotalsizecomp){
				$contents_total_size_comp = preg_replace("/[^0-9]/","",$contentstotalsizecomp);
				$unit_name  = preg_replace("/[^a-zA-Z]/","",($contentstotalsizecomp));
				if($where_comp_unit=="")
					$where_comp_unit="(evidcomp.unit_name = '".$unit_name."')";
				else
					$where_comp_unit.=" OR (evidcomp.unit_name = '".$unit_name."')";
			}
        	if($where_comp_unit!='') {
				$query->andWhere("(".$where_comp_unit.")");
			}
        }
        //'contents_total_size' => ,
        //'contents_total_size_comp' => $params['EvidenceSearch']['contents_total_size_comp'],

        if(isset($media_params['EvidenceSearch']['created_by']) && is_array($media_params['EvidenceSearch']['created_by'])){
        	$query->joinWith(['user' => function(\yii\db\ActiveQuery $query) use($media_params){
        		$query->select(['tbl_user.id',"CONCAT(tbl_user.usr_first_name,' ',tbl_user.usr_lastname) full_name"]);
        		$query->andFilterWhere(['or like', "CONCAT(tbl_user.usr_first_name,' ',tbl_user.usr_lastname)", $media_params['EvidenceSearch']['created_by']]);
        	}]);
        }

        $query->andFilterWhere(['tbl_evidence.serial'=>$media_params['EvidenceSearch']['serial']]);
        $query->andFilterWhere(['tbl_evidence.model'=>$media_params['EvidenceSearch']['model']]);
        $query->andFilterWhere(['like', 'evid_desc', $media_params['EvidenceSearch']['evid_desc']])
              ->andFilterWhere(['like', 'tbl_evidence.evid_notes' , $media_params['EvidenceSearch']['evid_notes']])
              ->andFilterWhere(['or like', 'tbl_evidence.received_from' , $media_params['EvidenceSearch']['received_from']])
              ->andFilterWhere(['or like', 'tbl_evidence.received_time' , $media_params['EvidenceSearch']['received_time']])
              ->andFilterWhere(['like', 'tbl_evidence.evid_label_desc' , $media_params['EvidenceSearch']['evid_label_desc']])
              ->andFilterWhere(['or like', 'tbl_evidence_type.evidence_name' , $media_params['EvidenceSearch']['evid_type']])
              ->andFilterWhere(['or like', 'tbl_evidence_category.category' , $media_params['EvidenceSearch']['cat_id']]);*/


					if(isset($media_params['client_case_id']) && $media_params['client_case_id']!=0){
						$clientcasevid_sql = "SELECT evid_num_id FROM tbl_client_case_evidence WHERE client_case_id IN (".$media_params['client_case_id'].") Group By evid_num_id";
			        	$query->andWhere('tbl_evidence.id IN ('.$clientcasevid_sql.')');
			        }
					if(isset($media_params['client_case_id']) && $media_params['client_case_id']==0){
						$clientcasevid_sql = "SELECT evid_num_id FROM tbl_client_case_evidence Group By evid_num_id";
			        	$query->andWhere('tbl_evidence.id NOT IN ('.$clientcasevid_sql.')');
					}


      /*  if(isset($media_params['EvidenceSearch']['client_case_id']) && !empty($media_params['EvidenceSearch']['client_case_id'])) {
        	$clientcasevid_sql = "SELECT evid_num_id FROM tbl_client_case_evidence WHERE client_case_id IN (".implode(',',$media_params['EvidenceSearch']['client_case_id']).") Group By evid_num_id";
        	$query->andWhere('tbl_evidence.id IN ('.$clientcasevid_sql.')');
        }
         if(isset($media_params['EvidenceSearch']['client_id']) && !empty($media_params['EvidenceSearch']['client_id'])) {
			$clientcasevid_sql = "SELECT evid_num_id
			FROM tbl_client_case_evidence
			WHERE client_id IN (".implode(',',$media_params['EvidenceSearch']['client_id']).") Group By evid_num_id";
        	$query->andWhere('tbl_evidence.id IN ('.$clientcasevid_sql.')');
        }*/
		/*Filters*/

		if($params['field']=='id'){
    		$query->select(['tbl_evidence.id']);
			$query->distinct(true);
    		if(isset($params['q']) && $params['q']!=""){
	    		$query->andFilterWhere(['like','tbl_evidence.id', $params['q'].'%',false]);
	    		$query->orderBy('tbl_evidence.id');
    		}//$start = microtime(true);
			$query->limit(-1);
    		$dataProvider = ArrayHelper::map($query->all(),'id','id');
    		//$time_elapsed_secs = microtime(true) - $start;
    		//echo "<pre>",print_r($dataProvider),"</prE>".$time_elapsed_secs;die;
    	}if($params['field']=='barcode'){
    		$query->select(['barcode']);
    		if(isset($params['q']) && $params['q']!=""){$query->andFilterWhere(['like','barcode',$params['q']]);
			}
    		$query->groupBy('barcode');
    		$query->orderBy('barcode');
    		$dataProvider = array('All'=>'All') + ArrayHelper::map($query->all(),'barcode','barcode');

    	}if($params['field']=='evid_type' ){
    		$query->select(['tbl_evidence_type.evidence_name'])->joinWith(['evidencetype'],false);

    		if(isset($params['q']) && $params['q']!=""){$query->andFilterWhere(['like','tbl_evidence_type.evidence_name', $params['q']]);}
    		$query->groupBy('tbl_evidence_type.evidence_name');
    		$query->orderBy('tbl_evidence_type.evidence_name');
    		$dataProvider = ArrayHelper::map($query->all(),'evidence_name','evidence_name');
    	}
    	if($params['field']=='cat_id' ){
    		$query->select(['cat_id'])->joinWith(['evidencecategory']);
    		if(isset($params['q']) && $params['q']!=""){
    			$query->andFilterWhere(['like','tbl_evidence_category.category', $params['q']]);
    		}
    		$query->groupBy('cat_id');
    		$query->orderBy(null);
    		$query->limit(-1);
    		$dataProvider = ArrayHelper::map(EvidenceCategory::find()->select('category')->where(['in', 'id', $query])->all(),'category','category');
    	}
    	if($params['field']=='quantity'){
    		$query->select(['quantity']);
    		if(isset($params['q']) && $params['q']!=""){$query->andFilterWhere(['like','quantity',$params['q']]);}
    		$query->groupBy('quantity');
    		$query->orderBy('quantity');
    		$dataProvider = ArrayHelper::map($query->all(),'quantity','quantity');
    	}
    	if($params['field']=='evid_desc'){
    		$query->select(['evid_desc']);
    		if(isset($params['q']) && $params['q']!=""){$query->andFilterWhere(['like','evid_desc',$params['q']]);}
    		$query->groupBy('evid_desc');
    		$query->orderBy('evid_desc');
    		$dataProvider = ArrayHelper::map($query->all(),'evid_desc','evid_desc');
    	}
    	if($params['field']=='contents_copied_to'){
    		$query->select(['contents_copied_to']);
    		if(isset($params['q']) && $params['q']!=""){$query->andFilterWhere(['like','contents_copied_to',$params['q']]);}
    		$query->groupBy('contents_copied_to');
    		$query->orderBy('contents_copied_to');
    		$dataProvider = ArrayHelper::map($query->all(),'contents_copied_to','contents_copied_to');
    	}
    	if($params['field']=='mpw'){
    		$query->select(['mpw']);
    		if(isset($params['q']) && $params['q']!=""){$query->andFilterWhere(['like','mpw',$params['q']]);}
    		$query->groupBy('mpw');
    		$query->orderBy('mpw');
    		$dataProvider = ArrayHelper::map($query->all(),'mpw','mpw');
    	}
    	if($params['field']=='bbates'){
    		$query->select(['bbates']);
    		if(isset($params['q']) && $params['q']!=""){$query->andFilterWhere(['like','bbates',$params['q']]);}
    		$query->groupBy('bbates');
    		$query->orderBy('bbates');
    		$dataProvider = ArrayHelper::map($query->all(),'bbates','bbates');
    	}
    	if($params['field']=='ebates'){
    		$query->select(['ebates']);
    		if(isset($params['q']) && $params['q']!=""){$query->andFilterWhere(['like','ebates',$params['q']]);}
    		$query->groupBy('ebates');
    		$query->orderBy('ebates');
    		$dataProvider = ArrayHelper::map($query->all(),'ebates','ebates');
    	}
    	if($params['field']=='m_vol'){
    		$query->select(['m_vol']);
    		if(isset($params['q']) && $params['q']!=""){$query->andFilterWhere(['like','m_vol',$params['q']]);}
    		$query->groupBy('m_vol');
    		$query->orderBy('m_vol');
    		$dataProvider = ArrayHelper::map($query->all(),'m_vol','m_vol');
    	}
    	if($params['field']=='ftpun'){
    		$query->select(['ftpun']);
    		if(isset($params['q']) && $params['q']!=""){$query->andFilterWhere(['like','ftpun',$params['q']]);}
    		$query->groupBy('ftpun');
    		$query->orderBy('ftpun');
    		$dataProvider = ArrayHelper::map($query->all(),'ftpun','ftpun');
    	}
    	if($params['field']=='ftppw'){
    		$query->select(['ftppw']);
    		if(isset($params['q']) && $params['q']!=""){$query->andFilterWhere(['like','ftppw',$params['q']]);}
    		$query->groupBy('ftppw');
    		$query->orderBy('ftppw');
    		$dataProvider = ArrayHelper::map($query->all(),'ftppw','ftppw');
    	}
    	if($params['field']=='enctype'){
    		$query->select(['enctype']);
    		if(isset($params['q']) && $params['q']!=""){$query->andFilterWhere(['like','enctype',$params['q']]);}
    		$query->groupBy('enctype');
    		$query->orderBy('enctype');
    		$dataProvider = ArrayHelper::map($query->all(),'enctype','enctype');
    	}
    	if($params['field']=='encpw'){
    		$query->select(['encpw']);
    		if(isset($params['q']) && $params['q']!=""){$query->andFilterWhere(['like','encpw',$params['q']]);}
    		$query->groupBy('encpw');
    		$query->orderBy('encpw');
    		$dataProvider = ArrayHelper::map($query->all(),'encpw','encpw');
    	}
    	if($params['field']=='evid_stored_location'){
    		$query->select(['evid_stored_location']);
    		if(isset($params['q']) && $params['q']!=""){$query->andFilterWhere(['like','evid_stored_location',$params['q']]);}
    		$query->groupBy('evid_stored_location');
    		$query->orderBy('evid_stored_location');
    		$dataProvider = ArrayHelper::map($query->all(),'evid_stored_location','evid_stored_location');
    	}
    	if($params['field']=='has_contents'){
    		$query->select(['has_contents']);
    		if(isset($params['q']) && $params['q']!=""){$query->andFilterWhere(['like','has_contents',$params['q']]);}
    		$query->groupBy('has_contents');
    		$query->orderBy('has_contents');
    		$dataProvider = ArrayHelper::map($query->all(),'has_contents','has_contents');
    	}
    	if($params['field']=='received_from'){
    		$query->select(['received_from']);
    		if(isset($params['q']) && $params['q']!=""){$query->andFilterWhere(['like','received_from',$params['q']]);}
    		$query->groupBy('received_from');
    		$query->orderBy('received_from');
    		$dataProvider = ArrayHelper::map($query->all(),'received_from','received_from');
    	}
    	if($params['field']=='received_time'){
			$timezoneOffset = (new Options)->getOffsetOfCurrenttimeZone();
			if (Yii::$app->db->driverName == 'mysql') {
				$data_query = "DATE_FORMAT(CONVERT_TZ(CONCAT(tbl_evidence.`received_date` , ' ', STR_TO_DATE(tbl_evidence.`received_time` , '%l:%i %p' )),'+00:00','{$timezoneOffset}'), '%Y-%m-%d %H:%i')";
				$where_date_query = "DATE_FORMAT(CONVERT_TZ(CONCAT(tbl_evidence.`received_date` , ' ', STR_TO_DATE(tbl_evidence.`received_time` , '%l:%i %p' )),'+00:00','{$timezoneOffset}'), '%Y-%m-%d')";
				$where_datetime_query = "DATE_FORMAT(CONVERT_TZ(CONCAT(tbl_evidence.`received_date` , ' ', STR_TO_DATE(tbl_evidence.`received_time` , '%l:%i %p' )),'+00:00','{$timezoneOffset}'), '%h:%i %p')";
				$query->select([$where_datetime_query.' as received_time']);
				$query->groupBy('received_time');
    			$query->orderBy('received_time');
			}else{
				$data_query = "CAST(switchoffset(todatetimeoffset(Cast((CAST(tbl_evidence.received_date as varchar)  + ' ' + tbl_evidence.received_time) as datetime), '+00:00'), '{$timezoneOffset}') as datetime)";
				$where_date_query = "CAST(switchoffset(todatetimeoffset(Cast((CAST(tbl_evidence.received_date as varchar)  + ' ' + tbl_evidence.received_time) as datetime), '+00:00'), '{$timezoneOffset}') as date)";
				$where_datetime_query = "right(convert(varchar(32),CAST(switchoffset(todatetimeoffset(Cast((CAST(tbl_evidence.received_date as varchar)  + ' ' + tbl_evidence.received_time) as datetime), '+00:00'), '{$timezoneOffset}') as datetime),100),8)";
				$query->select([$where_datetime_query.' as received_time']);
				$query->groupBy('received_time,received_date');
    			$query->orderBy('received_time,received_date');
			}

    		if(isset($params['q']) && $params['q']!=""){$query->andFilterWhere(['like','received_time',$params['q']]);}
    		//$query->groupBy('received_time');
    		//$query->orderBy('received_time');
    		$dataProvider = ArrayHelper::map($query->all(),'received_time','received_time');
    	}
    	if($params['field']=='serial'){
    		$query->select(['serial']);
    		if(isset($params['q']) && $params['q']!=""){$query->andFilterWhere(['like','serial',$params['q']]);}
    		$query->groupBy('serial');
    		$query->orderBy('serial');
    		$dataProvider = ArrayHelper::map($query->all(),'serial','serial');
    	}
    	if($params['field']=='model'){
    		$query->select(['model']);
    		if(isset($params['q']) && $params['q']!=""){$query->andFilterWhere(['like','model',$params['q']]);}
    		$query->groupBy('model');
    		$query->orderBy('model');
    		$dataProvider = ArrayHelper::map($query->all(),'model','model');
    	}
    	if($params['field']=='hash'){
    		$query->select(['hash']);
    		if(isset($params['q']) && $params['q']!=""){$query->andFilterWhere(['like','hash',$params['q']]);}
    		$query->groupBy('hash');
    		$query->orderBy('hash');
    		$dataProvider = ArrayHelper::map($query->all(),'hash','hash');
    	}
    	if($params['field']=='evd_Internal_no'){
    		$query->select(['evd_Internal_no']);
    		if(isset($params['q']) && $params['q']!=""){$query->andFilterWhere(['like','evd_Internal_no',$params['q']]);}
    		$query->groupBy('evd_Internal_no');
    		$query->orderBy('evd_Internal_no');
    		$dataProvider = ArrayHelper::map($query->all(),'evd_Internal_no','evd_Internal_no');
    	}
    	if($params['field']=='other_evid_num'){
    		$query->select(['other_evid_num']);
    		if(isset($params['q']) && $params['q']!=""){$query->andFilterWhere(['like','other_evid_num',$params['q']]);}
    		$query->groupBy('other_evid_num');
    		$query->orderBy('other_evid_num');
    		$dataProvider = ArrayHelper::map($query->all(),'other_evid_num','other_evid_num');
    	}
    	if($params['field']=='dup_evid'){
    		$query->select(['dup_evid']);
    		if(isset($params['q']) && $params['q']!=""){$query->andFilterWhere(['like','dup_evid',$params['q']]);}
    		$query->groupBy('dup_evid');
    		$query->orderBy('dup_evid');
    		$dataProvider = ArrayHelper::map($query->all(),'dup_evid','dup_evid');
    	}if($params['field']=='org_link'){
    		$query->select(['org_link']);
    		if(isset($params['q']) && $params['q']!=""){$query->andFilterWhere(['like','org_link',$params['q']]);}
    		$query->groupBy('org_link');
    		$query->orderBy('org_link');
    		$dataProvider = ArrayHelper::map($query->all(),'org_link','org_link');
    	}if($params['field']=='cont'){
    		$query->select(['cont']);
    		if(isset($params['q']) && $params['q']!=""){$query->andFilterWhere(['like','cont',$params['q']]);}
    		$query->groupBy('cont');
    		$query->orderBy('cont');
    		$dataProvider = ArrayHelper::map($query->all(),'cont','cont');
    	}


    	if($params['field']=='contents_total_size' ){
    		$query->select(["contents_total_size"])->joinWith(['evidenceunit'],false)->where("contents_total_size!=''");
    		if(isset($params['q']) && $params['q']!=""){$query->andFilterWhere(['like','contents_total_size',$params['q']]);}
    		$query->groupBy(['contents_total_size']);
    		$query->orderBy('contents_total_size');
    		$dataProvider = ArrayHelper::map($query->all(),function ($model){
				return $model->contents_total_size;
				//return $model->contents_total_size." ".$model->evidenceunit->unit_name;
    		},function ($model){
				return $model->contents_total_size;
				//return $model->contents_total_size." ".$model->evidenceunit->unit_name;
    		});
    	}
    	if($params['field']=='unit' ){
    		$query->select(["unit"])->joinWith(['evidenceunit'],false)->where("unit!=''");
    		if(isset($params['q']) && $params['q']!=""){$query->andFilterWhere(['like','tbl_unit.unit_name',$params['q']]);}
    		$query->groupBy(['unit']);
    		$query->orderBy('unit');
    		$dataProvider = ArrayHelper::map($query->all(),function ($model){
				return $model->evidenceunit->unit_name;
				//return $model->contents_total_size." ".$model->evidenceunit->unit_name;
    		},function ($model){
				return $model->evidenceunit->unit_name;
				//return $model->contents_total_size." ".$model->evidenceunit->unit_name;
    		});
    	}
    	if($params['field']=='contents_total_size_comp'){
    		$query->select(['contents_total_size_comp'])->joinWith(['evidencecompunit'],false)->where("contents_total_size_comp!=''");
    		if(isset($params['q']) && $params['q']!=""){$query->andFilterWhere(['like','contents_total_size_comp',$params['q']]);}
    		$query->groupBy(['contents_total_size_comp']);
    		$query->orderBy('contents_total_size_comp');
    		$dataProvider = ArrayHelper::map($query->all(),function ($model){
				return $model->contents_total_size_comp;
    		},function ($model){
				return $model->contents_total_size_comp;
    		});
    	}
    	if($params['field']=='comp_unit'){
    		$query->select(['comp_unit'])->joinWith(['evidencecompunit'],false)->where("comp_unit!=''");
    		if(isset($params['q']) && $params['q']!=""){$query->andFilterWhere(['like','evidcomp.unit_name',$params['q']]);}
    		$query->groupBy(['comp_unit']);
    		$query->orderBy('comp_unit');
    		$dataProvider = ArrayHelper::map($query->all(),function ($model){
				return $model->evidencecompunit->unit_name;
    		},function ($model){
				return $model->evidencecompunit->unit_name;
    		});
    	}

    	if($params['field']=='client_id'){
				$query = Client::find()->innerJoinWith(['clientCaseEvidences'])->groupBy('tbl_client.id,client_name')->orderBy(['tbl_client.id'=> SORT_DESC])->limit(100);
				if($roleId!=0){
					$query->joinWith(['clientcase' => function(\yii\db\ActiveQuery $query) use($userId, $roleId,$role_type){
						if($roleId!=0){
							$query->joinWith(['projectSecurity' => function(\yii\db\ActiveQuery $query) use ($userId){
								$query->where(['tbl_project_security.user_id' => $userId]);
							}], false);
						}
					}], false);
				}

				$query->groupBy('tbl_client.id,tbl_client.client_name')->orderBy(['tbl_client.client_name'=> SORT_ASC]);
				$query->select(['tbl_client.id','tbl_client.client_name']);

          /*  $query = Client::find()
				->innerJoinWith(['clientCaseEvidences' => function(\yii\db\ActiveQuery $query) use($userId, $roleId,$role_type){
					$query->joinWith(['clientcase' => function(\yii\db\ActiveQuery $query) use($userId, $roleId,$role_type){
						if($roleId!=0){
							$query->joinWith(['projectSecurity' => function(\yii\db\ActiveQuery $query) use ($userId){
								$query->where(['tbl_project_security.user_id' => $userId]);
							}], false);
						}
					}], false);
				}])->groupBy('tbl_client.id,client_name')->orderBy(['tbl_client.client_name'=> SORT_ASC])->limit(100);
			//$query->groupBy('tbl_client.id,client_name')->orderBy(['tbl_client.id'=> SORT_DESC]);
			$query->select(['tbl_client.id','client_name']);*/
			if(isset($params['q']) && $params['q']!=""){$query->andFilterWhere(['like','client_name',$params['q']]);}
			$dataProvider = array('All'=>'All') + ArrayHelper::map($query->all(),'id','client_name');
			return $dataProvider;
		}

		if($params['field']=='client_case_id'){
                    $query = ClientCase::find()->innerJoinWith(['clientCaseEvidences'])->groupBy('tbl_client_case.id,case_name')->orderBy(['tbl_client_case.id'=> SORT_DESC])->limit(100);
                    if($roleId!=0){
                            $query->joinWith(['projectSecurity' => function(\yii\db\ActiveQuery $query) use ($userId){
                                    $query->where(['tbl_project_security.user_id' => $userId]);
                            }],false);
                    }

                    $query->groupBy('tbl_client_case.id,tbl_client_case.case_name')->orderBy(['tbl_client_case.case_name'=> SORT_ASC]);
                    $query->select(['tbl_client_case.id','tbl_client_case.case_name']);
//					if(isset($params['EvidenceSearch']['client_id']) && !empty($params['EvidenceSearch']['client_id'])) {
//						$clientcasevid_sql = "SELECT tbl_client.id
//						FROM tbl_client
//						WHERE tbl_client.id IN (".implode(',',$params['EvidenceSearch']['client_id']).")";
//						$query->andWhere('tbl_client_case.client_id IN ('.$clientcasevid_sql.')');
//					}
                    if(isset($params['q']) && $params['q']!=""){
                        $query->andFilterWhere(['like','case_name',$params['q']]);
                    }
                    $dataProvider = array('All'=>'All') + ArrayHelper::map($query->all(),'id','case_name');
                    return $dataProvider;
		}

		if($params['field']=='created_by'){
			//$query = ClientCase::find()->innerJoinWith(['clientCaseEvidences'])->orderBy(['tbl_client_case.id'=> SORT_DESC])->limit(100);
			$query->select(['tbl_evidence.created_by']);
			$query->distinct = true;
			$query->limit(-1);
			$query->joinWith(['user' => function(\Yii\db\ActiveQuery $query) use($params){
				$query->select(["CONCAT(tbl_user.usr_first_name,' ',tbl_user.usr_lastname) full_name","tbl_user.id"]);
				if(isset($params['q']) && $params['q']!=""){
					$query->andFilterWhere(['like',"CONCAT(tbl_user.usr_first_name,' ',tbl_user.usr_lastname)",$params['q']]);
				}
			}]);
			$dataProvider = array('All'=>'All') + ArrayHelper::map($query->all(), function($model, $default){
				return $model->user->full_name;
			}, function($model, $default){
				return $model->user->full_name;
			});

			return $dataProvider;
		}

		return array_merge($dataProvider);
    }
    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchWithoutFilter($params)
    {
		$query = Evidence::find()->joinWith(['evidencetype','evidencecategory','evidenceunit','evidencecompunit','evidenceattachments'])
                ->orderBy(['tbl_evidence.id'=>SORT_DESC]);
    	$dataProvider = new ActiveDataProvider([
    			'query' => $query,
    			'pagination'=>['pageSize'=>25]
    	]);


    	$this->load($params);
		/* if (!$this->validate()) {
    		// uncomment the following line if you do not want to return any records when validation fails
    		// $query->where('0=1');
    		return $dataProvider;
    	} */

    	return $dataProvider;
    }

}