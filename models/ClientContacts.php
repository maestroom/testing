<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%client_contacts}}".
 *
 * @property integer $id
 * @property integer $client_id
 * @property string $contact_type
 * @property string $lname
 * @property string $fname
 * @property string $mi
 * @property string $title
 * @property string $phone_o
 * @property string $phone_m
 * @property string $email
 * @property string $add_1
 * @property string $add_2
 * @property string $city
 * @property string $state
 * @property integer $country_id
 * @property string $zip
 * @property string $notes
 * @property string $created
 * @property integer $created_by
 * @property string $modified
 * @property integer $modified_by
 *
 * @property CaseContacts[] $caseContacts
 * @property Client $client
 */
class ClientContacts extends \yii\db\ActiveRecord
{
	public $fullname,$address;
	public $iscontactexist = 0;
	/**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%client_contacts}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['client_id', 'contact_type', 'fname','lname', 'email', 'city'], 'required'],
            [['client_id', 'country_id', 'created_by', 'modified_by'], 'integer'],
            [['contact_type', 'fname', 'mi', 'title', 'phone_o', 'phone_m', 'email', 'add_1', 'add_2', 'city', 'state', 'zip', 'notes'], 'string'],
            [['email'],'email'],
            [['created', 'modified','fullname'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'client_id' => 'Client',
            'contact_type' => 'Contact Type',
            'lname' => 'Last name',
            'fname' => 'First name',
            'fullname' => 'Contact Name',
            'mi' => 'MI',
            'title' => 'Title',
            'phone_o' => 'Phone Office',
            'phone_m' => 'Phone Mobile',
            'email' => 'Email',
            'add_1' => 'Contact Address',
            'add_2' => 'Address2',
            'city' => 'City/Town',
            'state' => 'State/Province/Region',
            'country_id' => 'Country',
            'zip' => 'Zip/Postal Code',
            'notes' => 'Notes',
            'created' => 'Created',
            'created_by' => 'Created By',
            'modified' => 'Modified',
            'modified_by' => 'Modified By',
            'iscontactexist' => 'Is Contact Exist?'
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
    
    /**
     * @return full name by concating lastname, firstname middlename. 
     */
    public function getfullname(){
    	return $this->lname . ', ' . $this->fname. ' ' . $this->mi;
    }    
    
    /**
     * @return full address by concating Add1, Add2, city, country & zip. 
     */
    public function displaycontactaddress(){
    
    	$address = array();
    	if($this->add_1!=''){
    		$address[] = $this->add_1;
    	}
    	if($this->add_2!=''){
    		$address[] = $this->add_2;
    	}
    	if($this->city!=''){
    		$address[] = $this->city;
    	}
    	if($this->country_id!='' && $this->country_id!=0){
    		$address[] = Country::findOne($this->country_id)->country_name;
    	}
    	if($this->zip!=''){
    		$address[] = $this->zip;
    	}
    	return implode(", ",$address);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCaseContacts()
    {
        return $this->hasMany(CaseContacts::className(), ['client_contacts_id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClient()
    {
        return $this->hasOne(Client::className(), ['id' => 'client_id']);
    }
    
    public function getCountry(){
		return $this->hasOne(Country::className(),['id' => 'country_id']);
	}
	/**
    * @To remove client contact & case contact by ((client_id) or (client_id, case_id)) & contact_id wise[called when client contact deleted]
    * $client_id = 5 (int)
    * $client_contact_id = 10,20,23 (string)
    * $case_id = 1,2,5,8 (comma seperated string)
    */
    public static function removeClientCaseContacts($client_id, $client_contacts_id, $case_id=0, $from='Client')
    {
    	$whereclientids = '';
    	if($client_id != '' && $client_id != 0)
    	{
    		$whereclientids = ' AND client_id='.$client_id;
    	}
    
    	$wherecaseids = '';
    	if($case_id != '' && $case_id != 0)
    	{
    		$wherecaseids = ' AND client_case_id NOT IN ('.$case_id.')';
    	}
    	
    	$sql1 = "DELETE FROM tbl_case_contacts WHERE client_contacts_id IN ($client_contacts_id)".$wherecaseids;
    	
		\Yii::$app->db->createCommand($sql1)->execute();
		
		if($from == 'Client')
		{
			$sql2 = "DELETE FROM tbl_client_contacts WHERE id IN ($client_contacts_id)".$whereclientids;
			\Yii::$app->db->createCommand($sql2)->execute();
		}
    }
    
    /**
     * @return mixed
     */
	public function checkContactExist()
    {
        return intval($this->iscontactexist);
    }
}
