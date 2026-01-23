<?php

namespace backend\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%user_money_log}}".
 *
 * @property int $id
 * @property int|null $user_id
 * @property float|null $number
 * @property int|null $type
 * @property string|null $content
 * @property int|null $created_at
 * @property int|null $updated_at
 */
class UserMoneyLog extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_money_log}}';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['content'], 'default', 'value' => null],
            [['updated_at'], 'default', 'value' => 0],
            [['number'], 'default', 'value' => 0.00],
            [['type'], 'default', 'value' => 1],
            [['user_id', 'type', 'created_at', 'updated_at'], 'integer'],
            [['number'], 'number'],
            [['content'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'number' => 'Number',
            'type' => 'Type',
            'content' => 'Content',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

}
