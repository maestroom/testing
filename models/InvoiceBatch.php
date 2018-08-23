<?php

namespace app\models;

use Yii;
use app\models\User;
use app\models\Options;
use app\models\ProjectSecurity;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%invoice_batch}}".
 *
 * @property integer $id
 * @property string $datefrom
 * @property string $dateto
 * @property integer $display_by
 * @property string $display_invoice
 * @property string $created
 * @property integer $created_by
 * @property string $modified
 * @property integer $modified_by
 *
 * @property InvoiceBatchClientCase[] $invoiceBatchClientCases
 * @property InvoiceBatchTeams[] $invoiceBatchTeams
 */
class InvoiceBatch extends \yii\db\ActiveRecord
{
    public $modified_user='';
    public $created_user='';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%invoice_batch}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['datefrom', 'dateto', 'display_by'], 'required'],
            [['datefrom', 'dateto', 'created', 'modified','modified_user','created_user'], 'safe'],
            [['display_by', 'created_by', 'modified_by'], 'integer'],
            [['display_invoice'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'datefrom' => 'Datefrom',
            'dateto' => 'Dateto',
            'display_by' => '(1=>itemized,2=>consolidated)',
            'display_invoice' => 'Display Invoice',
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
     * @return \yii\db\ActiveQuery
     */
    public function getInvoiceBatchClientCases()
    {
        return $this->hasMany(InvoiceBatchClientCase::className(), ['invoice_batch_id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreateUser()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }
    
     /**
     * @return \yii\db\ActiveQuery
     */
    public function getModifiedUser()
    {
        return $this->hasOne(User::className(), ['id' => 'modified_by'])->alias('modifieduser');
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoiceBatchTeams()
    {
        return $this->hasMany(InvoiceBatchTeams::className(), ['invoice_batch_id' => 'id']);
    }
    
    /* get task due date and time according to user's timezone settings */
    public function getInvoicedate($dt)
    {
		$str = explode("-",$dt); return $str[1].'/'.$str[2].'/'.$str[0];
    }
    
     /* get task due date and time according to user's timezone settings */
    public function getInvoicedatedefault($dt)
    {
		return $result = (new Options)->ConvertOneTzToAnotherTz($dt, 'UTC', $_SESSION['usrTZ'],'MDY');
    }
    
    /**
     * get dispaly invoice details
     * @return mixed
     */
     public function getDisplayInvoiceDetails($id,$invoice){
		 // tbl invoice batch client
		 $select = 'SELECT client_case_id FROM tbl_invoice_batch_client_case WHERE invoice_batch_id = '.$id;
		 $batch = ArrayHelper::map(\Yii::$app->db->createCommand($select)->queryAll(),'client_case_id','client_case_id');
		 // case data
         if(!isset($_SESSION['Casedata1'])){
    	    $_SESSION['Casedata1'] = (new ProjectSecurity)->getCaseSecurityData();
         }
         $Casedata1=$_SESSION['Casedata1'];
		 $client_data_case = array();
		 foreach ($Casedata1 as $ccase){ 
            if(isset($ccase['client_case_id']) && $ccase['client_case_id']!="" && $ccase['client_case_id']!=0 && $ccase['clientCase'][0]['is_close']==0){
				foreach($batch as $value){
					if($value==$ccase['client_case_id'])
						$client_data_case[$value] = $ccase['client_name'] . '-' . $ccase['case_name']; //$client_data_case[$value] = $ccase['client_name'] . '-' . $ccase['clientCase'][0]['case_name'];
				}
            }
    	 }
    	 $res = implode("\n",$client_data_case); // convert array to string
    	 $html = '<a href="javascript:void(0);"><span title="'.$res.'">'.$invoice.'</span></a>'; // make a href link
		 return $html;
	 }
}
