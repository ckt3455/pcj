<?php

namespace api\controllers;

use api\extensions\ApiBaseController;
use api\services\BuyerLevelService;
use api\services\BuyerService;
use api\services\SalesApplyService;
use api\services\SalesMoneyLogService;
use backend\models\Buyer;
use backend\models\BuyerApply;
use backend\models\User;
use common\components\Helper;
use Yii;
/**
 * DefaultController controller
 */
class BuyerController extends ApiBaseController
{





    /**
     * 用户信息
     **/
    public function actionInfo()
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
        $buyer=Buyer::find()->where(['user_id'=>$user_id])->limit(1)->one();
        if(!$buyer){
            return $this->jsonError('没有成为订货商');
        }
        $data=[
            'buyer_id'=>$buyer['id'],
            'name'=>$buyer['name'],
            'image'=>Helper::setImg($buyer->image),
            'money'=>$buyer['money'],
            'goods_money'=>$buyer['goods_money'],
            'level_id'=>$buyer['level_id'],
            'level_message'=>$buyer->level['title'],
            'level_content'=>$buyer->level['content'],
            'worker_count'=>$buyer->getWorkerCount(),
            'buyer_count'=>0,
            'customer_count'=>0
        ];
        return $this->jsonSuccess($data);
    }



    //修改信息
    public function actionUpdateInfo()
    {
        $params = Yii::$app->request->post();
        // 自定义验证规则
        $customRules = [
            [['buyer_id'], 'required', 'message' => 'id不能为空'],
        ];
        $rules = $this->getRules(['token'],$customRules);
        $user_message=User::decrypt($params['token']);
        $validate = $this->validateParams($params, $rules);
        if ($validate) {
            return $this->jsonError($validate);
        }
        $sales=Buyer::findOne($params['buyer_id']);
        if($sales['user_id']==$user_message['user_id']){

            $sales->image=$params['image'];
            $sales->name=$params['name'];
            if(!$sales->save()){
                return $this->jsonError('修改失败');
            }

        }else{
            return $this->jsonError('修改失败');
        }
        $data=[
            'message'=>'修改成功'
        ];
        return $this->jsonSuccess($data);
    }




    public function actionTx()
    {
        $params = Yii::$app->request->post();
        // 自定义验证规则
        $customRules = [
            [['buyer_id'], 'required', 'message' => 'id不能为空'],
        ];
        $rules = $this->getRules(['token'],$customRules);
        $validate = $this->validateParams($params, $rules);
        if ($validate) {
            return $this->jsonError($validate);
        }
        $buyer=Buyer::findOne($params['buyer_id']);
        if(!$buyer){
            return $this->jsonError('没有成为分销商');
        }
        $data=[
            'buyer_id'=>$buyer['id'],
            'zfb_number'=>$buyer['zfb_number'],
            'zfb_name'=>$buyer['zfb_name'],
            'zfb_image'=>Helper::setImg($buyer->zfb_image),
            'wx_number'=>$buyer['wx_number'],
            'wx_name'=>$buyer['wx_name'],
            'wx_image'=>Helper::setImg($buyer->wx_image),
            'bank_number'=>$buyer['bank_number'],
            'bank_name'=>$buyer['bank_name'],
            'bank_kh'=>$buyer['bank_kh'],
        ];
        return $this->jsonSuccess($data);
    }


    public function actionTxSet()
    {
        $params = Yii::$app->request->post();
        $user_message=User::decrypt($params['token']);
        // 自定义验证规则
        $customRules = [
            [['buyer_id'], 'required', 'message' => 'id不能为空'],
        ];
        $rules = $this->getRules(['token'],$customRules);
        $validate = $this->validateParams($params, $rules);
        if ($validate) {
            return $this->jsonError($validate);
        }
        $buyer=Buyer::findOne($params['buyer_id']);
        if(!$buyer){
            return $this->jsonError('没有成为分销商');
        }

        $buyer->zfb_number=$params['zfb_number'];
        $buyer->zfb_name=$params['zfb_name'];
        $buyer->zfb_image=Helper::setImg($params['zfb_image']);
        $buyer->wx_number=$params['wx_number'];
        $buyer->wx_name=$params['wx_name'];
        $buyer->wx_image=Helper::setImg($params['wx_image']);
        $buyer->bank_number=$params['bank_number'];
        $buyer->bank_name=$params['bank_name'];
        $buyer->bank_kh=$params['bank_kh'];
        if(!$buyer->save()){
            return $this->jsonError('修改失败');
        }
        $data=[
            'message'=>'修改成功'
        ];


        return $this->jsonSuccess($data);
    }


    public function actionApply()
    {
        $params = Yii::$app->request->post();
        $user_message=User::decrypt($params['token']);
        // 自定义验证规则
        $customRules = [
            [['buyer_id'], 'required', 'message' => 'id不能为空'],
        ];
        $rules = $this->getRules(['token'],$customRules);
        $validate = $this->validateParams($params, $rules);
        if ($validate) {
            return $this->jsonError($validate);
        }
        $buyer=Buyer::findOne($params['buyer_id']);
        if(!$buyer){
            return $this->jsonError('没有成为订货商');
        }
        $tx_data=[
            'money'=>$params['money'],
            'payment'=>$params['type']
        ];
        $new=new BuyerApply();
        $re=$new->apply($tx_data,$buyer['id']);
        if($re['success']){
            $data=[
                'message'=>'发起提现成功'
            ];
            return $this->jsonSuccess($data);
        }else{
            return $this->jsonError($re['message']);
        }
    }


    //提现记录
    public function actionApplyLog()
    {
        $params = Yii::$app->request->post();
        $customRules = [
            [['buyer_id'], 'required', 'message' => 'id不能为空'],
        ];
        $rules = $this->getRules(['token'],$customRules);
        $user_message=User::decrypt($params['token']);
        $validate = $this->validateParams($params, $rules);
        if ($validate) {
            return $this->jsonError($validate);
        }
        $params['user_id']=$user_message['user_id'];
        $data = SalesApplyService::searchModel($params);
        return $this->jsonSuccess($data);

    }


    //奖励明细
    public function actionMoneyLog()
    {
        $params = Yii::$app->request->post();
        $customRules = [
            [['sales_id'], 'required', 'message' => 'id不能为空'],
        ];
        $rules = $this->getRules(['token'],$customRules);
        $user_message=User::decrypt($params['token']);
        $validate = $this->validateParams($params, $rules);
        if ($validate) {
            return $this->jsonError($validate);
        }
        $params['user_id']=$user_message['user_id'];
        $data = SalesMoneyLogService::searchModel($params);
        return $this->jsonSuccess($data);

    }

    public function actionChildren()
    {
        $params = Yii::$app->request->post();
        $customRules = [
            [['buyer_id'], 'required', 'message' => 'id不能为空'],
        ];
        $rules = $this->getRules(['token'],$customRules);
        $user_message=User::decrypt($params['token']);
        $validate = $this->validateParams($params, $rules);
        if ($validate) {
            return $this->jsonError($validate);
        }
        $params['user_id']=$user_message['user_id'];
        $data = BuyerService::searchModel($params);
        return $this->jsonSuccess($data);

    }


    public function actionLevelList()
    {
        $params = Yii::$app->request->post();
        $customRules = [
            [['buyer_id'], 'required', 'message' => 'id不能为空'],
        ];
        $rules = $this->getRules(['token'],$customRules);
        $user_message=User::decrypt($params['token']);
        $validate = $this->validateParams($params, $rules);
        if ($validate) {
            return $this->jsonError($validate);
        }
        $buyer=Buyer::findOne($params['buyer_id']);
        $buyer_message=[
            'now_level_id'=>$buyer['level_id'],
            'now_level_message'=>$buyer->level['title'],
        ];
        $params['user_id']=$user_message['user_id'];
        $data=[
            'buyer_message'=>$buyer_message,
            'level_message'=>BuyerLevelService::searchModel($params),
        ];
        return $this->jsonSuccess($data);

    }

    public function actionLevelDetail()
    {
        $params = Yii::$app->request->post();
        $customRules = [
            [['buyer_id'], 'required', 'message' => 'id不能为空'],
            [['level_id'], 'required', 'message' => 'id不能为空'],
        ];
        $rules = $this->getRules(['token'],$customRules);
        $user_message=User::decrypt($params['token']);
        $validate = $this->validateParams($params, $rules);
        if ($validate) {
            return $this->jsonError($validate);
        }
        $buyer=Buyer::findOne($params['buyer_id']);
        $buyer_message=[
            'now_level_id'=>$buyer['level_id'],
            'now_level_message'=>$buyer->level['title'],
        ];
        $params['user_id']=$user_message['user_id'];
        $data=[
            'buyer_message'=>$buyer_message,
            'level_message'=>BuyerLevelService::getOne($params['level_id']),
        ];
        return $this->jsonSuccess($data);

    }










}
