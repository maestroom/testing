<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%evidence_transactions}}".
 *
 * @property integer $id
 * @property integer $evid_num_id
 * @property integer $trans_type
 * @property string $trans_date
 * @property integer $trans_requested_by
 * @property integer $moved_to
 * @property string $trans_reason
 * @property integer $trans_by
 * @property integer $Trans_to
 */
class EvidenceTransaction extends \yii\db\ActiveRecord
{
	public $is_duplicate;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%evidence_transactions}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['evid_num_id', 'trans_type'], 'required'],
            [['evid_num_id', 'trans_type',  'moved_to', 'trans_by', 'Trans_to','trans_requested_by'], 'integer'], //,
            [['trans_date'], 'safe'],
            [['trans_reason'], 'string'],
            [['evid_num_id'], 'exist', 'skipOnError' => true, 'targetClass' => Evidence::className(), 'targetAttribute' => ['evid_num_id' => 'id']],
            [['Trans_to'], 'exist', 'skipOnError' => true, 'targetClass' => EvidenceTo::className(), 'targetAttribute' => ['Trans_to' => 'id']],
            [['trans_requested_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['trans_requested_by' => 'id']],
            [['trans_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['trans_by' => 'id']]            
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'evid_num_id' => 'Evid Num ID',
            'trans_type' => 'Transaction Type',
            'trans_date' => 'Trans Date',
            'trans_requested_by' => 'Trans Requested By',
            'moved_to' => 'Moved To',
            'trans_reason' => 'Reason for Transaction',
            'trans_by' => 'Trans By',
            'Trans_to' => 'Trans To',
        ];
    }
    public function getTransby()
    {
        return $this->hasOne(User::className(), ['id' => 'trans_by'])->alias('trans_by_user');;
    }
    public function getTransRequstedby()
    {
        return $this->hasOne(User::className(), ['id' => 'trans_requested_by'])->alias('trans_requested_user');
    }
    public function getStoredLoc()
    {
        return $this->hasOne(EvidenceStoredLoc::className(), ['id' => 'moved_to']);
    }
    public function getEvidenceTo()
    {
        return $this->hasOne(EvidenceTo::className(), ['id' => 'Trans_to']);
    }
    public function getEvidence()
    {
        return $this->hasOne(Evidence::className(), ['id' => 'evid_num_id']);
    }
    public function getEvidenceclientcase()
    {
        return $this->hasMany(ClientCaseEvidence::className(), ['evid_num_id' => 'evid_num_id']);
    }
    /**
     * get status image in Evidence Grid
     */
    public function getStatusImage($status) {
        if ($status == 1) {
             $statusImg ='<span tabindex="0" class="fa fa-download text-success" title="Checked In"></span>Checked In';
        } else if ($status == 2) {
            $statusImg ='<span tabindex="0" title="Checked Out" class="fa fa-upload text-warning"></span>Checked Out';
        } else if ($status == 3) {
             $statusImg ='<span tabindex="0" title="Destroyed" class="fa fa-times-circle text-danger"></span>Destroyed';
        } else if ($status == 4) {
           $statusImg ='<span tabindex="0" title="Moved" class="fa fa-arrow-right text-warning"></span>Moved';
        } else if ($status == 5) {
          $statusImg ='<span tabindex="0" title="Returned" class="fa fa-arrow-left text-danger"></span>Returned';
        } else {
            $statusImg = "Undefined";
        }
        return $statusImg;
    }
}
