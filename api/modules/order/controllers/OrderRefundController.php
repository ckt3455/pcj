<?php

namespace api\modules\order\controllers;

use api\extensions\ApiBaseController;
use api\services\order\OrderRefundService;

class OrderRefundController extends ApiBaseController
{
    public function init()
    {
        parent::init();
        $user = \Yii::$app->user->getInfo();
        if (empty($user)&& !in_array(\Yii::$app->requestedRoute, \Yii::$app->params['NOT_TOKEN_ROUTE'])) {
            return (new \api\extensions\ApiHttpException())->renderException(new \Exception('登录已失效，请重新登录', 202));
        }
    }

    /**
     * 退款列表
     * @return array
     */
    public function actionList()
    {
        $params = \Yii::$app->request->post();
        $rules = [
            [['state'], 'default', 'value' => 0, 'message' => '参数'],
            [['page'], 'default', 'value' => 1, 'message' => '页数'],
            [['page_size'], 'default', 'value' => 20, 'message' => '每页显示总数'],
        ];
        $validate = $this->validateParams($params, $rules);
        if ($validate) {
            return $this->jsonError($validate);
        }
        $data = OrderRefundService::getList($this->params);
        return $this->jsonSuccess($data);
    }

    /**
     * 退款详情
     * @return array
     */
    public function actionDetail()
    {
        $params = \Yii::$app->request->post();
        $rules = [
            [['refund_sn'], 'required', 'message' => '{attribute}属必填项'],
        ];
        $validate = $this->validateParams($params, $rules);
        if ($validate) {
            return $this->jsonError($validate);
        }
        $info = OrderRefundService::detail($this->params);
        return $this->jsonSuccess($info);
    }

    /**
     * 申请退款
     * @return array
     */
    public function actionApply()
    {
        $params = \Yii::$app->request->post();
        $rules = [
            [['order_sn', 'type'], 'required', 'message' => '{attribute}属必填项'],
            [['name'], 'required', 'message' => '联系人不能为空'],
            [['mobile'], 'required', 'message' => '联系人手机号不能为空'],
            [['money'], 'required', 'message' => '退款金额不能为空'],
            [['reason'], 'default', 'value' => 1, 'message' => '退款原因'],
            [['content'], 'default', 'value' => '', 'message' => '退款说明'],
            [['image'], 'default', 'value' => '', 'message' => '凭证图片'],
            [['goods_status'], 'default', 'value' => 1, 'message' => '商品状态'],
        ];
        $validate = $this->validateParams($params, $rules);
        if ($validate) {
            return $this->jsonError($validate);
        }
        return OrderRefundService::Apply($this->params);
    }

    /**
     * 取消退款申请
     * @return array
     */
    public function actionCancel()
    {
        $params = \Yii::$app->request->post();
        $rules = [
            [['refund_id'], 'required', 'message' => '退款ID不能为空'],
        ];
        $validate = $this->validateParams($params, $rules);
        if ($validate) {
            return $this->jsonError($validate);
        }
        return OrderRefundService::cancel($this->params);
    }

    /**
     * 填写退货物流信息
     * @return array
     */
    public function actionFillExpress()
    {
        $params = \Yii::$app->request->post();
        $rules = [
            [['refund_id', 'express_name', 'express_number'], 'required', 'message' => '{attribute}不能为空'],
        ];
        $validate = $this->validateParams($params, $rules);
        if ($validate) {
            return $this->jsonError($validate);
        }
        return OrderRefundService::fillExpress($this->params);
    }

    /**
     * 获取退款类型和原因选项
     * @return array
     */
    public function actionOptions()
    {
        $options = [
            'types' => \backend\models\OrderRefund::$type,
            'reasons' => \backend\models\OrderRefund::$reason,
            'goods_statuses' => \backend\models\OrderRefund::$goods_status,
            'statuses' => \backend\models\OrderRefund::$status,
        ];
        return $this->jsonSuccess($options);
    }

    /**
     * 检查订单是否可以退款
     * @return array
     */
    public function actionCheck()
    {
        $params = \Yii::$app->request->post();
        $rules = [
            [['order_sn'], 'required', 'message' => '订单编号不能为空'],
        ];
        $validate = $this->validateParams($params, $rules);
        if ($validate) {
            return $this->jsonError($validate);
        }

        $user = \Yii::$app->user->getInfo();
        $orderSn = $params['order_sn'];

        // 获取订单
        $order = \backend\models\Order::find()
            ->where(['order_number' => $orderSn, 'user_id' => $user['id']])
            ->one();

        if (!$order) {
            return $this->jsonError('订单不存在或无权操作');
        }

        // 检查订单状态
        $canRefund = in_array($order->status, [2, 3, 4, 5]);
        
        // 检查是否已有退款申请
        $hasRefund = \backend\models\OrderRefund::find()
            ->where(['order_id' => $order->id, 'status' => [1, 2, 4]])
            ->exists();

        $data = [
            'can_refund' => $canRefund && !$hasRefund,
            'order_status' => $order->status,
            'order_status_text' => $order->getStatusText(),
            'pay_price' => $order->pay_price,
            'has_refund' => $hasRefund,
            'message' => $canRefund ? ($hasRefund ? '该订单已有退款申请在处理中' : '可以申请退款') : '订单状态不允许退款',
        ];

        return $this->jsonSuccess($data);
    }
}
