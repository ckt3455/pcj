<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "{{%user_collect}}".
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $goods_id
 * @property int|null $time
 */
class UserCollect extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_collect}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['time'], 'default', 'value' => 0],
            [['user_id', 'goods_id', 'time'], 'integer'],
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
            'goods_id' => 'Goods ID',
            'time' => 'Time',
        ];
    }

}
