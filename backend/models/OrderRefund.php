<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "{{%order_refund}}".
 *
 * @property int $id
 * @property int|null $order_id
 * @property int|null $user_id
 * @property int|null $type
 * @property int|null $status
 * @property int|null $goods_status
 * @property int|null $reason
 * @property float|null $money
 * @property string|null $content
 * @property string|null $image
 * @property string|null $express_name
 * @property string|null $express_number
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $detail_id
 * @property string|null $message
 * @property string|null $order_number
 * @property string|null $contact
 * @property string|null $mobile
 */
class OrderRefund extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%order_refund}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['content', 'image', 'express_name', 'express_number', 'message', 'order_number', 'contact', 'mobile'], 'default', 'value' => null],
            [['detail_id'], 'default', 'value' => 0],
            [['reason'], 'default', 'value' => 1],
            [['money'], 'default', 'value' => 0.00],
            [['order_id', 'user_id', 'type', 'status', 'goods_status', 'reason', 'created_at', 'updated_at', 'detail_id'], 'integer'],
            [['money'], 'number'],
            [['content', 'image'], 'string', 'max' => 1000],
            [['express_name', 'express_number', 'message', 'order_number'], 'string', 'max' => 255],
            [['contact', 'mobile'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => '订单id',
            'user_id' => '用户',
            'type' => '类型',
            'status' => '状态',
            'goods_status' => '收货状态',
            'reason' => '理由',
            'money' => '金额',
            'content' => '内容',
            'image' => '图片',
            'express_name' => '快递名称',
            'express_number' => '快递单号',
            'created_at' => '添加时间',
            'updated_at' => 'Updated At',
            'detail_id' => 'Detail ID',
            'message' => '备注',
            'order_number' => '订单编号',
            'contact' => '联系人',
            'mobile' => '电话',
        ];
    }

}
