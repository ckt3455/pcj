<?php

namespace api\services\order;

use Yii;
use backend\models\OrderRefund;
use backend\models\Order;
use yii\db\Exception;

/**
 * 订单退款服务类
 */
class OrderRefundService
{
    /**
     * 获取退款列表
     *
     * @param array $params
     * @return array
     */
    public static function getList($params)
    {
        $user = Yii::$app->user->getInfo();
        if (empty($user)) {
            throw new Exception('用户未登录', 401);
        }

        $page = isset($params['page']) ? intval($params['page']) : 1;
        $pageSize = isset($params['page_size']) ? intval($params['page_size']) : 20;
        $state = isset($params['state']) ? intval($params['state']) : 0;

        $query = OrderRefund::find()
            ->where(['user_id' => $user['id']])
            ->orderBy(['created_at' => SORT_DESC]);

        // 状态筛选
        if ($state > 0) {
            $query->andWhere(['status' => $state]);
        }

        $total = $query->count();
        $offset = ($page - 1) * $pageSize;

        $list = $query->offset($offset)
            ->limit($pageSize)
            ->asArray()
            ->all();

        // 格式化数据
        foreach ($list as &$item) {
            $item['type_text'] = OrderRefund::$type[$item['type']] ?? '未知';
            $item['status_text'] = OrderRefund::$status[$item['status']] ?? '未知';
            $item['reason_text'] = OrderRefund::$reason[$item['reason']] ?? '未知';
            $item['goods_status_text'] = OrderRefund::$goods_status[$item['goods_status']] ?? '未知';
            $item['created_at_text'] = date('Y-m-d H:i:s', $item['created_at']);
            $item['updated_at_text'] = date('Y-m-d H:i:s', $item['updated_at']);
        }

        return [
            'list' => $list,
            'total' => $total,
            'page' => $page,
            'page_size' => $pageSize,
            'total_page' => ceil($total / $pageSize),
        ];
    }

    /**
     * 获取退款详情
     *
     * @param array $params
     * @return array
     */
    public static function detail($params)
    {
        $user = Yii::$app->user->getInfo();
        if (empty($user)) {
            throw new Exception('用户未登录', 401);
        }

        $refundSn = isset($params['refund_sn']) ? trim($params['refund_sn']) : '';
        if (empty($refundSn)) {
            throw new Exception('退款编号不能为空', 400);
        }

        // 注意：这里使用的是order_number字段，不是refund_sn
        $refund = OrderRefund::find()
            ->where(['order_number' => $refundSn, 'user_id' => $user['id']])
            ->asArray()
            ->one();

        if (!$refund) {
            throw new Exception('退款记录不存在', 404);
        }

        // 获取订单信息
        $order = Order::find()
            ->where(['id' => $refund['order_id']])
            ->asArray()
            ->one();

        // 格式化数据
        $refund['type_text'] = OrderRefund::$type[$refund['type']] ?? '未知';
        $refund['status_text'] = OrderRefund::$status[$refund['status']] ?? '未知';
        $refund['reason_text'] = OrderRefund::$reason[$refund['reason']] ?? '未知';
        $refund['goods_status_text'] = OrderRefund::$goods_status[$refund['goods_status']] ?? '未知';
        $refund['created_at_text'] = date('Y-m-d H:i:s', $refund['created_at']);
        $refund['updated_at_text'] = date('Y-m-d H:i:s', $refund['updated_at']);
        
        // 处理图片
        $refund['images'] = [];
        if (!empty($refund['image'])) {
            $refund['images'] = explode(',', $refund['image']);
        }

        return [
            'refund' => $refund,
            'order' => $order,
        ];
    }

    /**
     * 申请退款
     *
     * @param array $params
     * @return array
     */
    public static function Apply($params)
    {
        $user = Yii::$app->user->getInfo();
        if (empty($user)) {
            throw new Exception('用户未登录', 401);
        }

        $orderSn = isset($params['order_sn']) ? trim($params['order_sn']) : '';
        $type = isset($params['type']) ? intval($params['type']) : 0;
        $reason = isset($params['reason']) ? intval($params['reason']) : 1;
        $money = isset($params['money']) ? floatval($params['money']) : 0;
        $content = isset($params['content']) ? trim($params['content']) : '';
        $image = isset($params['image']) ? trim($params['image']) : '';
        $goodsStatus = isset($params['goods_status']) ? intval($params['goods_status']) : 1;
        $contact = isset($params['name']) ? trim($params['name']) : '';
        $mobile = isset($params['mobile']) ? trim($params['mobile']) : '';

        // 验证参数
        if (empty($orderSn)) {
            throw new Exception('订单编号不能为空', 400);
        }

        if (!in_array($type, [1, 2])) {
            throw new Exception('退款类型不正确', 400);
        }

        if (!in_array($reason, array_keys(OrderRefund::$reason))) {
            throw new Exception('退款原因不正确', 400);
        }

        if ($money <= 0) {
            throw new Exception('退款金额必须大于0', 400);
        }

        if (empty($contact)) {
            throw new Exception('联系人不能为空', 400);
        }

        if (empty($mobile)) {
            throw new Exception('联系电话不能为空', 400);
        }

        // 获取订单
        $order = Order::find()
            ->where(['order_number' => $orderSn, 'user_id' => $user['id']])
            ->one();

        if (!$order) {
            throw new Exception('订单不存在或无权操作', 404);
        }

        // 验证订单状态是否可以退款
        if (!in_array($order->status, [2, 3, 4, 5])) {
            throw new Exception('订单状态不允许退款', 400);
        }

        // 验证退款金额
        if ($money > $order->pay_price) {
            throw new Exception('退款金额不能超过订单支付金额', 400);
        }

        // 检查是否已有退款申请
        $existing = OrderRefund::find()
            ->where(['order_id' => $order->id, 'status' => [1, 2, 4]])
            ->exists();

        if ($existing) {
            throw new Exception('该订单已有退款申请在处理中', 400);
        }

        // 创建退款申请
        $result = OrderRefund::createRefund(
            $order->id,
            $user['id'],
            $type,
            $reason,
            $money,
            $content,
            $image,
            $goodsStatus
        );

        if ($result['error'] == 0) {
            // 更新联系信息
            $refund = OrderRefund::findOne($result['refund_id']);
            if ($refund) {
                $refund->contact = $contact;
                $refund->mobile = $mobile;
                $refund->save(false);
            }

            return [
                'error' => 0,
                'message' => '退款申请提交成功',
                'data' => [
                    'refund_id' => $result['refund_id'],
                    'order_number' => $orderSn,
                ]
            ];
        } else {
            throw new Exception($result['message'], 400);
        }
    }

    /**
     * 取消退款申请
     *
     * @param array $params
     * @return array
     */
    public static function cancel($params)
    {
        $user = Yii::$app->user->getInfo();
        if (empty($user)) {
            throw new Exception('用户未登录', 401);
        }

        $refundId = isset($params['refund_id']) ? intval($params['refund_id']) : 0;
        if ($refundId <= 0) {
            throw new Exception('退款ID不能为空', 400);
        }

        $refund = OrderRefund::find()
            ->where(['id' => $refundId, 'user_id' => $user['id']])
            ->one();

        if (!$refund) {
            throw new Exception('退款申请不存在', 404);
        }

        // 只有待审核状态可以取消
        if ($refund->status != 1) {
            throw new Exception('当前状态不能取消退款申请', 400);
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            // 删除退款记录
            if (!$refund->delete()) {
                throw new \Exception('取消退款申请失败');
            }

            // 恢复订单状态
            $order = $refund->order;
            if ($order) {
                if ($order->pay_status == 1) {
                    $order->status = 3; // 待收货
                } else {
                    $order->status = 2; // 待发货
                }
                $order->save(false);
            }

            $transaction->commit();

            return [
                'error' => 0,
                'message' => '退款申请已取消',
            ];

        } catch (\Exception $e) {
            $transaction->rollBack();
            throw new Exception($e->getMessage(), 400);
        }
    }

    /**
     * 填写退货物流信息
     *
     * @param array $params
     * @return array
     */
    public static function fillExpress($params)
    {
        $user = Yii::$app->user->getInfo();
        if (empty($user)) {
            throw new Exception('用户未登录', 401);
        }

        $refundId = isset($params['refund_id']) ? intval($params['refund_id']) : 0;
        $expressName = isset($params['express_name']) ? trim($params['express_name']) : '';
        $expressNumber = isset($params['express_number']) ? trim($params['express_number']) : '';

        if ($refundId <= 0) {
            throw new Exception('退款ID不能为空', 400);
        }

        if (empty($expressName)) {
            throw new Exception('快递公司不能为空', 400);
        }

        if (empty($expressNumber)) {
            throw new Exception('快递单号不能为空', 400);
        }

        $refund = OrderRefund::find()
            ->where(['id' => $refundId, 'user_id' => $user['id']])
            ->one();

        if (!$refund) {
            throw new Exception('退款申请不存在', 404);
        }

        // 只有退货退款类型且审核通过状态可以填写物流
        if ($refund->type != 2) {
            throw new Exception('非退货退款类型', 400);
        }

        if ($refund->status != 2) {
            throw new Exception('退款申请状态不正确', 400);
        }

        $result = OrderRefund::fillExpress($refundId, $expressName, $expressNumber);

        if ($result['error'] == 0) {
            return [
                'error' => 0,
                'message' => '物流信息填写成功',
            ];
        } else {
            throw new Exception($result['message'], 400);
        }
    }
}
