<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%reports_user_saved_filter_client_case}}".
 *
 * @property integer $id
 * @property integer $reports_user_saved_id
 * @property integer $client_id
 * @property integer $client_case_id
 *
 * @property ReportsUserSaved $reportsUserSaved
 * @property Client $client
 */
class ReportsUserSavedFilterClientCase extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%reports_user_saved_filter_client_case}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['reports_user_saved_id', 'client_id', 'client_case_id'], 'required'],
            [['reports_user_saved_id', 'client_id', 'client_case_id'], 'integer'],
            [['reports_user_saved_id'], 'exist', 'skipOnError' => true, 'targetClass' => ReportsUserSaved::className(), 'targetAttribute' => ['reports_user_saved_id' => 'id']],
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
            'reports_user_saved_id' => 'Reports User Saved ID',
            'client_id' => 'Client ID',
            'client_case_id' => 'Client Case ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReportsUserSaved()
    {
        return $this->hasOne(ReportsUserSaved::className(), ['id' => 'reports_user_saved_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClient()
    {
        return $this->hasOne(Client::className(), ['id' => 'client_id']);
    }
}
