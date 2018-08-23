<?php

namespace app\models;
use yii\helpers\ArrayHelper;
use Yii;

/**
 * This is the model class for table "{{%client}}".
 *
 * @property integer $id
 * @property string $client_name
 * @property string $description
 * @property integer $industry_id
 * @property string $address1
 * @property string $address2
 * @property string $city
 * @property string $state
 * @property integer $country_id
 * @property string $zip
 * @property string $phone
 * @property string $fax
 * @property string $website
 * @property integer $status
 * @property string $created
 * @property integer $created_by
 * @property string $modified
 * @property integer $modified_by
 *
 * @property CaseContacts[] $caseContacts
 * @property Industry $industry
 * @property Country $country
 * @property ClientCase[] $clientCases
 * @property ClientCase[] $clientCases0
 * @property ClientCase[] $clientCases1
 * @property ClientCaseCustodians[] $clientCaseCustodians
 * @property ClientCaseCustodians[] $clientCaseCustodians0
 * @property ClientCaseEvidence[] $clientCaseEvidences
 * @property ClientContacts[] $clientContacts
 * @property EvidenceProduction[] $evidenceProductions
 * @property EvidenceProduction[] $evidenceProductions0
 * @property EvidenceProduction[] $evidenceProductions1
 * @property EvidenceProduction[] $evidenceProductions2
 * @property InvoiceFinal[] $invoiceFinals
 * @property PricingClients[] $pricingClients
 * @property PricingClientscases[] $pricingClientscases
 * @property PricingTemplates[] $pricingTemplates
 * @property Tasks[] $tasks
 * @property TaxCodeClients[] $taxCodeClients
 */
class Client extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%client}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['client_name'], 'required'],
            [['industry_id', 'country_id', 'status', 'created_by', 'modified_by'], 'integer'],
            [['created', 'modified'], 'safe'],
            [['client_name'], 'string'],
            [['description', 'state'], 'string'],
            [['address1', 'address2', 'city', 'zip'], 'string'],
            [['phone', 'fax'], 'string'],
            [['website'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'client_name' => Yii::t('app', 'Client Name'),
            'description' => Yii::t('app', 'Client Description'),
            'industry_id' => Yii::t('app', 'Client Industry'),
            'address1' => Yii::t('app', 'Address 1'),
            'address2' => Yii::t('app', 'Address 2'),
            'city' => Yii::t('app', 'City/Town'),
            'state' => Yii::t('app', 'State/Province/Region'),
            'country_id' => Yii::t('app', 'Country'),
            'zip' => Yii::t('app', 'Zip/Postal Code'),
            'phone' => Yii::t('app', 'Phone'),
            'fax' => Yii::t('app', 'Fax'),
            'website' => Yii::t('app', 'Website'),
            'status' => Yii::t('app', 'Status'),
            'created' => Yii::t('app', 'Created'),
            'created_by' => Yii::t('app', 'Created By'),
            'modified' => Yii::t('app', 'Modified'),
            'modified_by' => Yii::t('app', 'Modified By'),
        ];
    }

	/**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
    	if (parent::beforeSave($insert)) {
    		
    		if($this->isNewRecord){
    			$this->created = date('Y-m-d H:i:s');
    			$this->created_by =Yii::$app->user->identity->id;
    			$this->modified =date('Y-m-d H:i:s');
    			$this->modified_by =Yii::$app->user->identity->id;
    			$this->status = 1;
    		}else{
    			$this->modified =date('Y-m-d H:i:s');
    			$this->modified_by =Yii::$app->user->identity->id;
    		}
    		// Place your custom code here
    		return true;
    	} else {
    		return false;
    	}
    }
    
    
    public function afterFind()
    {
		
		$this->client_name = html_entity_decode($this->client_name);
		
		return true;
	}
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCaseContacts()
    {
        return $this->hasMany(CaseContacts::className(), ['client_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIndustry()
    {
        return $this->hasOne(Industry::className(), ['id' => 'industry_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCountry()
    {
        return $this->hasOne(Country::className(), ['id' => 'country_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClientCases()
    {
        return $this->hasMany(ClientCase::className(), ['client_id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProjectSecurity()
    {
        return $this->hasMany(ProjectSecurity::className(), ['client_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClientCaseCustodians()
    {
        return $this->hasMany(ClientCaseCustodians::className(), ['client_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClientCaseEvidences()
    {
        return $this->hasMany(ClientCaseEvidence::className(), ['client_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClientContacts()
    {
        return $this->hasMany(ClientContacts::className(), ['client_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvidenceProductions()
    {
        return $this->hasMany(EvidenceProduction::className(), ['client_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoiceFinals()
    {
        return $this->hasMany(InvoiceFinal::className(), ['client_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPricingClients()
    {
        return $this->hasMany(PricingClients::className(), ['clientid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPricingClientscases()
    {
        return $this->hasMany(PricingClientscases::className(), ['clientid' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPricingTemplates()
    {
        return $this->hasMany(PricingTemplates::className(), ['client_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTasks()
    {
        return $this->hasMany(Tasks::className(), ['client_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTaxCodeClients()
    {
        return $this->hasMany(TaxCodeClients::className(), ['client_id' => 'id']);
    }
    
    /**
     * get all cases and client case details of user access manage user module
     * @return
     */
    public function getClientCasesdetails()
    {
    	$clientList= Client:: find()->select(['id','client_name'])->orderBy('client_name ASC')->asArray()->all();
    	$mycases = array();
    	foreach($clientList as $lists){
    		$mycases[$lists['client_name']][$lists['id']] = ClientCase:: find()->select(['id','case_name'])->where('client_id='.$lists['id'])->orderBy('case_name ASC')->asArray()->all();
    	}
    	return $mycases;
    }
    /**
     * get all cases and client case details of user access manage user module
     * @return
     */
    public function getClientCasesdetailsArray()
    {
    	$sql="SELECT tbl_client_case.id,client_id,tbl_client.client_name,case_name FROM tbl_client_case inner join tbl_client on tbl_client.id=client_id order by client_name ASC,case_name ASC" ;
        $clientList =Yii::$app->db->createCommand($sql)->queryAll();
        $mycases=[];
        foreach($clientList as $client_data){
            $mycases[$client_data['client_name']][$client_data['client_id']][$client_data['id']]=$client_data['case_name'];
        }
    	return $mycases;
    }
    public function getClientCasesWithPermissiondetailsArray()
    {
        $roleId = Yii::$app->user->identity->role_id;
    	$sql="SELECT tbl_client_case.id,client_id,tbl_client.client_name,case_name FROM tbl_client_case inner join tbl_client on tbl_client.id=client_id where tbl_client_case.is_close=0 order by client_name ASC,case_name ASC" ;
        if ($roleId != 0) {
            $uid = Yii::$app->user->identity->id;
            $sql="SELECT tbl_client_case.id,client_id,tbl_client.client_name,case_name FROM tbl_client_case inner join tbl_client on tbl_client.id=client_id WHERE tbl_client_case.is_close=0 AND tbl_client_case.id IN (SELECT client_case_id FROM tbl_project_security WHERE user_id=$uid AND client_case_id!=0 AND team_id=0 group by client_case_id)  order by client_name ASC,case_name ASC" ;
        }
        $clientList =Yii::$app->db->createCommand($sql)->queryAll();
        $mycases=[];
        foreach($clientList as $client_data){
            $mycases[$client_data['client_name']][$client_data['client_id']][$client_data['id']]=$client_data['case_name'];
        }
    	return $mycases;
    }
    /*IRT-434 
        Get only Cleints with cases
     */
    public function getCleintsArray(){
         $allClientWithCases_SQL = "SELECT tbl_client_case.client_id,client_name,count(tbl_client_case.client_id) AS total_clients FROM tbl_client Left JOIN tbl_client_case  ON tbl_client_case.client_id = tbl_client.id group BY tbl_client_case.client_id,client_name";         
        $clientList = ArrayHelper::map(Yii::$app->db->createCommand($allClientWithCases_SQL)->queryAll(),'client_id','client_name');
//        $clientList= ArrayHelper::map(Client:: find()->select(['id','client_name'])->orderBy('client_name ASC')->asArray()->all(),'id','client_name');
        return $clientList;        
    }
}
