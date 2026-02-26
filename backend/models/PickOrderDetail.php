<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%pick_order_detail}}".
 *
 * @property integer $id
 * @property integer $pick_order_id
 * @property string $pick_number
 * @property integer $goods_id
 * @property integer $sku_id
 * @property string $goods_name
 * @property string $goods_image
 * @property string $sku_name
 * @property integer $quantity
 * @property string $price
 * @property string $subtotal
 */
class PickOrderDetail extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%pick_order_detail}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pick_order_id', 'pick_number', 'goods_id', 'goods_name', 'quantity'], 'required'],
            [['pick_order_id', 'goods_id', 'sku_id', 'quantity'], 'integer'],
            [['price', 'subtotal'], 'number'],
            [['pick_number'], 'string', 'max' => 50],
            [['goods_name', 'goods_image', 'sku_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pick_order_id' => '提货订单 ID',
            'pick_number' => '提货单号',
            'goods_id' => '商品 ID',
            'sku_id' => 'SKU ID',
            'goods_name' => '商品名称',
            'goods_image' => '商品图片',
            'sku_name' => '规格名称',
            'quantity' => '数量',
            'price' => '单价',
            'subtotal' => '小计',
        ];
    }

    /**
     * 关联提货订单
     */
    public function getPickOrder()
    {
        return $this->hasOne(PickOrder::className(), ['id' => 'pick_order_id']);
    }

    /**
     * 关联商品
     */
    public function getGoods()
    {
        return $this->hasOne(Goods::className(), ['id' => 'goods_id']);
    }
}
