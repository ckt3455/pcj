<?php

namespace backend\models;

use Yii;
use \yii\behaviors\TimestampBehavior;

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

    // 退款类型
    public static $type = [
        1 => '仅退款',
        2 => '退货退款',
    ];

    // 退款状态
    public static $status = [
        1 => '待审核',
        2 => '审核通过',
        3 => '审核拒绝',
        4 => '退款中',
        5 => '退款成功',
        6 => '退款失败',
    ];

    // 商品状态
    public static $goods_status = [
        1 => '未收到货',
        2 => '已收到货',
    ];

    // 退款原因
    public static $reason = [
        1 => '不想要了',
        2 => '商品质量问题',
        3 => '商品与描述不符',
        4 => '商品损坏',
        5 => '其他',
    ];

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
            'updated_at' => '更新时间',
            'detail_id' => '订单详情ID',
            'message' => '备注',
            'order_number' => '订单编号',
            'contact' => '联系人',
            'mobile' => '电话',
        ];
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
     * 关联订单
     */
    public function getOrder()
    {
        return $this->hasOne(Order::className(), ['id' => 'order_id']);
    }

    /**
     * 关联用户
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * 获取类型文本
     */
    public function getTypeText()
    {
        return self::$type[$this->type] ?? '未知';
    }

    /**
     * 获取状态文本
     */
    public function getStatusText()
    {
        return self::$status[$this->status] ?? '未知';
    }

    /**
     * 获取原因文本
     */
    public function getReasonText()
    {
        return self::$reason[$this->reason] ?? '未知';
    }

    /**
     * 获取商品状态文本
     */
    public function getGoodsStatusText()
    {
        return self::$goods_status[$this->goods_status] ?? '未知';
    }

    /**
     * 创建退款申请
     *
     * @param int $order_id 订单ID
     * @param int $user_id 用户ID
     * @param int $type 退款类型 1=仅退款 2=退货退款
     * @param int $reason 退款原因
     * @param float $money 退款金额
     * @param string $content 退款说明
     * @param string $image 图片凭证
     * @param int $goods_status 商品状态 1=未收到货 2=已收到货
     * @return array
     */
    public static function createRefund($order_id, $user_id, $type, $reason, $money, $content = '', $image = '', $goods_status = 1)
    {
        $return = ['error' => 1, 'message' => '申请失败', 'refund_id' => 0];

        $transaction = Yii::$app->db->beginTransaction();
        try {
            // 验证订单
            $order = Order::findOne($order_id);
            if (!$order) {
                throw new \Exception('订单不存在');
            }

            // 验证订单属于该用户
            if ($order->user_id != $user_id) {
                throw new \Exception('无权操作该订单');
            }

            // 验证订单状态是否可以退款
            if (!in_array($order->status, [2, 3, 4, 5])) {
                throw new \Exception('订单状态不允许退款');
            }

            // 验证退款金额
            if ($money <= 0 || $money > $order->pay_price) {
                throw new \Exception('退款金额不正确');
            }

            // 检查是否已有退款申请
            $existing = self::find()
                ->where(['order_id' => $order_id, 'status' => [1, 2, 4]])
                ->exists();
            if ($existing) {
                throw new \Exception('该订单已有退款申请在处理中');
            }

            // 创建退款记录
            $refund = new self();
            $refund->order_id = $order_id;
            $refund->user_id = $user_id;
            $refund->order_number = $order->order_number;
            $refund->type = $type;
            $refund->reason = $reason;
            $refund->money = $money;
            $refund->content = $content;
            $refund->image = $image;
            $refund->goods_status = $goods_status;
            $refund->status = 1; // 待审核

            if (!$refund->save()) {
                $errors = $refund->getFirstErrors();
                throw new \Exception('保存失败：' . reset($errors));
            }

            // 更新订单状态为退款退货
            $order->status = 4;
            $order->save(false);

            $transaction->commit();
            $return['error'] = 0;
            $return['message'] = '退款申请提交成功';
            $return['refund_id'] = $refund->id;

        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::error("创建退款申请失败：{$e->getMessage()}, OrderID: {$order_id}, UserID: {$user_id}", __METHOD__);
            $return['message'] = $e->getMessage();
        }

        return $return;
    }

    /**
     * 审核退款申请
     *
     * @param int $id 退款ID
     * @param int $status 审核状态 2=通过 3=拒绝
     * @param string $message 审核备注
     * @return array
     */
    public static function auditRefund($id, $status, $message = '')
    {
        $return = ['error' => 1, 'message' => '审核失败'];

        $refund = self::findOne($id);
        if (!$refund) {
            $return['message'] = '退款申请不存在';
            return $return;
        }

        if ($refund->status != 1) {
            $return['message'] = '退款申请状态不正确';
            return $return;
        }

        if (!in_array($status, [2, 3])) {
            $return['message'] = '审核状态不正确';
            return $return;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $refund->status = $status;
            $refund->message = $message;

            if (!$refund->save()) {
                $errors = $refund->getFirstErrors();
                throw new \Exception('保存失败：' . reset($errors));
            }

            // 如果审核拒绝，恢复订单状态
            if ($status == 3) {
                $order = $refund->order;
                if ($order) {
                    // 根据订单原始状态恢复
                    if ($order->pay_status == 1) {
                        $order->status = 3; // 待收货
                    } else {
                        $order->status = 2; // 待发货
                    }
                    $order->save(false);
                }
            }

            $transaction->commit();
            $return['error'] = 0;
            $return['message'] = '审核成功';

        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::error("审核退款申请失败：{$e->getMessage()}, RefundID: {$id}", __METHOD__);
            $return['message'] = $e->getMessage();
        }

        return $return;
    }

    /**
     * 处理退款（退款操作）
     *
     * @param int $id 退款ID
     * @param int $status 处理状态 5=成功 6=失败
     * @param string $message 处理备注
     * @return array
     */
    public static function processRefund($id, $status, $message = '')
    {
        $return = ['error' => 1, 'message' => '处理失败'];

        $refund = self::findOne($id);
        if (!$refund) {
            $return['message'] = '退款申请不存在';
            return $return;
        }

        if ($refund->status != 2) {
            $return['message'] = '退款申请状态不正确';
            return $return;
        }

        if (!in_array($status, [5, 6])) {
            $return['message'] = '处理状态不正确';
            return $return;
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $refund->status = $status;
            $refund->message = $message;

            if (!$refund->save()) {
                $errors = $refund->getFirstErrors();
                throw new \Exception('保存失败：' . reset($errors));
            }

            // 如果退款成功，更新订单状态
            if ($status == 5) {
                $order = $refund->order;
                if ($order) {
                    $order->status = -1; // 已取消/已退款
                    $order->save(false);
                }
            }

            $transaction->commit();
            $return['error'] = 0;
            $return['message'] = '处理成功';

        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::error("处理退款失败：{$e->getMessage()}, RefundID: {$id}", __METHOD__);
            $return['message'] = $e->getMessage();
        }

        return $return;
    }

    /**
     * 填写退货物流信息
     *
     * @param int $id 退款ID
     * @param string $express_name 快递公司
     * @param string $express_number 快递单号
     * @return array
     */
    public static function fillExpress($id, $express_name, $express_number)
    {
        $return = ['error' => 1, 'message' => '填写失败'];

        $refund = self::findOne($id);
        if (!$refund) {
            $return['message'] = '退款申请不存在';
            return $return;
        }

        if ($refund->type != 2) {
            $return['message'] = '非退货退款类型';
            return $return;
        }

        if ($refund->status != 2) {
            $return['message'] = '退款申请状态不正确';
            return $return;
        }

        $refund->express_name = $express_name;
        $refund->express_number = $express_number;

        if ($refund->save()) {
            $return['error'] = 0;
            $return['message'] = '填写成功';
        }

        return $return;
    }

}
