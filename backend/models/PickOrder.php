<?php

namespace backend\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%pick_order}}".
 *
 * @property integer $id
 * @property string $pick_number
 * @property integer $user_id
 * @property integer $address_id
 * @property string $consignee
 * @property string $phone
 * @property string $province
 * @property string $city
 * @property string $area
 * @property string $address_detail
 * @property string $total_amount
 * @property integer $status
 * @property string $content
 * @property integer $audit_time
 * @property integer $audit_user_id
 * @property integer $pick_time
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $is_delete
 */
class PickOrder extends ActiveRecord
{
    // 状态定义
    public static $status = [
        1 => '待审核',
        2 => '待提货',
        3 => '已提货',
        4 => '已取消',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%pick_order}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'address_id'], 'required'],
            [['user_id', 'address_id', 'status', 'audit_user_id', 'created_at', 'updated_at', 'audit_time', 'pick_time', 'is_delete'], 'integer'],
            [['total_amount', 'price'], 'number'],
            [['content'], 'string'],
            [['pick_number'], 'string', 'max' => 50],
            [['consignee', 'address_detail', 'province', 'city', 'area'], 'string', 'max' => 255],
            [['phone'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pick_number' => '提货单号',
            'user_id' => '用户',
            'address_id' => '地址',
            'consignee' => '收货人',
            'phone' => '联系电话',
            'province' => '省',
            'city' => '市',
            'area' => '区',
            'address_detail' => '详细地址',
            'total_amount' => '总金额',
            'status' => '状态',
            'content' => '备注',
            'audit_time' => '审核时间',
            'audit_user_id' => '审核人',
            'pick_time' => '提货时间',
            'created_at' => '申请时间',
            'updated_at' => '更新时间',
            'is_delete' => '是否删除',
        ];
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->pick_number = $this->generatePickNumber();
        }
        return parent::beforeSave($insert);
    }

    /**
     * 生成提货单号
     */
    private function generatePickNumber()
    {
        return 'P' . date('YmdHis') . substr(microtime(), 2, 4) . sprintf('%04d', mt_rand(0, 9999));
    }

    /**
     * 关联用户
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * 关联地址
     */
    public function getAddress()
    {
        return $this->hasOne(Address::className(), ['id' => 'address_id']);
    }

    /**
     * 关联详情
     */
    public function getDetails()
    {
        return $this->hasMany(PickOrderDetail::className(), ['pick_order_id' => 'id']);
    }

    /**
     * 关联审核人
     */
    public function getAuditUser()
    {
        return $this->hasOne(Manager::className(), ['id' => 'audit_user_id']);
    }

    /**
     * 获取状态文本
     */
    public function getStatusText()
    {
        return self::$status[$this->status] ?? '未知';
    }

    /**
     * 创建提货订单
     *
     * @param int $user_id 用户 ID
     * @param array $items 商品数组 ['goods_id'=>数量] 或 ['sku_id'=>数量]
     * @param int $address_id 地址 ID
     * @param string $content 备注
     * @return array ['error'=>0|1, 'pick_order_id'=>int, 'message'=>'']
     */
    public static function createPickOrder($user_id, $items, $address_id, $content = '')
    {
        $return = [
            'error' => 1,
            'pick_order_id' => 0,
            'message' => ''
        ];

        $transaction = Yii::$app->db->beginTransaction();
        try {
            // 1. 验证地址
            $address = Address::findOne($address_id);
            if (!$address) {
                throw new \Exception('地址不存在');
            }

            // 2. 验证商品数据
            if (empty($items) || !is_array($items)) {
                throw new \Exception('商品不能为空');
            }

            // 3. 创建提货订单主记录
            $pickOrder = new self();
            $pickOrder->user_id = $user_id;
            $pickOrder->address_id = $address_id;
            $pickOrder->consignee = $address->user;
            $pickOrder->phone = $address->phone;
            $pickOrder->province = $address->province;
            $pickOrder->city = $address->city;
            $pickOrder->area = $address->area;
            $pickOrder->address_detail = $address->content;
            $pickOrder->content = $content;
            $pickOrder->status = 1; // 待审核

            if (!$pickOrder->save()) {
                $errors = $pickOrder->getFirstErrors();
                throw new \Exception('创建订单失败：' . reset($errors));
            }

            // 4. 处理商品详情
            $totalAmount = self::processItems($pickOrder, $items);
            $pickOrder->total_amount = $totalAmount;
            $pickOrder->save(false);

            $transaction->commit();
            $return['error'] = 0;
            $return['pick_order_id'] = $pickOrder->id;

        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::error("创建提货订单失败：{$e->getMessage()}, UserID: {$user_id}", __METHOD__);
            $return['message'] = $e->getMessage();
        }

        return $return;
    }

    /**
     * 处理商品项
     */
    private static function processItems($pickOrder, $items)
    {
        $totalAmount = 0;
        $details = [];

        foreach ($items as $goodsId => $quantity) {
            if ($quantity <= 0) {
                continue;
            }

            $goods = Goods::findOne($goodsId);
            if (!$goods) {
                throw new \Exception("商品 {$goodsId} 不存在");
            }

            if ($goods->status != 1) {
                throw new \Exception("商品 {$goods->title} 已下架");
            }

            if ($goods->stock < $quantity) {
                throw new \Exception("商品 {$goods->title} 库存不足，剩余 {$goods->stock}");
            }

            $subtotal = $goods->price * $quantity;

            $details[] = [
                'pick_order_id' => $pickOrder->id,
                'pick_number' => $pickOrder->pick_number,
                'goods_id' => $goodsId,
                'sku_id' => 0,
                'goods_name' => $goods->title,
                'goods_image' => $goods->thumb,
                'sku_name' => '',
                'quantity' => $quantity,
                'price' => $goods->price,
                'subtotal' => $subtotal,
            ];

            $totalAmount += $subtotal;
        }

        // 批量插入详情
        if (!empty($details)) {
            Yii::$app->db->createCommand()->batchInsert(
                PickOrderDetail::tableName(),
                ['pick_order_id', 'pick_number', 'goods_id', 'sku_id', 'goods_name', 'goods_image', 'sku_name', 'quantity', 'price', 'subtotal'],
                $details
            )->execute();
        }

        return $totalAmount;
    }

    /**
     * 审核提货订单
     *
     * @param int $id 订单 ID
     * @param int $status 状态 2=待提货 4=已取消
     * @param int $audit_user_id 审核人 ID
     * @return array
     */
    public static function audit($id, $status, $audit_user_id)
    {
        $return = ['error' => 1, 'message' => '审核失败'];

        $pickOrder = self::findOne($id);
        if (!$pickOrder) {
            $return['message'] = '订单不存在';
            return $return;
        }

        if ($pickOrder->status != 1) {
            $return['message'] = '订单状态不正确，只能审核待审核的订单';
            return $return;
        }

        if (!in_array($status, [2, 4])) {
            $return['message'] = '审核状态不正确';
            return $return;
        }

        $pickOrder->status = $status;
        $pickOrder->audit_user_id = $audit_user_id;
        $pickOrder->audit_time = time();

        if ($pickOrder->save()) {
            $return['error'] = 0;
            $return['message'] = '审核成功';
        }

        return $return;
    }

    /**
     * 确认提货
     *
     * @param int $id 订单 ID
     * @return array
     */
    public static function confirmPick($id)
    {
        $return = ['error' => 1, 'message' => '操作失败'];

        $pickOrder = self::findOne($id);
        if (!$pickOrder) {
            $return['message'] = '订单不存在';
            return $return;
        }

        if ($pickOrder->status != 2) {
            $return['message'] = '订单状态不正确，只能提货待提货的订单';
            return $return;
        }

        $pickOrder->status = 3;
        $pickOrder->pick_time = time();

        if ($pickOrder->save()) {
            $return['error'] = 0;
            $return['message'] = '提货成功';
        }

        return $return;
    }

    /**
     * 取消订单
     *
     * @param int $id 订单 ID
     * @return array
     */
    public static function cancel($id)
    {
        $return = ['error' => 1, 'message' => '取消失败'];

        $pickOrder = self::findOne($id);
        if (!$pickOrder) {
            $return['message'] = '订单不存在';
            return $return;
        }

        if (!in_array($pickOrder->status, [1, 2])) {
            $return['message'] = '订单状态不允许取消';
            return $return;
        }

        $pickOrder->status = 4;

        if ($pickOrder->save()) {
            $return['error'] = 0;
            $return['message'] = '取消成功';
        }

        return $return;
    }
}
