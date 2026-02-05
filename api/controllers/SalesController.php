<?php

namespace api\controllers;

use api\extensions\ApiBaseController;
use api\services\SalesApplyService;
use api\services\SalesMoneyLogService;
use api\services\SalesService;
use backend\models\Sales;
use backend\models\SalesApply;
use backend\models\User;
use common\components\Helper;
use Yii;
/**
 * DefaultController controller
 */
class SalesController extends ApiBaseController
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
        $sales=Sales::find()->where(['user_id'=>$user_id])->limit(1)->one();
        if(!$sales){
            return $this->jsonError('没有成为分销商');
        }
        $data=[
            'sales_id'=>$sales['id'],
            'name'=>$sales['name'],
            'image'=>Helper::setImg($sales->image),
            'money'=>$sales['money'],
            'level'=>$sales['level'],
            'level_message'=>Sales::$level_message[$sales['level']],
            'fee'=>10,
        ];
        return $this->jsonSuccess($data);
    }



    //修改信息
    public function actionUpdateInfo()
    {
        $params = Yii::$app->request->post();
        // 自定义验证规则
        $customRules = [
            [['sales_id'], 'required', 'message' => 'id不能为空'],
        ];
        $rules = $this->getRules(['token'],$customRules);
        $user_message=User::decrypt($params['token']);
        $validate = $this->validateParams($params, $rules);
        if ($validate) {
            return $this->jsonError($validate);
        }
        $sales=Sales::findOne($params['sales_id']);
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
        $user_message=User::decrypt($params['token']);
        // 自定义验证规则
        $customRules = [
            [['sales_id'], 'required', 'message' => 'id不能为空'],
        ];
        $rules = $this->getRules(['token'],$customRules);
        $validate = $this->validateParams($params, $rules);
        if ($validate) {
            return $this->jsonError($validate);
        }
        $user_id=$user_message['user_id'];
        $sales=Sales::find()->where(['user_id'=>$user_id])->limit(1)->one();
        if(!$sales){
            return $this->jsonError('没有成为分销商');
        }
        $data=[
            'sales_id'=>$sales['id'],
            'zfb_number'=>$sales['zfb_number'],
            'zfb_name'=>$sales['zfb_name'],
            'zfb_image'=>Helper::setImg($sales->zfb_image),
            'wx_number'=>$sales['wx_number'],
            'wx_name'=>$sales['wx_name'],
            'wx_image'=>Helper::setImg($sales->wx_image),
            'bank_number'=>$sales['bank_number'],
            'bank_name'=>$sales['bank_name'],
            'bank_kh'=>$sales['bank_kh'],
        ];
        return $this->jsonSuccess($data);
    }


    public function actionTxSet()
    {
        $params = Yii::$app->request->post();
        $user_message=User::decrypt($params['token']);
        // 自定义验证规则
        $customRules = [
            [['sales_id'], 'required', 'message' => 'id不能为空'],
        ];
        $rules = $this->getRules(['token'],$customRules);
        $validate = $this->validateParams($params, $rules);
        if ($validate) {
            return $this->jsonError($validate);
        }
        $user_id=$user_message['user_id'];
        $sales=Sales::find()->where(['user_id'=>$user_id])->limit(1)->one();
        if(!$sales){
            return $this->jsonError('没有成为分销商');
        }

        $sales->zfb_number=$params['zfb_number'];
        $sales->zfb_name=$params['zfb_name'];
        $sales->zfb_image=Helper::setImg($params['zfb_image']);
        $sales->wx_number=$params['wx_number'];
        $sales->wx_name=$params['wx_name'];
        $sales->wx_image=Helper::setImg($params['wx_image']);
        $sales->bank_number=$params['bank_number'];
        $sales->bank_name=$params['bank_name'];
        $sales->bank_kh=$params['bank_kh'];
        if(!$sales->save()){
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
            [['sales_id'], 'required', 'message' => 'id不能为空'],
        ];
        $rules = $this->getRules(['token'],$customRules);
        $validate = $this->validateParams($params, $rules);
        if ($validate) {
            return $this->jsonError($validate);
        }
        $user_id=$user_message['user_id'];
        $sales=Sales::find()->where(['user_id'=>$user_id])->limit(1)->one();
        if(!$sales){
            return $this->jsonError('没有成为分销商');
        }
        $tx_data=[
            'money'=>$params['money'],
            'payment'=>$params['type']
        ];
        $new=new SalesApply();
        $re=$new->apply($tx_data,$sales['id']);
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
            [['sales_id'], 'required', 'message' => 'id不能为空'],
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
            [['sales_id'], 'required', 'message' => 'id不能为空'],
        ];
        $rules = $this->getRules(['token'],$customRules);
        $user_message=User::decrypt($params['token']);
        $validate = $this->validateParams($params, $rules);
        if ($validate) {
            return $this->jsonError($validate);
        }
        $params['user_id']=$user_message['user_id'];
        $data = SalesService::searchModel($params);
        return $this->jsonSuccess($data);

    }







}
