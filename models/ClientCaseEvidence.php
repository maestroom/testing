<?php
namespace app\models;

use Yii;
use app\models\Client;
use app\models\ClientCase;
use yii\helpers\ArrayHelper;
/**
 * This is the model class for table "{{%client_case_evidence}}".
 *
 * @property integer $id
 * @property integer $client_case_id
 * @property integer $evid_num_id
 * @property integer $cust_id
 */
class ClientCaseEvidence extends \yii\db\ActiveRecord
{
	public $custodianname='';
	public $evidnum=0;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%client_case_evidence}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['client_case_id', 'evid_num_id', 'cust_id','client_id'], 'required'],
            [['client_case_id', 'evid_num_id', 'cust_id','client_id'], 'integer'],
            [['client_case_id'], 'exist', 'skipOnError' => true, 'targetClass' => ClientCase::className(), 'targetAttribute' => ['client_case_id' => 'id']],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Client::className(), 'targetAttribute' => ['client_id' => 'id']],
            [['evid_num_id'], 'exist', 'skipOnError' => true, 'targetClass' => Evidence::className(), 'targetAttribute' => ['evid_num_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'client_case_id' => 'Client Case ID',
            'evid_num_id' => 'Evid Num ID',
            'cust_id' => 'Cust ID',
        	'custodianname' => 'Custodian Name',
        	'evidnum' => 'Evidence Count By Custodian'
        ];
    } 
    
    /**
     * @inheritdoc
     */
    public function getEvidenceCustodians()
    {
        return $this->hasMany(EvidenceCustodians::className(), ['cust_id' => 'cust_id']);
    }
    /**
     * @return \yii\db\ActiveQuery
    
    public function getClient()
    {
        return $this->hasOne(Client::className(), ['id' => 'client_id']);
    }
     */
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClientcase()
    {
        return $this->hasOne(ClientCase::className(), ['id' => 'client_case_id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvidence()
    {
        return $this->hasOne(Evidence::className(), ['id' => 'evid_num_id']);
    }
    /*
     * @return All Clients names associated with Evidence Id
     * */
     public function getEvidenceClients($media_id){
		$client_names = '';
		$ClientList = ArrayHelper::map(Client::find()->select('client_name')->where("id IN (select client_id from tbl_client_case_evidence where evid_num_id = {$media_id})")->all(),'client_name','client_name');		
		if(!empty($ClientList)){
			$client_names = implode('; ',$ClientList);
		}
		return $client_names; 
	 }
    /*
     * @return All Cases names associated with Evidence Id
     * */
     public function getEvidenceCases($media_id){
		$client_names = '';
		$CasetList = ArrayHelper::map(ClientCase::find()->select('case_name')->where("id IN (select client_case_id from tbl_client_case_evidence where evid_num_id = {$media_id})")->all(),'case_name','case_name');		
		if(!empty($CasetList)){
			$case_names = implode('; ',$CasetList);
		}
		return $case_names; 
	 }
}
