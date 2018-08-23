<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_saved_reports_shared_with".
 *
 * @property integer $id
 * @property integer $saved_report_id
 * @property integer $user_id
 * @property integer $client_id
 * @property integer $client_case_id
 * @property integer $team_id
 * @property integer $team_loc
 * @property integer $role_id
 *
 * @property SavedReports $savedReport
 */
class ReportsUserSavedSharedWith extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_reports_user_saved_shared_with';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['saved_report_id'], 'required'],
            [['saved_report_id', 'user_id', 'client_id', 'client_case_id', 'team_id', 'team_loc', 'role_id'], 'integer'],
            [['saved_report_id'], 'exist', 'skipOnError' => true, 'targetClass' => ReportsUserSaved::className(), 'targetAttribute' => ['saved_report_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'saved_report_id' => 'Saved Report ID',
            'user_id' => 'User ID',
            'client_id' => 'Client ID',
            'client_case_id' => 'Client Case ID',
            'team_id' => 'Team ID',
            'team_loc' => 'Team Loc',
            'role_id' => 'Role Id'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReportsUserSaved()
    {
        return $this->hasOne(ReportsUserSaved::className(), ['id' => 'saved_report_id']);
    }
}
