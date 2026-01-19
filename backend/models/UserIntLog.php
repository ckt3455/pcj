<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "{{%user_int_log}}".
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $type
 * @property float|null $number
 * @property string|null $content
 * @property int|null $created_at
 * @property int|null $updated_at
 */
class UserIntLog extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_int_log}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['content'], 'default', 'value' => null],
            [['updated_at'], 'default', 'value' => 0],
            [['type'], 'default', 'value' => 1],
            [['number'], 'default', 'value' => 0.00],
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
            'type' => 'Type',
            'number' => 'Number',
            'content' => 'Content',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

}
