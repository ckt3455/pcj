<?php

namespace backend\controllers;

use Yii;
use backend\models\PickOrder;
use backend\models\PickOrderDetail;
use backend\search\PickOrderSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * PickOrderController implements the CRUD actions for PickOrder model.
 */
class PickOrderController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all PickOrder models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PickOrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single PickOrder model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $details = PickOrderDetail::find()->where(['pick_order_id' => $id])->all();

        return $this->render('view', [
            'model' => $model,
            'details' => $details,
        ]);
    }

    /**
     * 审核提货订单
     * @param integer $id
     * @return mixed
     */
    public function actionAudit($id)
    {
        $model = $this->findModel($id);

        if (Yii::$app->request->isPost) {
            $status = Yii::$app->request->post('status');
            $result = PickOrder::audit($id, $status, Yii::$app->user->id);

            if ($result['error'] === 0) {
                Yii::$app->session->setFlash('success', $result['message']);
            } else {
                Yii::$app->session->setFlash('error', $result['message']);
            }

            return $this->redirect(['view', 'id' => $id]);
        }

        return $this->render('audit', [
            'model' => $model,
        ]);
    }

    /**
     * 确认提货
     * @param integer $id
     * @return mixed
     */
    public function actionConfirmPick($id)
    {
        $result = PickOrder::confirmPick($id);

        if ($result['error'] === 0) {
            Yii::$app->session->setFlash('success', $result['message']);
        } else {
            Yii::$app->session->setFlash('error', $result['message']);
        }

        return $this->redirect(['view', 'id' => $id]);
    }

    /**
     * 取消订单
     * @param integer $id
     * @return mixed
     */
    public function actionCancel($id)
    {
        $result = PickOrder::cancel($id);

        if ($result['error'] === 0) {
            Yii::$app->session->setFlash('success', $result['message']);
        } else {
            Yii::$app->session->setFlash('error', $result['message']);
        }

        return $this->redirect(['view', 'id' => $id]);
    }

    /**
     * Deletes a single PickOrder model.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->is_delete = 1;
        $model->save();

        return $this->redirect(['index']);
    }

    /**
     * Finds the PickOrder model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PickOrder the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PickOrder::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    // ==================== API 接口 ====================

    /**
     * API: 创建提货订单
     * POST /pick-order/api-create
     * 
     * @return array
     */
    public function actionApiCreate()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $user_id = Yii::$app->request->post('user_id');
        $items = Yii::$app->request->post('items', []);
        $address_id = Yii::$app->request->post('address_id');
        $content = Yii::$app->request->post('content', '');

        if (empty($user_id)) {
            return ['error' => 1, 'message' => '用户 ID 不能为空'];
        }

        if (empty($address_id)) {
            return ['error' => 1, 'message' => '地址 ID 不能为空'];
        }

        if (empty($items)) {
            return ['error' => 1, 'message' => '商品不能为空'];
        }

        $result = PickOrder::createPickOrder($user_id, $items, $address_id, $content);

        return $result;
    }

    /**
     * API: 获取提货订单列表
     * GET /pick-order/api-list
     * 
     * @return array
     */
    public function actionApiList()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $page = Yii::$app->request->get('page', 1);
        $pageSize = Yii::$app->request->get('page_size', 20);
        $status = Yii::$app->request->get('status');
        $user_id = Yii::$app->request->get('user_id');

        $query = PickOrder::find()
            ->andWhere(['is_delete' => 0])
            ->orderBy(['id' => SORT_DESC]);

        if ($status !== null) {
            $query->andWhere(['status' => $status]);
        }

        if ($user_id) {
            $query->andWhere(['user_id' => $user_id]);
        }

        $count = $query->count();
        $models = $query->limit($pageSize)
            ->offset(($page - 1) * $pageSize)
            ->all();

        $list = [];
        foreach ($models as $model) {
            $list[] = [
                'id' => $model->id,
                'pick_number' => $model->pick_number,
                'user_id' => $model->user_id,
                'consignee' => $model->consignee,
                'phone' => $model->phone,
                'address' => $model->province . $model->city . $model->area . $model->address_detail,
                'total_amount' => $model->total_amount,
                'status' => $model->status,
                'status_text' => $model->getStatusText(),
                'created_at' => date('Y-m-d H:i:s', $model->created_at),
            ];
        }

        return [
            'error' => 0,
            'data' => [
                'list' => $list,
                'total' => $count,
                'page' => $page,
                'page_size' => $pageSize,
            ],
        ];
    }

    /**
     * API: 获取提货订单详情
     * GET /pick-order/api-view?id=1
     * 
     * @param integer $id
     * @return array
     */
    public function actionApiView($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = PickOrder::findOne($id);
        if (!$model || $model->is_delete) {
            return ['error' => 1, 'message' => '订单不存在'];
        }

        $details = PickOrderDetail::find()->where(['pick_order_id' => $id])->all();
        $items = [];
        foreach ($details as $detail) {
            $items[] = [
                'goods_id' => $detail->goods_id,
                'goods_name' => $detail->goods_name,
                'goods_image' => $detail->goods_image,
                'sku_name' => $detail->sku_name,
                'quantity' => $detail->quantity,
                'price' => $detail->price,
                'subtotal' => $detail->subtotal,
            ];
        }

        return [
            'error' => 0,
            'data' => [
                'id' => $model->id,
                'pick_number' => $model->pick_number,
                'user_id' => $model->user_id,
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
            ],
        ];
    }

    /**
     * API: 审核提货订单
     * POST /pick-order/api-audit
     * 
     * @return array
     */
    public function actionApiAudit()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $id = Yii::$app->request->post('id');
        $status = Yii::$app->request->post('status'); // 2=通过待提货 4=拒绝已取消
        $audit_user_id = Yii::$app->request->post('audit_user_id', Yii::$app->user->id);

        if (empty($id)) {
            return ['error' => 1, 'message' => '订单 ID 不能为空'];
        }

        if (!in_array($status, [2, 4])) {
            return ['error' => 1, 'message' => '审核状态不正确'];
        }

        $result = PickOrder::audit($id, $status, $audit_user_id);

        return $result;
    }

    /**
     * API: 确认提货
     * POST /pick-order/api-confirm-pick
     * 
     * @return array
     */
    public function actionApiConfirmPick()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $id = Yii::$app->request->post('id');

        if (empty($id)) {
            return ['error' => 1, 'message' => '订单 ID 不能为空'];
        }

        $result = PickOrder::confirmPick($id);

        return $result;
    }

    /**
     * API: 取消订单
     * POST /pick-order/api-cancel
     * 
     * @return array
     */
    public function actionApiCancel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $id = Yii::$app->request->post('id');

        if (empty($id)) {
            return ['error' => 1, 'message' => '订单 ID 不能为空'];
        }

        $result = PickOrder::cancel($id);

        return $result;
    }

    /**
     * API: 获取商品列表（用于选择商品）
     * GET /pick-order/api-goods-list
     * 
     * @return array
     */
    public function actionApiGoodsList()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $goodsList = \backend\models\Goods::find()
            ->where(['status' => 1, 'is_del' => 0])
            ->orderBy(['sort' => SORT_ASC, 'id' => SORT_DESC])
            ->select(['id', 'title', 'price', 'thumb', 'stock'])
            ->asArray()
            ->all();

        return [
            'error' => 0,
            'data' => $goodsList,
        ];
    }

    /**
     * API: 获取用户地址列表
     * GET /pick-order/api-address-list?user_id=1
     * 
     * @return array
     */
    public function actionApiAddressList()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $user_id = Yii::$app->request->get('user_id');

        if (empty($user_id)) {
            return ['error' => 1, 'message' => '用户 ID 不能为空'];
        }

        $addressList = \backend\models\UserAddress::find()
            ->where(['user_id' => $user_id])
            ->orderBy(['is_default' => SORT_DESC, 'id' => SORT_DESC])
            ->asArray()
            ->all();

        return [
            'error' => 0,
            'data' => $addressList,
        ];
    }
}
