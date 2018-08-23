<?php
namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%case_contacts}}".
 *
 * @property integer $id
 * @property integer $client_id
 * @property integer $client_case_id
 * @property integer $client_contacts_id
 * @property string $created
 * @property integer $created_by
 * @property string $modified
 * @property integer $modified_by
 */
class CaseContacts extends \yii\db\ActiveRecord
{
	public $contact_type,$lname,$add_1,$notes;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%case_contacts}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['client_id', 'client_case_id', 'client_contacts_id', 'created', 'created_by', 'modified', 'modified_by'], 'required'],
            [['client_id', 'client_case_id', 'client_contacts_id', 'created_by', 'modified_by'], 'integer'],
            [['created', 'modified'], 'safe'],
            [['client_case_id'], 'exist', 'skipOnError' => true, 'targetClass' => ClientCase::className(), 'targetAttribute' => ['client_case_id' => 'id']],
            [['client_contacts_id'], 'exist', 'skipOnError' => true, 'targetClass' => ClientContacts::className(), 'targetAttribute' => ['client_contacts_id' => 'id']],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Client::className(), 'targetAttribute' => ['client_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'client_id' => 'Client ID',
            'client_case_id' => 'Client Case ID',
            'client_contacts_id' => 'Client Contacts ID',
            'created' => 'Created',
            'created_by' => 'Created By',
            'modified' => 'Modified',
            'modified_by' => 'Modified By',
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
    * @To adjust case Contact client, case & contact_id wise[called when client contact updated]
    * $client_id = 5 (int)
    * $client_contact_id = 10 (int)
    * $case_id = 1,2,5,8 (comma seperated string)
    */
    public static function adjustCaseContacts($client_id, $client_contacts_id, $case_id)
    {
    	$date = date('Y-m-d H:i:s');
    	$userid = Yii::$app->user->identity->id;
    	//echo $client_id," - ", $client_contacts_id;
    	$sql1 = "DELETE FROM tbl_case_contacts WHERE client_contacts_id=:client_contacts_id AND client_case_id NOT IN ($case_id)";
		\Yii::$app->db->createCommand($sql1,[':client_contacts_id' => $client_contacts_id ] )->execute();
    	    	
		$sql2 = "INSERT INTO tbl_case_contacts(client_case_id,client_contacts_id,created,created_by,modified,modified_by) SELECT id, $client_contacts_id as client_contacts_id, '$date' as created, $userid as created_by, '$date' as modified, $userid as modified_by FROM tbl_client_case WHERE id IN ($case_id) AND id NOT IN (SELECT client_case_id FROM tbl_case_contacts WHERE client_contacts_id=$client_contacts_id)";
		\Yii::$app->db->createCommand($sql2)->execute();
		
    }
    
	/**
    * @To adjust case Contact client, case & contact_id wise[called when Case Contacts updated]
    * $client_id = 5 (int)
    * $client_contact_id = 10,20,30 (comma seperated string)
    * $case_id = 1 (int)
    */
    public static function adjustCaseContactsByCase($client_id, $client_contacts_id, $case_id)
    {
    	$date = date('Y-m-d H:i:s');
    	$userid = Yii::$app->user->identity->id;
    	
    	if($client_contacts_id!=0)
    	{
	    	$sql1 = "DELETE FROM tbl_case_contacts WHERE client_case_id IN (:case_id) AND client_contacts_id NOT IN ($client_contacts_id)";
			\Yii::$app->db->createCommand($sql1,[':case_id' => $case_id ] )->execute();
	    	
			$sql2 = "INSERT INTO tbl_case_contacts(client_case_id,client_contacts_id,created,created_by,modified,modified_by) SELECT  $case_id as client_case_id, id, '$date' as created, $userid as created_by, '$date' as modified, $userid as modified_by FROM tbl_client_contacts WHERE id IN ($client_contacts_id) AND id NOT IN (SELECT client_contacts_id FROM tbl_case_contacts WHERE client_case_id=$case_id)";
			\Yii::$app->db->createCommand($sql2)->execute();
    	} 
    	else 
    	{
    		$sql1 = "DELETE FROM tbl_case_contacts WHERE client_id=:client_id AND client_case_id IN (:case_id)";
			\Yii::$app->db->createCommand($sql1,[ ':client_id' => $client_id, ':case_id' => $case_id ] )->execute();
    	}
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClientContacts()
    {
    	return $this->hasOne(ClientContacts::className(),['id' => 'client_contacts_id']);
    }
}
