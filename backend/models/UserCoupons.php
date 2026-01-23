<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "{{%user_coupons}}".
 *
 * @property int $id
 * @property int|null $type
 * @property string|null $goods_id
 * @property string|null $category_id
 * @property int|null $user_id
 * @property int|null $status
 * @property int|null $end_time
 * @property int|null $created_at
 * @property float|null $min_money
 * @property float|null $money
 * @property int|null $start_time
 * @property int|null $updated_at
 */
class UserCoupons extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_coupons}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['goods_id', 'category_id'], 'default', 'value' => null],
            [['status'], 'default', 'value' => 1],
            [['updated_at'], 'default', 'value' => 0],
            [['money'], 'default', 'value' => 0.00],
            [['type', 'user_id', 'status', 'end_time', 'created_at', 'start_time', 'updated_at'], 'integer'],
            [['min_money', 'money'], 'number'],
            [['goods_id'], 'string', 'max' => 1000],
            [['category_id'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'goods_id' => 'Goods ID',
            'category_id' => 'Category ID',
            'user_id' => 'User ID',
            'status' => 'Status',
            'end_time' => 'End Time',
            'created_at' => 'Created At',
            'min_money' => 'Min Money',
            'money' => 'Money',
            'start_time' => 'Start Time',
            'updated_at' => 'Updated At',
        ];
    }

}
