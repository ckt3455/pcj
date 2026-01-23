<?php

namespace api\controllers;

use api\extensions\ApiBaseController;
use api\services\UserMoneyLogQueryService;
use backend\models\Goods;
use backend\models\RechargeSet;
use backend\models\SetImage;
use backend\models\User;
use backend\models\UserWorkerOrder;
use common\components\Helper;
use Yii;

/**
 * DefaultController controller
 */
class RechargeController extends ApiBaseController
{


    public function actionList()
    {

        $model=RechargeSet::find()->orderBy('money asc')->all();
        $data=[
            'list'=>[],
            'message'=>'注：XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXAI淳泡大师账号含有余额、积分、优惠券，可在购物时抵扣货款，注销后资产会清零，请谨慎注销账号！'
        ];
        foreach ($model as $k=>$v){
            $data['list'][]=[
                'recharge_id'=>$v['id'],
                'money'=>$v->money,
                'give_money'=>$v->give_money
            ];
        }
        return $this->jsonSuccess($data);

    }

    public function actionLogList()
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
        $data = UserMoneyLogQueryService::searchModel($params);
        return $this->jsonSuccess($data);
    }


    public function actionLogList2()
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
        $params['type']=2;
        $data = UserMoneyLogQueryService::searchModel($params);
        return $this->jsonSuccess($data);
    }



    public function actionMoney()
    {
        $params = Yii::$app->request->post();
        $user_message=User::decrypt($params['token']);
        // 自定义验证规则
        $customRules = [];
        $rules = $this->getRules(['token'],$customRules);
        $validate = $this->validateParams($params, $rules);
        if ($validate) {
            return $this->jsonError($validate);
        }
        $user_id=$user_message['user_id'];
        $user=User::findOne($user_id);
        $data=[
            'money'=>$user['money'],
        ];
        return $this->jsonSuccess($data);
    }



}
