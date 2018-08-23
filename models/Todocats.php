<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%todo_cats}}".
 *
 * @property integer $id
 * @property string $todo_cat
 * @property string $todo_desc
 * @property string $notes
 * @property integer $stop
 * @property integer $remove
 */
class Todocats extends \yii\db\ActiveRecord
{
	public $cat_desc;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%todo_cats}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['todo_cat'], 'required'],
            [['todo_cat', 'todo_desc', 'notes'], 'string'],
            [['stop', 'remove'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'todo_cat' => 'Todo Category',
            'todo_desc' => 'Todo Description',
            'notes' => 'Notes',
            'stop' => 'Stop SLA Clock',
            'remove' => 'Remove',
        ];
    }
}
