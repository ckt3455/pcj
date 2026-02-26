<?php

namespace api\controllers;

use api\extensions\ApiBaseController;
use backend\models\Goods;
use backend\models\PickOrder;
use backend\models\PickOrderDetail;
use backend\models\User;
use backend\models\Address;
use Yii;

/**
 * 提货申请 API 控制器
 */
class PickOrderController extends ApiBaseController
{
    /**
     * 创建提货订单
     * POST /api/pick-order/create
     * 
     * @return array
     */
    public function actionCreate()
    {
        $params = Yii::$app->request->post();
        
        $customRules = [
            [['address_id'], 'required', 'message' => '地址不能为空'],
            [['items'], 'required', 'message' => '商品不能为空'],
        ];
        
        $rules = $this->getRules(['token'], $customRules);
        $user_message = User::decrypt($params['token']);
        
        $validate = $this->validateParams($params, $rules);
        if ($validate) {
            return $this->jsonError($validate);
        }
        
        $user_id = $user_message['user_id'];
        $address_id = $params['address_id'];
        $items = $params['items'];
        $content = $params['content'] ?? '';
        
        // 验证 items 格式
        if (!is_array($items) || empty($items)) {
            return $this->jsonError('商品格式不正确');
        }
        
        $result = PickOrder::createPickOrder($user_id, $items, $address_id, $content);
        
        if ($result['error'] === 0) {
            return $this->jsonSuccess([
                'pick_order_id' => $result['pick_order_id'],
                'message' => '提货申请提交成功'
            ]);
        } else {
            return $this->jsonError($result['message']);
        }
    }

    /**
     * 提货订单列表
     * POST /api/pick-order/list
     * 
     * @return array
     */
    public function actionList()
    {
        $params = Yii::$app->request->post();
        
        $customRules = [];
        $rules = $this->getRules(['token'], $customRules);
        $user_message = User::decrypt($params['token']);
        
        $validate = $this->validateParams($params, $rules);
        if ($validate) {
            return $this->jsonError($validate);
        }
        
        $user_id = $user_message['user_id'];
        $page = $params['page'] ?? 1;
        $page_size = $params['page_size'] ?? 20;
        $status = $params['status'] ?? null;
        
        $query = PickOrder::find()
            ->where(['user_id' => $user_id, 'is_delete' => 0])
            ->orderBy(['id' => SORT_DESC]);
        
        if ($status !== null) {
            $query->andWhere(['status' => $status]);
        }
        
        $total = $query->count();
        $models = $query
            ->limit($page_size)
            ->offset(($page - 1) * $page_size)
            ->all();
        
        $list = [];
        foreach ($models as $model) {
            $list[] = [
                'id' => $model->id,
                'pick_number' => $model->pick_number,
                'consignee' => $model->consignee,
                'phone' => $model->phone,
                'address' => $model->province . $model->city . $model->area . $model->address_detail,
                'total_amount' => $model->total_amount,
                'status' => $model->status,
                'status_text' => $model->getStatusText(),
                'created_at' => date('Y-m-d H:i:s', $model->created_at),
            ];
        }
        
        return $this->jsonSuccess([
            'list' => $list,
            'total' => $total,
            'page' => $page,
            'page_size' => $page_size,
        ]);
    }

    /**
     * 提货订单详情
     * POST /api/pick-order/detail
     * 
     * @return array
     */
    public function actionDetail()
    {
        $params = Yii::$app->request->post();
        
        $customRules = [
            [['id'], 'required', 'message' => '订单 ID 不能为空'],
        ];
        
        $rules = $this->getRules(['token'], $customRules);
        $user_message = User::decrypt($params['token']);
        
        $validate = $this->validateParams($params, $rules);
        if ($validate) {
            return $this->jsonError($validate);
        }
        
        $user_id = $user_message['user_id'];
        $id = $params['id'];
        
        $model = PickOrder::findOne($id);
        if (!$model || $model->is_delete) {
            return $this->jsonError('订单不存在');
        }
        
        // 验证权限
        if ($model->user_id != $user_id) {
            return $this->jsonError('无权查看该订单');
        }
        
        $details = PickOrderDetail::find()->where(['pick_order_id' => $id])->all();
        $items = [];
        foreach ($details as $detail) {
            $items[] = [
                'goods_id' => $detail->goods_id,
                'goods_name' => $detail->goods_name,
                'goods_image' => $this->setImg($detail->goods_image),
                'sku_name' => $detail->sku_name ?: '',
                'quantity' => $detail->quantity,
                'price' => $detail->price,
                'subtotal' => $detail->subtotal,
            ];
        }
        
        return $this->jsonSuccess([
            'id' => $model->id,
            'pick_number' => $model->pick_number,
            'consignee' => $model->consignee,
            'phone' => $model->phone,
            'province' => $model->province,
            'city' => $model->city,
            'area' => $model->area,
            'address_detail' => $model->address_detail,
            'total_amount' => $model->total_amount,
            'status' => $model->status,
            'status_text' => $model->getStatusText(),
            'content' => $model->content,
            'created_at' => date('Y-m-d H:i:s', $model->created_at),
            'audit_time' => $model->audit_time ? date('Y-m-d H:i:s', $model->audit_time) : null,
            'pick_time' => $model->pick_time ? date('Y-m-d H:i:s', $model->pick_time) : null,
            'items' => $items,
        ]);
    }

    /**
     * 取消提货订单
     * POST /api/pick-order/cancel
     * 
     * @return array
     */
    public function actionCancel()
    {
        $params = Yii::$app->request->post();
        
        $customRules = [
            [['id'], 'required', 'message' => '订单 ID 不能为空'],
        ];
        
        $rules = $this->getRules(['token'], $customRules);
        $user_message = User::decrypt($params['token']);
        
        $validate = $this->validateParams($params, $rules);
        if ($validate) {
            return $this->jsonError($validate);
        }
        
        $user_id = $user_message['user_id'];
        $id = $params['id'];
        
        $model = PickOrder::findOne($id);
        if (!$model) {
            return $this->jsonError('订单不存在');
        }
        
        // 验证权限
        if ($model->user_id != $user_id) {
            return $this->jsonError('无权操作该订单');
        }
        
        if (!in_array($model->status, [1, 2])) {
            return $this->jsonError('订单状态不允许取消');
        }
        
        $result = PickOrder::cancel($id);
        
        if ($result['error'] === 0) {
            return $this->jsonSuccess(['message' => $result['message']]);
        } else {
            return $this->jsonError($result['message']);
        }
    }

    /**
     * 获取可提货商品列表
     * POST /api/pick-order/goods-list
     * 
     * @return array
     */
    public function actionGoodsList()
    {
        $params = Yii::$app->request->post();
        
        $goodsList = Goods::find()
            ->where(['status' => 1, 'is_del' => 0])
            ->orderBy(['sort' => SORT_ASC, 'id' => SORT_DESC])
            ->all();
        
        $list = [];
        foreach ($goodsList as $goods) {
            $list[] = [
                'id' => $goods->id,
                'title' => $goods->title,
                'price' => $goods->price,
                'thumb' => $this->setImg($goods->thumb),
                'stock' => $goods->stock,
                'has_option' => $goods->has_option,
            ];
        }
        
        return $this->jsonSuccess(['list' => $list]);
    }

    /**
     * 获取用户地址列表
     * POST /api/pick-order/address-list
     * 
     * @return array
     */
    public function actionAddressList()
    {
        $params = Yii::$app->request->post();
        
        $customRules = [];
        $rules = $this->getRules(['token'], $customRules);
        $user_message = User::decrypt($params['token']);
        
        $validate = $this->validateParams($params, $rules);
        if ($validate) {
            return $this->jsonError($validate);
        }
        
        $user_id = $user_message['user_id'];
        
        $addressList = Address::find()
            ->where(['user_id' => $user_id])
            ->orderBy(['is_default' => SORT_DESC, 'id' => SORT_DESC])
            ->all();
        
        $list = [];
        foreach ($addressList as $address) {
            $list[] = [
                'id' => $address->id,
                'province' => $address->province,
                'city' => $address->city,
                'area' => $address->area,
                'content' => $address->content,
                'user' => $address->user,
                'phone' => $address->phone,
                'is_default' => $address->is_default,
                'full_address' => $address->province . $address->city . $address->area . $address->content,
            ];
        }
        
        return $this->jsonSuccess(['list' => $list]);
    }
}
