<?php

namespace api\controllers;

use api\extensions\ApiBaseController;
use api\services\OrderQueryService;
use backend\models\Goods;
use backend\models\GoodsOption;
use backend\models\Order;
use backend\models\User;
use backend\models\UserCart;
use Yii;

/**
 * DefaultController controller
 */
class OrderController extends ApiBaseController
{




    //立即购买
    public function actionBuy()
    {

        $params = Yii::$app->request->post();
        $customRules = [
            [['goods_id'], 'required', 'message' => '产品不能为空'],
            [['address_id'], 'required', 'message' => '地址不能为空'],
            [['number'], 'required', 'message' => '数量不能为空'],
        ];
        $rules = $this->getRules(['token'],$customRules);
        $user_message=User::decrypt($params['token']);
        $validate = $this->validateParams($params, $rules);
        if ($validate) {
            return $this->jsonError($validate);
        }
        $params['user_id']=$user_message['user_id'];
        if(isset($params['sku_value']) and $params['sku_value']){
            $sku=GoodsOption::find()->where(['goods_id'=>$params['goods_id'],'specs'=>$params['sku_value']])->limit(1)->one();
            if(!$sku){
                return $this->jsonError('规格不正确');
            }
            $value='sku_'.$sku['id'];

        }else{
            $value='goods_'.$params['goods_id'];
        }

        $re=Order::addOrder($params['user_id'],[$value=>$params['number']],$params['address_id'],0,$params['content']);

        if($re['error']==0){
            $data=[
                'message'=>'下单成功',
                'order_id'=>$re['order_id']
            ];
            return $this->jsonSuccess($data);
        }else{
            return $this->jsonError($re['message']);
        }

    }


    //购物车购买
    public function actionCartBuy()
    {

        $params = Yii::$app->request->post();
        $customRules = [
            [['cart_id'], 'required', 'message' => '购物车id不能为空'],
            [['address_id'], 'required', 'message' => '地址不能为空'],
        ];
        $rules = $this->getRules(['token'],$customRules);
        $user_message=User::decrypt($params['token']);
        $validate = $this->validateParams($params, $rules);
        if ($validate) {
            return $this->jsonError($validate);
        }
        $params['user_id']=$user_message['user_id'];
        $cart_ids=explode(',',$params['cart_id']);
        $cart=UserCart::find()->where(['in','id',$cart_ids])->all();
        $arr_value=[];
        foreach ($cart as $k=>$v){
            if($v['sku_id']){
                $value='sku_'.$v['sku_id'];
                $arr_value[$value]=$v['number'];
            }else{
                $value='goods_'.$v['goods_id'];
                $arr_value[$value]=$v['number'];
            }
        }

        $re=Order::addOrder($params['user_id'],$arr_value,$params['address_id'],0,$params['content']);

        if($re['error']==0){
            $data=[
                'message'=>'下单成功',
                'order_id'=>$re['order_id']
            ];
            UserCart::deleteAll(['in','id',$cart_ids]);
            return $this->jsonSuccess($data);
        }else{
            return $this->jsonError($re['message']);
        }

    }


    //订单列表
    public function actionList()
    {
        $params = Yii::$app->request->post();
        $customRules = [];
        $rules = $this->getRules(['token'],$customRules);
        $user_message=User::decrypt($params['token']);
        $validate = $this->validateParams($params, $rules);
        if ($validate) {
            return $this->jsonError($validate);
        }
        $params['user_id']=$user_message['user_id'];
        $params = Yii::$app->request->post();
        $goods = OrderQueryService::searchModel($params);
        return $this->jsonSuccess($goods);

    }


    public function actionDetail()
    {
        $params = Yii::$app->request->post();

        // 自定义验证规则
        $customRules = [
            [['order_id'], 'required', 'message' => 'id不能为空'],
        ];
        $rules = $this->getRules(['token'],$customRules);
        $user_message=User::decrypt($params['token']);
        $validate = $this->validateParams($params, $rules);
        if ($validate) {
            return $this->jsonError($validate);
        }
        $params['user_id']=$user_message['user_id'];
        $order=Order::findOne($params['order_id']);
        if(!$order){
            return $this->jsonError('订单不正确');
        }
        if($order->user_id!=$params['user_id']){
            return $this->jsonError('订单不正确');
        }
        $data['detail'] =OrderQueryService::get_one($params['order_id']);
        return $this->jsonSuccess($data);
    }


    public function actionCancel()
    {
        $params = Yii::$app->request->post();
        $customRules = [
            [['order_id'], 'required', 'message' => '订单id不能为空'],
        ];
        $rules = $this->getRules(['token'],$customRules);
        $user_message=User::decrypt($params['token']);
        $validate = $this->validateParams($params, $rules);
        if ($validate) {
            return $this->jsonError($validate);
        }
        $params['user_id']=$user_message['user_id'];
        $order=Order::findOne($params['order_id']);
        if($order and $order['user_id']==$params['user_id']){
            $re=Order::cancel_order($order->id);
            if($re['error']==0){
                $data=[
                    'message'=>'取消成功'
                ];
                return $this->jsonSuccess($data);
            }else{
                return $this->jsonError($re['message']);
            }
        }else{
            return $this->jsonError('无法取消');
        }

    }


    public function actionConfirm()
    {
        $params = Yii::$app->request->post();
        $customRules = [
            [['order_id'], 'required', 'message' => '订单id不能为空'],
        ];
        $rules = $this->getRules(['token'],$customRules);
        $user_message=User::decrypt($params['token']);
        $validate = $this->validateParams($params, $rules);
        if ($validate) {
            return $this->jsonError($validate);
        }
        $params['user_id']=$user_message['user_id'];
        $order=Order::findOne($params['order_id']);
        if($order and $order['user_id']==$params['user_id']){
            $re=Order::finishOrder($order->id);
            if($re['error']==0){
                $data=[
                    'message'=>'确认成功'
                ];
                return $this->jsonSuccess($data);
            }else{
                return $this->jsonError($re['message']);
            }
        }else{
            return $this->jsonError('无法确认');
        }

    }


    public function actionExpress()
    {
        $params = Yii::$app->request->post();
        $customRules = [
            [['order_id'], 'required', 'message' => '订单id不能为空'],
        ];
        $rules = $this->getRules(['token'],$customRules);
        $user_message=User::decrypt($params['token']);
        $validate = $this->validateParams($params, $rules);
        if ($validate) {
            return $this->jsonError($validate);
        }
        $params['user_id']=$user_message['user_id'];
        $order=Order::findOne($params['order_id']);
        if($order and $order['user_id']==$params['user_id']){
            $data=[
                'list'=>[
                    0=>[
                        'time'=>'2026-01-11',
                        'status'=>'已完成',
                        'message'=>'到达宁波'
                    ],
                    1=>[
                        'time'=>'2026-01-10',
                        'status'=>'派送中',
                        'message'=>'到达宁波'
                    ],
                ],
                'express_number'=>$order->express_number,
                'express_name'=>$order->express_name,
            ];
            return $this->jsonSuccess($data);
        }else{
            return $this->jsonError('发生错误');
        }
    }

}
