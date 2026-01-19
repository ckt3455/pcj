<?php

namespace backend\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%buyer_order}}".
 *
 * @property int $id
 * @property int|null $buyer_id
 * @property int|null $user_id
 * @property int|null $type
 * @property int|null $status
 * @property int|null $pay_type
 * @property string|null $order_number
 * @property float|null $money
 * @property float|null $discount
 * @property float|null $total_money
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $paid_time
 * @property int|null $parent_id
 * @property int|null $level
 * @property int|null $audit_time
 */
class BuyerOrder extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%buyer_order}}';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_number', 'discount'], 'default', 'value' => null],
            [['audit_time'], 'default', 'value' => 0],
            [['level'], 'default', 'value' => 1],
            [['total_money'], 'default', 'value' => 0.00],
            [['buyer_id', 'user_id', 'type', 'status', 'pay_type', 'created_at', 'updated_at', 'paid_time', 'parent_id', 'level', 'audit_time'], 'integer'],
            [['money', 'discount', 'total_money'], 'number'],
            [['order_number'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'buyer_id' => 'Buyer ID',
            'user_id' => 'User ID',
            'type' => 'Type',
            'status' => '状态',
            'pay_type' => '支付方式',
            'order_number' => '订单号',
            'money' => '金额',
            'discount' => '折扣',
            'total_money' => '原价',
            'created_at' => '申请时间',
            'updated_at' => 'Updated At',
            'paid_time' => '支付时间',
            'parent_id' => '审批人',
            'level' => 'Level',
            'audit_time' => '审核时间',
        ];
    }

}
