<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "{{%user_cart}}".
 *
 * @property int $id
 * @property int|null $sku_id
 * @property int|null $goods_id
 * @property int|null $number
 * @property int|null $user_id
 * @property int|null $created_at
 * @property int|null $updated_at
 */
class UserCart extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_cart}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['updated_at'], 'default', 'value' => 0],
            [['sku_id', 'goods_id', 'number', 'user_id', 'created_at', 'updated_at'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sku_id' => 'Sku ID',
            'goods_id' => 'Goods ID',
            'number' => 'Number',
            'user_id' => 'User ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

}
